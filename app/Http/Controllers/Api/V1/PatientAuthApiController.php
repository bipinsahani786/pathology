<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class PatientAuthApiController extends BaseApiController
{
    /**
     * API: Authenticate patient from an external website login form and return SSO redirect URL.
     */
    public function login(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $validator = Validator::make($request->all(), [
            'patient_id'  => 'nullable|string',
            'bill_number' => 'nullable|string',
            'phone'       => 'nullable|string',
            'mobile'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error.', 422, $validator->errors()->toArray());
        }

        $rawPhone = $request->input('phone') ?: $request->input('mobile');
        $identifier = trim($request->input('patient_id') ?: $request->input('bill_number') ?: '');

        if (empty($rawPhone)) {
            return $this->error('Phone / Mobile number is required.', 422);
        }

        if (empty($identifier)) {
            return $this->error('Patient ID or Bill Number is required.', 422);
        }

        // Clean phone to last 10 digits
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        $phone10 = substr($cleanPhone, -10);

        // Extract numeric portion if alphanumeric
        $numericId = (int) preg_replace('/[^0-9]/', '', $identifier);

        // Check if identifier matches an invoice / bill number
        $invoicePatientId = \App\Models\Invoice::where('company_id', $company->id)
            ->where(function ($inv) use ($identifier) {
                $inv->where('invoice_number', $identifier)
                    ->orWhere('barcode', $identifier);
            })->value('patient_id');

        // Find patient belonging to this company
        $user = User::where('company_id', $company->id)
            ->whereHas('patientProfile')
            ->where(function ($q) use ($phone10) {
                $q->where('phone', 'like', "%{$phone10}");
            })
            ->where(function ($query) use ($identifier, $numericId, $invoicePatientId) {
                if ($invoicePatientId) {
                    $query->where('id', $invoicePatientId);
                } elseif ($numericId > 0) {
                    $query->where('id', $numericId);
                }

                // Check PatientProfile ID string (e.g. PAT-0012)
                $query->orWhereHas('patientProfile', function ($p) use ($identifier) {
                    $p->where('patient_id_string', 'like', "%{$identifier}%");
                });
            })
            ->with('patientProfile')
            ->first();

        if (!$user) {
            return $this->error('Patient details not found. Please verify your Patient ID / Bill Number and Registered Mobile Number.', 404);
        }

        // Generate tamper-proof temporary signed SSO login URL (valid for 15 minutes)
        $ssoUrl = URL::temporarySignedRoute(
            'portal.sso',
            now()->addMinutes(15),
            ['user' => $user->id]
        );

        $patientIdString = $user->patientProfile->patient_id_string ?? ('PAT-' . str_pad($user->id, 4, '0', STR_PAD_LEFT));

        return $this->success([
            'patient_name'  => $user->name,
            'patient_id'    => $patientIdString,
            'phone'         => $user->phone,
            'redirect_url'  => $ssoUrl,
            'dashboard_url' => route('portal.dashboard'),
        ], 'Login verified successfully. Redirecting to patient dashboard...');
    }

    /**
     * Web Route: Handle the signed SSO redirect and log the patient into their portal session.
     */
    public function ssoLogin(Request $request, $userId): RedirectResponse
    {
        // 1. Verify cryptographic URL signature and expiration
        if (!$request->hasValidSignature()) {
            return redirect()->route('portal.login')->with('error', 'Login session link has expired or is invalid. Please log in again.');
        }

        // 2. Fetch the patient user
        $user = User::with(['patientProfile', 'company'])->find($userId);

        if (!$user || !$user->patientProfile) {
            return redirect()->route('portal.login')->with('error', 'Invalid patient account.');
        }

        // 3. Log the patient in
        Auth::login($user, true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        // 4. Redirect straight to their Patient Portal Dashboard
        return redirect()->route('portal.dashboard');
    }

    /**
     * Web Route: Direct POST login from an external HTML form (Form Action).
     */
    public function directFormLogin(Request $request): RedirectResponse
    {
        $apiKey = $request->input('api_key');
        $company = Company::with('plan')->where('api_key', $apiKey)->first();

        if (!$company || !$company->api_enabled || !$company->hasWebsiteApiFeature()) {
            return redirect()->back()->with('error', 'Website API integration is inactive or unauthorized.');
        }

        $rawPhone = $request->input('phone') ?: $request->input('mobile');
        $identifier = trim($request->input('patient_id') ?: $request->input('bill_number') ?: '');

        if (empty($rawPhone) || empty($identifier)) {
            return redirect()->back()->with('error', 'Please provide both Patient ID/Bill Number and Mobile Number.');
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        $phone10 = substr($cleanPhone, -10);
        $numericId = (int) preg_replace('/[^0-9]/', '', $identifier);

        $invoicePatientId = \App\Models\Invoice::where('company_id', $company->id)
            ->where(function ($inv) use ($identifier) {
                $inv->where('invoice_number', $identifier)
                    ->orWhere('barcode', $identifier);
            })->value('patient_id');

        $user = User::where('company_id', $company->id)
            ->whereHas('patientProfile')
            ->where(function ($q) use ($phone10) {
                $q->where('phone', 'like', "%{$phone10}");
            })
            ->where(function ($query) use ($identifier, $numericId, $invoicePatientId) {
                if ($invoicePatientId) {
                    $query->where('id', $invoicePatientId);
                } elseif ($numericId > 0) {
                    $query->where('id', $numericId);
                }
                $query->orWhereHas('patientProfile', function ($p) use ($identifier) {
                    $p->where('patient_id_string', 'like', "%{$identifier}%");
                });
            })
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Patient details not found.');
        }

        Auth::login($user, true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return redirect()->route('portal.dashboard');
    }
}

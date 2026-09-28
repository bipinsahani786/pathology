<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $identifier = trim(ltrim(trim($request->input('patient_id') ?: $request->input('bill_number') ?: ''), '#'));

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

        // Check if identifier matches an invoice / bill number (case-insensitive)
        $invoicePatientId = \App\Models\Invoice::where('company_id', $company->id)
            ->where(function ($inv) use ($identifier) {
                $inv->whereRaw('LOWER(invoice_number) = ?', [strtolower($identifier)])
                    ->orWhere('invoice_number', $identifier)
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
                } else {
                    $query->where(function ($sub) use ($identifier, $numericId) {
                        if ($numericId > 0) {
                            $sub->where('id', $numericId);
                        }
                        $sub->orWhereHas('patientProfile', function ($p) use ($identifier, $numericId) {
                            $p->whereRaw('LOWER(patient_id_string) = ?', [strtolower($identifier)])
                              ->orWhere('patient_id_string', 'like', "%{$identifier}%");
                            if ($numericId > 0) {
                                $p->orWhere('patient_id_string', 'like', "%{$numericId}%");
                            }
                        });
                    });
                }
            })
            ->with('patientProfile')
            ->first();

        if (!$user) {
            return $this->error('Patient details not found. Please verify your Patient ID / Bill Number and Registered Mobile Number.', 404);
        }

        // Generate tamper-proof single-use token (valid for 15 minutes)
        // Works reliably across domains, reverse proxies, and ports
        $token = Str::random(64);
        Cache::put("sso_patient_{$token}", [
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'created_at' => now()->timestamp,
        ], now()->addMinutes(15));

        $ssoUrl = route('portal.sso.consume', ['token' => $token]);

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
     * Web Route: Handle single-use token SSO redirect and log the patient into their portal session.
     */
    public function consumeSso(Request $request): RedirectResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('portal.login')->with('error', 'SSO token is missing. Please log in again.');
        }

        $data = Cache::pull("sso_patient_{$token}");

        if (!$data || empty($data['user_id'])) {
            return redirect()->route('portal.login')->with('error', 'Login session link has expired or has already been used. Please log in again.');
        }

        $user = User::with(['patientProfile', 'company'])->find($data['user_id']);

        if (!$user || !$user->patientProfile) {
            return redirect()->route('portal.login')->with('error', 'Invalid patient account.');
        }

        Auth::login($user, true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        Session::forget('patient_id');

        return redirect()->route('portal.dashboard');
    }

    /**
     * Web Route: Handle legacy signed SSO redirect for backward compatibility.
     */
    public function ssoLogin(Request $request, $userId = null): RedirectResponse
    {
        // If passed as token query parameter
        if ($request->has('token')) {
            return $this->consumeSso($request);
        }

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
        Session::forget('patient_id');

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
        $identifier = trim(ltrim(trim($request->input('patient_id') ?: $request->input('bill_number') ?: ''), '#'));

        if (empty($rawPhone) || empty($identifier)) {
            return redirect()->back()->with('error', 'Please provide both Patient ID/Bill Number and Mobile Number.');
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        $phone10 = substr($cleanPhone, -10);
        $numericId = (int) preg_replace('/[^0-9]/', '', $identifier);

        $invoicePatientId = \App\Models\Invoice::where('company_id', $company->id)
            ->where(function ($inv) use ($identifier) {
                $inv->whereRaw('LOWER(invoice_number) = ?', [strtolower($identifier)])
                    ->orWhere('invoice_number', $identifier)
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
                } else {
                    $query->where(function ($sub) use ($identifier, $numericId) {
                        if ($numericId > 0) {
                            $sub->where('id', $numericId);
                        }
                        $sub->orWhereHas('patientProfile', function ($p) use ($identifier, $numericId) {
                            $p->whereRaw('LOWER(patient_id_string) = ?', [strtolower($identifier)])
                              ->orWhere('patient_id_string', 'like', "%{$identifier}%");
                            if ($numericId > 0) {
                                $p->orWhere('patient_id_string', 'like', "%{$numericId}%");
                            }
                        });
                    });
                }
            })
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Patient details not found.');
        }

        Auth::login($user, true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        Session::forget('patient_id');

        return redirect()->route('portal.dashboard');
    }
}

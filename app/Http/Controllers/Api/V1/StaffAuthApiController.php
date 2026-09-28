<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StaffAuthApiController extends BaseApiController
{
    /**
     * API: Authenticate lab staff / admin / doctor from external website login form
     * and return secure SSO redirect URL.
     */
    public function login(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $validator = Validator::make($request->all(), [
            'login'    => 'nullable|string',
            'email'    => 'nullable|string',
            'phone'    => 'nullable|string',
            'username' => 'nullable|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error.', 422, $validator->errors()->toArray());
        }

        $loginInput = trim($request->input('login') ?: $request->input('email') ?: $request->input('phone') ?: $request->input('username') ?: '');
        $password = (string) $request->input('password');

        if (empty($loginInput)) {
            return $this->error('Email, Phone, or Username is required.', 422);
        }

        // Clean phone to last 10 digits if numeric
        $cleanPhone = preg_replace('/[^0-9]/', '', $loginInput);
        $phone10 = (strlen($cleanPhone) >= 10) ? substr($cleanPhone, -10) : null;

        // Find user belonging to this company (or super_admin)
        $user = User::where(function ($q) use ($company) {
            $q->where('company_id', $company->id)
              ->orWhere(function ($sub) {
                  $sub->whereNull('company_id')
                      ->whereHas('roles', fn ($r) => $r->where('name', 'super_admin'));
              });
        })
        ->where(function ($q) use ($loginInput, $phone10) {
            $q->whereRaw('LOWER(email) = ?', [strtolower($loginInput)])
              ->orWhere('email', $loginInput);

            if ($phone10) {
                $q->orWhere('phone', 'like', "%{$phone10}");
            }
        })
        ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->error('Invalid credentials. Please verify your email/phone and password.', 401);
        }

        if (!$user->is_active) {
            return $this->error('User account is currently inactive. Please contact the administrator.', 403);
        }

        // Determine destination dashboard
        $targetRoute = $this->determineDashboardRoute($user, $company->id);

        // Generate tamper-proof, single-use SSO token valid for 15 minutes
        $token = Str::random(64);
        Cache::put("sso_staff_{$token}", [
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'target'     => $targetRoute,
            'created_at' => now()->timestamp,
        ], now()->addMinutes(15));

        $redirectUrl = route('auth.sso.consume', ['token' => $token]);

        return $this->success([
            'user_id'       => $user->id,
            'user_name'     => $user->name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'role'          => $user->roles->first()?->name ?? 'staff',
            'redirect_url'  => $redirectUrl,
            'dashboard_url' => route($targetRoute),
        ], 'Login verified successfully. Redirecting to dashboard...');
    }

    /**
     * Web Route: Handle single-use SSO token redirect and authenticate staff session.
     */
    public function consumeSso(Request $request): RedirectResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'SSO token is missing. Please log in again.');
        }

        // Pull token atomically from cache to ensure single-use
        $data = Cache::pull("sso_staff_{$token}");

        if (!$data || empty($data['user_id'])) {
            return redirect()->route('login')->with('error', 'Login session link has expired or has already been used. Please log in again.');
        }

        $user = User::find($data['user_id']);

        if (!$user || !$user->is_active) {
            return redirect()->route('login')->with('error', 'User account is inactive or not found.');
        }

        // Log the user in
        Auth::login($user, true);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        // Clear Spatie permissions cache for fresh roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $targetRoute = $data['target'] ?? 'dashboard';

        return redirect()->route($targetRoute);
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

        $loginInput = trim($request->input('login') ?: $request->input('email') ?: $request->input('phone') ?: $request->input('username') ?: '');
        $password = (string) $request->input('password');

        if (empty($loginInput) || empty($password)) {
            return redirect()->back()->with('error', 'Please provide both email/phone and password.');
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $loginInput);
        $phone10 = (strlen($cleanPhone) >= 10) ? substr($cleanPhone, -10) : null;

        $user = User::where(function ($q) use ($company) {
            $q->where('company_id', $company->id)
              ->orWhere(function ($sub) {
                  $sub->whereNull('company_id')
                      ->whereHas('roles', fn ($r) => $r->where('name', 'super_admin'));
              });
        })
        ->where(function ($q) use ($loginInput, $phone10) {
            $q->whereRaw('LOWER(email) = ?', [strtolower($loginInput)])
              ->orWhere('email', $loginInput);

            if ($phone10) {
                $q->orWhere('phone', 'like', "%{$phone10}");
            }
        })
        ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid email/phone or password.');
        }

        if (!$user->is_active) {
            return redirect()->back()->with('error', 'User account is currently inactive.');
        }

        Auth::login($user, true);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $targetRoute = $this->determineDashboardRoute($user, $company->id);

        return redirect()->route($targetRoute);
    }

    /**
     * Helper to determine appropriate dashboard route based on user roles and settings.
     */
    protected function determineDashboardRoute(User $user, int $companyId): string
    {
        if ($user->hasRole('super_admin')) {
            return 'admin.dashboard';
        }

        if ($user->hasAnyRole(['doctor', 'agent', 'collection_center']) || $user->collection_center_id) {
            return 'partner.dashboard';
        }

        if ($user->hasRole('phlebotomist')) {
            return 'phlebotomist.dashboard';
        }

        $defaultPage = \App\Models\Configuration::getFor('default_login_page', 'lab.dashboard', $companyId, $user->branch_id);

        return $defaultPage ?: 'lab.dashboard';
    }
}

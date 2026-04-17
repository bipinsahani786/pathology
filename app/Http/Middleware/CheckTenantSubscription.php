<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckTenantSubscription
{
    /**
     * Handle an incoming request.
     * Checks if the tenant's subscription/trial is active before granting access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 1. Allow super admins to bypass all subscription checks
        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }

        // 2. Check subscription status for lab admins, staff members, and partners
        if ($user && $user->company_id) {
            $company = $user->company;

            if (!$company) {
                abort(403, 'Workspace not found.');
            }

            if ($company->status !== 'active') {
                abort(403, 'Your workspace has been suspended. Please contact support.');
            }

            // Check if the subscription period has expired
            if ($company->trial_ends_at && Carbon::now()->greaterThan($company->trial_ends_at)) {
                
                $currentRoute = $request->route()->getName();
                $allowedRoutes = ['lab.subscription.expired', 'lab.billing.upgrade', 'logout'];

                if (!in_array($currentRoute, $allowedRoutes)) {
                    return redirect()->route('lab.subscription.expired')
                        ->with('error', 'Your subscription has expired. Please renew to continue.');
                }
            }
        }

        return $next($request);
    }
}
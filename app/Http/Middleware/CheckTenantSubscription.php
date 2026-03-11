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

        // 2. Check subscription status for lab admins or staff members
        if ($user && $user->company_id) {
            $company = $user->company; 

            if (!$company) {
                abort(403, 'Workspace not found. It may have been deleted.');
            }
            // Block access if the company account has been manually suspended by a super admin
            if ($company->status !== 'active') {
                abort(403, 'Your workspace has been suspended. Please contact support.');
            }

            // Check if the 15-day trial or active subscription period has expired
            if ($company->trial_ends_at && Carbon::now()->greaterThan($company->trial_ends_at)) {
                
                // Allow access ONLY to the billing/upgrade page to prevent an infinite redirection loop
                if ($request->route()->getName() !== 'lab.billing.upgrade') {
                    
                    return redirect()->route('lab.billing.upgrade')
                        ->with('error', 'Your free trial has expired. Please choose a plan to continue using Zytrixon SaaS.');
                }
            }
        }

        return $next($request);
    }
}
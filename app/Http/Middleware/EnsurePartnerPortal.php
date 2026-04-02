<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePartnerPortal
{
    /**
     * Handle an incoming request for the Partner Portal (/partner/*).
     * Ensures only Doctors, Agents, and Collection Centers enter.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Super Admin bypass
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // 2. Identify if they are a Partner (by Role OR attributes)
        $isPartner = $user->hasAnyRole(['doctor', 'agent', 'collection_center', 'partner']) || 
                     $user->collection_center_id || 
                     $user->doctorProfile || 
                     $user->agentProfile;

        if ($isPartner) {
            return $next($request);
        }

        // 3. If they are internal lab staff trying to enter the partner portal
        if ($user->hasAnyRole(['lab_admin', 'staff', 'branch_admin']) || $user->company_id) {
            return redirect()->route('lab.dashboard')->with('error', 'You do not have access to the Partner Portal.');
        }

        abort(403, 'Unauthorized access to the Partner Portal.');
    }
}

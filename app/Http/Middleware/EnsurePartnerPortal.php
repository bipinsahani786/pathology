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
        $roles = $user->roles->pluck('name')->toArray();
        $isPartnerRole = collect($roles)->contains(fn($r) => in_array($r, ['doctor', 'agent', 'collection_center', 'partner']) || str_ends_with($r, '_doctor') || str_ends_with($r, '_agent') || str_ends_with($r, '_collection_center'));

        $isPartner = $isPartnerRole || 
                     $user->collection_center_id || 
                     $user->doctorProfile || 
                     $user->agentProfile;

        if ($isPartner) {
            return $next($request);
        }

        // 3. If they are internal lab staff or patients trying to enter the partner portal
        $isPatient = $user->hasRole('patient') || $user->patientProfile || collect($roles)->contains(fn($r) => str_ends_with($r, '_patient'));
        if ($isPatient) {
            return redirect()->route('portal.dashboard')->with('error', 'Redirected to your patient portal.');
        }

        $isInternalRole = collect($roles)->contains(fn($r) => in_array($r, ['lab_admin', 'staff', 'branch_admin']) || str_ends_with($r, '_admin') || str_ends_with($r, '_staff'));
        
        if ($isInternalRole || $user->company_id) {
            return redirect()->route('lab.dashboard')->with('error', 'You do not have access to the Partner Portal.');
        }

        abort(403, 'Unauthorized access to the Partner Portal.');
    }
}

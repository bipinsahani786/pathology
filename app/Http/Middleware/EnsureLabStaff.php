<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLabStaff
{
    /**
     * Handle an incoming request.
     * Ensures the user is an internal lab staff member (has company_id and is not a external partner/patient).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 1. Must be logged in (auth middleware usually handles this, but safety first)
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. Super Admin always has access
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // 3. Must belong to a company/tenant
        if (!$user->company_id) {
            abort(403, 'Unauthorized. You do not belong to any workspace.');
        }

        // 4. Strictly Block external partners from internal lab areas
        // (External roles: patient, doctor, agent, collection_center)
        $roles = $user->roles->pluck('name')->toArray();
        $isPartnerRole = collect($roles)->contains(fn($r) => in_array($r, ['patient', 'doctor', 'agent', 'collection_center']) || str_ends_with($r, '_patient') || str_ends_with($r, '_doctor') || str_ends_with($r, '_agent') || str_ends_with($r, '_collection_center'));

        $isPartner = $isPartnerRole || 
                     $user->collection_center_id || 
                     $user->doctorProfile || 
                     $user->agentProfile;

        if ($isPartner) {
            // WHITELIST: Allow Collection Centers to access POS, Invoices, and Profile only.
            $isCollector = $user->hasRole('collection_center') || 
                           $user->collection_center_id || 
                           collect($roles)->contains(fn($r) => str_ends_with($r, '_collection_center'));

            if ($isCollector) {
                $allowedPaths = ['lab/pos*', 'lab/invoices*', 'lab/profile*', 'lab/invoice*'];
                foreach ($allowedPaths as $path) {
                    if ($request->is($path)) {
                        return $next($request);
                    }
                }
            }
            
            // Differentiate redirect based on partner type
            $isPatient = $user->hasRole('patient') || $user->patientProfile || collect($roles)->contains(fn($r) => str_ends_with($r, '_patient'));
            
            if ($isPatient) {
                return redirect()->route('portal.dashboard')->with('error', 'Redirected to your patient portal.');
            }

            return redirect()->route('partner.dashboard')->with('error', 'Redirected to your partner dashboard.');
        }

        // 5. If they passed all above, they are considered lab-internal staff
        // (This includes lab_admin, staff, and any custom dynamic roles)
        return $next($request);
    }
}

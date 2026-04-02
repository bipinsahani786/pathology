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
        $isPartner = $user->hasAnyRole(['patient', 'doctor', 'agent', 'collection_center']) || 
                     $user->collection_center_id || 
                     $user->doctorProfile || 
                     $user->agentProfile;

        if ($isPartner) {
            // WHITELIST: Allow Collection Centers to access POS, Invoices, and Profile only.
            // If they hit the lab dashboard or settings, block them.
            if ($user->hasRole('collection_center') || $user->collection_center_id) {
                $allowedPaths = ['lab/pos*', 'lab/invoices*', 'lab/profile*', 'lab/invoice*'];
                foreach ($allowedPaths as $path) {
                    if ($request->is($path)) {
                        return $next($request);
                    }
                }
            }
            
            // Logged in as partner but trying to access lab-internal dashboard or management
            return redirect()->route('partner.dashboard')->with('error', 'Redirected to your dashboard.');
        }

        // 5. If they passed all above, they are considered lab-internal staff
        // (This includes lab_admin, staff, and any custom dynamic roles)
        return $next($request);
    }
}

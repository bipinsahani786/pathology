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

        // 4. Must NOT be an external role (patient, doctor, agent, collection_center)
        // These roles have their own portals/prefixes.
        if ($user->hasAnyRole(['patient', 'doctor', 'agent', 'collection_center'])) {
            // Exception: Collection Centers are allowed to access POS and Invoices if needed
            if ($user->hasRole('collection_center') && ($request->is('lab/pos*') || $request->is('lab/invoices*'))) {
                return $next($request);
            }
            abort(403, 'Unauthorized access to lab internal area.');
        }

        // 5. If they passed all above, they are considered lab-internal staff
        // (This includes lab_admin, staff, and any custom dynamic roles)
        return $next($request);
    }
}

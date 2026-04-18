<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatientSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Relying on standard Laravel Auth
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('portal.login')->with('error', 'Please login to access your portal.');
        }

        // Verify the authenticated user is actually a patient
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if (!$user->patientProfile) {
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('portal.login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}

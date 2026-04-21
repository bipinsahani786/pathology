<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user
     */
    public function loginAs(User $user)
    {
        // 0. Feature Check
        if (!config('features.impersonation', true)) {
            abort(403, 'Impersonation feature is disabled by the administrator.');
        }

        // 1. Security Check: Only admins can impersonate
        $originalUser = auth()->user();
        if (!$originalUser->hasAnyRole(['super_admin', 'lab_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Prevent infinite loop/impersonating other admins (optional, but safer)
        if ($user->hasAnyRole(['super_admin', 'lab_admin'])) {
            return back()->with('error', 'Cannot impersonate another administrator.');
        }

        // 3. Store the original admin ID in session
        session(['impersonate_original_id' => $originalUser->id]);

        // 4. Log in as the target user
        Auth::login($user);

        // 5. Redirect based on role
        if ($user->hasRole('collection_center') || $user->hasAnyRole(['doctor', 'agent'])) {
            return redirect()->route('partner.dashboard');
        }

        return redirect()->route('lab.dashboard');
    }

    /**
     * Stop impersonating and return to the original admin
     */
    public function stopImpersonating()
    {
        $originalId = session()->pull('impersonate_original_id');

        if ($originalId) {
            Auth::loginUsingId($originalId);
            return redirect()->route('lab.dashboard')->with('message', 'Back to Admin session.');
        }

        return redirect('/');
    }
}

<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|string',
        'password' => 'required|min:6',
    ];

    public function mount()
    {
        // If already logged in as a staff/admin/partner, redirect to their dashboard
        // But if logged in as a patient, stay here so they can log in as staff
        if (Auth::check()) {
            $user = Auth::user();
            
            // 1. If it's a patient, don't redirect (let them switch to a staff account if needed)
            if ($user->patientProfile) {
                return;
            }

            // 2. Super Admin
            if ($user->hasRole('super_admin')) {
                return redirect()->route('admin.dashboard');
            }

            // 3. Partner Portal (Doctor, Agent, CC)
            if ($user->hasAnyRole(['doctor', 'agent', 'collection_center']) || $user->collection_center_id) {
                return redirect()->route('partner.dashboard');
            }

            // 4. Lab Staff
            if ($user->company_id) {
                return redirect()->route('lab.dashboard');
            }
        }
    }

    public function login()
    {
        $this->validate();

        $loginValue = trim($this->email);
        
        // Determine if logging in with email or phone
        $loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $loginField => $loginValue,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            $user = Auth::user();
            
            // Check if account is active separately for better feedback
            if (!$user->is_active) {
                Auth::logout();
                $this->addError('email', 'Your account is currently inactive. Please contact the administrator.');
                return;
            }

            session()->regenerate();
            
            // Redirect based on user role
            if ($user->hasRole('super_admin')) {
                return redirect()->route('admin.dashboard');
            } 
            
            // 1. External Referral Partners & Collection Centers (Priority)
            if ($user->hasAnyRole(['doctor', 'agent', 'collection_center']) || $user->collection_center_id) {
                return redirect()->route('partner.dashboard');
            } 
            
            // 2. Internal Lab Staff (Standard Lab Operations)
            if ($user->hasAnyRole(['lab_admin', 'staff', 'branch_admin']) || $user->company_id) {
                return redirect()->route('lab.dashboard');
            }

            return redirect('/');
        }

        $this->addError('email', 'Invalid email/phone or password.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth'); 
    }
}
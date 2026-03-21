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

    public function login()
    {
        $this->validate();

        // Determine if logging in with email or phone
        $loginField = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $loginField => $this->email,
            'password' => $this->password,
            'is_active' => true
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->hasRole('super_admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasAnyRole(['doctor', 'agent'])) {
                return redirect()->route('partner.dashboard');
            } elseif ($user->company_id) {
                // Anyone else with a company ID is lab staff (lab_admin, staff, or custom role)
                return redirect()->route('lab.dashboard');
            }
            return redirect('/');
        }

        $this->addError('email', 'Invalid credentials or inactive account.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest'); 
    }
}
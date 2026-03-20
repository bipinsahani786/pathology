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
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => true], $this->remember)) {
            session()->regenerate();
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->hasRole('super_admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('lab_admin')) {
                return redirect()->route('lab.dashboard');
            } elseif ($user->hasAnyRole(['doctor', 'agent', 'collection_center'])) {
                return redirect()->route('partner.dashboard');
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
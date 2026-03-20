<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PartnerProfile extends Component
{
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id,
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $this->validate([
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->password)
        ]);

        $this->reset(['password', 'password_confirmation']);
        session()->flash('password_success', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.partner.partner-profile');
    }
}

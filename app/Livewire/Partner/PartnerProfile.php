<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\{User, UserDetail};
use Illuminate\Support\Facades\{Auth, Hash, Storage};
use App\Traits\HasSecureStorage;
use Illuminate\Validation\Rules\Password;

class PartnerProfile extends Component
{
    use WithFileUploads, HasSecureStorage;

    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;
    
    public $new_photo;
    public $profile_photo_url;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        
        // Load profile photo from details
        if ($user->details && $user->details->profile_photo) {
            $this->profile_photo_url = $this->getSecureUrl($user->details->profile_photo);
        }
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id,
            'new_photo' => 'nullable|image|max:1024',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        // Handle Photo Upload
        if ($this->new_photo) {
            $path = $this->new_photo->store('profile-photos');
            
            $details = UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_id' => $user->company_id,
                    'profile_photo' => $path
                ]
            );

            $this->profile_photo_url = $this->getSecureUrl($path);
            $this->new_photo = null;
            
            $this->dispatch('profile-updated');
        }

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

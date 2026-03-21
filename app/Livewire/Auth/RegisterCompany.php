<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Services\CompanyRegistrationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

#[Title('Create Workspace | Pathology Lab Management Software')]
class RegisterCompany extends Component
{
    public $lab_name, $owner_name, $email, $phone, $password, $password_confirmation;
    public $agree_terms = false;

    protected function rules()
    {
        return [
            'lab_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            // Ensure email is unique across both users and companies tables
            'email' => 'required|email|unique:users,email|unique:companies,email', 
            'phone' => 'required|numeric|digits:10',
            'password' => 'required|min:8|same:password_confirmation',
            'agree_terms' => 'accepted',
        ];
    }

    /**
     * Handle the registration form submission
     */
    public function register()
    {
        $registrationService = new CompanyRegistrationService();
        $validatedData = $this->validate();

        // Trigger the service to setup the database architecture for the new tenant
        $user = $registrationService->registerNewLab($validatedData);

        // Automatically log the user in after successful registration
        Auth::login($user);

        // Redirect to the tenant's main dashboard
        // Note: Make sure the route name matches your actual dashboard route
        return redirect()->route('lab.dashboard'); 
    }

    public function render()
    {
        return view('livewire.auth.register-company')
            ->layout('layouts.guest', ['title' => 'Register Your Lab']);
    }
}
<?php

namespace App\Livewire\Patient;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PatientLogin extends Component
{
    public $patient_id = '';

    public $mobile = '';

    public $errorMessage = '';

    protected $rules = [
        'patient_id' => 'required',
        'mobile' => 'required|min:10',
    ];

    public function mount()
    {
        // If already logged in as a patient, redirect to portal dashboard
        // If logged in as staff, stay here so they can log in as a patient (test account)
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user->patientProfile) {
                return redirect()->route('portal.dashboard');
            }
        }
    }

    public $conflictingUsers = null;

    public function login()
    {
        $this->validate();

        // 1. Clean data
        $inputId = strtoupper(trim($this->patient_id));
        $inputMobile = trim($this->mobile);

        // 2. Extract strictly numeric part
        // If PAT-0032 or PAT0032, this gives 32.
        $numericId = (int) preg_replace('/[^0-9]/', '', $inputId);

        // Attempt search:
        // We explicitly avoid checking Spatie's role('patient') just in case
        // demo seeders or manual entries didn't assign the exact role string.
        // Checking for the existence of `patientProfile` is mathematically secure.
        $users = User::with('company')
            ->where('phone', $inputMobile)
            ->whereHas('patientProfile') // Ensure they actually are a patient
            ->where(function ($query) use ($inputId, $numericId) {
                if ($numericId > 0) {
                    $query->where('id', $numericId);
                }

                // Also check by PatientProfile patient_id_string
                $query->orWhereHas('patientProfile', function ($q) use ($inputId) {
                    $q->where('patient_id_string', 'like', '%'.$inputId.'%');
                });
            })
            ->get();

        if ($users->count() === 1) {
            $this->loginAsUser($users->first()->id);
        } elseif ($users->count() > 1) {
            // Conflict! Multiple patients with same Mobile + Patient ID across different labs
            $this->conflictingUsers = $users;
        } else {
            $this->errorMessage = 'Patient details not found. Please verify your ID and Mobile Number.';
        }
    }

    public function loginAsUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            \Illuminate\Support\Facades\Auth::login($user, true); // Log in using standard auth

            // Clean up old custom session if it exists to prevent side effects
            Session::forget('patient_id');

            return redirect()->route('portal.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.patient.patient-login')
            ->layout('layouts.auth');
    }
}

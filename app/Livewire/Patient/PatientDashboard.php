<?php

namespace App\Livewire\Patient;

use App\Models\User;
use App\Models\TestReport;
use App\Models\Invoice;
use App\Models\SiteSetting;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PatientDashboard extends Component
{
    public $patient;
    public $reportsCount = 0;
    public $pendingReportsCount = 0;
    public $activeMembership;
    public $totalSavings = 0;
    public $greeting;
    public $lab;
    public $branch;
    public $siteSetting;

    public function mount()
    {
        $this->patient = Auth::user();
        $this->patient->load(['company', 'branch']);

        // Stats summary
        $this->reportsCount = TestReport::where('patient_id', $this->patient->id)->count();
        $this->pendingReportsCount = TestReport::where('patient_id', $this->patient->id)->where('status', 'pending')->count();
        $this->activeMembership = $this->patient->activeMembership ? $this->patient->activeMembership->load('membership') : null;
        
        $this->totalSavings = Invoice::where('patient_id', $this->patient->id)->sum('discount_amount');

        $this->lab = $this->patient->company;
        $this->branch = $this->patient->branch;
        $this->siteSetting = SiteSetting::first();

        // Random medical greeting
        $greetings = [
            "Wishing you a speedy and full recovery!",
            "Take care of yourself, and get well soon!",
            "Sending you strength and healthy vibes for your recovery.",
            "Health is wealth. We are here to help you get back on your feet.",
            "Rest up and feel better soon. Your health is our priority.",
            "Hope you feel better with each passing day!",
        ];
        $this->greeting = $greetings[array_rand($greetings)];
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('portal.login');
    }

    public function render()
    {
        return view('livewire.patient.patient-dashboard');
    }
}

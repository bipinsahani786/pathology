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
    public $reports;
    public $activeMembership;
    public $totalSavings = 0;
    public $lab;
    public $branch;
    public $collectionCenter;
    public $siteSetting;

    public function mount()
    {
        // Auth check is handled by middleware, so user is guaranteed to be logged in
        $this->patient = Auth::user();
        
        // Ensure relationships are loaded
        $this->patient->load(['company', 'branch', 'collectionCenter']);

        // Fetch reports
        $this->reports = TestReport::where('patient_id', $this->patient->id)
            ->with(['invoice'])
            ->latest()
            ->get();

        $this->activeMembership = $this->patient->activeMembership;

        // Calculate total savings across all invoices
        $this->totalSavings = Invoice::where('patient_id', $this->patient->id)
                                     ->sum('discount_amount');

        $this->lab = $this->patient->company;
        $this->branch = $this->patient->branch;
        $this->collectionCenter = $this->patient->collectionCenter;
        $this->siteSetting = SiteSetting::first();
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

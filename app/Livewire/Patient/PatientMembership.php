<?php

namespace App\Livewire\Patient;

use App\Models\Invoice;
use App\Models\PatientMembership as MembershipRecord;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PatientMembership extends Component
{
    public $patient;
    public $activeMembership;
    public $membershipHistory;
    public $totalSavings = 0;
    public $testsBenefitedCount = 0;

    public function mount()
    {
        $this->patient = Auth::user();
        
        $this->activeMembership = $this->patient->activeMembership ? $this->patient->activeMembership->load('membership') : null;

        $this->membershipHistory = MembershipRecord::where('patient_id', $this->patient->id)
            ->where('id', '!=', $this->activeMembership->id ?? 0)
            ->with('membership')
            ->latest()
            ->get();

        $invoicesWithSavings = Invoice::where('patient_id', $this->patient->id)
                                      ->where('discount_amount', '>', 0);
        
        $this->totalSavings = $invoicesWithSavings->sum('discount_amount');
        $this->testsBenefitedCount = $invoicesWithSavings->count();
    }

    public function render()
    {
        return view('livewire.patient.patient-membership');
    }
}

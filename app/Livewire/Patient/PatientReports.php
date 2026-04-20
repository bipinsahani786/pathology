<?php

namespace App\Livewire\Patient;

use App\Models\TestReport;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PatientReports extends Component
{
    public $patient;
    public $reports;

    public function mount()
    {
        $this->patient = Auth::user();
        
        $this->reports = TestReport::where('patient_id', $this->patient->id)
            ->with(['invoice'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.patient.patient-reports');
    }
}

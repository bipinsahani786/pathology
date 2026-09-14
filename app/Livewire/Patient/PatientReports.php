<?php

namespace App\Livewire\Patient;

use App\Models\TestReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientReports extends Component
{
    public $patient;

    public $reports;

    public $selectedVisit = null;

    public $showTrackingModal = false;

    public function mount()
    {
        $this->patient = Auth::user();

        $this->reports = TestReport::where('patient_id', $this->patient->id)
            ->with([
                'invoice.homeCollection.phlebotomist.phlebotomistProfile',
                'invoice.homeCollection.statusLogs.changedBy',
            ])
            ->latest()
            ->get();
    }

    public function viewTracking($visitId)
    {
        $this->selectedVisit = \App\Models\HomeCollection::where('patient_id', $this->patient->id)
            ->with([
                'phlebotomist.phlebotomistProfile',
                'invoice.items.labTest',
                'statusLogs.changedBy',
            ])
            ->find($visitId);

        if ($this->selectedVisit) {
            $this->showTrackingModal = true;
        }
    }

    public function closeTrackingModal()
    {
        $this->showTrackingModal = false;
        $this->selectedVisit = null;
    }

    public function render()
    {
        return view('livewire.patient.patient-reports');
    }
}

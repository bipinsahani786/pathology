<?php

namespace App\Livewire\Patient;

use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Models\TestReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

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

    public $activeHomeVisits;

    public $selectedVisit = null;

    public $showTrackingModal = false;

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

        // Recent / Active Home Collections for this patient
        $this->loadHomeVisits();

        // Random medical greeting
        $greetings = [
            'Wishing you a speedy and full recovery!',
            'Take care of yourself, and get well soon!',
            'Sending you strength and healthy vibes for your recovery.',
            'Health is wealth. We are here to help you get back on your feet.',
            'Rest up and feel better soon. Your health is our priority.',
            'Hope you feel better with each passing day!',
        ];
        $this->greeting = $greetings[array_rand($greetings)];
    }

    public function loadHomeVisits()
    {
        $this->activeHomeVisits = \App\Models\HomeCollection::where('patient_id', $this->patient->id)
            ->with([
                'phlebotomist.phlebotomistProfile',
                'invoice.items.labTest',
                'statusLogs.changedBy',
            ])
            ->orderByRaw("
                CASE status
                    WHEN 'En Route' THEN 1
                    WHEN 'Arrived' THEN 2
                    WHEN 'Assigned' THEN 3
                    WHEN 'Pending' THEN 4
                    WHEN 'Collected' THEN 5
                    WHEN 'Dispatched' THEN 6
                    WHEN 'Received' THEN 7
                    WHEN 'Cancelled' THEN 8
                    ELSE 9
                END
            ")
            ->latest('id')
            ->take(5)
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

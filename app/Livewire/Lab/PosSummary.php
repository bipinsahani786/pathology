<?php

namespace App\Livewire\Lab;

use App\Models\Invoice;
use Livewire\Component;

class PosSummary extends Component
{
    public $invoiceId;
    public $invoice;

    public function mount($invoice)
    {
        $this->invoiceId = $invoice;
        $this->loadInvoice();
    }

    public function loadInvoice()
    {
        $companyId = auth()->user()->company_id;
        
        // Find the invoice strictly for this company
        $this->invoice = Invoice::with([
            'patient.patientProfile', 
            'doctor.doctorProfile', 
            'items', 
            'payments',
            'branch',
            'membership',
            'patientMembership'
        ])
        ->where('company_id', $companyId)
        ->findOrFail($this->invoiceId);
    }

    public function render()
    {
        return view('livewire.lab.pos-summary')->layout('layouts.app', ['title' => 'Bill Summary']);
    }
}

<?php

namespace App\Livewire\Lab;

use App\Models\Company;
use App\Models\Configuration;
use App\Models\Invoice;
use Livewire\Component;

class InvoicePrint extends Component
{
    public $invoiceId;

    public Invoice $invoice;

    public $company;

    public $template;

    public function mount($id)
    {
        $this->invoiceId = $id;
        $this->invoice = Invoice::with(['items', 'payments.paymentMode', 'patient.patientProfile', 'doctor.doctorProfile', 'collectionCenter', 'creator'])
            ->findOrFail($id);

        $this->company = Company::find(auth()->user()->company_id);
        $this->template = Configuration::getFor('bill_template', 'classic');
    }

    public function render()
    {
        return view('livewire.lab.invoice-templates.'.$this->template, [
            'invoice' => $this->invoice,
            'company' => $this->company,
        ])->layout('layouts.app', ['title' => 'Print Invoice #'.$this->invoice->invoice_number]);
    }
}

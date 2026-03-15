<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\{Company, Configuration};
use Illuminate\Support\Facades\Storage;

class SettingsManager extends Component
{
    use WithFileUploads;

    // Active Tab
    public $activeTab = 'profile';

    // ==========================================
    // LAB PROFILE
    // ==========================================
    public $lab_name, $lab_email, $lab_phone, $lab_address;
    public $lab_website, $lab_gst_number, $lab_tagline;
    public $lab_logo;
    public $new_logo;
    public $profileSaved = false;

    // ==========================================
    // INVOICE SETTINGS
    // ==========================================
    public $invoice_prefix = 'INV';
    public $invoice_separator = '-';
    public $invoice_date_format = 'ym';
    public $invoice_counter_digits = 4;
    public $invoice_counter_reset = 'monthly';
    public $invoiceSaved = false;

    // ==========================================
    // BILL TEMPLATE
    // ==========================================
    public $bill_template = 'classic';
    public $templateSaved = false;

    public function mount()
    {
        $company = Company::find(auth()->user()->company_id);
        if ($company) {
            $this->lab_name = $company->name;
            $this->lab_email = $company->email;
            $this->lab_phone = $company->phone;
            $this->lab_address = $company->address;
            $this->lab_website = $company->website;
            $this->lab_gst_number = $company->gst_number;
            $this->lab_tagline = $company->tagline;
            $this->lab_logo = $company->logo;
        }

        // Load invoice settings from configurations table
        $this->invoice_prefix = Configuration::getFor('invoice_prefix', 'INV');
        $this->invoice_separator = Configuration::getFor('invoice_separator', '-');
        $this->invoice_date_format = Configuration::getFor('invoice_date_format', 'ym');
        $this->invoice_counter_digits = (int) Configuration::getFor('invoice_counter_digits', 4);
        $this->invoice_counter_reset = Configuration::getFor('invoice_counter_reset', 'monthly');
        $this->bill_template = Configuration::getFor('bill_template', 'classic');
    }

    // ==========================================
    // SAVE LAB PROFILE
    // ==========================================
    public function saveProfile()
    {
        $this->validate([
            'lab_name' => 'required|string|max:255',
            'lab_email' => 'nullable|email|max:255',
            'lab_phone' => 'nullable|string|max:20',
            'lab_address' => 'nullable|string|max:500',
            'lab_website' => 'nullable|url|max:255',
            'lab_gst_number' => 'nullable|string|max:50',
            'lab_tagline' => 'nullable|string|max:255',
            'new_logo' => 'nullable|image|max:2048',
        ]);

        $company = Company::find(auth()->user()->company_id);

        // Handle logo upload
        $logoPath = $company->logo;
        if ($this->new_logo) {
            $logoPath = $this->new_logo->store('logos', 'public');
        }

        $company->update([
            'name' => $this->lab_name,
            'email' => $this->lab_email,
            'phone' => $this->lab_phone,
            'address' => $this->lab_address,
            'website' => $this->lab_website,
            'gst_number' => $this->lab_gst_number,
            'tagline' => $this->lab_tagline,
            'logo' => $logoPath,
        ]);

        $this->lab_logo = $logoPath;
        $this->new_logo = null;
        $this->profileSaved = true;
    }

    // ==========================================
    // SAVE INVOICE SETTINGS
    // ==========================================
    public function saveInvoiceSettings()
    {
        $this->validate([
            'invoice_prefix' => 'required|string|max:20',
            'invoice_separator' => 'nullable|string|max:5',
            'invoice_date_format' => 'required|string|in:ym,ymd,Ymd,Y,none',
            'invoice_counter_digits' => 'required|integer|min:2|max:10',
            'invoice_counter_reset' => 'required|string|in:daily,monthly,yearly,never',
        ]);

        Configuration::setFor('invoice_prefix', $this->invoice_prefix);
        Configuration::setFor('invoice_separator', $this->invoice_separator);
        Configuration::setFor('invoice_date_format', $this->invoice_date_format);
        Configuration::setFor('invoice_counter_digits', $this->invoice_counter_digits);
        Configuration::setFor('invoice_counter_reset', $this->invoice_counter_reset);

        $this->invoiceSaved = true;
    }

    // ==========================================
    // SAVE BILL TEMPLATE
    // ==========================================
    public function saveTemplate()
    {
        Configuration::setFor('bill_template', $this->bill_template);
        $this->templateSaved = true;
    }

    /**
     * Generate a preview of the invoice number format.
     */
    public function getInvoicePreviewProperty(): string
    {
        $sep = $this->invoice_separator ?: '';
        $prefix = $this->invoice_prefix ?: 'INV';

        $dateMap = [
            'ym' => date('ym'),
            'ymd' => date('ymd'),
            'Ymd' => date('Ymd'),
            'Y' => date('Y'),
            'none' => '',
        ];
        $datePart = $dateMap[$this->invoice_date_format] ?? date('ym');

        $counter = str_pad(1, max((int)$this->invoice_counter_digits, 2), '0', STR_PAD_LEFT);

        $parts = array_filter([$prefix, $datePart, $counter]);
        return implode($sep, $parts);
    }

    public function render()
    {
        return view('livewire.lab.settings-manager')
            ->layout('layouts.app', ['title' => 'Settings']);
    }
}

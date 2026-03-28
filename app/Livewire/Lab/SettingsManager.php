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
    public $activeTab = 'profile'; // profile, invoice, template, pdf, staff

    // ==========================================
    // LAB PROFILE
    // ==========================================
    public $lab_name, $lab_email, $lab_phone, $lab_address;
    public $lab_website, $lab_gst_number, $lab_tagline;
    public $lab_logo, $lab_favicon;
    public $new_logo, $new_favicon;
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
    // PATIENT SETTINGS
    // ==========================================
    public $patient_id_prefix = 'PAT';
    public $patient_id_digits = 4;
    public $patientSettingsSaved = false;

    // ==========================================
    // BARCODE SETTINGS
    // ==========================================
    public $barcode_prefix = 'LAB';
    public $barcode_date_format = 'ymd';
    public $barcode_counter_digits = 6;
    public $barcodeSaved = false;

    // ==========================================
    // BILL TEMPLATE
    // ==========================================
    public $bill_template = 'classic';
    public $templateSaved = false;

    // ==========================================
    // PDF HEADER / FOOTER
    // ==========================================
    public $pdf_show_header = true;
    public $pdf_show_footer = true;
    public $pdf_header_image;       // stored path
    public $pdf_footer_image;       // stored path
    public $new_header_image;       // upload
    public $new_footer_image;       // upload
    public $pdfSaved = false;

    // Report Signatory
    public $authorized_signatory_name;
    public $authorized_signatory_designation;
    public $signature_image;
    public $new_signature_image;

    public function mount()
    {
        $this->authorize('view settings');
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
            $this->lab_favicon = Configuration::getFor('lab_favicon');
        }

        // Load invoice settings from configurations table
        $this->invoice_prefix = Configuration::getFor('invoice_prefix', 'INV');
        $this->invoice_separator = Configuration::getFor('invoice_separator', '-');
        $this->invoice_date_format = Configuration::getFor('invoice_date_format', 'ym');
        $this->invoice_counter_digits = (int) Configuration::getFor('invoice_counter_digits', 4);
        $this->invoice_counter_reset = Configuration::getFor('invoice_counter_reset', 'monthly');
        $this->bill_template = Configuration::getFor('bill_template', 'classic');

        // Patient ID settings
        $this->patient_id_prefix = Configuration::getFor('patient_id_prefix', 'PAT');
        $this->patient_id_digits = (int) Configuration::getFor('patient_id_digits', 4);

        // PDF header/footer
        $this->pdf_show_header = Configuration::getFor('pdf_show_header', '1') === '1';
        $this->pdf_show_footer = Configuration::getFor('pdf_show_footer', '1') === '1';
        $this->pdf_header_image = Configuration::getFor('pdf_header_image', null);
        $this->pdf_footer_image = Configuration::getFor('pdf_footer_image', null);
        
        $this->authorized_signatory_name = Configuration::getFor('authorized_signatory_name', 'Dr. Authorized Pathologist');
        $this->authorized_signatory_designation = Configuration::getFor('authorized_signatory_designation', 'Consultant Pathologist');
        $this->signature_image = Configuration::getFor('signature_image', null);

        // Barcode settings
        $this->barcode_prefix = Configuration::getFor('barcode_prefix', 'LAB');
        $this->barcode_date_format = Configuration::getFor('barcode_date_format', 'ymd');
        $this->barcode_counter_digits = (int) Configuration::getFor('barcode_counter_digits', 6);
    }

    // ==========================================
    // SAVE LAB PROFILE
    // ==========================================
    public function saveProfile()
    {
        $this->authorize('edit settings');
        $this->validate([
            'lab_name' => 'required|string|max:255',
            'lab_email' => 'nullable|email|max:255',
            'lab_phone' => 'nullable|string|max:20',
            'lab_address' => 'nullable|string|max:500',
            'lab_website' => 'nullable|url|max:255',
            'lab_gst_number' => 'nullable|string|max:50',
            'lab_tagline' => 'nullable|string|max:255',
            'new_logo' => 'nullable|image|max:2048',
            'new_favicon' => 'nullable|image|max:1024',
        ]);

        $company = Company::find(auth()->user()->company_id);

        // Handle logo upload
        $logoPath = $company->logo;
        if (is_object($this->new_logo) && method_exists($this->new_logo, 'store')) {
            $logoPath = $this->new_logo->store('logos', 'public');
        }

        // Handle favicon upload
        $faviconPath = $this->lab_favicon;
        if (is_object($this->new_favicon) && method_exists($this->new_favicon, 'store')) {
            $faviconPath = $this->new_favicon->store('favicons', 'public');
            Configuration::setFor('lab_favicon', $faviconPath);
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
        $this->lab_favicon = $faviconPath;
        $this->new_logo = null;
        $this->new_favicon = null;
        $this->profileSaved = true;
    }

    // ==========================================
    // SAVE INVOICE SETTINGS
    // ==========================================
    public function saveInvoiceSettings()
    {
        $this->authorize('edit settings');
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
    // SAVE PATIENT SETTINGS
    // ==========================================
    public function savePatientSettings()
    {
        $this->authorize('edit settings');
        $this->validate([
            'patient_id_prefix' => 'required|string|max:10',
            'patient_id_digits' => 'required|integer|min:2|max:10',
        ]);

        Configuration::setFor('patient_id_prefix', $this->patient_id_prefix);
        Configuration::setFor('patient_id_digits', $this->patient_id_digits);

        $this->patientSettingsSaved = true;
    }

    // ==========================================
    // SAVE BARCODE SETTINGS
    // ==========================================
    public function saveBarcodeSettings()
    {
        $this->authorize('edit settings');
        $this->validate([
            'barcode_prefix' => 'required|string|max:10',
            'barcode_date_format' => 'required|string|in:ym,ymd,Ymd,Y,none',
            'barcode_counter_digits' => 'required|integer|min:2|max:12',
        ]);

        Configuration::setFor('barcode_prefix', $this->barcode_prefix);
        Configuration::setFor('barcode_date_format', $this->barcode_date_format);
        Configuration::setFor('barcode_counter_digits', $this->barcode_counter_digits);

        $this->barcodeSaved = true;
    }

    // ==========================================
    // SAVE BILL TEMPLATE
    // ==========================================
    public function saveTemplate()
    {
        $this->authorize('edit settings');
        Configuration::setFor('bill_template', $this->bill_template);
        $this->templateSaved = true;
    }

    // ==========================================
    // SAVE PDF HEADER / FOOTER SETTINGS
    // ==========================================
    public function savePdfSettings()
    {
        $this->authorize('edit settings');
        $this->validate([
            'new_header_image' => 'nullable|image|max:3072',
            'new_footer_image' => 'nullable|image|max:3072',
        ]);

        // Upload header image
        if (is_object($this->new_header_image) && method_exists($this->new_header_image, 'store')) {
            $this->pdf_header_image = $this->new_header_image->store('invoice-headers', 'public');
            $this->new_header_image = null;
        }

        // Upload footer image
        if (is_object($this->new_footer_image) && method_exists($this->new_footer_image, 'store')) {
            $this->pdf_footer_image = $this->new_footer_image->store('invoice-footers', 'public');
            $this->new_footer_image = null;
        }

        // Upload signature image
        if (is_object($this->new_signature_image) && method_exists($this->new_signature_image, 'store')) {
            $this->signature_image = $this->new_signature_image->store('signatures', 'public');
            $this->new_signature_image = null;
        }

        Configuration::setFor('pdf_show_header', $this->pdf_show_header ? '1' : '0');
        Configuration::setFor('pdf_show_footer', $this->pdf_show_footer ? '1' : '0');
        Configuration::setFor('pdf_header_image', $this->pdf_header_image);
        Configuration::setFor('pdf_footer_image', $this->pdf_footer_image);
        
        Configuration::setFor('authorized_signatory_name', $this->authorized_signatory_name);
        Configuration::setFor('authorized_signatory_designation', $this->authorized_signatory_designation);
        Configuration::setFor('signature_image', $this->signature_image);

        $this->pdfSaved = true;
    }

    public function removeHeaderImage()
    {
        $this->pdf_header_image = null;
        Configuration::setFor('pdf_header_image', null);
    }

    public function removeFooterImage()
    {
        $this->pdf_footer_image = null;
        Configuration::setFor('pdf_footer_image', null);
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

    /**
     * Generate a preview of the barcode format.
     */
    public function getBarcodePreviewProperty(): string
    {
        $prefix = $this->barcode_prefix ?: 'LAB';
        $dateMap = [
            'ym' => date('ym'),
            'ymd' => date('ymd'),
            'Ymd' => date('Ymd'),
            'Y' => date('Y'),
            'none' => '',
        ];
        $datePart = $dateMap[$this->barcode_date_format] ?? date('ymd');
        $counter = str_pad(1, max((int)$this->barcode_counter_digits, 2), '0', STR_PAD_LEFT);

        return $prefix . $datePart . $counter;
    }

    /**
     * Generate a preview of the Patient ID format.
     */
    public function getPatientIdPreviewProperty(): string
    {
        $prefix = $this->patient_id_prefix ?: 'PAT';
        $counter = str_pad(1, max((int)$this->patient_id_digits, 2), '0', STR_PAD_LEFT);
        return $prefix . $counter;
    }

    public function render()
    {
        return view('livewire.lab.settings-manager')
            ->layout('layouts.app', ['title' => 'Settings']);
    }
}

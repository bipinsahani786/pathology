<?php

namespace App\Livewire\Lab;

use App\Models\Company;
use App\Models\Configuration;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsManager extends Component
{
    use WithFileUploads;

    // Active Tab
    public $activeTab = 'profile'; // profile, invoice, template, pdf, signatures, staff

    // ==========================================
    // LAB PROFILE
    // ==========================================
    public $lab_name;

    public $lab_email;

    public $lab_phone;

    public $lab_address;

    public $lab_website;

    public $lab_gst_number;

    public $lab_tagline;

    public $lab_logo;

    public $lab_favicon;

    public $new_logo;

    public $new_favicon;

    public $profileSaved = false;

    // UI SETTINGS
    public $ui_font_scale = 100;

    // ==========================================
    // INVOICE SETTINGS
    // ==========================================
    public $invoice_prefix = 'INV';

    public $invoice_separator = '-';

    public $invoice_date_format = 'ym';

    public $invoice_counter_digits = 4;

    public $invoice_counter_reset = 'monthly';

    public $restrict_billing_below_b2b = false;

    public $commission_basis_doctor = 'gross';

    public $commission_basis_agent = 'gross';

    public $restrict_unpaid_reports = false;

    // Invoice Print & Layout Settings
    public $invoice_show_header = true;

    public $invoice_show_footer = true;

    public $invoice_header_image;

    public $invoice_footer_image;

    public $new_invoice_header_image;

    public $new_invoice_footer_image;

    public $invoice_margin_top = 310;

    public $invoice_margin_bottom = 255;

    public $invoice_header_height = 200;

    public $invoice_footer_height = 180;

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

    public $pdf_show_signatures = true; // New Toggle

    public $pdf_show_test_method = true;

    public $pdf_show_watermark = true;

    public $pdf_header_image;       // stored path

    public $pdf_footer_image;       // stored path

    public $new_header_image;       // upload

    public $new_footer_image;       // upload

    // PDF Typography & Layout
    public $pdf_font_size = 13;

    public $pdf_font_family = 'Helvetica';

    public $pdf_margin_top = 310;

    public $pdf_margin_bottom = 255;

    public $pdf_header_height = 200;

    public $pdf_footer_height = 180;

    public $report_page_break_style = 'continuous';

    public $report_show_dept_header_always = true;

    public $report_show_interpretation = true;

    public $report_flag_high_color = '#cc0000';

    public $report_flag_low_color = '#0055aa';

    public $report_abnormal_indicator = '*';

    public $report_abnormal_color = '#d32f2f';

    public $pdfSaved = false;

    // ==========================================
    // BRANCH CONTROLS
    // ==========================================
    public $branch_share_patients = true;

    public $branch_share_doctors = true;

    public $branch_share_agents = true;

    public $branch_share_tests = true;

    public $restrict_branch_access = true;

    public $branchControlsSaved = false;

    // ==========================================
    // MODULE VISIBILITY SETTINGS
    // ==========================================
    public $default_login_page = 'lab.dashboard';
    
    public $show_dashboard_stats = true;

    public $module_pos = true;

    public $module_invoices = true;

    public $module_departments = true;

    public $module_tests = true;

    public $module_packages = true;

    public $module_branches = true;

    public $module_collection_centers = true;

    public $module_patients = true;

    public $module_doctors = true;

    public $module_agents = true;

    public $module_settlements = true;

    public $module_marketing = true;

    public $module_inventory = true;

    public $modulesSaved = false;

    // Report Signatory (Global 1)
    public $authorized_signatory_name;

    public $authorized_signatory_designation;

    public $signature_image;

    public $new_signature_image;

    // Global Signatories (2 & 3)
    public $global_sig_2_name;

    public $global_sig_2_desig;

    public $global_sig_2_path;

    public $new_global_sig_2;

    public $global_sig_3_name;

    public $global_sig_3_desig;

    public $global_sig_3_path;

    public $new_global_sig_3;

    public $sig_1_position = 'right';
    public $sig_1_enabled = true;

    public $sig_2_position = 'left';
    public $sig_2_enabled = true;

    public $sig_3_position = 'center';
    public $sig_3_enabled = true;

    // Department-wise Signatures
    public $report_signature_mode = 'global_bottom';

    public $selected_dept_id;

    public $dept_sig_1_name;

    public $dept_sig_1_desig;

    public $dept_sig_1_path;

    public $new_dept_sig_1;

    public $dept_sig_2_name;

    public $dept_sig_2_desig;

    public $dept_sig_2_path;

    public $new_dept_sig_2;

    public $dept_sig_3_name;

    public $dept_sig_3_desig;

    public $dept_sig_3_path;

    public $new_dept_sig_3;

    public $signaturesSaved = false;

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
        $this->restrict_billing_below_b2b = Configuration::getFor('restrict_billing_below_b2b', '0') === '1';
        $this->restrict_unpaid_reports = Configuration::getFor('restrict_unpaid_reports', '0') === '1';
        $this->commission_basis_doctor = Configuration::getFor('commission_basis_doctor', 'gross');
        $this->commission_basis_agent = Configuration::getFor('commission_basis_agent', 'gross');
        $this->bill_template = Configuration::getFor('bill_template', 'classic');

        // Invoice Print Layout (with fallback to pdf_ report equivalents for backward compatibility)
        $this->invoice_show_header = Configuration::getFor('invoice_show_header', Configuration::getFor('pdf_show_header', '1')) === '1';
        $this->invoice_show_footer = Configuration::getFor('invoice_show_footer', Configuration::getFor('pdf_show_footer', '1')) === '1';
        $this->invoice_header_image = Configuration::getFor('invoice_header_image', Configuration::getFor('pdf_header_image', null));
        $this->invoice_footer_image = Configuration::getFor('invoice_footer_image', Configuration::getFor('pdf_footer_image', null));
        $this->invoice_margin_top = (int) Configuration::getFor('invoice_margin_top', Configuration::getFor('pdf_margin_top', 310));
        $this->invoice_margin_bottom = (int) Configuration::getFor('invoice_margin_bottom', Configuration::getFor('pdf_margin_bottom', 255));
        $this->invoice_header_height = (int) Configuration::getFor('invoice_header_height', Configuration::getFor('pdf_header_height', 200));
        $this->invoice_footer_height = (int) Configuration::getFor('invoice_footer_height', Configuration::getFor('pdf_footer_height', 180));

        // Patient ID settings
        $this->patient_id_prefix = Configuration::getFor('patient_id_prefix', 'PAT');
        $this->patient_id_digits = (int) Configuration::getFor('patient_id_digits', 4);

        // PDF header/footer
        $this->pdf_show_header = Configuration::getFor('pdf_show_header', '1') === '1';
        $this->pdf_show_footer = Configuration::getFor('pdf_show_footer', '1') === '1';
        $this->pdf_show_signatures = Configuration::getFor('pdf_show_signatures', '1') === '1';
        $this->pdf_show_test_method = Configuration::getFor('pdf_show_test_method', '1') === '1';
        $this->pdf_show_watermark = Configuration::getFor('pdf_show_watermark', '1') === '1';
        $this->pdf_header_image = Configuration::getFor('pdf_header_image', null);
        $this->pdf_footer_image = Configuration::getFor('pdf_footer_image', null);

        // PDF Typography & Layout
        $this->pdf_font_size = (int) Configuration::getFor('pdf_font_size', 13);
        $this->pdf_font_family = Configuration::getFor('pdf_font_family', 'Helvetica');
        $this->pdf_margin_top = (int) Configuration::getFor('pdf_margin_top', 310);
        $this->pdf_margin_bottom = (int) Configuration::getFor('pdf_margin_bottom', 255);
        $this->pdf_header_height = (int) Configuration::getFor('pdf_header_height', 200);
        $this->pdf_footer_height = (int) Configuration::getFor('pdf_footer_height', 180);

        $this->report_page_break_style = Configuration::getFor('report_page_break_style', 'continuous');
        $this->report_show_dept_header_always = Configuration::getFor('report_show_dept_header_always', '1') === '1';
        $this->report_show_interpretation = Configuration::getFor('report_show_interpretation', '1') === '1';

        $this->report_flag_high_color = Configuration::getFor('report_flag_high_color', '#cc0000');
        $this->report_flag_low_color = Configuration::getFor('report_flag_low_color', '#0055aa');
        
        $this->report_abnormal_indicator = Configuration::getFor('report_abnormal_indicator', '*');
        $this->report_abnormal_color = Configuration::getFor('report_abnormal_color', '#d32f2f');

        $this->authorized_signatory_name = Configuration::getFor('authorized_signatory_name', 'Dr. Authorized Pathologist');
        $this->authorized_signatory_designation = Configuration::getFor('authorized_signatory_designation', 'Consultant Pathologist');
        $this->signature_image = Configuration::getFor('signature_image', null);

        // Global 2 & 3
        $this->global_sig_2_name = Configuration::getFor('global_sig_2_name', '');
        $this->global_sig_2_desig = Configuration::getFor('global_sig_2_desig', '');
        $this->global_sig_2_path = Configuration::getFor('global_sig_2_path', null);
        $this->global_sig_3_name = Configuration::getFor('global_sig_3_name', '');
        $this->global_sig_3_desig = Configuration::getFor('global_sig_3_desig', '');
        $this->global_sig_3_path = Configuration::getFor('global_sig_3_path', null);

        $this->sig_1_position = Configuration::getFor('sig_1_position', 'right');
        $this->sig_1_enabled = Configuration::getFor('sig_1_enabled', '1') === '1';
        $this->sig_2_position = Configuration::getFor('sig_2_position', 'left');
        $this->sig_2_enabled = Configuration::getFor('sig_2_enabled', '1') === '1';
        $this->sig_3_position = Configuration::getFor('sig_3_position', 'center');
        $this->sig_3_enabled = Configuration::getFor('sig_3_enabled', '1') === '1';

        $this->report_signature_mode = Configuration::getFor('report_signature_mode', 'global_bottom');

        // Barcode settings
        $this->barcode_prefix = Configuration::getFor('barcode_prefix', 'LAB');
        $this->barcode_date_format = Configuration::getFor('barcode_date_format', 'ymd');
        $this->barcode_counter_digits = (int) Configuration::getFor('barcode_counter_digits', 6);

        // Branch Controls
        $this->branch_share_patients = Configuration::getFor('branch_share_patients', '1') === '1';
        $this->branch_share_doctors = Configuration::getFor('branch_share_doctors', '1') === '1';
        $this->branch_share_agents = Configuration::getFor('branch_share_agents', '1') === '1';
        $this->branch_share_tests = Configuration::getFor('branch_share_tests', '1') === '1';
        $this->restrict_branch_access = Configuration::getFor('restrict_branch_access', '1') === '1';

        // Module Visibility
        $this->default_login_page = Configuration::getFor('default_login_page', 'lab.dashboard');
        $this->show_dashboard_stats = Configuration::getFor('show_dashboard_stats', '1') === '1';
        $this->module_pos = Configuration::getFor('module_pos', '1') === '1';
        $this->module_invoices = Configuration::getFor('module_invoices', '1') === '1';
        $this->module_departments = Configuration::getFor('module_departments', '1') === '1';
        $this->module_tests = Configuration::getFor('module_tests', '1') === '1';
        $this->module_packages = Configuration::getFor('module_packages', '1') === '1';
        $this->module_branches = Configuration::getFor('module_branches', '1') === '1';
        $this->module_collection_centers = Configuration::getFor('module_collection_centers', '1') === '1';
        $this->module_patients = Configuration::getFor('module_patients', '1') === '1';
        $this->module_doctors = Configuration::getFor('module_doctors', '1') === '1';
        $this->module_agents = Configuration::getFor('module_agents', '1') === '1';
        $this->module_settlements = Configuration::getFor('module_settlements', '1') === '1';
        $this->module_marketing = Configuration::getFor('module_marketing', '1') === '1';
        $this->module_inventory = Configuration::getFor('module_inventory', '1') === '1';

        // UI Scaling
        $this->ui_font_scale = (int) Configuration::getFor('ui_font_scale', 100);

        // Branch Admin Restriction: Force default tab to staff
        if (auth()->user()->hasRole('branch_admin')) {
            $this->activeTab = 'staff';
        }
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
            $logoPath = $this->new_logo->store('logos');
        }

        // Handle favicon upload
        $faviconPath = $this->lab_favicon;
        if (is_object($this->new_favicon) && method_exists($this->new_favicon, 'store')) {
            $faviconPath = $this->new_favicon->store('favicons');
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

        Configuration::setFor('ui_font_scale', $this->ui_font_scale);

        $this->lab_logo = $logoPath;
        $this->lab_favicon = $faviconPath;
        $this->new_logo = null;
        $this->new_favicon = null;

        session()->flash('ui_updated', 'Settings updated successfully!');
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
        Configuration::getFor('invoice_counter_reset', $this->invoice_counter_reset);
        Configuration::setFor('restrict_billing_below_b2b', $this->restrict_billing_below_b2b ? '1' : '0');
        Configuration::setFor('restrict_unpaid_reports', $this->restrict_unpaid_reports ? '1' : '0');
        Configuration::setFor('commission_basis_doctor', $this->commission_basis_doctor);
        Configuration::setFor('commission_basis_agent', $this->commission_basis_agent);

        $hasCustomInvoice = auth()->user()->company->plan?->features['custom_invoice'] ?? false;
        if (! $hasCustomInvoice) {
            if (is_object($this->new_invoice_header_image) || is_object($this->new_invoice_footer_image)) {
                session()->flash('error', 'Plan Restriction: Uploading custom letterhead images is a premium feature. Please upgrade your plan.');
                $this->new_invoice_header_image = null;
                $this->new_invoice_footer_image = null;

                return;
            }
        }

        if (is_object($this->new_invoice_header_image) && method_exists($this->new_invoice_header_image, 'store')) {
            $this->invoice_header_image = $this->new_invoice_header_image->store('invoice-headers');
            $this->new_invoice_header_image = null;
        }

        if (is_object($this->new_invoice_footer_image) && method_exists($this->new_invoice_footer_image, 'store')) {
            $this->invoice_footer_image = $this->new_invoice_footer_image->store('invoice-footers');
            $this->new_invoice_footer_image = null;
        }

        Configuration::setFor('invoice_show_header', $this->invoice_show_header ? '1' : '0');
        Configuration::setFor('invoice_show_footer', $this->invoice_show_footer ? '1' : '0');
        Configuration::setFor('invoice_header_image', $this->invoice_header_image);
        Configuration::setFor('invoice_footer_image', $this->invoice_footer_image);
        Configuration::setFor('invoice_margin_top', $this->invoice_margin_top);
        Configuration::setFor('invoice_margin_bottom', $this->invoice_margin_bottom);
        Configuration::setFor('invoice_header_height', $this->invoice_header_height);
        Configuration::setFor('invoice_footer_height', $this->invoice_footer_height);

        $this->invoiceSaved = true;
    }

    public function removeInvoiceHeaderImage()
    {
        $this->authorize('edit settings');
        if ($this->invoice_header_image) {
            Configuration::setFor('invoice_header_image', '');
            $this->invoice_header_image = null;
        }
    }

    public function removeInvoiceFooterImage()
    {
        $this->authorize('edit settings');
        if ($this->invoice_footer_image) {
            Configuration::setFor('invoice_footer_image', '');
            $this->invoice_footer_image = null;
        }
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

        // SaaS Plan Enforcement
        $hasCustomInvoice = auth()->user()->company->plan?->features['custom_invoice'] ?? false;
        if (! $hasCustomInvoice && $this->bill_template !== 'classic') {
            session()->flash('error', 'Plan Restriction: Your current plan only supports the Classic invoice template. Upgrade to a premium plan to use Modern or Professional templates.');
            $this->bill_template = 'classic';

            return;
        }

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

        // SaaS Plan Enforcement for Custom Branding
        $hasCustomInvoice = auth()->user()->company->plan?->features['custom_invoice'] ?? false;
        if (! $hasCustomInvoice) {
            if (is_object($this->new_header_image) || is_object($this->new_footer_image)) {
                session()->flash('error', 'Plan Restriction: Uploading custom letterhead images is a premium feature. Please upgrade your plan.');
                $this->new_header_image = null;
                $this->new_footer_image = null;

                return;
            }
        }

        // Upload header image
        if (is_object($this->new_header_image) && method_exists($this->new_header_image, 'store')) {
            $this->pdf_header_image = $this->new_header_image->store('invoice-headers');
            $this->new_header_image = null;
        }

        // Upload footer image
        if (is_object($this->new_footer_image) && method_exists($this->new_footer_image, 'store')) {
            $this->pdf_footer_image = $this->new_footer_image->store('invoice-footers');
            $this->new_footer_image = null;
        }

        // Upload signature image
        if (is_object($this->new_signature_image) && method_exists($this->new_signature_image, 'store')) {
            $this->signature_image = $this->new_signature_image->store('signatures');
            $this->new_signature_image = null;
        }

        Configuration::setFor('pdf_show_header', $this->pdf_show_header ? '1' : '0');
        Configuration::setFor('pdf_show_footer', $this->pdf_show_footer ? '1' : '0');
        Configuration::setFor('pdf_show_signatures', $this->pdf_show_signatures ? '1' : '0');
        Configuration::setFor('pdf_show_test_method', $this->pdf_show_test_method ? '1' : '0');
        Configuration::setFor('pdf_show_watermark', $this->pdf_show_watermark ? '1' : '0');
        Configuration::setFor('pdf_header_image', $this->pdf_header_image);
        Configuration::setFor('pdf_footer_image', $this->pdf_footer_image);

        // Layout & Typography
        Configuration::setFor('pdf_font_size', $this->pdf_font_size);
        Configuration::setFor('pdf_font_family', $this->pdf_font_family);
        Configuration::setFor('pdf_margin_top', $this->pdf_margin_top);
        Configuration::setFor('pdf_margin_bottom', $this->pdf_margin_bottom);
        Configuration::setFor('pdf_header_height', $this->pdf_header_height);
        Configuration::setFor('pdf_footer_height', $this->pdf_footer_height);

        Configuration::setFor('report_page_break_style', $this->report_page_break_style);
        Configuration::setFor('report_show_dept_header_always', $this->report_show_dept_header_always ? '1' : '0');
        Configuration::setFor('report_show_interpretation', $this->report_show_interpretation ? '1' : '0');

        Configuration::setFor('report_flag_high_color', $this->report_flag_high_color);
        Configuration::setFor('report_flag_low_color', $this->report_flag_low_color);
        
        Configuration::setFor('report_abnormal_indicator', $this->report_abnormal_indicator);
        Configuration::setFor('report_abnormal_color', $this->report_abnormal_color);

        Configuration::setFor('authorized_signatory_name', $this->authorized_signatory_name);
        Configuration::setFor('authorized_signatory_designation', $this->authorized_signatory_designation);
        Configuration::setFor('signature_image', $this->signature_image);

        $this->pdfSaved = true;
    }

    // ==========================================
    // SAVE SIGNATURE SETTINGS (Global & Dept)
    // ==========================================
    public function updatedSelectedDeptId($value)
    {
        if (! $value) {
            $this->reset(['dept_sig_1_name', 'dept_sig_1_desig', 'dept_sig_1_path', 'dept_sig_2_name', 'dept_sig_2_desig', 'dept_sig_2_path', 'dept_sig_3_name', 'dept_sig_3_desig', 'dept_sig_3_path']);

            return;
        }

        $dept = \App\Models\Department::find($value);
        if ($dept) {
            $this->dept_sig_1_name = $dept->sig_1_name;
            $this->dept_sig_1_desig = $dept->sig_1_desig;
            $this->dept_sig_1_path = $dept->sig_1_path;

            $this->dept_sig_2_name = $dept->sig_2_name;
            $this->dept_sig_2_desig = $dept->sig_2_desig;
            $this->dept_sig_2_path = $dept->sig_2_path;

            $this->dept_sig_3_name = $dept->sig_3_name;
            $this->dept_sig_3_desig = $dept->sig_3_desig;
            $this->dept_sig_3_path = $dept->sig_3_path;
        }
    }

    public function saveSignatures()
    {
        $this->authorize('edit settings');

        // 1. Save Global Signatures
        if ($this->new_signature_image) {
            $this->signature_image = $this->new_signature_image->store('signatures');
            $this->new_signature_image = null;
        }
        if ($this->new_global_sig_2) {
            $this->global_sig_2_path = $this->new_global_sig_2->store('signatures');
            $this->new_global_sig_2 = null;
        }
        if ($this->new_global_sig_3) {
            $this->global_sig_3_path = $this->new_global_sig_3->store('signatures');
            $this->new_global_sig_3 = null;
        }

        Configuration::setFor('report_signature_mode', $this->report_signature_mode);
        Configuration::setFor('authorized_signatory_name', $this->authorized_signatory_name);
        Configuration::setFor('authorized_signatory_designation', $this->authorized_signatory_designation);
        Configuration::setFor('signature_image', $this->signature_image);

        Configuration::setFor('global_sig_2_name', $this->global_sig_2_name);
        Configuration::setFor('global_sig_2_desig', $this->global_sig_2_desig);
        Configuration::setFor('global_sig_2_path', $this->global_sig_2_path);

        Configuration::setFor('global_sig_3_name', $this->global_sig_3_name);
        Configuration::setFor('global_sig_3_desig', $this->global_sig_3_desig);
        Configuration::setFor('global_sig_3_path', $this->global_sig_3_path);

        Configuration::setFor('sig_1_position', $this->sig_1_position);
        Configuration::setFor('sig_1_enabled', $this->sig_1_enabled ? '1' : '0');
        Configuration::setFor('sig_2_position', $this->sig_2_position);
        Configuration::setFor('sig_2_enabled', $this->sig_2_enabled ? '1' : '0');
        Configuration::setFor('sig_3_position', $this->sig_3_position);
        Configuration::setFor('sig_3_enabled', $this->sig_3_enabled ? '1' : '0');

        // 2. Save Selected Department Signatures
        if ($this->selected_dept_id) {
            $dept = \App\Models\Department::find($this->selected_dept_id);
            if ($dept) {
                $updateData = [
                    'sig_1_name' => $this->dept_sig_1_name,
                    'sig_1_desig' => $this->dept_sig_1_desig,
                    'sig_2_name' => $this->dept_sig_2_name,
                    'sig_2_desig' => $this->dept_sig_2_desig,
                    'sig_3_name' => $this->dept_sig_3_name,
                    'sig_3_desig' => $this->dept_sig_3_desig,
                ];

                if ($this->new_dept_sig_1) {
                    $updateData['sig_1_path'] = $this->new_dept_sig_1->store('signatures');
                    $this->dept_sig_1_path = $updateData['sig_1_path'];
                    $this->new_dept_sig_1 = null;
                }
                if ($this->new_dept_sig_2) {
                    $updateData['sig_2_path'] = $this->new_dept_sig_2->store('signatures');
                    $this->dept_sig_2_path = $updateData['sig_2_path'];
                    $this->new_dept_sig_2 = null;
                }
                if ($this->new_dept_sig_3) {
                    $updateData['sig_3_path'] = $this->new_dept_sig_3->store('signatures');
                    $this->dept_sig_3_path = $updateData['sig_3_path'];
                    $this->new_dept_sig_3 = null;
                }

                $dept->update($updateData);
            }
        }

        $this->signaturesSaved = true;
    }

    // ==========================================
    // SAVE MODULE VISIBILITY
    // ==========================================
    public function saveModules()
    {
        $this->authorize('edit settings');

        Configuration::setFor('default_login_page', $this->default_login_page);
        Configuration::setFor('show_dashboard_stats', $this->show_dashboard_stats ? '1' : '0');
        Configuration::setFor('module_pos', $this->module_pos ? '1' : '0');
        Configuration::setFor('module_invoices', $this->module_invoices ? '1' : '0');
        Configuration::setFor('module_departments', $this->module_departments ? '1' : '0');
        Configuration::setFor('module_tests', $this->module_tests ? '1' : '0');
        Configuration::setFor('module_packages', $this->module_packages ? '1' : '0');
        Configuration::setFor('module_branches', $this->module_branches ? '1' : '0');
        Configuration::setFor('module_collection_centers', $this->module_collection_centers ? '1' : '0');
        Configuration::setFor('module_patients', $this->module_patients ? '1' : '0');
        Configuration::setFor('module_doctors', $this->module_doctors ? '1' : '0');
        Configuration::setFor('module_agents', $this->module_agents ? '1' : '0');
        Configuration::setFor('module_settlements', $this->module_settlements ? '1' : '0');
        Configuration::setFor('module_marketing', $this->module_marketing ? '1' : '0');
        Configuration::setFor('module_inventory', $this->module_inventory ? '1' : '0');

        $this->modulesSaved = true;
    }

    // ==========================================
    // SAVE BRANCH CONTROLS
    // ==========================================
    public function saveBranchControls()
    {
        $this->authorize('edit settings');

        Configuration::setFor('branch_share_patients', $this->branch_share_patients ? '1' : '0');
        Configuration::setFor('branch_share_doctors', $this->branch_share_doctors ? '1' : '0');
        Configuration::setFor('branch_share_agents', $this->branch_share_agents ? '1' : '0');
        Configuration::setFor('branch_share_tests', $this->branch_share_tests ? '1' : '0');
        Configuration::setFor('restrict_branch_access', $this->restrict_branch_access ? '1' : '0');

        $this->branchControlsSaved = true;
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

        $counter = str_pad(1, max((int) $this->invoice_counter_digits, 2), '0', STR_PAD_LEFT);

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
        $counter = str_pad(1, max((int) $this->barcode_counter_digits, 2), '0', STR_PAD_LEFT);

        return $prefix.$datePart.$counter;
    }

    /**
     * Generate a preview of the Patient ID format.
     */
    public function getPatientIdPreviewProperty(): string
    {
        $prefix = $this->patient_id_prefix ?: 'PAT';
        $counter = str_pad(1, max((int) $this->patient_id_digits, 2), '0', STR_PAD_LEFT);

        return $prefix.$counter;
    }

    public function getFontFamiliesProperty()
    {
        return [
            'Helvetica' => 'Helvetica / Arial (Standard)',
            'DejaVu Sans' => 'DejaVu Sans (UTF-8 Support)',
            'Times-Roman' => 'Times New Roman (Serif)',
            'Courier' => 'Courier (Monospace)',
        ];
    }

    public function render()
    {
        return view('livewire.lab.settings-manager')
            ->layout('layouts.app', ['title' => 'Settings']);
    }
}

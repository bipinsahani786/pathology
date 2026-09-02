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

    // Branch Scoping
    public $branches = [];

    public $selectedBranchId = 'global';

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

    public $partner_show_payment_distribution = true;

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

    public $barcode_print_mode = 'sample'; // 'sample' or 'test'

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

    public $pdf_show_page_number = true;

    public $pdf_show_time = true;

    public $pdf_page_number_bg_color = 'rgba(255, 255, 255, 0.85)';

    public $pdf_header_image;       // stored path

    public $pdf_footer_image;       // stored path

    public $pdf_letterhead_mode = 'separate'; // 'separate' or 'full_background'

    public $pdf_letterhead_image;   // stored path

    public $new_header_image;       // upload

    public $new_footer_image;       // upload

    public $new_letterhead_image;   // upload

    // PDF Typography & Layout
    public $pdf_font_size = 13;

    public $pdf_font_family = 'Helvetica';

    public $pdf_margin_top = 310;

    public $pdf_margin_bottom = 255;

    public $pdf_margin_left = 25;

    public $pdf_margin_right = 25;

    public $pdf_header_height = 200;

    public $pdf_footer_height = 180;

    // Outsourced PDF Default Crop Settings
    public $outsourced_crop_top = 33;
    public $outsourced_crop_bottom = 8;
    
    public $outsourced_pdf_mode = 'crop_to_image';

    public $report_page_break_style = 'continuous';

    public $report_show_dept_header_always = true;

    public $report_show_interpretation = true;

    public $report_show_note = true;

    public $report_group_by_dept = false; // false = selection order, true = department grouped

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

    // ==========================================
    // WHATSAPP SETTINGS
    // ==========================================
    public $whatsapp_share_mode = 'pdf';

    public $whatsapp_invoice_message = '';

    public $whatsapp_report_message = '';

    public $whatsappSaved = false;

    public $previewType = 'invoice';

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
        $this->branches = \App\Models\Branch::where('company_id', auth()->user()->company_id)->get();

        if (auth()->user()->hasRole('branch_admin')) {
            $this->selectedBranchId = auth()->user()->branch_id;
        } else {
            $this->selectedBranchId = session('settings_branch_id', 'global');
        }

        $this->loadSettings();

        // Branch Admin Restriction: Force default tab to staff
        if (auth()->user()->hasRole('branch_admin')) {
            $this->activeTab = 'staff';
        }
    }

    public function updatedSelectedBranchId($value)
    {
        session(['settings_branch_id' => $value]);
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $company = Company::find(auth()->user()->company_id);
        if (!$company) {
            return;
        }

        $branchId = $this->selectedBranchId;

        // Profile Tab Loading
        if ($branchId !== 'global') {
            $branch = \App\Models\Branch::where('company_id', $company->id)->where('id', $branchId)->first();
            if ($branch) {
                $this->lab_name = $branch->name;
                $this->lab_phone = $branch->contact_number;
                $this->lab_address = $branch->address;
            } else {
                $this->lab_name = '';
                $this->lab_phone = '';
                $this->lab_address = '';
            }
            $this->lab_email = Configuration::getFor('lab_email', $company->email, $company->id, $branchId);
            $this->lab_website = Configuration::getFor('lab_website', $company->website, $company->id, $branchId);
            $this->lab_gst_number = Configuration::getFor('lab_gst_number', $company->gst_number, $company->id, $branchId);
            $this->lab_tagline = Configuration::getFor('lab_tagline', $company->tagline, $company->id, $branchId);
            $this->lab_logo = Configuration::getFor('lab_logo', $company->logo, $company->id, $branchId);
            $this->lab_favicon = Configuration::getFor('lab_favicon', null, $company->id, $branchId);
        } else {
            $this->lab_name = $company->name;
            $this->lab_email = $company->email;
            $this->lab_phone = $company->phone;
            $this->lab_address = $company->address;
            $this->lab_website = $company->website;
            $this->lab_gst_number = $company->gst_number;
            $this->lab_tagline = $company->tagline;
            $this->lab_logo = $company->logo;
            $this->lab_favicon = Configuration::getFor('lab_favicon', null, $company->id, 'global');
        }

        // Load invoice settings from configurations table
        $this->invoice_prefix = Configuration::getFor('invoice_prefix', 'INV', $company->id, $branchId);
        $this->invoice_separator = Configuration::getFor('invoice_separator', '-', $company->id, $branchId);
        $this->invoice_date_format = Configuration::getFor('invoice_date_format', 'ym', $company->id, $branchId);
        $this->invoice_counter_digits = (int) Configuration::getFor('invoice_counter_digits', 4, $company->id, $branchId);
        $this->invoice_counter_reset = Configuration::getFor('invoice_counter_reset', 'monthly', $company->id, $branchId);
        $this->restrict_billing_below_b2b = Configuration::getFor('restrict_billing_below_b2b', '0', $company->id, $branchId) === '1';
        $this->restrict_unpaid_reports = Configuration::getFor('restrict_unpaid_reports', '0', $company->id, $branchId) === '1';
        $this->commission_basis_doctor = Configuration::getFor('commission_basis_doctor', 'gross', $company->id, $branchId);
        $this->commission_basis_agent = Configuration::getFor('commission_basis_agent', 'gross', $company->id, $branchId);
        $this->partner_show_payment_distribution = Configuration::getFor('partner_show_payment_distribution', '1', $company->id, $branchId) === '1';
        $this->bill_template = Configuration::getFor('bill_template', 'classic', $company->id, $branchId);

        // Invoice Print Layout (with fallback to pdf_ report equivalents for backward compatibility)
        $this->invoice_show_header = Configuration::getFor('invoice_show_header', Configuration::getFor('pdf_show_header', '1', $company->id, $branchId), $company->id, $branchId) === '1';
        $this->invoice_show_footer = Configuration::getFor('invoice_show_footer', Configuration::getFor('pdf_show_footer', '1', $company->id, $branchId), $company->id, $branchId) === '1';
        $this->invoice_header_image = Configuration::getFor('invoice_header_image', Configuration::getFor('pdf_header_image', null, $company->id, $branchId), $company->id, $branchId);
        $this->invoice_footer_image = Configuration::getFor('invoice_footer_image', Configuration::getFor('pdf_footer_image', null, $company->id, $branchId), $company->id, $branchId);
        $this->invoice_margin_top = (int) Configuration::getFor('invoice_margin_top', Configuration::getFor('pdf_margin_top', 310, $company->id, $branchId), $company->id, $branchId);
        $this->invoice_margin_bottom = (int) Configuration::getFor('invoice_margin_bottom', Configuration::getFor('pdf_margin_bottom', 255, $company->id, $branchId), $company->id, $branchId);
        $this->invoice_header_height = (int) Configuration::getFor('invoice_header_height', Configuration::getFor('pdf_header_height', 200, $company->id, $branchId), $company->id, $branchId);
        $this->invoice_footer_height = (int) Configuration::getFor('invoice_footer_height', Configuration::getFor('pdf_footer_height', 180, $company->id, $branchId), $company->id, $branchId);

        // Patient ID settings
        $this->patient_id_prefix = Configuration::getFor('patient_id_prefix', 'PAT', $company->id, $branchId);
        $this->patient_id_digits = (int) Configuration::getFor('patient_id_digits', 4, $company->id, $branchId);

        // PDF header/footer
        $this->pdf_show_header = Configuration::getFor('pdf_show_header', '1', $company->id, $branchId) === '1';
        $this->pdf_show_footer = Configuration::getFor('pdf_show_footer', '1', $company->id, $branchId) === '1';
        $this->pdf_show_signatures = Configuration::getFor('pdf_show_signatures', '1', $company->id, $branchId) === '1';
        $this->pdf_show_test_method = Configuration::getFor('pdf_show_test_method', '1', $company->id, $branchId) === '1';
        $this->pdf_show_watermark = Configuration::getFor('pdf_show_watermark', '1', $company->id, $branchId) === '1';
        $this->pdf_show_page_number = Configuration::getFor('pdf_show_page_number', '1', $company->id, $branchId) === '1';
        $this->pdf_show_time = Configuration::getFor('pdf_show_time', '1', $company->id, $branchId) === '1';
        $this->pdf_page_number_bg_color = Configuration::getFor('pdf_page_number_bg_color', 'rgba(255, 255, 255, 0.85)', $company->id, $branchId);
        $this->pdf_header_image = Configuration::getFor('pdf_header_image', null, $company->id, $branchId);
        $this->pdf_footer_image = Configuration::getFor('pdf_footer_image', null, $company->id, $branchId);
        $this->pdf_letterhead_mode = Configuration::getFor('pdf_letterhead_mode', 'separate', $company->id, $branchId);
        $this->pdf_letterhead_image = Configuration::getFor('pdf_letterhead_image', null, $company->id, $branchId);

        // PDF Typography & Layout
        $this->pdf_font_size = (int) Configuration::getFor('pdf_font_size', 13, $company->id, $branchId);
        $this->pdf_font_family = Configuration::getFor('pdf_font_family', 'Helvetica', $company->id, $branchId);
        $this->pdf_margin_top = (int) Configuration::getFor('pdf_margin_top', 310, $company->id, $branchId);
        $this->pdf_margin_bottom = (int) Configuration::getFor('pdf_margin_bottom', 255, $company->id, $branchId);
        $this->pdf_margin_left = (int) Configuration::getFor('pdf_margin_left', 25, $company->id, $branchId);
        $this->pdf_margin_right = (int) Configuration::getFor('pdf_margin_right', 25, $company->id, $branchId);
        $this->pdf_header_height = (int) Configuration::getFor('pdf_header_height', 200, $company->id, $branchId);
        $this->pdf_footer_height = (int) Configuration::getFor('pdf_footer_height', 180, $company->id, $branchId);

        $this->outsourced_crop_top = (int) Configuration::getFor('outsourced_crop_top', 18, $company->id, $branchId);
        $this->outsourced_crop_bottom = (int) Configuration::getFor('outsourced_crop_bottom', 8, $company->id, $branchId);
        $this->outsourced_pdf_mode = Configuration::getFor('outsourced_pdf_mode', 'crop_to_image', $company->id, $branchId);

        $this->report_page_break_style = Configuration::getFor('report_page_break_style', 'continuous', $company->id, $branchId);
        $this->report_show_dept_header_always = Configuration::getFor('report_show_dept_header_always', '1', $company->id, $branchId) === '1';
        $this->report_show_interpretation = Configuration::getFor('report_show_interpretation', '1', $company->id, $branchId) === '1';
        $this->report_show_note = Configuration::getFor('report_show_note', '1', $company->id, $branchId) === '1';
        $this->report_group_by_dept = Configuration::getFor('report_group_by_dept', '0', $company->id, $branchId) === '1';

        $this->report_flag_high_color = Configuration::getFor('report_flag_high_color', '#cc0000', $company->id, $branchId);
        $this->report_flag_low_color = Configuration::getFor('report_flag_low_color', '#0055aa', $company->id, $branchId);

        $this->report_abnormal_indicator = Configuration::getFor('report_abnormal_indicator', '*', $company->id, $branchId);
        $this->report_abnormal_color = Configuration::getFor('report_abnormal_color', '#d32f2f', $company->id, $branchId);

        $this->authorized_signatory_name = Configuration::getFor('authorized_signatory_name', 'Dr. Authorized Pathologist', $company->id, $branchId);
        $this->authorized_signatory_designation = Configuration::getFor('authorized_signatory_designation', 'Consultant Pathologist', $company->id, $branchId);
        $this->signature_image = Configuration::getFor('signature_image', null, $company->id, $branchId);

        // Global 2 & 3
        $this->global_sig_2_name = Configuration::getFor('global_sig_2_name', '', $company->id, $branchId);
        $this->global_sig_2_desig = Configuration::getFor('global_sig_2_desig', '', $company->id, $branchId);
        $this->global_sig_2_path = Configuration::getFor('global_sig_2_path', null, $company->id, $branchId);
        $this->global_sig_3_name = Configuration::getFor('global_sig_3_name', '', $company->id, $branchId);
        $this->global_sig_3_desig = Configuration::getFor('global_sig_3_desig', '', $company->id, $branchId);
        $this->global_sig_3_path = Configuration::getFor('global_sig_3_path', null, $company->id, $branchId);

        $this->sig_1_position = Configuration::getFor('sig_1_position', 'right', $company->id, $branchId);
        $this->sig_1_enabled = Configuration::getFor('sig_1_enabled', '1', $company->id, $branchId) === '1';
        $this->sig_2_position = Configuration::getFor('sig_2_position', 'left', $company->id, $branchId);
        $this->sig_2_enabled = Configuration::getFor('sig_2_enabled', '1', $company->id, $branchId) === '1';
        $this->sig_3_position = Configuration::getFor('sig_3_position', 'center', $company->id, $branchId);
        $this->sig_3_enabled = Configuration::getFor('sig_3_enabled', '1', $company->id, $branchId) === '1';

        $this->report_signature_mode = Configuration::getFor('report_signature_mode', 'global_bottom', $company->id, $branchId);

        // Barcode settings
        $this->barcode_prefix = Configuration::getFor('barcode_prefix', 'LAB', $company->id, $branchId);
        $this->barcode_date_format = Configuration::getFor('barcode_date_format', 'ymd', $company->id, $branchId);
        $this->barcode_counter_digits = (int) Configuration::getFor('barcode_counter_digits', 6, $company->id, $branchId);
        $this->barcode_print_mode = Configuration::getFor('barcode_print_mode', 'sample', $company->id, $branchId);

        // Branch Controls (Always Global/Company Wide context)
        $this->branch_share_patients = Configuration::getFor('branch_share_patients', '1', $company->id, 'global') === '1';
        $this->branch_share_doctors = Configuration::getFor('branch_share_doctors', '1', $company->id, 'global') === '1';
        $this->branch_share_agents = Configuration::getFor('branch_share_agents', '1', $company->id, 'global') === '1';
        $this->branch_share_tests = Configuration::getFor('branch_share_tests', '1', $company->id, 'global') === '1';
        $this->restrict_branch_access = Configuration::getFor('restrict_branch_access', '1', $company->id, 'global') === '1';

        // Module Visibility (Always Global/Company Wide context)
        $this->default_login_page = Configuration::getFor('default_login_page', 'lab.dashboard', $company->id, 'global');
        $this->show_dashboard_stats = Configuration::getFor('show_dashboard_stats', '1', $company->id, 'global') === '1';
        $this->module_pos = Configuration::getFor('module_pos', '1', $company->id, 'global') === '1';
        $this->module_invoices = Configuration::getFor('module_invoices', '1', $company->id, 'global') === '1';
        $this->module_departments = Configuration::getFor('module_departments', '1', $company->id, 'global') === '1';
        $this->module_tests = Configuration::getFor('module_tests', '1', $company->id, 'global') === '1';
        $this->module_packages = Configuration::getFor('module_packages', '1', $company->id, 'global') === '1';
        $this->module_branches = Configuration::getFor('module_branches', '1', $company->id, 'global') === '1';
        $this->module_collection_centers = Configuration::getFor('module_collection_centers', '1', $company->id, 'global') === '1';
        $this->module_patients = Configuration::getFor('module_patients', '1', $company->id, 'global') === '1';
        $this->module_doctors = Configuration::getFor('module_doctors', '1', $company->id, 'global') === '1';
        $this->module_agents = Configuration::getFor('module_agents', '1', $company->id, 'global') === '1';
        $this->module_settlements = Configuration::getFor('module_settlements', '1', $company->id, 'global') === '1';
        $this->module_marketing = Configuration::getFor('module_marketing', '1', $company->id, 'global') === '1';
        $this->module_inventory = Configuration::getFor('module_inventory', '1', $company->id, 'global') === '1';

        // UI Scaling (Always Global/Company Wide context)
        $this->ui_font_scale = (int) Configuration::getFor('ui_font_scale', 100, $company->id, 'global');

        // WhatsApp Settings (Always Global/Company Wide context)
        $this->whatsapp_share_mode = Configuration::getFor('whatsapp_share_mode', 'pdf', $company->id, 'global');
        $this->whatsapp_invoice_message = Configuration::getFor('whatsapp_invoice_message', '', $company->id, 'global');
        $this->whatsapp_report_message = Configuration::getFor('whatsapp_report_message', '', $company->id, 'global');

        if ($this->selected_dept_id) {
            $this->updatedSelectedDeptId($this->selected_dept_id);
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
        $branchId = $this->selectedBranchId;

        // Handle logo upload
        $logoPath = $this->lab_logo;
        if ($branchId === 'global') {
            $logoPath = $company->logo;
        }
        if (is_object($this->new_logo) && method_exists($this->new_logo, 'store')) {
            $logoPath = $this->new_logo->store('logos');
            if ($branchId !== 'global') {
                Configuration::setFor('lab_logo', $logoPath, $company->id, $branchId);
            } else {
                $company->update(['logo' => $logoPath]);
            }
        }

        // Handle favicon upload
        $faviconPath = $this->lab_favicon;
        if (is_object($this->new_favicon) && method_exists($this->new_favicon, 'store')) {
            $faviconPath = $this->new_favicon->store('favicons');
            Configuration::setFor('lab_favicon', $faviconPath, $company->id, $branchId);
        }

        if ($branchId !== 'global') {
            \App\Models\Branch::where('company_id', $company->id)
                ->where('id', $branchId)
                ->update([
                    'name' => $this->lab_name,
                    'contact_number' => $this->lab_phone,
                    'address' => $this->lab_address,
                ]);
            Configuration::setFor('lab_email', $this->lab_email, $company->id, $branchId);
            Configuration::setFor('lab_website', $this->lab_website, $company->id, $branchId);
            Configuration::setFor('lab_gst_number', $this->lab_gst_number, $company->id, $branchId);
            Configuration::setFor('lab_tagline', $this->lab_tagline, $company->id, $branchId);
        } else {
            $company->update([
                'name' => $this->lab_name,
                'email' => $this->lab_email,
                'phone' => $this->lab_phone,
                'address' => $this->lab_address,
                'website' => $this->lab_website,
                'gst_number' => $this->lab_gst_number,
                'tagline' => $this->lab_tagline,
            ]);
        }

        Configuration::setFor('ui_font_scale', $this->ui_font_scale, $company->id, 'global');

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

        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;

        Configuration::setFor('invoice_prefix', $this->invoice_prefix, $companyId, $branchId);
        Configuration::setFor('invoice_separator', $this->invoice_separator, $companyId, $branchId);
        Configuration::setFor('invoice_date_format', $this->invoice_date_format, $companyId, $branchId);
        Configuration::setFor('invoice_counter_digits', $this->invoice_counter_digits, $companyId, $branchId);
        Configuration::setFor('invoice_counter_reset', $this->invoice_counter_reset, $companyId, $branchId);
        Configuration::setFor('restrict_billing_below_b2b', $this->restrict_billing_below_b2b ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('restrict_unpaid_reports', $this->restrict_unpaid_reports ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('commission_basis_doctor', $this->commission_basis_doctor, $companyId, $branchId);
        Configuration::setFor('commission_basis_agent', $this->commission_basis_agent, $companyId, $branchId);
        Configuration::setFor('partner_show_payment_distribution', $this->partner_show_payment_distribution ? '1' : '0', $companyId, $branchId);

        $hasCustomInvoice = auth()->user()->company->plan?->features['custom_invoice'] ?? false;
        if (!$hasCustomInvoice) {
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

        Configuration::setFor('invoice_show_header', $this->invoice_show_header ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('invoice_show_footer', $this->invoice_show_footer ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('invoice_header_image', $this->invoice_header_image, $companyId, $branchId);
        Configuration::setFor('invoice_footer_image', $this->invoice_footer_image, $companyId, $branchId);
        Configuration::setFor('invoice_margin_top', $this->invoice_margin_top, $companyId, $branchId);
        Configuration::setFor('invoice_margin_bottom', $this->invoice_margin_bottom, $companyId, $branchId);
        Configuration::setFor('invoice_header_height', $this->invoice_header_height, $companyId, $branchId);
        Configuration::setFor('invoice_footer_height', $this->invoice_footer_height, $companyId, $branchId);

        $this->invoiceSaved = true;
    }

    public function removeInvoiceHeaderImage()
    {
        $this->authorize('edit settings');
        if ($this->invoice_header_image) {
            Configuration::setFor('invoice_header_image', '', auth()->user()->company_id, $this->selectedBranchId);
            $this->invoice_header_image = null;
        }
    }

    public function removeInvoiceFooterImage()
    {
        $this->authorize('edit settings');
        if ($this->invoice_footer_image) {
            Configuration::setFor('invoice_footer_image', '', auth()->user()->company_id, $this->selectedBranchId);
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

        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;

        Configuration::setFor('patient_id_prefix', $this->patient_id_prefix, $companyId, $branchId);
        Configuration::setFor('patient_id_digits', $this->patient_id_digits, $companyId, $branchId);

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
            'barcode_print_mode' => 'required|string|in:sample,test',
        ]);

        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;

        Configuration::setFor('barcode_prefix', $this->barcode_prefix, $companyId, $branchId);
        Configuration::setFor('barcode_date_format', $this->barcode_date_format, $companyId, $branchId);
        Configuration::setFor('barcode_counter_digits', $this->barcode_counter_digits, $companyId, $branchId);
        Configuration::setFor('barcode_print_mode', $this->barcode_print_mode, $companyId, $branchId);

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
        if (!$hasCustomInvoice && $this->bill_template !== 'classic') {
            session()->flash('error', 'Plan Restriction: Your current plan only supports the Classic invoice template. Upgrade to a premium plan to use Modern or Professional templates.');
            $this->bill_template = 'classic';

            return;
        }

        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;

        Configuration::setFor('bill_template', $this->bill_template, $companyId, $branchId);
        $this->templateSaved = true;
    }

    // ==========================================
    // SAVE PDF HEADER / FOOTER SETTINGS
    // ==========================================
    public function savePdfSettings()
    {
        $this->authorize('edit settings');
        $this->validate([
            'pdf_letterhead_mode' => 'required|in:separate,full_background',
            'new_header_image' => 'nullable|image|max:3072',
            'new_footer_image' => 'nullable|image|max:3072',
            'new_letterhead_image' => 'nullable|image|max:5120',
        ]);

        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;

        // SaaS Plan Enforcement for Custom Branding
        $hasCustomInvoice = auth()->user()->company->plan?->features['custom_invoice'] ?? false;
        if (!$hasCustomInvoice) {
            if (is_object($this->new_header_image) || is_object($this->new_footer_image) || is_object($this->new_letterhead_image)) {
                session()->flash('error', 'Plan Restriction: Uploading custom letterhead images is a premium feature. Please upgrade your plan.');
                $this->new_header_image = null;
                $this->new_footer_image = null;
                $this->new_letterhead_image = null;

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

        // Upload letterhead image
        if (is_object($this->new_letterhead_image) && method_exists($this->new_letterhead_image, 'store')) {
            $this->pdf_letterhead_image = $this->new_letterhead_image->store('invoice-headers');
            $this->new_letterhead_image = null;
        }

        // Upload signature image
        if (is_object($this->new_signature_image) && method_exists($this->new_signature_image, 'store')) {
            $this->signature_image = $this->new_signature_image->store('signatures');
            $this->new_signature_image = null;
        }

        Configuration::setFor('pdf_show_header', $this->pdf_show_header ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('pdf_show_footer', $this->pdf_show_footer ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('pdf_show_signatures', $this->pdf_show_signatures ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('pdf_show_test_method', $this->pdf_show_test_method ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('pdf_show_watermark', $this->pdf_show_watermark ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('pdf_show_page_number', $this->pdf_show_page_number ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('pdf_show_time', $this->pdf_show_time ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('pdf_page_number_bg_color', $this->pdf_page_number_bg_color, $companyId, $branchId);
        Configuration::setFor('pdf_letterhead_mode', $this->pdf_letterhead_mode, $companyId, $branchId);
        Configuration::setFor('pdf_header_image', $this->pdf_header_image, $companyId, $branchId);
        Configuration::setFor('pdf_footer_image', $this->pdf_footer_image, $companyId, $branchId);
        Configuration::setFor('pdf_letterhead_image', $this->pdf_letterhead_image, $companyId, $branchId);

        // Layout & Typography
        Configuration::setFor('pdf_font_size', $this->pdf_font_size, $companyId, $branchId);
        Configuration::setFor('pdf_font_family', $this->pdf_font_family, $companyId, $branchId);
        Configuration::setFor('pdf_margin_top', $this->pdf_margin_top, $companyId, $branchId);
        Configuration::setFor('pdf_margin_bottom', $this->pdf_margin_bottom, $companyId, $branchId);
        Configuration::setFor('pdf_margin_left', $this->pdf_margin_left, $companyId, $branchId);
        Configuration::setFor('pdf_margin_right', $this->pdf_margin_right, $companyId, $branchId);
        Configuration::setFor('pdf_header_height', $this->pdf_header_height, $companyId, $branchId);
        Configuration::setFor('pdf_footer_height', $this->pdf_footer_height, $companyId, $branchId);

        Configuration::setFor('outsourced_crop_top', $this->outsourced_crop_top, $companyId, $branchId);
        Configuration::setFor('outsourced_crop_bottom', $this->outsourced_crop_bottom, $companyId, $branchId);
        Configuration::setFor('outsourced_pdf_mode', $this->outsourced_pdf_mode, $companyId, $branchId);

        Configuration::setFor('report_page_break_style', $this->report_page_break_style, $companyId, $branchId);
        Configuration::setFor('report_show_dept_header_always', $this->report_show_dept_header_always ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('report_show_interpretation', $this->report_show_interpretation ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('report_show_note', $this->report_show_note ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('report_group_by_dept', $this->report_group_by_dept ? '1' : '0', $companyId, $branchId);

        Configuration::setFor('report_flag_high_color', $this->report_flag_high_color, $companyId, $branchId);
        Configuration::setFor('report_flag_low_color', $this->report_flag_low_color, $companyId, $branchId);

        Configuration::setFor('report_abnormal_indicator', $this->report_abnormal_indicator, $companyId, $branchId);
        Configuration::setFor('report_abnormal_color', $this->report_abnormal_color, $companyId, $branchId);

        Configuration::setFor('authorized_signatory_name', $this->authorized_signatory_name, $companyId, $branchId);
        Configuration::setFor('authorized_signatory_designation', $this->authorized_signatory_designation, $companyId, $branchId);
        Configuration::setFor('signature_image', $this->signature_image, $companyId, $branchId);

        $this->pdfSaved = true;
    }

    // ==========================================
    // SAVE SIGNATURE SETTINGS (Global & Dept)
    // ==========================================
    public function updatedSelectedDeptId($value)
    {
        if (!$value) {
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

        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;

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

        Configuration::setFor('report_signature_mode', $this->report_signature_mode, $companyId, $branchId);
        Configuration::setFor('authorized_signatory_name', $this->authorized_signatory_name, $companyId, $branchId);
        Configuration::setFor('authorized_signatory_designation', $this->authorized_signatory_designation, $companyId, $branchId);
        Configuration::setFor('signature_image', $this->signature_image, $companyId, $branchId);

        Configuration::setFor('global_sig_2_name', $this->global_sig_2_name, $companyId, $branchId);
        Configuration::setFor('global_sig_2_desig', $this->global_sig_2_desig, $companyId, $branchId);
        Configuration::setFor('global_sig_2_path', $this->global_sig_2_path, $companyId, $branchId);

        Configuration::setFor('global_sig_3_name', $this->global_sig_3_name, $companyId, $branchId);
        Configuration::setFor('global_sig_3_desig', $this->global_sig_3_desig, $companyId, $branchId);
        Configuration::setFor('global_sig_3_path', $this->global_sig_3_path, $companyId, $branchId);

        Configuration::setFor('sig_1_position', $this->sig_1_position, $companyId, $branchId);
        Configuration::setFor('sig_1_enabled', $this->sig_1_enabled ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('sig_2_position', $this->sig_2_position, $companyId, $branchId);
        Configuration::setFor('sig_2_enabled', $this->sig_2_enabled ? '1' : '0', $companyId, $branchId);
        Configuration::setFor('sig_3_position', $this->sig_3_position, $companyId, $branchId);
        Configuration::setFor('sig_3_enabled', $this->sig_3_enabled ? '1' : '0', $companyId, $branchId);

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
        $companyId = auth()->user()->company_id;

        Configuration::setFor('default_login_page', $this->default_login_page, $companyId, 'global');
        Configuration::setFor('show_dashboard_stats', $this->show_dashboard_stats ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_pos', $this->module_pos ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_invoices', $this->module_invoices ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_departments', $this->module_departments ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_tests', $this->module_tests ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_packages', $this->module_packages ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_branches', $this->module_branches ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_collection_centers', $this->module_collection_centers ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_patients', $this->module_patients ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_doctors', $this->module_doctors ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_agents', $this->module_agents ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_settlements', $this->module_settlements ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_marketing', $this->module_marketing ? '1' : '0', $companyId, 'global');
        Configuration::setFor('module_inventory', $this->module_inventory ? '1' : '0', $companyId, 'global');

        $this->modulesSaved = true;
    }

    // ==========================================
    // SAVE BRANCH CONTROLS
    // ==========================================
    public function saveBranchControls()
    {
        $this->authorize('edit settings');
        $companyId = auth()->user()->company_id;

        Configuration::setFor('branch_share_patients', $this->branch_share_patients ? '1' : '0', $companyId, 'global');
        Configuration::setFor('branch_share_doctors', $this->branch_share_doctors ? '1' : '0', $companyId, 'global');
        Configuration::setFor('branch_share_agents', $this->branch_share_agents ? '1' : '0', $companyId, 'global');
        Configuration::setFor('branch_share_tests', $this->branch_share_tests ? '1' : '0', $companyId, 'global');
        Configuration::setFor('restrict_branch_access', $this->restrict_branch_access ? '1' : '0', $companyId, 'global');

        $this->branchControlsSaved = true;
    }

    // ==========================================
    // SAVE WHATSAPP SETTINGS
    // ==========================================
    public function saveWhatsappSettings()
    {
        $this->authorize('edit settings');

        // SaaS Plan Enforcement
        $hasWhatsappCustom = auth()->user()->company->plan?->features['whatsapp_custom'] ?? false;
        if (!$hasWhatsappCustom) {
            session()->flash('error', 'Plan Restriction: WhatsApp message customization is not available on your current plan.');

            return;
        }

        $this->validate([
            'whatsapp_share_mode' => 'required|string|in:link,pdf',
            'whatsapp_invoice_message' => 'nullable|string|max:1000',
            'whatsapp_report_message' => 'nullable|string|max:1000',
        ]);

        $companyId = auth()->user()->company_id;

        Configuration::setFor('whatsapp_share_mode', $this->whatsapp_share_mode, $companyId, 'global');
        Configuration::setFor('whatsapp_invoice_message', $this->whatsapp_invoice_message, $companyId, 'global');
        Configuration::setFor('whatsapp_report_message', $this->whatsapp_report_message, $companyId, 'global');

        $this->whatsappSaved = true;
    }

    public function removeHeaderImage()
    {
        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;
        $this->pdf_header_image = null;
        Configuration::setFor('pdf_header_image', null, $companyId, $branchId);
    }

    public function removeFooterImage()
    {
        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;
        $this->pdf_footer_image = null;
        Configuration::setFor('pdf_footer_image', null, $companyId, $branchId);
    }

    public function removeLetterheadImage()
    {
        $companyId = auth()->user()->company_id;
        $branchId = $this->selectedBranchId;
        $this->pdf_letterhead_image = null;
        Configuration::setFor('pdf_letterhead_image', null, $companyId, $branchId);
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

        return $prefix . $datePart . $counter;
    }

    /**
     * Generate a preview of the Patient ID format.
     */
    public function getPatientIdPreviewProperty(): string
    {
        $prefix = $this->patient_id_prefix ?: 'PAT';
        $counter = str_pad(1, max((int) $this->patient_id_digits, 2), '0', STR_PAD_LEFT);

        return $prefix . $counter;
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

    public function getWhatsappInvoicePreviewProperty()
    {
        $text = $this->whatsapp_invoice_message ?: "Hi *{patient_name}*, your invoice *#{invoice_no}* from *{lab_name}* is ready. \n\nYou can download it here: {url}";
        $url = ($this->whatsapp_share_mode === 'link')
            ? 'https://yourlab.com/portal/login'
            : 'https://yourlab.com/bill/aF82j';

        return $this->formatPreviewMessage($text, $url);
    }

    public function getWhatsappReportPreviewProperty()
    {
        $text = $this->whatsapp_report_message ?: "Hi *{patient_name}*, your test report for invoice *#{invoice_no}* from *{lab_name}* is ready. \n\nYou can view it here: {url}";
        $url = ($this->whatsapp_share_mode === 'link')
            ? 'https://yourlab.com/portal/login'
            : 'https://yourlab.com/v/aF82j';

        return $this->formatPreviewMessage($text, $url);
    }

    private function formatPreviewMessage($text, $url)
    {
        $safeText = e($text);

        $processed = str_replace(
            ['{patient_name}', '{invoice_no}', '{lab_name}', '{url}'],
            ['John Doe', 'INV-2026-0001', e($this->lab_name ?? 'My Pathology Lab'), e($url)],
            $safeText
        );

        $processed = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $processed);

        return nl2br($processed, false);
    }

    public function render()
    {
        return view('livewire.lab.settings-manager')
            ->layout('layouts.app', ['title' => 'Settings']);
    }
}

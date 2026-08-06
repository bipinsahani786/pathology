<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\Invoice;
use App\Models\TestReport;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PdfStorageService
{
    /**
     * Generate and save report PDF to R2.
     */
    public function storeReportPdf(TestReport $report, $template = null)
    {
        $companyId = $report->invoice->company_id;
        $branchId = $report->invoice->branch_id;

        if (!$template) {
            $template = Configuration::getFor('report_template', 'new', $companyId, $branchId);
        }
        $report->load([
            'invoice.company', // Critical for logo/watermark
            'invoice.patient.patientProfile',
            'invoice.collectionCenter',
            'invoice.doctor',
            'invoice.items.labTest',
            'results.labTest.dept',
            'invoice.branch',
        ]);

        // ── Configuration settings ──────────────────────────────────────────
        $settings = $this->getSettings($companyId, $branchId);

        // ── QR Code ─────────────────────────────────────────────────────────
        $publicUrl = route('public.report.download', ['hash' => base64_encode($report->invoice_id)]);
        $options = new QROptions([
            'version' => 5,
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => EccLevel::L,
            'scale' => 4,
            'imageTransparent' => false,
        ]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);

        // ── Barcode ─────────────────────────────────────────────────────────
        $generator = new BarcodeGeneratorPNG;
        $barcodeBase64 = base64_encode($generator->getBarcode($report->invoice->invoice_number, $generator::TYPE_CODE_128, 2, 40));
        $barcodeUri = 'data:image/png;base64,' . $barcodeBase64;

        // ── Group Results ───────────────────────────────────────────────────
        $results = $report->results;
        
        // Safety: Filter out results for items that are no longer in the invoice
        $activeItemIds = $report->invoice->items->sortBy('id')->pluck('id')->values()->toArray();
        $results = $results->whereIn('invoice_item_id', $activeItemIds);

        // Sort results to exactly match the sequence of test selection (invoice_item_id order)
        // and preserve the parameter order (result ID).
        $results = $results->sortBy(function ($result) use ($activeItemIds) {
            $itemOrder = array_search($result->invoice_item_id, $activeItemIds);
            return ($itemOrder === false ? 999999 : $itemOrder) . '_' . sprintf('%010d', $result->id);
        });

        // Group by consecutive departments to strictly preserve sequence 
        // without grouping all same-department tests together if they were selected at different times.
        $groupIndex = 0;
        $lastDeptId = -1;
        foreach ($results as $result) {
            $deptId = $result->labTest->department_id ?? 0;
            if ($deptId !== $lastDeptId) {
                $groupIndex++;
                $lastDeptId = $deptId;
            }
            $result->_group_index = $groupIndex;
        }

        $groupedResults = $results->groupBy('_group_index')->map(function ($deptGroup) use ($report) {
            return [
                'department' => $deptGroup->first()->labTest->dept ?? null,
                'tests' => $deptGroup->groupBy(function ($r) {
                    return $r->invoice_item_id . '_' . $r->lab_test_id;
                })->map(function ($testGroup) use ($report) {
                    $first = $testGroup->first();
                    $labTest = $first->labTest;

                    // Sort test results based on the parameter order defined in LabTest
                    $parameterOrder = [];
                    if (is_array($labTest->parameters)) {
                        foreach ($labTest->parameters as $index => $param) {
                            $paramName = is_array($param) ? ($param['name'] ?? '') : $param;
                            $parameterOrder[strtolower(trim($paramName))] = $index;
                        }
                    }

                    $testGroup = $testGroup->sortBy(function ($r) use ($parameterOrder) {
                        $pName = strtolower(trim($r->parameter_name));
                        return $parameterOrder[$pName] ?? 999999;
                    })->values();

                    $itemId = $first->invoice_item_id;
                    $testId = $first->lab_test_id;

                    // Find the invoice item to get comments
                    $item = $report->invoice->items->where('id', $itemId)->first();
                    $remark = '';
                    if ($item) {
                        $raw = $item->report_comments;
                        $decoded = json_decode($raw, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $remark = $decoded[$testId] ?? '';
                        } else {
                            $remark = $raw;
                        }
                    }

                    return [
                        'name' => $first->labTest->name,
                        'labTest' => $first->labTest,
                        'results' => $testGroup,
                        'remark' => $remark,
                    ];
                }),
            ];
        });

        $viewName = 'pdf.report-' . $template;
        if (!view()->exists($viewName)) {
            $viewName = 'pdf.report-new';
        }

        $pdf = Pdf::loadView($viewName, [
            'report' => $report,
            'invoice' => $report->invoice,
            'patient' => $report->invoice->patient,
            'profile' => $report->invoice->patient->patientProfile,
            'groupedResults' => $groupedResults,
            'settings' => $settings,
            'company' => $report->invoice->company,
            'showHeader' => $settings['pdf_show_header'],
            'showFooter' => $settings['pdf_show_footer'],
            'qrCodeUri' => $qrCodeUri,
            'barcodeUri' => $barcodeUri,
        ])->setPaper('A4', 'portrait');

        $path = "reports/{$companyId}/" . md5($report->invoice_id) . '.pdf';
        Storage::disk('r2')->put($path, $pdf->output());

        $report->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * Generate and save invoice PDF to R2.
     */
    public function storeInvoicePdf(Invoice $invoice, $template = null)
    {
        $invoice->load(['company', 'patient.patientProfile', 'collectionCenter', 'doctor', 'items.labTest', 'branch']);
        $companyId = $invoice->company_id;
        $branchId = $invoice->branch_id;
        $settings = $this->getSettings($companyId, $branchId);

        if (!$template) {
            $template = Configuration::getFor('bill_template', 'classic', $companyId, $branchId);
        }

        // ── QR Code ─────────────────────────────────────────────────────────
        $publicUrl = route('public.bill.download', ['hash' => base64_encode($invoice->id)]);
        $options = new QROptions([
            'version' => 5,
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => EccLevel::L,
            'scale' => 4,
            'imageTransparent' => false,
        ]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);

        // ── Barcode ─────────────────────────────────────────────────────────
        $generator = new BarcodeGeneratorPNG;
        $barcodeBase64 = base64_encode($generator->getBarcode($invoice->invoice_number, $generator::TYPE_CODE_128, 2, 40));
        $barcodeUri = 'data:image/png;base64,' . $barcodeBase64;

        $viewName = 'pdf.invoice-' . $template;
        if (!view()->exists($viewName)) {
            $viewName = 'pdf.invoice-classic';
        }

        $pdf = Pdf::loadView($viewName, [
            'invoice' => $invoice,
            'patient' => $invoice->patient,
            'profile' => $invoice->patient->patientProfile,
            'settings' => $settings,
            'company' => $invoice->company,
            'showHeader' => $settings['pdf_show_header'],
            'showFooter' => $settings['pdf_show_footer'],
            'qrCodeUri' => $qrCodeUri,
            'barcodeUri' => $barcodeUri,
        ])->setPaper('A4', 'portrait');

        $path = "invoices/{$companyId}/" . md5($invoice->id) . '.pdf';
        Storage::disk('r2')->put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    private function getSettings($companyId, $branchId = null)
    {
        return [
            'pdf_header_image' => storage_base64(Configuration::getFor('pdf_header_image', null, $companyId, $branchId)),
            'pdf_footer_image' => storage_base64(Configuration::getFor('pdf_footer_image', null, $companyId, $branchId)),
            'report_signature_mode' => Configuration::getFor('report_signature_mode', null, $companyId, $branchId) ?: 'global_bottom',

            'global_sig_1_name' => Configuration::getFor('authorized_signatory_name', null, $companyId, $branchId) ?: 'Authorized Signatory',
            'global_sig_1_desig' => Configuration::getFor('authorized_signatory_designation', null, $companyId, $branchId) ?: '',
            'global_sig_1_path' => storage_base64(Configuration::getFor('signature_image', null, $companyId, $branchId)),

            'global_sig_2_name' => Configuration::getFor('global_sig_2_name', '', $companyId, $branchId) ?: '',
            'global_sig_2_desig' => Configuration::getFor('global_sig_2_desig', '', $companyId, $branchId) ?: '',
            'global_sig_2_path' => storage_base64(Configuration::getFor('global_sig_2_path', null, $companyId, $branchId)),

            'global_sig_3_name' => Configuration::getFor('global_sig_3_name', '', $companyId, $branchId) ?: '',
            'global_sig_3_desig' => Configuration::getFor('global_sig_3_desig', '', $companyId, $branchId) ?: '',
            'global_sig_3_path' => storage_base64(Configuration::getFor('global_sig_3_path', null, $companyId, $branchId)),

            'sig_1_position' => Configuration::getFor('sig_1_position', 'right', $companyId, $branchId) ?: 'right',
            'sig_1_enabled' => Configuration::getFor('sig_1_enabled', '1', $companyId, $branchId) !== '0',
            'sig_2_position' => Configuration::getFor('sig_2_position', 'left', $companyId, $branchId) ?: 'left',
            'sig_2_enabled' => Configuration::getFor('sig_2_enabled', '1', $companyId, $branchId) !== '0',
            'sig_3_position' => Configuration::getFor('sig_3_position', 'center', $companyId, $branchId) ?: 'center',
            'sig_3_enabled' => Configuration::getFor('sig_3_enabled', '1', $companyId, $branchId) !== '0',

            'report_flag_high_color' => Configuration::getFor('report_flag_high_color', '#cc0000', $companyId, $branchId) ?: '#cc0000',
            'report_flag_low_color' => Configuration::getFor('report_flag_low_color', '#0055aa', $companyId, $branchId) ?: '#0055aa',

            'report_abnormal_indicator' => Configuration::getFor('report_abnormal_indicator', '*', $companyId, $branchId) ?: '*',
            'report_abnormal_color' => Configuration::getFor('report_abnormal_color', '#d32f2f', $companyId, $branchId) ?: '#d32f2f',

            'pdf_font_size' => Configuration::getFor('pdf_font_size', null, $companyId, $branchId) ?: 13,
            'pdf_font_family' => Configuration::getFor('pdf_font_family', null, $companyId, $branchId) ?: 'Helvetica',
            'pdf_margin_top' => Configuration::getFor('pdf_margin_top', null, $companyId, $branchId) ?: 310,
            'pdf_margin_bottom' => Configuration::getFor('pdf_margin_bottom', null, $companyId, $branchId) ?: 255,
            'pdf_header_height' => Configuration::getFor('pdf_header_height', null, $companyId, $branchId) ?: 200,
            'pdf_footer_height' => Configuration::getFor('pdf_footer_height', null, $companyId, $branchId) ?: 180,

            // Visibility
            'pdf_show_header' => Configuration::getFor('pdf_show_header', null, $companyId, $branchId) !== '0',
            'pdf_show_footer' => Configuration::getFor('pdf_show_footer', null, $companyId, $branchId) !== '0',
            'pdf_show_signatures' => Configuration::getFor('pdf_show_signatures', null, $companyId, $branchId) !== '0',
            'pdf_show_test_method' => Configuration::getFor('pdf_show_test_method', null, $companyId, $branchId) !== '0',
            'pdf_show_watermark' => Configuration::getFor('pdf_show_watermark', null, $companyId, $branchId) !== '0',
            'pdf_show_page_number' => Configuration::getFor('pdf_show_page_number', null, $companyId, $branchId) !== '0',
            'pdf_show_time' => Configuration::getFor('pdf_show_time', null, $companyId, $branchId) !== '0',
            'pdf_page_number_bg_color' => Configuration::getFor('pdf_page_number_bg_color', 'rgba(255, 255, 255, 0.85)', $companyId, $branchId),

            'report_page_break_style' => Configuration::getFor('report_page_break_style', 'continuous', $companyId, $branchId),
            'report_show_dept_header_always' => Configuration::getFor('report_show_dept_header_always', '1', $companyId, $branchId) === '1',
            'report_show_interpretation' => Configuration::getFor('report_show_interpretation', '1', $companyId, $branchId) === '1',
            'report_show_note' => Configuration::getFor('report_show_note', '1', $companyId, $branchId) === '1',
        ];
    }
}

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
        if (!$template) {
            $template = Configuration::getFor('report_template', 'new', $report->invoice->company_id);
        }
        $report->load([
            'invoice.company', // Critical for logo/watermark
            'invoice.patient.patientProfile',
            'invoice.collectionCenter',
            'invoice.doctor',
            'invoice.items.labTest',
            'results.labTest.dept',
        ]);

        $companyId = $report->invoice->company_id;

        // ── Configuration settings ──────────────────────────────────────────
        $settings = $this->getSettings($companyId);

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
        $groupedResults = $results->groupBy(function ($result) {
            return $result->labTest->department_id ?? 0;
        })->map(function ($deptGroup) use ($report) {
            return [
                'department' => $deptGroup->first()->labTest->dept ?? null,
                'tests' => $deptGroup->groupBy(function ($r) {
                    return $r->invoice_item_id . '_' . $r->lab_test_id;
                })->map(function ($testGroup) use ($report) {
                    $first = $testGroup->first();
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
        $invoice->load(['company', 'patient.patientProfile', 'collectionCenter', 'doctor', 'items.labTest']);
        $companyId = $invoice->company_id;
        $settings = $this->getSettings($companyId);

        if (!$template) {
            $template = Configuration::getFor('bill_template', 'classic', $companyId);
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

    private function getSettings($companyId)
    {
        return [
            'pdf_header_image' => storage_base64(Configuration::getFor('pdf_header_image', null, $companyId)),
            'pdf_footer_image' => storage_base64(Configuration::getFor('pdf_footer_image', null, $companyId)),
            'report_signature_mode' => Configuration::getFor('report_signature_mode', null, $companyId) ?: 'global_bottom',

            'global_sig_1_name' => Configuration::getFor('authorized_signatory_name', null, $companyId) ?: 'Authorized Signatory',
            'global_sig_1_desig' => Configuration::getFor('authorized_signatory_designation', null, $companyId) ?: '',
            'global_sig_1_path' => storage_base64(Configuration::getFor('signature_image', null, $companyId)),

            'global_sig_2_name' => Configuration::getFor('global_sig_2_name', '', $companyId) ?: '',
            'global_sig_2_desig' => Configuration::getFor('global_sig_2_desig', '', $companyId) ?: '',
            'global_sig_2_path' => storage_base64(Configuration::getFor('global_sig_2_path', null, $companyId)),

            'global_sig_3_name' => Configuration::getFor('global_sig_3_name', '', $companyId) ?: '',
            'global_sig_3_desig' => Configuration::getFor('global_sig_3_desig', '', $companyId) ?: '',
            'global_sig_3_path' => storage_base64(Configuration::getFor('global_sig_3_path', null, $companyId)),

            'sig_1_position' => Configuration::getFor('sig_1_position', 'right', $companyId) ?: 'right',
            'sig_1_enabled' => Configuration::getFor('sig_1_enabled', '1', $companyId) !== '0',
            'sig_2_position' => Configuration::getFor('sig_2_position', 'left', $companyId) ?: 'left',
            'sig_2_enabled' => Configuration::getFor('sig_2_enabled', '1', $companyId) !== '0',
            'sig_3_position' => Configuration::getFor('sig_3_position', 'center', $companyId) ?: 'center',
            'sig_3_enabled' => Configuration::getFor('sig_3_enabled', '1', $companyId) !== '0',

            'report_flag_high_color' => Configuration::getFor('report_flag_high_color', '#cc0000', $companyId) ?: '#cc0000',
            'report_flag_low_color' => Configuration::getFor('report_flag_low_color', '#0055aa', $companyId) ?: '#0055aa',

            'report_abnormal_indicator' => Configuration::getFor('report_abnormal_indicator', '*', $companyId) ?: '*',
            'report_abnormal_color' => Configuration::getFor('report_abnormal_color', '#d32f2f', $companyId) ?: '#d32f2f',

            'pdf_font_size' => Configuration::getFor('pdf_font_size', null, $companyId) ?: 13,
            'pdf_font_family' => Configuration::getFor('pdf_font_family', null, $companyId) ?: 'Helvetica',
            'pdf_margin_top' => Configuration::getFor('pdf_margin_top', null, $companyId) ?: 310,
            'pdf_margin_bottom' => Configuration::getFor('pdf_margin_bottom', null, $companyId) ?: 255,
            'pdf_header_height' => Configuration::getFor('pdf_header_height', null, $companyId) ?: 200,
            'pdf_footer_height' => Configuration::getFor('pdf_footer_height', null, $companyId) ?: 180,

            // Visibility
            'pdf_show_header' => Configuration::getFor('pdf_show_header', null, $companyId) !== '0',
            'pdf_show_footer' => Configuration::getFor('pdf_show_footer', null, $companyId) !== '0',
            'pdf_show_signatures' => Configuration::getFor('pdf_show_signatures', null, $companyId) !== '0',
            'pdf_show_test_method' => Configuration::getFor('pdf_show_test_method', null, $companyId) !== '0',
            'pdf_show_watermark' => Configuration::getFor('pdf_show_watermark', null, $companyId) !== '0',

            'report_page_break_style' => Configuration::getFor('report_page_break_style', 'continuous', $companyId),
            'report_show_dept_header_always' => Configuration::getFor('report_show_dept_header_always', '1', $companyId) === '1',
            'report_show_interpretation' => Configuration::getFor('report_show_interpretation', '1', $companyId) === '1',
            'report_show_note' => Configuration::getFor('report_show_note', '1', $companyId) === '1',
        ];
    }
}

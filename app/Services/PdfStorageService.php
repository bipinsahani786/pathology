<?php

namespace App\Services;

use App\Models\TestReport;
use App\Models\Invoice;
use App\Models\Configuration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
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
            'invoice.patient.patientProfile',
            'invoice.collectionCenter',
            'invoice.doctor',
            'invoice.items.labTest',
            'results.labTest.dept'
        ]);

        $companyId = $report->invoice->company_id;

        // ── Configuration settings ──────────────────────────────────────────
        $settings = $this->getSettings($companyId);

        // ── QR Code ─────────────────────────────────────────────────────────
        $publicUrl = route('public.report.download', ['hash' => base64_encode($report->invoice_id)]);
        $options = new QROptions([
            'version'         => 5,
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel'        => EccLevel::L,
            'scale'           => 4,
            'imageTransparent'=> false,
        ]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);

        // ── Barcode ─────────────────────────────────────────────────────────
        $generator = new BarcodeGeneratorPNG();
        $barcodeBase64 = base64_encode($generator->getBarcode($report->invoice->invoice_number, $generator::TYPE_CODE_128, 2, 40));
        $barcodeUri = 'data:image/png;base64,' . $barcodeBase64;

        // ── Group Results ───────────────────────────────────────────────────
        $results = $report->results;
        $groupedResults = $results->groupBy(function ($result) {
            return $result->labTest->department_id ?? 0;
        })->map(function ($deptGroup) {
            return [
                'department' => $deptGroup->first()->labTest->dept ?? null,
                'tests'      => $deptGroup->groupBy('invoice_item_id')->map(function ($testGroup) {
                    return [
                        'name'    => $testGroup->first()->labTest->name,
                        'labTest' => $testGroup->first()->labTest,
                        'results' => $testGroup,
                    ];
                }),
            ];
        });

        $viewName = 'pdf.report-' . $template;
        if (!view()->exists($viewName)) {
            $viewName = 'pdf.report-new';
        }

        $pdf = Pdf::loadView($viewName, [
            'report'         => $report,
            'invoice'        => $report->invoice,
            'patient'        => $report->invoice->patient,
            'profile'        => $report->invoice->patient->patientProfile,
            'groupedResults' => $groupedResults,
            'settings'       => $settings,
            'company'        => $report->invoice->company,
            'showHeader'     => $settings['pdf_show_header'],
            'showFooter'     => $settings['pdf_show_footer'],
            'qrCodeUri'      => $qrCodeUri,
            'barcodeUri'     => $barcodeUri,
        ])->setPaper('A4', 'portrait');

        $path = "reports/{$companyId}/" . md5($report->invoice_id) . ".pdf";
        Storage::disk('r2')->put($path, $pdf->output());

        $report->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * Generate and save invoice PDF to R2.
     */
    public function storeInvoicePdf(Invoice $invoice, $template = null)
    {
        $invoice->load(['patient.patientProfile', 'collectionCenter', 'doctor', 'items.labTest']);
        $companyId = $invoice->company_id;
        $settings = $this->getSettings($companyId);

        if (!$template) {
            $template = Configuration::getFor('bill_template', 'classic', $companyId);
        }

        // ── QR Code ─────────────────────────────────────────────────────────
        $publicUrl = route('public.bill.download', ['hash' => base64_encode($invoice->id)]);
        $options = new QROptions([
            'version'         => 5,
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel'        => EccLevel::L,
            'scale'           => 4,
            'imageTransparent'=> false,
        ]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);

        // ── Barcode ─────────────────────────────────────────────────────────
        $generator = new BarcodeGeneratorPNG();
        $barcodeBase64 = base64_encode($generator->getBarcode($invoice->invoice_number, $generator::TYPE_CODE_128, 2, 40));
        $barcodeUri = 'data:image/png;base64,' . $barcodeBase64;

        $viewName = 'pdf.invoice-' . $template;
        if (!view()->exists($viewName)) {
            $viewName = 'pdf.invoice-classic';
        }

        $pdf = Pdf::loadView($viewName, [
            'invoice'    => $invoice,
            'patient'    => $invoice->patient,
            'profile'    => $invoice->patient->patientProfile,
            'settings'   => $settings,
            'company'    => $invoice->company,
            'showHeader' => $settings['pdf_show_header'],
            'showFooter' => $settings['pdf_show_footer'],
            'qrCodeUri'  => $qrCodeUri,
            'barcodeUri' => $barcodeUri,
        ])->setPaper('A4', 'portrait');

        $path = "invoices/{$companyId}/" . md5($invoice->id) . ".pdf";
        Storage::disk('r2')->put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    private function getSettings($companyId)
    {
        return [
            'pdf_header_image'       => storage_base64(Configuration::getFor('pdf_header_image', null, $companyId)),
            'pdf_footer_image'       => storage_base64(Configuration::getFor('pdf_footer_image', null, $companyId)),
            'report_signature_mode'  => Configuration::getFor('report_signature_mode', null, $companyId) ?: 'global_bottom',

            'global_sig_1_name'      => Configuration::getFor('authorized_signatory_name', null, $companyId) ?: 'Authorized Signatory',
            'global_sig_1_desig'     => Configuration::getFor('authorized_signatory_designation', null, $companyId) ?: '',
            'global_sig_1_path'      => storage_base64(Configuration::getFor('signature_image', null, $companyId)),

            'global_sig_2_name'      => Configuration::getFor('global_sig_2_name', '', $companyId) ?: '',
            'global_sig_2_desig'     => Configuration::getFor('global_sig_2_desig', '', $companyId) ?: '',
            'global_sig_2_path'      => storage_base64(Configuration::getFor('global_sig_2_path', null, $companyId)),

            'global_sig_3_name'      => Configuration::getFor('global_sig_3_name', '', $companyId) ?: '',
            'global_sig_3_desig'     => Configuration::getFor('global_sig_3_desig', '', $companyId) ?: '',
            'global_sig_3_path'      => storage_base64(Configuration::getFor('global_sig_3_path', null, $companyId)),

            'pdf_font_size'          => Configuration::getFor('pdf_font_size', null, $companyId) ?: 13,
            'pdf_font_family'        => Configuration::getFor('pdf_font_family', null, $companyId) ?: 'Helvetica',
            'pdf_margin_top'         => Configuration::getFor('pdf_margin_top', null, $companyId) ?: 310,
            'pdf_margin_bottom'      => Configuration::getFor('pdf_margin_bottom', null, $companyId) ?: 255,
            'pdf_header_height'      => Configuration::getFor('pdf_header_height', null, $companyId) ?: 200,
            'pdf_footer_height'      => Configuration::getFor('pdf_footer_height', null, $companyId) ?: 180,

            // Visibility
            'pdf_show_header'        => Configuration::getFor('pdf_show_header', null, $companyId) !== '0',
            'pdf_show_footer'        => Configuration::getFor('pdf_show_footer', null, $companyId) !== '0',
        ];
    }
}

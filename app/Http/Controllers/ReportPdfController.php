<?php

namespace App\Http\Controllers;

use App\Models\TestReport;
use App\Models\Configuration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ReportPdfController extends Controller
{
    /**
     * Generate and stream the Lab Report PDF.
     */
    public function download(Request $request, $id, $template = 'new')
    {
        return $this->generateReport($request, $id, $template, false);
    }

    /**
     * Publicly stream/download a report via ID (bypass auth)
     */
    public function streamPublicLink($id)
    {
        $report = TestReport::where('invoice_id', $id)->first();
        if ($report && $report->pdf_path && \Illuminate\Support\Facades\Storage::disk('r2')->exists($report->pdf_path)) {
            // Get public URL from R2
            $url = \Illuminate\Support\Facades\Storage::disk('r2')->url($report->pdf_path);
            return redirect($url);
        }

        // If not pre-generated, generate now (and optionally save)
        return $this->generateReport(new Request(['header' => '1']), $id, 'new', true);
    }

    private function generateReport(Request $request, $invoiceId, $template, $isPublic = false)
    {
        // ── R2 Offload Check ────────────────────────────────────────────────
        // If this is a standard full report request, try to serve from R2
        if ($request->get('header', '1') === '1' && !$request->has('tests')) {
            $report = TestReport::where('invoice_id', $invoiceId)->first();
            if ($report && $report->pdf_path && \Illuminate\Support\Facades\Storage::disk('r2')->exists($report->pdf_path)) {
                return redirect(\Illuminate\Support\Facades\Storage::disk('r2')->url($report->pdf_path));
            }
        }

        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '512M');

        // Load report with invoice_id (which is a string-based ID in this context)
        $report = TestReport::with([
            'invoice.patient.patientProfile',
            'invoice.collectionCenter',
            'invoice.doctor',
            'invoice.items.labTest',
            'results.labTest.dept'
        ])->where('invoice_id', $invoiceId)->firstOrFail();

        // Auth & Isolation check for non-public access
        if (!$isPublic) {
            $user = auth()->user();
            
            // 1. Company Isolation
            if ($report->invoice->company_id !== $user->company_id) {
                abort(403, 'Unauthorized company access.');
            }

            // 2. Patient Isolation: Patients can only see their own reports
            if ($user->hasRole('patient') && $report->invoice->patient_id !== $user->id) {
                abort(403, 'You are not authorized to view this report.');
            }
            
            // 3. Branch Isolation: If enabled, staff can only see their branch's reports
            $companyId = $user->company_id;
            $restrictBranch = Configuration::getFor('restrict_branch_access', '1', $companyId) === '1';
            $isGlobalAdmin = $user->hasAnyRole(['lab_admin', 'super_admin']);
            
            if ($restrictBranch && !$isGlobalAdmin && $report->invoice->branch_id !== $user->branch_id) {
                abort(403, 'You do not have access to reports from this branch.');
            }
        }

        $companyId = $report->invoice->company_id;
        $showHeader = $request->get('header', '1') === '1';

        // ── Configuration settings ──────────────────────────────────────────
        $settings = [
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

        // Determine final visibility (Setting toggle AND override via URL)
        $showHeaderSetting = (bool)($settings['pdf_show_header'] ?? true);
        $showFooterSetting = (bool)($settings['pdf_show_footer'] ?? true);
        
        $showHeader = $showHeaderSetting && ($request->get('header', '1') === '1');
        $showFooter = $showFooterSetting;

        // ── QR Code Generation ──────────────────────────────────────────────
        $publicUrl = route('public.report.download', ['hash' => base64_encode($report->invoice_id)]);
        $options = new QROptions([
            'version'         => 5,
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel'        => EccLevel::L,
            'scale'           => 4,
            'imageTransparent'=> false,
        ]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);

        // ── Barcode Generation ──────────────────────────────────────────────
        $generator = new BarcodeGeneratorPNG();
        $barcodeBase64 = base64_encode($generator->getBarcode($report->invoice->invoice_number, $generator::TYPE_CODE_128, 2, 40));
        $barcodeUri = 'data:image/png;base64,' . $barcodeBase64;

        // ── Group Results ───────────────────────────────────────────────────
        $results = $report->results;
        if ($request->has('tests')) {
            $testIds = explode(',', $request->tests);
            $results = $results->whereIn('invoice_item_id', $testIds);
        }

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
            'showHeader'     => $showHeader,
            'showFooter'     => $showFooter,
            'qrCodeUri'      => $qrCodeUri,
            'barcodeUri'     => $barcodeUri,
        ])->setPaper('A4', 'portrait');

        $filename = 'Report_' . str_replace(' ', '_', $report->invoice->patient->name)
                  . '_' . $report->invoice->invoice_number . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Stream a "New" report format (legacy support)
     */
    public function generateNew($reportId, Request $request)
    {
        return $this->download($request, $reportId, 'new');
    }
}

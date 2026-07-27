<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\TestReport;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ReportPdfController extends Controller
{
    /**
     * Generate and stream the Lab Report PDF.
     */
    public function download(Request $request, $id, $template = 'new', $filename = null)
    {
        return $this->generateReport($request, $id, $template, false);
    }

    /**
     * Publicly stream/download a report via ID (bypass auth)
     */
    public function streamPublicLink($id)
    {
        $report = TestReport::with(['invoice.company', 'invoice.branch'])
            ->where('invoice_id', $id)
            ->latest()
            ->first();

        // Always generate fresh PDF so latest settings (page breaks, font size, etc.) are applied.
        // Previously, a stale R2-cached PDF was served which didn't reflect settings changes.

        // If not pre-generated, generate now using company's preferred template
        $companyId = $report ? $report->invoice->company_id : null;
        $branchId = $report ? $report->invoice->branch_id : null;

        // Restriction Check for Unpaid Reports
        $restrict = Configuration::getFor('restrict_unpaid_reports', '0', $companyId, $branchId) === '1';
        if ($restrict && $report && strtolower($report->invoice->payment_status) !== 'paid') {
            return response()->view('public.restricted-report', ['invoice' => $report->invoice]);
        }

        $template = Configuration::getFor('report_template', 'new', $companyId, $branchId);

        return $this->generateReport(new Request(['header' => '1']), $id, $template, true);
    }

    private function generateReport(Request $request, $invoiceId, $template, $isPublic = false)
    {
        // ── R2 Offload Check ────────────────────────────────────────────────
        // If this is a standard full report request, try to serve from R2
        /*
        if ($request->get('header', '1') === '1' && !$request->has('tests')) {
            $report = TestReport::where('invoice_id', $invoiceId)->first();
            if ($report && $report->pdf_path && \Illuminate\Support\Facades\Storage::disk('r2')->exists($report->pdf_path)) {
                return redirect(\Illuminate\Support\Facades\Storage::disk('r2')->url($report->pdf_path));
            }
        }
        */

        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '512M');

        // Load report with invoice_id (which is a string-based ID in this context)
        $report = TestReport::with([
            'invoice.company', // Critical for logo/watermark
            'invoice.patient.patientProfile',
            'invoice.collectionCenter',
            'invoice.doctor',
            'invoice.items.labTest',
            'results.labTest.dept',
            'invoice.branch',
        ])->where('invoice_id', $invoiceId)->firstOrFail();

        $patientName = str_replace([' ', '/', '\\'], '_', $report->invoice->patient->name);
        $filename = 'Report_'.$patientName.'_'.$report->invoice->invoice_number.'.pdf';

        if (!$isPublic) {
            $currentPath = $request->getPathInfo();
            if (!str_ends_with($currentPath, $filename)) {
                $newPath = rtrim($currentPath, '/') . '/' . $filename;
                $queryString = $request->getQueryString();
                return redirect()->to($newPath . ($queryString ? '?' . $queryString : ''));
            }
        }

        // Auth & Isolation check for non-public access
        if (! $isPublic) {
            $user = auth()->user();

            // 1. Company Isolation
            if ($report->invoice->company_id !== $user->company_id) {
                abort(403, 'Unauthorized company access.');
            }

            // 2. Patient Isolation: Patients can only see their own reports
            if ($user->hasRole('patient')) {
                if ($report->invoice->patient_id !== $user->id) {
                    abort(403, 'You are not authorized to view this report.');
                }

                $restrict = Configuration::getFor('restrict_unpaid_reports', '0', $report->invoice->company_id, $report->invoice->branch_id) === '1';
                if ($restrict && strtolower($report->invoice->payment_status) !== 'paid') {
                    abort(403, 'Payment Pending. Please clear your dues to view this report.');
                }
            }

            // 3. Branch Isolation: If enabled, staff can only see their branch's reports
            $companyId = $user->company_id;
            $restrictBranch = Configuration::getFor('restrict_branch_access', '1', $companyId) === '1';
            
            $roles = $user->roles->pluck('name')->toArray();
            $isGlobalAdmin = ($user->hasAnyRole(['lab_admin', 'super_admin']) ||
                collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_contains(strtolower($r), 'admin')))
                && ! $user->hasRole('branch_admin');

            if ($restrictBranch && ! $isGlobalAdmin && $user->branch_id !== null && $report->invoice->branch_id !== $user->branch_id) {
                abort(403, 'You do not have access to reports from this branch.');
            }

            // 4. Partner Isolation: Doctors/Agents/CCs only see their referrals
            if (! $isGlobalAdmin && ! $user->hasRole('patient')) {
                $isDoctor = $user->hasRole('doctor') || $user->doctorProfile;
                $isAgent = $user->hasRole('agent') || $user->agentProfile;
                $isCC = $user->hasRole('collection_center') || $user->collection_center_id;

                if ($isDoctor && $report->invoice->referred_by_doctor_id !== $user->id) {
                    abort(403, 'Unauthorized referral access.');
                }
                if ($isAgent && $report->invoice->referred_by_agent_id !== $user->id) {
                    abort(403, 'Unauthorized agent referral access.');
                }
                if ($isCC && $report->invoice->collection_center_id !== $user->collection_center_id) {
                    abort(403, 'Unauthorized collection center access.');
                }
            }
        }

        $companyId = $report->invoice->company_id;
        $branchId = $report->invoice->branch_id;
        $showHeader = $request->get('header', '1') === '1';

        $headerImage = Configuration::getFor('pdf_header_image', null, $companyId, $branchId);
        $footerImage = Configuration::getFor('pdf_footer_image', null, $companyId, $branchId);
        $letterheadImage = Configuration::getFor('pdf_letterhead_image', null, $companyId, $branchId);

        // ── Configuration settings ──────────────────────────────────────────
        $settings = [
            'pdf_header_image' => storage_base64($headerImage),
            'pdf_footer_image' => storage_base64($footerImage),
            'pdf_letterhead_image' => storage_base64($letterheadImage),
            'pdf_letterhead_mode' => Configuration::getFor('pdf_letterhead_mode', 'separate', $companyId, $branchId),
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

            // ALWAYS reserve space for physical letterhead (1 inch = ~96px minimum, but user wants settings-driven)
            'pdf_margin_top' => Configuration::getFor('pdf_margin_top', null, $companyId, $branchId) ?: 320,
            'pdf_margin_bottom' => Configuration::getFor('pdf_margin_bottom', null, $companyId, $branchId) ?: 280,

            'pdf_header_height' => Configuration::getFor('pdf_header_height', null, $companyId, $branchId) ?: 200,
            'pdf_footer_height' => Configuration::getFor('pdf_footer_height', null, $companyId, $branchId) ?: 180,
            'pdf_header_image' => ($request->get('header', '1') === '1' && $headerImage) ? storage_base64($headerImage) : null,
            'pdf_footer_image' => ($request->get('header', '1') === '1' && Configuration::getFor('pdf_show_footer', '1', $companyId, $branchId) === '1' && $footerImage) ? storage_base64($footerImage) : null,
            'pdf_letterhead_image' => ($request->get('header', '1') === '1' && $letterheadImage) ? storage_base64($letterheadImage) : null,

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

        // Determine final visibility (Setting toggle AND override via URL)
        $showHeaderSetting = (bool) ($settings['pdf_show_header'] ?? true);
        $showFooterSetting = (bool) ($settings['pdf_show_footer'] ?? true);

        $showHeader = $showHeaderSetting && ($request->get('header', '1') === '1');
        $showFooter = $showFooterSetting && ($request->get('header', '1') === '1');

        // ── QR Code Generation ──────────────────────────────────────────────
        $publicUrl = route('public.report.download', ['hash' => base64_encode($report->invoice_id)]);
        $options = new QROptions([
            'version' => 5,
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => EccLevel::L,
            'scale' => 4,
            'imageTransparent' => false,
        ]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);

        // ── Barcode Generation ──────────────────────────────────────────────
        $generator = new BarcodeGeneratorPNG;
        $barcodeBase64 = base64_encode($generator->getBarcode($report->invoice->invoice_number, $generator::TYPE_CODE_128, 2, 40));
        $barcodeUri = 'data:image/png;base64,'.$barcodeBase64;

        // ── Group Results ───────────────────────────────────────────────────
        $results = $report->results;

        // Safety: Filter out results for items that are no longer in the invoice
        $activeItemIds = $report->invoice->items->pluck('id')->toArray();
        $results = $results->whereIn('invoice_item_id', $activeItemIds);

        if ($request->has('tests')) {
            $testIds = explode(',', $request->tests);
            
            $selectedItemIds = [];
            $selectedItemTestIds = [];
            
            foreach ($testIds as $t) {
                if (str_contains($t, '_')) {
                    $selectedItemTestIds[] = $t;
                } else {
                    $selectedItemIds[] = $t;
                }
            }

            $results = $results->filter(function($r) use ($selectedItemIds, $selectedItemTestIds) {
                $matchItem = in_array((string)$r->invoice_item_id, $selectedItemIds);
                $matchItemTest = in_array($r->invoice_item_id . '_' . $r->lab_test_id, $selectedItemTestIds);
                return $matchItem || $matchItemTest;
            });
        }

        $groupedResults = $results->groupBy(function ($result) {
            return $result->labTest->department_id ?? 0;
        })->map(function ($deptGroup) use ($report) {
            return [
                'department' => $deptGroup->first()->labTest->dept ?? null,
                'tests' => $deptGroup->groupBy(function ($r) {
                    return $r->invoice_item_id.'_'.$r->lab_test_id;
                })->map(function ($testGroup) use ($report) {
                    $first = $testGroup->first();
                    $labTest = $first->labTest;

                    // Sort test results based on the parameter order defined in LabTest
                    $parameterOrder = [];
                    if (is_array($labTest->parameters)) {
                        foreach ($labTest->parameters as $index => $param) {
                            $paramName = is_array($param) ? ($param['name'] ?? '') : $param;
                            $parameterOrder[$paramName] = $index;
                        }
                    }

                    $testGroup = $testGroup->sortBy(function ($r) use ($parameterOrder) {
                        return $parameterOrder[$r->parameter_name] ?? 9999;
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
                            // New granular format (JSON keyed by test_id)
                            $remark = $decoded[$testId] ?? '';
                        } else {
                            // Legacy format (String).
                            // If it's a package, we don't know which test it belongs to,
                            // but usually it was intended for the whole item, so we show it for all
                            // or maybe just the last one? Showing for all is safer for not losing data.
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

        // ── Process Outsourced Report ────────────────────────────────────────
        $outsourcedImages = [];
        $pdfService = app(\App\Services\OutsourcedReportService::class);
        $outsourcedPdfMode = \App\Models\Configuration::getFor('outsourced_pdf_mode', 'crop_to_image', $companyId, $branchId);
        $mergeOutsourcedPdf = false;
        $mergedPdfPath = null;
        
        if (!empty($report->outsourced_pdf_path)) {
            $paths = is_array($report->outsourced_pdf_path) ? $report->outsourced_pdf_path : [$report->outsourced_pdf_path];
            
            if ($outsourcedPdfMode === 'merge_with_header_footer') {
                $mergeOutsourcedPdf = true;
                
                // Fetch the stored paths (not base64) for FPDI
                $headerImagePath = $showHeader ? \App\Models\Configuration::getFor('pdf_header_image', null, $companyId, $branchId) : null;
                $footerImagePath = $showFooter ? \App\Models\Configuration::getFor('pdf_footer_image', null, $companyId, $branchId) : null;
                
                try {
                    $mergedParts = [];
                    foreach ($paths as $path) {
                        $mergedParts[] = $pdfService->mergeHeaderFooter(
                            $path,
                            $headerImagePath,
                            $footerImagePath
                        );
                    }
                    if (count($mergedParts) > 1) {
                        $mergedPdfPath = $pdfService->mergePdfs($mergedParts);
                        // Cleanup individual merged parts
                        foreach ($mergedParts as $part) @unlink($part);
                    } elseif (count($mergedParts) === 1) {
                        $mergedPdfPath = $mergedParts[0];
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to merge FPDI pdf: " . $e->getMessage());
                    $mergeOutsourcedPdf = false;
                }
            }
            
            if (!$mergeOutsourcedPdf) {
                $allImages = [];
                foreach ($paths as $path) {
                    $images = $pdfService->convertAndCrop(
                        $path,
                        $report->outsourced_crop_top ?? (int) \App\Models\Configuration::getFor('outsourced_crop_top', 18, $companyId, $branchId),
                        $report->outsourced_crop_bottom ?? (int) \App\Models\Configuration::getFor('outsourced_crop_bottom', 8, $companyId, $branchId)
                    );
                    $allImages = array_merge($allImages, $images);
                }
                
                if (!empty($allImages)) {
                    $outsourcedImages['report'] = [
                        'lab_name' => $report->outsourced_lab_name,
                        'images' => $allImages,
                    ];
                }
            }
        }

        $viewName = 'pdf.report-'.$template;
        if (! view()->exists($viewName)) {
            $viewName = 'pdf.report-new';
        }

        $pdf = Pdf::loadView($viewName, [
            'report' => $report,
            'invoice' => $report->invoice,
            'patient' => $report->invoice->patient,
            'profile' => $report->invoice->patient->patientProfile,
            'groupedResults' => $groupedResults,
            'outsourcedImages' => $outsourcedImages,
            'settings' => $settings,
            'company' => $report->invoice->company,
            'showHeader' => $showHeader,
            'showFooter' => $showFooter,
            'qrCodeUri' => $qrCodeUri,
            'barcodeUri' => $barcodeUri,
        ])->setPaper('A4', 'portrait');

        $patientName = str_replace([' ', '/', '\\'], '_', $report->invoice->patient->name);
        $filename = 'Report_'.$patientName.'_'.$report->invoice->invoice_number.'.pdf';

        // If we merged the outsourced PDF with FPDI
        if ($mergeOutsourcedPdf && $mergedPdfPath) {
            // If there are normal results, merge them with the outsourced PDF
            if ($groupedResults->isNotEmpty()) {
                $domPdfContent = $pdf->output();
                $tempDomPdf = tempnam(sys_get_temp_dir(), 'dompdf_') . '.pdf';
                file_put_contents($tempDomPdf, $domPdfContent);
                
                $finalPdfPath = $pdfService->mergePdfs([$tempDomPdf, $mergedPdfPath]);
                
                $finalPdfContent = file_get_contents($finalPdfPath);
                @unlink($tempDomPdf);
                @unlink($mergedPdfPath);
                @unlink($finalPdfPath);
                
                return response($finalPdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
            } else {
                // Only outsourced results exist
                $finalPdfContent = file_get_contents($mergedPdfPath);
                @unlink($mergedPdfPath);
                
                return response($finalPdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
            }
        }

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

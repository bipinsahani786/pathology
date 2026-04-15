<?php

namespace App\Http\Controllers;

use App\Models\TestReport;
use App\Models\Configuration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function download(Request $request, $id, $template = 'modern')
    {
        $report = TestReport::with([
            'invoice.patient.patientProfile', 
            'invoice.collectionCenter', 
            'invoice.doctor',
            'results.labTest.department'
        ])->where('invoice_id', $id)->firstOrFail();

        if ($report->invoice->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Configuration bindings
        $companyId = $report->company_id;
        $settings = [
            'pdf_show_header' => Configuration::getFor('pdf_show_header', '1', $companyId) === '1',
            'pdf_show_footer' => Configuration::getFor('pdf_show_footer', '1', $companyId) === '1',
            'pdf_header_image' => Configuration::getFor('pdf_header_image', null, $companyId),
            'pdf_footer_image' => Configuration::getFor('pdf_footer_image', null, $companyId),
            
            'report_signature_mode' => Configuration::getFor('report_signature_mode', 'global_bottom', $companyId),
            
            // Global 1
            'global_sig_1_name' => Configuration::getFor('authorized_signatory_name', 'Dr. Authorized Pathologist', $companyId),
            'global_sig_1_desig' => Configuration::getFor('authorized_signatory_designation', 'Consultant Pathologist', $companyId),
            'global_sig_1_path' => Configuration::getFor('signature_image', null, $companyId),

            // Global 2
            'global_sig_2_name' => Configuration::getFor('global_sig_2_name', '', $companyId),
            'global_sig_2_desig' => Configuration::getFor('global_sig_2_desig', '', $companyId),
            'global_sig_2_path' => Configuration::getFor('global_sig_2_path', null, $companyId),

            // Global 3
            'global_sig_3_name' => Configuration::getFor('global_sig_3_name', '', $companyId),
            'global_sig_3_desig' => Configuration::getFor('global_sig_3_desig', '', $companyId),
            'global_sig_3_path' => Configuration::getFor('global_sig_3_path', null, $companyId),
        ];

        // Format data for view: Group by Department => Test Name => Results
        $results = $report->results;
        
        // Filter by selected tests if provided in request
        if ($request->has('tests')) {
            $testIds = explode(',', $request->tests);
            $results = $results->whereIn('invoice_item_id', $testIds);
        }

        $groupedResults = $results->groupBy(function($result) {
            return $result->labTest->department_id ?? 0;
        })->map(function($deptGroup) {
            return [
                'department' => $deptGroup->first()->labTest->department ?? null,
                'tests' => $deptGroup->groupBy(function($result) {
                    return $result->labTest->name;
                })
            ];
        });

        $pdf = Pdf::loadView('pdf.report-' . $template, [
            'report' => $report,
            'invoice' => $report->invoice,
            'patient' => $report->invoice->patient,
            'profile' => $report->invoice->patient->patientProfile,
            'groupedResults' => $groupedResults,
            'settings' => $settings,
            'company' => auth()->user()->company
        ]);

        return $pdf->stream('Report_' . $report->invoice->patient->name . '_' . $report->invoice->invoice_number . '.pdf');
    }
}

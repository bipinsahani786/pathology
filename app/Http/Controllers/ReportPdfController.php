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
            'results.labTest'
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
            'authorized_signatory_name' => Configuration::getFor('authorized_signatory_name', 'Dr. Authorized Pathologist', $companyId),
            'authorized_signatory_designation' => Configuration::getFor('authorized_signatory_designation', 'Consultant Pathologist', $companyId),
            'signature_image' => Configuration::getFor('signature_image', null, $companyId),
        ];

        // Format data for view: Group by Department => Test Name => Results
        $groupedResults = $report->results->groupBy(function($result) {
            return $result->labTest->department ?? 'General';
        })->map(function($deptGroup) {
            return $deptGroup->groupBy(function($result) {
                return $result->labTest->name;
            });
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

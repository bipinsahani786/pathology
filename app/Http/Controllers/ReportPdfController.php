<?php

namespace App\Http\Controllers;

use App\Models\TestReport;
use App\Models\Configuration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function downloadOld(Request $request, $id, $template = 'modern')
    {
        $report = TestReport::with([
            'invoice.patient.patientProfile', 
            'invoice.collectionCenter', 
            'invoice.doctor',
            'results.labTest.dept'
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
                'department' => $deptGroup->first()->labTest->dept ?? null,
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

    public function download(Request $request, $id, $template = 'modern')
    {
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '512M');

        // ── Patient metadata ─────────────────────────────────────────────────
        $patient = [
            'name'            => 'Mr. RAJU MANDAL',
            'age'             => '32 Year',
            'gender'          => 'Male',
            'referred_by'     => 'MEDICAL ADV PK BARNWAL',
            'patient_id'      => '260224024',
            'report_id'       => 'RE7940',
            'collection_date' => '24/02/2026 02:22 PM',
            'report_date'     => '24/02/2026 03:25 PM',
        ];
 
        // ── CBC data ─────────────────────────────────────────────────────────
        // type: 'row' | 'subheader'
        // flag: 'H' (high, red) | 'L' (low, blue) | null (normal)
        // bold: true to render result in bold
        $cbc = [
            ['type' => 'row',  'name' => 'Haemoglobin',           'result' => '13.8',  'flag' => null, 'range' => '11.5 – 16.5', 'unit' => 'g/dl'],
            ['type' => 'subheader', 'label' => 'Total Count'],
            ['type' => 'row',  'name' => 'Leucocytes (WBC)',       'result' => '9500',  'flag' => null, 'range' => '4000 – 11000', 'unit' => '/cumm'],
            ['type' => 'row',  'name' => 'Erythrocytes (RBC)',     'result' => '5.37',  'flag' => null, 'range' => '3.8 – 5.8',   'unit' => 'milli./cumm'],
            ['type' => 'row',  'name' => 'Platelets',              'result' => '1.21',  'flag' => 'L',  'range' => '1.5 – 4.5',   'unit' => 'Lac/cumm', 'bold' => true],
            ['type' => 'subheader', 'label' => 'Differential Count of W.B.C'],
            ['type' => 'row',  'name' => 'Neutrophils',            'result' => '78',    'flag' => 'H',  'range' => '40 – 70',     'unit' => '%',  'bold' => true],
            ['type' => 'row',  'name' => 'Lymphocytes',            'result' => '16',    'flag' => 'L',  'range' => '20 – 40',     'unit' => '%',  'bold' => true],
            ['type' => 'row',  'name' => 'Monocytes',              'result' => '01',    'flag' => null, 'range' => '1 – 5',       'unit' => '%'],
            ['type' => 'row',  'name' => 'Eosinophils',            'result' => '05',    'flag' => null, 'range' => '1 – 6',       'unit' => '%'],
            ['type' => 'row',  'name' => 'Basophils',              'result' => '00',    'flag' => null, 'range' => '0 – 1',       'unit' => '%'],
            ['type' => 'subheader', 'label' => 'Absolute Count of WBCs'],
            ['type' => 'row',  'name' => 'Absolute Neutrophils',   'result' => '7410',  'flag' => null, 'range' => '4400 – 7700', 'unit' => ''],
            ['type' => 'row',  'name' => 'Absolute Lymphocytes',   'result' => '1520',  'flag' => 'L',  'range' => '2200 – 4950', 'unit' => '', 'bold' => true],
            ['type' => 'row',  'name' => 'Absolute Monocytes',     'result' => '95',    'flag' => 'L',  'range' => '220 – 550',   'unit' => '', 'bold' => true],
            ['type' => 'row',  'name' => 'Absolute Eosinophils',   'result' => '475',   'flag' => null, 'range' => '110 – 660',   'unit' => ''],
            ['type' => 'subheader', 'label' => 'Other Parameters'],
            ['type' => 'row',  'name' => 'HCT (PCV)',              'result' => '44.5',  'flag' => null, 'range' => '35 – 55',     'unit' => '%'],
            ['type' => 'row',  'name' => 'MCV',                    'result' => '82.87', 'flag' => null, 'range' => '76 – 100',    'unit' => 'fL'],
            ['type' => 'row',  'name' => 'MCH',                    'result' => '25.7',  'flag' => 'L',  'range' => '27 – 32',     'unit' => 'pg', 'bold' => true],
            ['type' => 'row',  'name' => 'MCHC',                   'result' => '31.01', 'flag' => null, 'range' => '30 – 35',     'unit' => 'g/dL'],
            ['type' => 'row',  'name' => 'RDW-CV',                 'result' => '13.9',  'flag' => null, 'range' => '11 – 16',     'unit' => '%'],
            ['type' => 'row',  'name' => 'MPV',                    'result' => '14.7',  'flag' => 'H',  'range' => '8.0 – 12.0',  'unit' => 'fl', 'bold' => true],


            
            // ['type' => 'row',  'name' => 'Absolute Lymphocytes',   'result' => '1520',  'flag' => 'L',  'range' => '2200 – 4950', 'unit' => '', 'bold' => true],
            // ['type' => 'row',  'name' => 'Absolute Monocytes',     'result' => '95',    'flag' => 'L',  'range' => '220 – 550',   'unit' => '', 'bold' => true],
            // ['type' => 'row',  'name' => 'Absolute Eosinophils',   'result' => '475',   'flag' => null, 'range' => '110 – 660',   'unit' => ''],
            // ['type' => 'subheader', 'label' => 'Other Parameters'],
            // ['type' => 'row',  'name' => 'HCT (PCV)',              'result' => '44.5',  'flag' => null, 'range' => '35 – 55',     'unit' => '%'],
            // ['type' => 'row',  'name' => 'MCV',                    'result' => '82.87', 'flag' => null, 'range' => '76 – 100',    'unit' => 'fL'],
            // ['type' => 'row',  'name' => 'MCH',                    'result' => '25.7',  'flag' => 'L',  'range' => '27 – 32',     'unit' => 'pg', 'bold' => true],
            // ['type' => 'row',  'name' => 'MCHC',                   'result' => '31.01', 'flag' => null, 'range' => '30 – 35',     'unit' => 'g/dL'],
            // ['type' => 'row',  'name' => 'RDW-CV',                 'result' => '13.9',  'flag' => null, 'range' => '11 – 16',     'unit' => '%'],
            // ['type' => 'row',  'name' => 'MPV',                    'result' => '14.7',  'flag' => 'H',  'range' => '8.0 – 12.0',  'unit' => 'fl', 'bold' => true],
        ];
 
        // ── Lipid Profile data ───────────────────────────────────────────────
        $lipid = [
            ['name' => 'Total Cholesterol',          'result' => '178.2', 'flag' => null, 'range' => '0 – 200',    'unit' => 'mg/dl'],
            ['name' => 'Triglycerides Level',         'result' => '142.7', 'flag' => null, 'range' => '<150',       'unit' => 'mg/dl'],
            ['name' => 'HDL Cholesterol',             'result' => '50.1',  'flag' => null, 'range' => '40 – 70',    'unit' => 'mg/dl'],
            ['name' => 'LDL Cholesterol',             'result' => '99.56', 'flag' => null, 'range' => '0 – 100',    'unit' => 'mg/dl'],
            ['name' => 'VLDL Cholesterol',            'result' => '28.54', 'flag' => null, 'range' => '6 – 38',     'unit' => 'mg/dl'],
            ['name' => 'LDL/HDL Ratio',               'result' => '1.99',  'flag' => 'L',  'range' => '2.5 – 3.5',  'unit' => '',  'bold' => true],
            ['name' => 'Total Cholesterol/HDL Ratio', 'result' => '3.56',  'flag' => null, 'range' => '3.5 – 5',    'unit' => ''],
        ];
 
        // ── Liver Function Test data ─────────────────────────────────────────
        $lft = [
            ['name' => 'Bilirubin Total',                    'result' => '0.79',  'flag' => null, 'range' => '0.1 – 1.2',  'unit' => 'mg/dL'],
            ['name' => 'Bilirubin Direct',                   'result' => '0.22',  'flag' => 'H',  'range' => '0.0 – 0.2',  'unit' => 'mg/dL', 'bold' => true],
            ['name' => 'Bilirubin Indirect',                 'result' => '0.57',  'flag' => null, 'range' => '0.1 – 1.0',  'unit' => 'mg/dL'],
            ['name' => 'SGPT',                               'result' => '37.3',  'flag' => 'H',  'range' => '7 – 33',     'unit' => 'IU/L',  'bold' => true],
            ['name' => 'SGOT',                               'result' => '31.7',  'flag' => null, 'range' => '8 – 33',     'unit' => 'IU/L'],
            ['name' => 'Alkaline Phosphatase',               'result' => '168.2', 'flag' => null, 'range' => '90 – 240',   'unit' => 'U/L'],
            ['name' => 'Gamma Glutamyl Transferase (GGT)',   'result' => '21.1',  'flag' => null, 'range' => '0 – 55',     'unit' => 'U/L'],
            ['name' => 'Total Proteins',                     'result' => '6.8',   'flag' => null, 'range' => '6.0 – 8.0',  'unit' => 'gm/dL'],
            ['name' => 'Albumin',                            'result' => '4.6',   'flag' => null, 'range' => '3.5 – 5.0',  'unit' => 'gm/dL'],
            ['name' => 'Globulin',                           'result' => '2.2',   'flag' => 'L',  'range' => '2.3 – 3.5',  'unit' => 'gm/dL', 'bold' => true],
            ['name' => 'A : G Ratio',                        'result' => '2.09',  'flag' => 'H',  'range' => '1.0 – 1.2',  'unit' => 'Ratio', 'bold' => true],
        ];
        // ── Generate PDF ─────────────────────────────────────────────────────
        $pdf = Pdf::loadView('pdf.report-new', compact('patient', 'cbc', 'lipid', 'lft'))
            ->setPaper('A4', 'portrait');
        $filename = 'lab_report_' . str_replace([' ', '.'], '_', $patient['name']) . '.pdf';
 
        return $pdf->stream($filename);
    }
}

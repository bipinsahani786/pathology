<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Company;
use App\Models\Department;
use App\Models\LabTest;
use App\Models\Invoice;
use App\Models\CollectionCenter;
use App\Models\PatientProfile;
use App\Models\ReportResult;
use App\Models\TestReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class ReportContinuousPageBreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_continuous_report_keeps_tests_intact_and_groups_small_tests_together()
    {
        $company = Company::create([
            'name' => 'Break Test Lab',
            'email' => 'break@example.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
        ]);
        $user = User::factory()->create(['company_id' => $company->id]);
        $patient = User::create([
            'company_id' => $company->id,
            'name' => 'Ramesh Kumar',
            'email' => 'ramesh@example.com',
            'password' => bcrypt('password'),
        ]);
        $profile = PatientProfile::create([
            'user_id' => $patient->id,
            'company_id' => $company->id,
            'patient_id_string' => 'PAT-9999',
            'gender' => 'Male',
            'age' => 45,
            'age_type' => 'Years',
        ]);
        $dept = Department::create([
            'name' => 'Biochemistry',
            'is_system' => true,
            'is_active' => true,
        ]);
        $center = CollectionCenter::create([
            'company_id' => $company->id,
            'name' => 'Main Lab',
            'is_main_lab' => true,
            'is_active' => true,
        ]);
        $invoice = Invoice::create([
            'company_id' => $company->id,
            'collection_center_id' => $center->id,
            'patient_id' => $patient->id,
            'created_by' => $user->id,
            'invoice_number' => 'INV-BRK-001',
            'invoice_date' => now(),
            'subtotal' => 1000,
            'total_amount' => 1000,
            'paid_amount' => 1000,
            'payment_status' => 'Paid',
            'status' => 'Completed',
        ]);
        $testReport = TestReport::create([
            'company_id' => $company->id,
            'invoice_id' => $invoice->id,
            'patient_id' => $patient->id,
            'status' => 'Approved',
            'approved_at' => now(),
        ]);

        // Scenario:
        // Test 1: Blood Glucose Fasting (1 param)
        // Test 2: Blood Glucose PP (1 param)
        // Test 3: HbA1c (2 params)
        // Test 4: Lipid Profile (7 params)
        // Test 5: Liver Function Test (11 params)
        $tests = [];
        $scenario = [
            'Blood Glucose Fasting' => 1,
            'Blood Glucose PP' => 1,
            'HbA1c' => 2,
            'Lipid Profile' => 7,
            'Liver Function Test' => 11,
        ];
        foreach ($scenario as $tName => $pCount) {
            $params = [];
            for ($i = 1; $i <= $pCount; $i++) {
                $params[] = [
                    'name' => "Param $i",
                    'unit' => 'mg/dL',
                    'ref_range' => '10 - 50',
                ];
            }
            $lt = LabTest::create([
                'company_id' => $company->id,
                'department_id' => $dept->id,
                'name' => $tName,
                'test_code' => strtoupper(str_replace(' ', '_', $tName)),
                'parameters' => $params,
            ]);

            $results = collect();
            for ($i = 1; $i <= $pCount; $i++) {
                $results->push(new ReportResult([
                    'test_report_id' => $testReport->id,
                    'lab_test_id' => $lt->id,
                    'parameter_name' => "Param $i",
                    'result_value' => '25',
                    'reference_range' => '10 - 50',
                    'unit' => 'mg/dL',
                    'status' => 'Normal',
                    'is_highlighted' => false,
                ]));
            }

            $tests[$lt->id] = [
                'name' => $lt->name,
                'labTest' => $lt,
                'results' => $results,
                'remark' => '',
            ];
        }

        $groupedResults = [
            $dept->id => [
                'department' => $dept,
                'tests' => $tests,
            ],
        ];

        $settings = [
            'report_page_break_style' => 'continuous',
            'pdf_margin_top' => 200,
            'pdf_margin_bottom' => 150,
            'pdf_margin_left' => 25,
            'pdf_margin_right' => 25,
            'pdf_header_height' => 150,
            'pdf_footer_height' => 120,
            'pdf_font_size' => 13,
            'pdf_font_family' => 'Helvetica',
            'pdf_show_test_method' => false,
            'pdf_show_header' => false,
            'pdf_show_footer' => false,
            'pdf_show_signatures' => false,
            'report_signature_mode' => 'global_bottom',
            'sig_1_enabled' => false,
            'sig_2_enabled' => false,
            'sig_3_enabled' => false,
        ];

        $html = View::make('pdf.report-new', [
            'invoice' => $invoice,
            'patient' => $patient,
            'profile' => $profile,
            'report' => $testReport,
            'groupedResults' => $groupedResults,
            'settings' => $settings,
            'showHeader' => false,
            'showFooter' => false,
            'company' => $company,
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $canvas = $pdf->getDomPDF()->getCanvas();
        $pdfContent = $pdf->output();

        $tmpPdf = tempnam(sys_get_temp_dir(), 'test_pdf_') . '.pdf';
        file_put_contents($tmpPdf, $pdfContent);

        $page1Text = shell_exec("pdftotext -f 1 -l 1 " . escapeshellarg($tmpPdf) . " -");
        $page2Text = shell_exec("pdftotext -f 2 -l 2 " . escapeshellarg($tmpPdf) . " -");
        @unlink($tmpPdf);

        // Page 1 should hold the small tests together
        $this->assertStringContainsString('BLOOD GLUCOSE FASTING', $page1Text);
        $this->assertStringContainsString('BLOOD GLUCOSE PP', $page1Text);
        $this->assertStringContainsString('HBA1C', $page1Text);
        $this->assertStringContainsString('LIPID PROFILE', $page1Text);

        // Liver Function Test does not fit in page 1 leftover space, so it must NOT split onto page 1
        $this->assertStringNotContainsString('LIVER FUNCTION TEST', $page1Text);
        // Liver Function Test starts cleanly on Page 2
        $this->assertStringContainsString('LIVER FUNCTION TEST', $page2Text);
    }
}

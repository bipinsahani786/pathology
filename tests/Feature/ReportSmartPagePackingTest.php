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
use App\Services\ReportLayoutOptimizer;

class ReportSmartPagePackingTest extends TestCase
{
    use RefreshDatabase;

    private function createReportScenario()
    {
        $company = Company::create([
            'name' => 'Smart Packing Test Lab',
            'email' => 'smart@example.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
        ]);
        $user = User::factory()->create(['company_id' => $company->id]);
        $patient = User::create([
            'company_id' => $company->id,
            'name' => 'Sunil Verma',
            'email' => 'sunil@example.com',
            'password' => bcrypt('password'),
        ]);
        $profile = PatientProfile::create([
            'user_id' => $patient->id,
            'company_id' => $company->id,
            'patient_id_string' => 'PAT-8888',
            'gender' => 'Male',
            'age' => 50,
            'age_type' => 'Years',
        ]);
        $deptBio = Department::create([
            'name' => 'Biochemistry',
            'is_system' => true,
            'is_active' => true,
        ]);
        $deptHem = Department::create([
            'name' => 'Hematology',
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
            'invoice_number' => 'INV-SMT-001',
            'invoice_date' => now(),
            'subtotal' => 2000,
            'total_amount' => 2000,
            'paid_amount' => 2000,
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
        // Test 1: Blood Glucose Fasting (1 param, Bio) - Small
        // Test 2: Liver Function Test (11 params, Bio) - Large
        // Test 3: Complete Blood Count (18 params, Hem) - Large
        // Test 4: Blood Glucose PP (1 param, Bio) - Small
        $scenario = [
            'Blood Glucose Fasting' => ['count' => 1, 'dept' => $deptBio],
            'Liver Function Test' => ['count' => 11, 'dept' => $deptBio],
            'Complete Blood Count' => ['count' => 18, 'dept' => $deptHem],
            'Blood Glucose PP' => ['count' => 1, 'dept' => $deptBio],
        ];

        $tests = [];
        foreach ($scenario as $tName => $info) {
            $pCount = $info['count'];
            $dept = $info['dept'];
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
                'dept' => $dept,
            ];
        }

        $groupedResults = collect([
            'group_1' => [
                'department' => $deptBio,
                'tests' => collect($tests),
            ],
        ]);

        return compact('company', 'patient', 'profile', 'testReport', 'invoice', 'groupedResults');
    }

    public function test_smart_packing_combines_non_adjacent_small_tests_on_same_page()
    {
        $data = $this->createReportScenario();

        $settings = [
            'report_page_break_style' => 'continuous',
            'report_smart_page_packing' => true,
            'pdf_margin_top' => 320,
            'pdf_margin_bottom' => 280,
            'pdf_margin_left' => 25,
            'pdf_margin_right' => 25,
            'pdf_header_height' => 200,
            'pdf_footer_height' => 180,
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

        // Optimize grouped results
        $optimizedGroupedResults = ReportLayoutOptimizer::optimizeGroupedResults($data['groupedResults'], $settings);

        $html = View::make('pdf.report-new', [
            'invoice' => $data['invoice'],
            'patient' => $data['patient'],
            'profile' => $data['profile'],
            'report' => $data['testReport'],
            'groupedResults' => $optimizedGroupedResults,
            'settings' => $settings,
            'showHeader' => false,
            'showFooter' => false,
            'company' => $data['company'],
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $canvas = $pdf->getDomPDF()->getCanvas();
        $pdfContent = $pdf->output();

        $tmpPdf = tempnam(sys_get_temp_dir(), 'test_smart_') . '.pdf';
        file_put_contents($tmpPdf, $pdfContent);

        $page1Text = shell_exec("pdftotext -f 1 -l 1 " . escapeshellarg($tmpPdf) . " -");
        $page2Text = shell_exec("pdftotext -f 2 -l 2 " . escapeshellarg($tmpPdf) . " -");
        $pageCount = $canvas->get_page_count();
        for ($p = 1; $p <= $pageCount; $p++) {
            $txt = shell_exec("pdftotext -f $p -l $p " . escapeshellarg($tmpPdf) . " -");
            echo "\n--- SMART PAGE $p ---\n" . trim($txt) . "\n";
        }
        @unlink($tmpPdf);

        // Test 1 (Glucose Fasting) AND Test 4 (Glucose PP) must be combined on Page 1!
        $this->assertStringContainsString('BLOOD GLUCOSE FASTING', $page1Text);
        $this->assertStringContainsString('BLOOD GLUCOSE PP', $page1Text);

        // Liver Function Test & Complete Blood Count should be on subsequent pages
        $this->assertStringNotContainsString('LIVER FUNCTION TEST', $page1Text);
        $this->assertStringNotContainsString('COMPLETE BLOOD COUNT', $page1Text);
    }

    public function test_default_sequential_order_is_preserved_when_smart_packing_is_disabled()
    {
        $data = $this->createReportScenario();

        $settings = [
            'report_page_break_style' => 'continuous',
            'report_smart_page_packing' => false,
            'pdf_margin_top' => 320,
            'pdf_margin_bottom' => 280,
            'pdf_margin_left' => 25,
            'pdf_margin_right' => 25,
            'pdf_header_height' => 200,
            'pdf_footer_height' => 180,
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

        // Should return original grouped results unchanged
        $groupedResults = ReportLayoutOptimizer::optimizeGroupedResults($data['groupedResults'], $settings);
        $this->assertSame($data['groupedResults'], $groupedResults);

        $html = View::make('pdf.report-new', [
            'invoice' => $data['invoice'],
            'patient' => $data['patient'],
            'profile' => $data['profile'],
            'report' => $data['testReport'],
            'groupedResults' => $groupedResults,
            'settings' => $settings,
            'showHeader' => false,
            'showFooter' => false,
            'company' => $data['company'],
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $canvas = $pdf->getDomPDF()->getCanvas();
        $pdfContent = $pdf->output();

        $tmpPdf = tempnam(sys_get_temp_dir(), 'test_seq_') . '.pdf';
        file_put_contents($tmpPdf, $pdfContent);

        $page1Text = shell_exec("pdftotext -f 1 -l 1 " . escapeshellarg($tmpPdf) . " -");
        $pageCount = $canvas->get_page_count();
        @unlink($tmpPdf);

        // In sequential order, Test 4 (Glucose PP) is at the very end, so it is NOT on Page 1
        $this->assertStringContainsString('BLOOD GLUCOSE FASTING', $page1Text);
        $this->assertStringNotContainsString('BLOOD GLUCOSE PP', $page1Text);
        $this->assertEquals(4, $pageCount);
    }
}

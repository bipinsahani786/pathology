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

class ReportEndOfReportSettingTest extends TestCase
{
    use RefreshDatabase;

    private function createReportData()
    {
        $company = Company::create([
            'name' => 'End Report Test Lab',
            'email' => 'end@example.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
        ]);
        $user = User::factory()->create(['company_id' => $company->id]);
        $patient = User::create([
            'company_id' => $company->id,
            'name' => 'Aakash Sharma',
            'email' => 'aakash@example.com',
            'password' => bcrypt('password'),
        ]);
        $profile = PatientProfile::create([
            'user_id' => $patient->id,
            'company_id' => $company->id,
            'patient_id_string' => 'PAT-7777',
            'gender' => 'Male',
            'age' => 35,
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
            'invoice_number' => 'INV-EOR-001',
            'invoice_date' => now(),
            'subtotal' => 500,
            'total_amount' => 500,
            'paid_amount' => 500,
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

        $lt = LabTest::create([
            'company_id' => $company->id,
            'department_id' => $dept->id,
            'name' => 'Blood Glucose Fasting',
            'test_code' => 'FBS',
            'parameters' => [
                ['name' => 'Fasting Blood Sugar', 'unit' => 'mg/dL', 'ref_range' => '70 - 100']
            ],
        ]);

        $results = collect([
            new ReportResult([
                'test_report_id' => $testReport->id,
                'lab_test_id' => $lt->id,
                'parameter_name' => 'Fasting Blood Sugar',
                'result_value' => '85',
                'reference_range' => '70 - 100',
                'unit' => 'mg/dL',
                'status' => 'Normal',
                'is_highlighted' => false,
            ])
        ]);

        $groupedResults = collect([
            'group_1' => [
                'department' => $dept,
                'tests' => collect([
                    $lt->id => [
                        'name' => $lt->name,
                        'labTest' => $lt,
                        'results' => $results,
                        'remark' => '',
                        'dept' => $dept,
                    ]
                ]),
            ],
        ]);

        return compact('company', 'patient', 'profile', 'testReport', 'invoice', 'groupedResults');
    }

    public function test_end_of_report_is_shown_when_setting_is_enabled()
    {
        $data = $this->createReportData();

        $settings = [
            'report_show_end_of_report' => true,
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

        // 1. Test report-new
        $htmlNew = View::make('pdf.report-new', [
            'invoice' => $data['invoice'],
            'patient' => $data['patient'],
            'profile' => $data['profile'],
            'report' => $data['testReport'],
            'groupedResults' => $data['groupedResults'],
            'settings' => $settings,
            'showHeader' => false,
            'showFooter' => false,
            'company' => $data['company'],
        ])->render();

        $this->assertStringContainsString('*** End of Report ***', $htmlNew);

        // 2. Test report-modern
        $htmlModern = View::make('pdf.report-modern', [
            'invoice' => $data['invoice'],
            'patient' => $data['patient'],
            'profile' => $data['profile'],
            'report' => $data['testReport'],
            'groupedResults' => $data['groupedResults'],
            'settings' => $settings,
            'showHeader' => false,
            'showFooter' => false,
            'company' => $data['company'],
        ])->render();

        $this->assertStringContainsString('*** End Of Report ***', $htmlModern);
    }

    public function test_end_of_report_is_hidden_when_setting_is_disabled()
    {
        $data = $this->createReportData();

        $settings = [
            'report_show_end_of_report' => false,
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

        // 1. Test report-new
        $htmlNew = View::make('pdf.report-new', [
            'invoice' => $data['invoice'],
            'patient' => $data['patient'],
            'profile' => $data['profile'],
            'report' => $data['testReport'],
            'groupedResults' => $data['groupedResults'],
            'settings' => $settings,
            'showHeader' => false,
            'showFooter' => false,
            'company' => $data['company'],
        ])->render();

        $this->assertStringNotContainsString('*** End of Report ***', $htmlNew);

        // 2. Test report-modern
        $htmlModern = View::make('pdf.report-modern', [
            'invoice' => $data['invoice'],
            'patient' => $data['patient'],
            'profile' => $data['profile'],
            'report' => $data['testReport'],
            'groupedResults' => $data['groupedResults'],
            'settings' => $settings,
            'showHeader' => false,
            'showFooter' => false,
            'company' => $data['company'],
        ])->render();

        $this->assertStringNotContainsString('*** End Of Report ***', $htmlModern);
    }
}

<?php

use App\Models\Department;
use App\Models\LabTest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PatientProfile;
use App\Models\ReportResult;
use App\Models\TestReport;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use App\Livewire\Lab\ResultEntryManager;
use Illuminate\Support\Facades\View;

test('can enter and save culture sensitivity results', function () {
    // 1. Setup Roles & Permissions
    $role = Role::firstOrCreate(['name' => 'lab_admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'view reports', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'edit reports', 'guard_name' => 'web']);
    $role->givePermissionTo(['view reports', 'edit reports']);

    // 2. Setup Company & User
    $company = Company::create([
        'name' => 'Microbiology Test Lab',
        'email' => 'micro@example.com',
        'phone' => '1234567890',
        'address' => 'Test Address',
    ]);
    
    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);
    $user->assignRole($role);
    $this->actingAs($user);

    // 3. Setup Patient & Department
    $patient = User::create([
        'company_id' => $company->id,
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $profile = PatientProfile::create([
        'user_id' => $patient->id,
        'company_id' => $company->id,
        'patient_id_string' => 'PAT-1001',
        'gender' => 'Male',
        'age' => 30,
        'age_type' => 'Years',
    ]);

    $department = Department::create([
        'name' => 'Microbiology',
        'is_system' => true,
        'is_active' => true,
    ]);

    // 4. Create Microbiology C&S Lab Test
    $labTest = LabTest::create([
        'company_id' => $company->id,
        'department_id' => $department->id,
        'name' => 'Urine Culture & Sensitivity',
        'test_code' => 'UCS-01',
        'mrp' => 500,
        'parameters' => [
            [
                'name' => 'Urine Culture',
                'short_code' => 'UC',
                'input_type' => 'culture_sensitivity',
                'method' => 'Aerobic Incubation',
                'unit' => '',
                'ranges' => [],
            ]
        ],
        'is_active' => true,
    ]);

    // 5. Create CollectionCenter & Invoice & InvoiceItem
    $center = \App\Models\CollectionCenter::create([
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
        'invoice_number' => 'INV-CS-001',
        'invoice_date' => now(),
        'subtotal' => 500,
        'discount_amount' => 0,
        'total_amount' => 500,
        'paid_amount' => 500,
        'due_amount' => 0,
        'payment_status' => 'Paid',
    ]);

    $invoiceItem = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'lab_test_id' => $labTest->id,
        'test_name' => $labTest->name,
        'mrp' => 500,
        'price' => 500,
        'status' => 'Pending',
    ]);

    // 6. Test ResultEntryManager Livewire component
    $paramKey = $invoiceItem->id . '_' . $labTest->id . '_' . md5('Urine Culture');

    $livewireTest = Livewire::test(ResultEntryManager::class, ['id' => $invoice->id])
        ->assertOk();

    // Verify cultureResult state is initialized
    $cultureResultsState = $livewireTest->get('cultureResults');
    expect($cultureResultsState)->toHaveKey($paramKey);
    expect($cultureResultsState[$paramKey]['growth_status'])->toBe('Growth');

    $livewireTest->call('addAntibioticRow', $paramKey);
    $cultureResultsState = $livewireTest->get('cultureResults');
    expect(count($cultureResultsState[$paramKey]['antibiotics']))->toBeGreaterThanOrEqual(1);

    // Set organism, colony count, and antibiotic details
    $livewireTest->set("cultureResults.{$paramKey}.organism_name", 'Escherichia coli')
        ->set("cultureResults.{$paramKey}.colony_count", '10^5 CFU/mL')
        ->set("cultureResults.{$paramKey}.antibiotics.0.name", 'Amikacin')
        ->set("cultureResults.{$paramKey}.antibiotics.0.sensitivity", 'S')
        ->set("cultureResults.{$paramKey}.antibiotics.0.mic", '<= 2');

    // Save as Draft
    $livewireTest->call('saveReport', 'Draft')
        ->assertHasNoErrors();

    // Check database result
    $result = ReportResult::where('invoice_item_id', $invoiceItem->id)
        ->where('lab_test_id', $labTest->id)
        ->first();

    expect($result)->not->toBeNull();
    expect($result->result_value)->toBe('Escherichia coli (10^5 CFU/mL)');
    expect($result->culture_data)->toBeArray();
    expect($result->culture_data['organism_name'])->toBe('Escherichia coli');
    expect($result->culture_data['colony_count'])->toBe('10^5 CFU/mL');
    expect(count($result->culture_data['antibiotics']))->toBeGreaterThanOrEqual(1);
    expect($result->culture_data['antibiotics'][0]['name'])->toBe('Amikacin');
    expect($result->culture_data['antibiotics'][0]['sensitivity'])->toBe('S');
    expect($result->culture_data['antibiotics'][0]['mic'])->toBe('<= 2');

    // Test PDF rendering with the saved report
    $testReport = TestReport::where('invoice_id', $invoice->id)->first();
    expect($testReport)->not->toBeNull();

    // Setup mock settings
    $settings = [
        'pdf_show_watermark' => false,
        'pdf_show_header' => false,
        'pdf_show_footer' => false,
        'pdf_show_signatures' => false,
        'report_signature_mode' => 'global_bottom',
        'sig_1_enabled' => false,
        'sig_2_enabled' => false,
        'sig_3_enabled' => false,
        'global_sig_1_name' => '',
        'global_sig_1_desig' => '',
        'global_sig_1_path' => null,
        'global_sig_2_name' => '',
        'global_sig_2_desig' => '',
        'global_sig_2_path' => null,
        'global_sig_3_name' => '',
        'global_sig_3_desig' => '',
        'global_sig_3_path' => null,
        'pdf_margin_top' => 10,
        'pdf_margin_bottom' => 10,
        'pdf_header_height' => 10,
        'pdf_footer_height' => 10,
        'pdf_font_size' => 13,
        'pdf_font_family' => 'Helvetica',
        'pdf_show_test_method' => true,
    ];

    $groupedResults = [
        $department->id => [
            'department' => $department,
            'tests' => [
                $labTest->id => [
                    'name' => $labTest->name,
                    'labTest' => $labTest,
                    'results' => collect([$result]),
                    'remark' => '',
                ]
            ]
        ]
    ];

    $htmlNew = View::make('pdf.report-new', [
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

    expect($htmlNew)->toContain('Escherichia coli');
    expect($htmlNew)->toContain('Amikacin');
    expect($htmlNew)->toContain('Sensitive');
    expect($htmlNew)->toContain('&lt;= 2');

    $htmlModern = View::make('pdf.report-modern', [
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

    expect($htmlModern)->toContain('Escherichia coli');
    expect($htmlModern)->toContain('Amikacin');
    expect($htmlModern)->toContain('Sensitive');
    expect($htmlModern)->toContain('&lt;= 2');
});

test('renders culture sensitivity in 2-column left-right box layout', function () {
    $company = Company::create([
        'name' => 'Microbiology Test Lab 2',
        'email' => 'micro2@example.com',
        'phone' => '1234567890',
        'address' => 'Test Address',
    ]);
    $patient = User::create([
        'company_id' => $company->id,
        'name' => 'Sunita Devi',
        'email' => 'sunita@example.com',
        'password' => bcrypt('password'),
    ]);
    $center = \App\Models\CollectionCenter::create([
        'company_id' => $company->id,
        'name' => 'Main Lab',
        'is_main_lab' => true,
        'is_active' => true,
    ]);
    $invoice = Invoice::create([
        'company_id' => $company->id,
        'collection_center_id' => $center->id,
        'patient_id' => $patient->id,
        'created_by' => $patient->id,
        'invoice_number' => 'INV-2609-0015',
        'invoice_date' => now(),
        'subtotal' => 500,
        'discount_amount' => 0,
        'total_amount' => 500,
        'paid_amount' => 500,
        'due_amount' => 0,
        'payment_status' => 'Paid',
    ]);

    $department = Department::firstOrCreate(['name' => 'Microbiology']);
    $labTest = LabTest::create([
        'company_id' => $company->id,
        'department_id' => $department->id,
        'name' => 'Urine Culture & Sensitivity',
        'test_code' => 'UC_TEST',
        'sample_type' => 'Urine',
        'mrp' => 500,
        'b2b_price' => 300,
        'tat_hours' => 48,
    ]);

    $antibiotics = [
        ['name' => 'Amikacin', 'sensitivity' => 'S', 'mic' => '<= 2'],
        ['name' => 'Ampicillin', 'sensitivity' => 'R', 'mic' => '>= 32'],
        ['name' => 'Azithromycin', 'sensitivity' => 'S', 'mic' => '<= 1'],
        ['name' => 'Ceftriaxone', 'sensitivity' => 'S', 'mic' => '<= 1'],
        ['name' => 'Ciprofloxacin', 'sensitivity' => 'R', 'mic' => '>= 4'],
        ['name' => 'Levofloxacin', 'sensitivity' => 'S', 'mic' => '<= 2'],
    ];

    $result = new ReportResult([
        'parameter_name' => 'Culture & Sensitivity',
        'result_value' => 'Escherichia coli',
        'culture_data' => [
            'growth_status' => 'Growth',
            'organism_name' => 'Escherichia coli',
            'colony_count' => '10^5 CFU/mL',
            'antibiotics' => $antibiotics,
        ],
    ]);

    $groupedResults = collect([
        $department->id => [
            'department' => $department,
            'tests' => [
                $labTest->id => [
                    'name' => $labTest->name,
                    'labTest' => $labTest,
                    'results' => collect([$result]),
                    'remark' => '',
                ]
            ]
        ]
    ]);

    $html = View::make('pdf.report-new', [
        'invoice' => $invoice,
        'patient' => $patient,
        'profile' => null,
        'report' => new TestReport(['invoice_id' => $invoice->id]),
        'groupedResults' => $groupedResults,
        'settings' => [],
        'showHeader' => false,
        'showFooter' => false,
        'company' => $company,
    ])->render();

    expect($html)->toContain('antibiotic-box-table');
    expect($html)->toContain('Amikacin');
    expect($html)->toContain('Ciprofloxacin');
    expect($html)->toContain('Significant Growth Isolated');
    expect($html)->toContain('Escherichia coli');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-new', [
        'invoice' => $invoice,
        'patient' => $patient,
        'profile' => null,
        'report' => new TestReport(['invoice_id' => $invoice->id]),
        'groupedResults' => $groupedResults,
        'settings' => [
            'pdf_show_watermark' => false,
            'pdf_show_header' => false,
            'pdf_show_footer' => false,
            'pdf_show_signatures' => false,
        ],
        'showHeader' => false,
        'showFooter' => false,
        'company' => $company,
    ])->setPaper('A4', 'portrait');

    $pdf->render();
    $pageCount = $pdf->getDomPDF()->getCanvas()->get_page_count();
    expect($pageCount)->toBe(1);
});

test('standard pathology tests continue to render standard 5-column table unaffected', function () {
    $company = Company::create([
        'name' => 'Hematology Test Lab',
        'email' => 'hem@example.com',
        'phone' => '1234567890',
        'address' => 'Test Address',
    ]);
    $patient = User::create([
        'company_id' => $company->id,
        'name' => 'Ramesh Kumar',
        'email' => 'ramesh@example.com',
        'password' => bcrypt('password'),
    ]);
    $center = \App\Models\CollectionCenter::create([
        'company_id' => $company->id,
        'name' => 'Main Lab',
        'is_main_lab' => true,
        'is_active' => true,
    ]);
    $invoice = Invoice::create([
        'company_id' => $company->id,
        'collection_center_id' => $center->id,
        'patient_id' => $patient->id,
        'created_by' => $patient->id,
        'invoice_number' => 'INV-2609-0099',
        'invoice_date' => now(),
        'subtotal' => 300,
        'discount_amount' => 0,
        'total_amount' => 300,
        'paid_amount' => 300,
        'due_amount' => 0,
        'payment_status' => 'Paid',
    ]);

    $department = Department::firstOrCreate(['name' => 'Hematology']);
    $labTest = LabTest::create([
        'company_id' => $company->id,
        'department_id' => $department->id,
        'name' => 'Complete Blood Count (CBC)',
        'test_code' => 'CBC_TEST',
        'sample_type' => 'EDTA Whole Blood',
        'is_culture_sensitivity' => false,
        'mrp' => 300,
        'b2b_price' => 200,
        'tat_hours' => 6,
    ]);

    $results = collect([
        new ReportResult([
            'parameter_name' => 'Hemoglobin',
            'result_value' => '14.5',
            'reference_range' => '13.0 - 17.0',
            'unit' => 'g/dL',
            'is_highlighted' => false,
        ]),
        new ReportResult([
            'parameter_name' => 'Total Leukocyte Count (TLC)',
            'result_value' => '7500',
            'reference_range' => '4000 - 11000',
            'unit' => '/cumm',
            'is_highlighted' => false,
        ]),
    ]);

    $groupedResults = collect([
        $department->id => [
            'department' => $department,
            'tests' => [
                $labTest->id => [
                    'name' => $labTest->name,
                    'labTest' => $labTest,
                    'results' => $results,
                    'remark' => '',
                ]
            ]
        ]
    ]);

    $testSettings = [
        'pdf_show_header' => true,
        'pdf_show_footer' => true,
        'pdf_show_signatures' => false,
        'pdf_show_watermark' => false,
        'pdf_header_image' => null,
        'pdf_footer_image' => null,
    ];

    // Test report-new
    $htmlNew = View::make('pdf.report-new', [
        'invoice' => $invoice,
        'patient' => $patient,
        'profile' => null,
        'report' => new TestReport(['invoice_id' => $invoice->id]),
        'groupedResults' => $groupedResults,
        'settings' => $testSettings,
        'showHeader' => false,
        'showFooter' => false,
        'company' => $company,
    ])->render();

    expect($htmlNew)->toContain('Test Description');
    expect($htmlNew)->toContain('Ref. Range');
    expect($htmlNew)->toContain('HEMOGLOBIN');
    expect($htmlNew)->toContain('14.5');
    expect($htmlNew)->toContain('TOTAL LEUKOCYTE COUNT (TLC)');
    expect($htmlNew)->not->toContain('antibiotic-box-table');

    // Test report-modern
    $htmlModern = View::make('pdf.report-modern', [
        'invoice' => $invoice,
        'patient' => $patient,
        'profile' => null,
        'report' => new TestReport(['invoice_id' => $invoice->id]),
        'groupedResults' => $groupedResults,
        'settings' => $testSettings,
        'showHeader' => false,
        'showFooter' => false,
        'company' => $company,
    ])->render();

    expect($htmlModern)->toContain('Investigation');
    expect($htmlModern)->toContain('Reference Value');
    expect($htmlModern)->toContain('Hemoglobin');
    expect($htmlModern)->toContain('14.5');
    expect($htmlModern)->not->toContain('antibiotic-box-table');
});


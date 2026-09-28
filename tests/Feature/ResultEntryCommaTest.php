<?php

use App\Livewire\Lab\ResultEntryManager;
use App\Models\Company;
use App\Models\Department;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabTest;
use App\Models\PatientProfile;
use App\Models\TestReport;
use App\Models\User;
use Livewire\Livewire;

test('comma-separated values like 4,001 are evaluated numerically without false Low flag', function () {
    $company = Company::create([
        'name' => 'Hematology Lab',
        'email' => 'hem@example.com',
        'phone' => '1234567890',
        'address' => 'Test Address',
    ]);

    $patient = User::create([
        'company_id' => $company->id,
        'name' => 'Amit Sharma',
        'email' => 'amit@example.com',
        'password' => bcrypt('password'),
    ]);

    $profile = PatientProfile::create([
        'company_id' => $company->id,
        'user_id' => $patient->id,
        'patient_id_string' => 'PAT-101',
        'gender' => 'Male',
        'age' => 30,
        'age_type' => 'Years',
    ]);

    $department = Department::firstOrCreate(['name' => 'Hematology']);

    $labTest = LabTest::create([
        'company_id' => $company->id,
        'department_id' => $department->id,
        'name' => 'Complete Blood Count',
        'test_code' => 'CBC',
        'sample_type' => 'EDTA Whole Blood',
        'parameters' => [
            [
                'name' => 'Total Leukocyte Count (TLC)',
                'short_code' => 'TLC',
                'unit' => 'cells/cumm',
                'input_type' => 'numeric',
                'ranges' => [
                    [
                        'gender' => 'Both',
                        'age_min' => 0,
                        'age_max' => 120,
                        'age_unit' => 'Years',
                        'min_val' => '4000',
                        'max_val' => '11000',
                        'display_range' => '4000 - 11000',
                    ]
                ]
            ],
            [
                'name' => 'Platelet Count',
                'short_code' => 'PLT',
                'unit' => 'cells/cumm',
                'input_type' => 'numeric',
                'ranges' => [
                    [
                        'gender' => 'Both',
                        'age_min' => 0,
                        'age_max' => 120,
                        'age_unit' => 'Years',
                        'min_val' => '150000',
                        'max_val' => '450000',
                        'display_range' => '150000 - 450000',
                    ]
                ]
            ]
        ],
        'mrp' => 300,
        'b2b_price' => 200,
        'tat_hours' => 6,
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
        'invoice_number' => 'INV-TEST-001',
        'invoice_date' => now(),
        'subtotal' => 300,
        'discount_amount' => 0,
        'total_amount' => 300,
        'paid_amount' => 300,
        'due_amount' => 0,
        'payment_status' => 'Paid',
    ]);

    $item = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'lab_test_id' => $labTest->id,
        'test_name' => $labTest->name,
        'mrp' => 300,
        'price' => 300,
        'status' => 'Pending',
    ]);

    $report = TestReport::create([
        'invoice_id' => $invoice->id,
        'patient_id' => $patient->id,
        'company_id' => $company->id,
        'status' => 'Pending',
    ]);

    $tlcKey = $item->id.'_'.$labTest->id.'_'.md5('Total Leukocyte Count (TLC)');
    $pltKey = $item->id.'_'.$labTest->id.'_'.md5('Platelet Count');

    $staff = User::create([
        'company_id' => $company->id,
        'name' => 'Lab Admin User',
        'email' => 'admin@lab.com',
        'password' => bcrypt('password'),
    ]);
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'lab_admin', 'guard_name' => 'web']);
    $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create reports', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    $staff->assignRole($role);
    $staff->givePermissionTo($permission);

    // Test with Livewire
    Livewire::actingAs($staff)
        ->test(ResultEntryManager::class, ['id' => $invoice->id])
        // 1. Enter 4,001 (Between 4000 and 11000) -> MUST BE NORMAL (not Low)
        ->set("results.{$tlcKey}", '4,001')
        ->assertSet("flags.{$tlcKey}", '')
        ->assertSet("highlights.{$tlcKey}", false)

        // 2. Enter 3,999 (Less than 4000) -> MUST BE LOW
        ->set("results.{$tlcKey}", '3,999')
        ->assertSet("flags.{$tlcKey}", 'L')
        ->assertSet("highlights.{$tlcKey}", true)

        // 3. Enter 11,500 (Greater than 11000) -> MUST BE HIGH
        ->set("results.{$tlcKey}", '11,500')
        ->assertSet("flags.{$tlcKey}", 'H')
        ->assertSet("highlights.{$tlcKey}", true)

        // 4. Enter Platelets with Indian format 1,50,000 -> MUST BE NORMAL
        ->set("results.{$pltKey}", '1,50,000')
        ->assertSet("flags.{$pltKey}", '')
        ->assertSet("highlights.{$pltKey}", false)

        // 5. Enter Platelets 80,000 -> MUST BE LOW
        ->set("results.{$pltKey}", '80,000')
        ->assertSet("flags.{$pltKey}", 'L')
        ->assertSet("highlights.{$pltKey}", true);
});

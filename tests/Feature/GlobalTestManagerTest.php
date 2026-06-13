<?php

use App\Models\Department;
use App\Models\GlobalTest;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use App\Livewire\Admin\GlobalTestManager;
use App\Livewire\Admin\LabManager;

test('super admin can toggle a global test default state', function () {
    $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);
    
    $department = Department::create([
        'name' => 'Biochemistry',
        'is_system' => true,
        'is_active' => true,
    ]);

    $globalTest = GlobalTest::create([
        'test_code' => 'T123',
        'name' => 'Test 123',
        'department_id' => $department->id,
        'is_default' => false,
    ]);
    
    $this->actingAs($user);
    
    Livewire::test(GlobalTestManager::class)
        ->call('toggleDefault', $globalTest->id)
        ->assertHasNoErrors();
        
    expect($globalTest->refresh()->is_default)->toBeTrue();

    // Toggle back
    Livewire::test(GlobalTestManager::class)
        ->call('toggleDefault', $globalTest->id)
        ->assertHasNoErrors();
        
    expect($globalTest->refresh()->is_default)->toBeFalse();
});

test('super admin onboarding imports default global tests', function () {
    $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);
    
    $department = Department::create([
        'name' => 'Hematology',
        'is_system' => true,
        'is_active' => true,
    ]);

    // Create active default test
    $defaultTest = GlobalTest::create([
        'test_code' => 'CBC',
        'name' => 'Complete Blood Count',
        'department_id' => $department->id,
        'is_active' => true,
        'is_default' => true,
    ]);

    // Create active non-default test
    $nonDefaultTest = GlobalTest::create([
        'test_code' => 'LFT',
        'name' => 'Liver Function Test',
        'department_id' => $department->id,
        'is_active' => true,
        'is_default' => false,
    ]);

    $plan = \App\Models\Plan::create([
        'name' => 'Basic Plan',
        'price' => 1000,
        'duration_in_days' => 30,
        'is_active' => true,
    ]);

    $this->actingAs($user);

    Livewire::test(LabManager::class)
        ->set('labName', 'Test Lab')
        ->set('labEmail', 'testlab@example.com')
        ->set('labPhone', '1234567890')
        ->set('labAddress', 'Test Address')
        ->set('planId', $plan->id)
        ->set('adminName', 'Lab Admin')
        ->set('adminEmail', 'labadmin@example.com')
        ->set('adminPassword', 'password123')
        ->call('createLab')
        ->assertHasNoErrors();

    // Verify company was created
    $company = \App\Models\Company::where('email', 'testlab@example.com')->first();
    expect($company)->not->toBeNull();

    // Verify default tests are imported to the company's lab_tests table
    $importedDefault = \App\Models\LabTest::where('company_id', $company->id)
        ->where('test_code', 'CBC')
        ->first();
    expect($importedDefault)->not->toBeNull();

    // Verify non-default tests are NOT imported
    $importedNonDefault = \App\Models\LabTest::where('company_id', $company->id)
        ->where('test_code', 'LFT')
        ->first();
    expect($importedNonDefault)->toBeNull();
});

test('public registration onboarding imports default global tests', function () {
    $department = Department::create([
        'name' => 'Pathology',
        'is_system' => true,
        'is_active' => true,
    ]);

    // Create active default test
    $defaultTest = GlobalTest::create([
        'test_code' => 'GLU',
        'name' => 'Glucose Test',
        'department_id' => $department->id,
        'is_active' => true,
        'is_default' => true,
    ]);

    // Create active non-default test
    $nonDefaultTest = GlobalTest::create([
        'test_code' => 'CHO',
        'name' => 'Cholesterol Test',
        'department_id' => $department->id,
        'is_active' => true,
        'is_default' => false,
    ]);

    // Make sure a plan with price 0 exists or mock it
    $freePlan = \App\Models\Plan::create([
        'name' => 'Free Trial Plan',
        'price' => 0,
        'duration_in_days' => 15,
        'is_active' => true,
    ]);

    $service = new \App\Services\CompanyRegistrationService();
    $user = $service->registerNewLab([
        'lab_name' => 'Self Reg Lab',
        'owner_name' => 'Owner Name',
        'email' => 'selfreg@example.com',
        'phone' => '0987654321',
        'password' => 'password123',
    ]);

    expect($user)->not->toBeNull();
    $company = $user->company;
    expect($company)->not->toBeNull();

    // Verify default test imported
    $importedDefault = \App\Models\LabTest::where('company_id', $company->id)
        ->where('test_code', 'GLU')
        ->first();
    expect($importedDefault)->not->toBeNull();

    // Verify non-default test not imported
    $importedNonDefault = \App\Models\LabTest::where('company_id', $company->id)
        ->where('test_code', 'CHO')
        ->first();
    expect($importedNonDefault)->toBeNull();
});


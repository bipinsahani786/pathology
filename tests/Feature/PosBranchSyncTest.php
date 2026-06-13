<?php

use App\Models\Branch;
use App\Models\CollectionCenter;
use App\Models\Company;
use App\Models\User;
use App\Models\Invoice;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use App\Livewire\Lab\PosManager;
use App\Livewire\Lab\PosEditManager;

test('POS manager correctly pre-selects and syncs branch and collection center', function () {
    // 1. Setup Roles
    $role = Role::firstOrCreate(['name' => 'lab_admin', 'guard_name' => 'web']);

    // 2. Setup Company & User
    $company = Company::create([
        'name' => 'Sync Test Lab Group',
        'email' => 'syncgroup@example.com',
        'phone' => '1234567890',
        'address' => 'Test HQ',
    ]);
    
    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);
    $user->assignRole($role);
    $this->actingAs($user);

    // 3. Setup Branches
    $branchA = Branch::create([
        'company_id' => $company->id,
        'name' => 'Branch A',
    ]);
    $branchB = Branch::create([
        'company_id' => $company->id,
        'name' => 'Branch B',
    ]);

    // 4. Setup Collection Centers
    $ccA = CollectionCenter::create([
        'company_id' => $company->id,
        'name' => 'CC on Branch A',
        'branch_id' => $branchA->id,
        'is_active' => true,
    ]);
    $ccB = CollectionCenter::create([
        'company_id' => $company->id,
        'name' => 'CC on Branch B',
        'branch_id' => $branchB->id,
        'is_active' => true,
    ]);

    // Set active branch in session to 'all' (null)
    session(['active_branch_id' => null]);

    // Test PosManager initialization
    $pos = Livewire::test(PosManager::class);
    $pos->assertOk();

    // Verify branch_id is pre-selected to the first branch or collection center's branch
    expect($pos->get('branch_id'))->not->toBeNull();

    // Set collection_center_id to ccB (Branch B)
    $pos->set('collection_center_id', $ccB->id);
    
    // Assert branch_id is synced to Branch B
    expect($pos->get('branch_id'))->toBe($branchB->id);

    // Set branch_id to branchA
    $pos->set('branch_id', $branchA->id);
    
    // Assert collection_center_id is synced to ccA (since it's the first active collection center of Branch A)
    expect($pos->get('collection_center_id'))->toBe($ccA->id);
});

test('POS Edit manager correctly pre-selects and syncs branch and collection center', function () {
    $role = Role::firstOrCreate(['name' => 'lab_admin', 'guard_name' => 'web']);
    $company = Company::create([
        'name' => 'Sync Test Lab Group Edit',
        'email' => 'syncgroupedit@example.com',
        'phone' => '1234567891',
        'address' => 'Test HQ Edit',
    ]);
    
    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);
    $user->assignRole($role);
    $this->actingAs($user);

    $branchA = Branch::create([
        'company_id' => $company->id,
        'name' => 'Branch A',
    ]);
    $branchB = Branch::create([
        'company_id' => $company->id,
        'name' => 'Branch B',
    ]);

    $ccA = CollectionCenter::create([
        'company_id' => $company->id,
        'name' => 'CC A',
        'branch_id' => $branchA->id,
        'is_active' => true,
    ]);
    $ccB = CollectionCenter::create([
        'company_id' => $company->id,
        'name' => 'CC B',
        'branch_id' => $branchB->id,
        'is_active' => true,
    ]);

    // Create an Invoice to edit
    $invoice = Invoice::create([
        'company_id' => $company->id,
        'collection_center_id' => $ccA->id,
        'branch_id' => $branchA->id,
        'patient_id' => $user->id,
        'created_by' => $user->id,
        'invoice_number' => 'INV-EDIT-101',
        'invoice_date' => now(),
        'subtotal' => 0,
        'discount_amount' => 0,
        'total_amount' => 0,
        'paid_amount' => 0,
        'due_amount' => 0,
        'payment_status' => 'Paid',
    ]);

    $posEdit = Livewire::test(PosEditManager::class, ['id' => $invoice->id]);
    $posEdit->assertOk();

    // Verify initial values
    expect($posEdit->get('branch_id'))->toBe($branchA->id);
    expect($posEdit->get('collection_center_id'))->toBe($ccA->id);

    // Sync to B
    $posEdit->set('collection_center_id', $ccB->id);
    expect($posEdit->get('branch_id'))->toBe($branchB->id);

    // Sync back to A
    $posEdit->set('branch_id', $branchA->id);
    expect($posEdit->get('collection_center_id'))->toBe($ccA->id);
});

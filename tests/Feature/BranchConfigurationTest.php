<?php

use App\Models\Branch;
use App\Models\Company;
use App\Models\Configuration;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use App\Livewire\Lab\SettingsManager;

test('configuration getFor and setFor correctly scoped to branch and falls back to global', function () {
    $company = Company::create([
        'name' => 'Main Lab Group',
        'email' => 'group@example.com',
        'phone' => '1234567890',
        'address' => 'Global HQ',
    ]);

    $branchA = Branch::create([
        'company_id' => $company->id,
        'name' => 'Branch A East',
        'contact_number' => '1111111111',
        'address' => 'East Area',
    ]);

    $branchB = Branch::create([
        'company_id' => $company->id,
        'name' => 'Branch B West',
        'contact_number' => '2222222222',
        'address' => 'West Area',
    ]);

    // Set global settings
    Configuration::setFor('invoice_prefix', 'GLO', $company->id, 'global');

    // Set branch specific settings for Branch A
    Configuration::setFor('invoice_prefix', 'BRA', $company->id, $branchA->id);

    // Assert Branch A gets its own settings
    expect(Configuration::getFor('invoice_prefix', 'INV', $company->id, $branchA->id))->toBe('BRA');

    // Assert Branch B falls back to global settings
    expect(Configuration::getFor('invoice_prefix', 'INV', $company->id, $branchB->id))->toBe('GLO');

    // Assert global context explicitly gets global settings
    expect(Configuration::getFor('invoice_prefix', 'INV', $company->id, 'global'))->toBe('GLO');
});

test('settings manager correctly scopes load and save per branch', function () {
    $role = Role::firstOrCreate(['name' => 'lab_admin', 'guard_name' => 'web']);
    $company = Company::create([
        'name' => 'Alpha Group',
        'email' => 'alpha@example.com',
        'phone' => '1234567890',
        'address' => 'Alpha HQ',
    ]);

    $branch = Branch::create([
        'company_id' => $company->id,
        'name' => 'Branch Alpha South',
        'contact_number' => '3333333333',
        'address' => 'South Area',
    ]);

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);
    $user->assignRole($role);

    // Log in
    $this->actingAs($user);

    // Livewire component test: Global edit
    Livewire::test(SettingsManager::class)
        ->set('selectedBranchId', 'global')
        ->call('loadSettings')
        ->set('invoice_prefix', 'GLOBAL-PRE')
        ->call('saveInvoiceSettings')
        ->assertHasNoErrors();

    // Check database has global entry
    expect(Configuration::where('company_id', $company->id)->whereNull('branch_id')->where('config_key', 'invoice_prefix')->first()->config_value)->toBe('GLOBAL-PRE');

    // Livewire component test: Branch edit
    Livewire::test(SettingsManager::class)
        ->set('selectedBranchId', $branch->id)
        ->call('loadSettings')
        ->set('invoice_prefix', 'BRANCH-PRE')
        ->call('saveInvoiceSettings')
        ->assertHasNoErrors();

    // Check database has branch-specific entry
    expect(Configuration::where('company_id', $company->id)->where('branch_id', $branch->id)->where('config_key', 'invoice_prefix')->first()->config_value)->toBe('BRANCH-PRE');

    // Check fallback still works
    expect(Configuration::getFor('invoice_prefix', 'INV', $company->id, $branch->id))->toBe('BRANCH-PRE');
    // Global remains unchanged
    expect(Configuration::getFor('invoice_prefix', 'INV', $company->id, 'global'))->toBe('GLOBAL-PRE');
});

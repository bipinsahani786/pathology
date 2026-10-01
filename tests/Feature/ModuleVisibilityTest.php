<?php

use App\Models\Company;
use App\Models\Configuration;
use App\Models\Plan;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Lab\SettingsManager;
use Spatie\Permission\Models\Role;

test('settings manager can toggle and save new module visibility settings', function () {
    $role = Role::firstOrCreate(['name' => 'lab_admin', 'guard_name' => 'web']);
    $company = Company::create([
        'name' => 'Visibility Lab',
        'email' => 'vis@example.com',
        'phone' => '9988776655',
        'address' => 'Vis Address',
    ]);

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);
    $user->assignRole($role);
    $this->actingAs($user);

    // Initial state: defaults should be true
    Livewire::test(SettingsManager::class)
        ->assertSet('module_reports', true)
        ->assertSet('module_web_bookings', true)
        ->assertSet('module_payment_modes', true)
        ->assertSet('module_audit_logs', true)
        ->assertSet('module_support', true)
        // Disable them
        ->set('module_reports', false)
        ->set('module_web_bookings', false)
        ->set('module_payment_modes', false)
        ->set('module_audit_logs', false)
        ->set('module_support', false)
        ->call('saveModules')
        ->assertHasNoErrors();

    // Verify configurations saved to database
    expect(Configuration::getFor('module_reports', '1', $company->id, 'global'))->toBe('0');
    expect(Configuration::getFor('module_web_bookings', '1', $company->id, 'global'))->toBe('0');
    expect(Configuration::getFor('module_payment_modes', '1', $company->id, 'global'))->toBe('0');
    expect(Configuration::getFor('module_audit_logs', '1', $company->id, 'global'))->toBe('0');
    expect(Configuration::getFor('module_support', '1', $company->id, 'global'))->toBe('0');
});

test('sidebar respects module visibility configuration for reports, web bookings and payment modes', function () {
    $plan = Plan::create([
        'name' => 'Enterprise Plan',
        'price' => 999,
        'duration_days' => 365,
        'features' => [
            'website_api' => true,
            'home_collection' => true,
        ],
    ]);

    $company = Company::create([
        'name' => 'Sidebar Lab',
        'email' => 'sidebar@example.com',
        'phone' => '1122334455',
        'address' => 'Sidebar Address',
        'plan_id' => $plan->id,
    ]);

    $role = Role::firstOrCreate(['name' => 'lab_admin', 'guard_name' => 'web']);
    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);
    $user->assignRole($role);
    $this->actingAs($user);

    // With defaults (enabled)
    $view = $this->view('layouts.partials.sidebar');
    $view->assertSee('Test Reports');
    $view->assertSee('Web Bookings');
    $view->assertSee('Payment Modes');

    // Turn off module_reports and module_web_bookings
    Configuration::setFor('module_reports', '0', $company->id, 'global');
    Configuration::setFor('module_web_bookings', '0', $company->id, 'global');
    Configuration::setFor('module_payment_modes', '0', $company->id, 'global');

    $viewDisabled = $this->view('layouts.partials.sidebar');
    $viewDisabled->assertDontSee('Test Reports');
    $viewDisabled->assertDontSee('Web Bookings');
    $viewDisabled->assertDontSee('Payment Modes');
});

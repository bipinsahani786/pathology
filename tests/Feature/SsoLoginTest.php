<?php

use App\Models\Company;
use App\Models\Invoice;
use App\Models\PatientProfile;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup plan with website_api enabled
    $this->plan = Plan::create([
        'name' => 'Enterprise Plan',
        'slug' => 'enterprise-plan',
        'price' => 5000,
        'features' => ['website_api' => true],
        'is_active' => true,
    ]);

    $this->company = Company::create([
        'name' => 'Apex Healthcare Labs',
        'email' => 'apex@example.com',
        'phone' => '9988776655',
        'address' => '123 Medical Enclave',
        'status' => 'active',
        'plan_id' => $this->plan->id,
        'api_key' => 'test_api_key_sso_12345',
        'api_enabled' => true,
    ]);
});

test('options preflight request succeeds with CORS headers', function () {
    $response = $this->call('OPTIONS', '/api/v1/patient/login', [], [], [], [
        'HTTP_ORIGIN' => 'https://external-lab.com',
        'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
    ]);

    $response->assertSuccessful();
    $response->assertHeader('Access-Control-Allow-Origin');
});

test('patient can authenticate via API and consume SSO token to reach portal dashboard', function () {
    // Create patient user
    $patientUser = User::create([
        'company_id' => $this->company->id,
        'name' => 'Sunita Devi',
        'email' => 'sunita.patient@example.com',
        'phone' => '9876543210',
        'password' => bcrypt('password123'),
        'is_active' => true,
    ]);

    $profile = PatientProfile::create([
        'company_id' => $this->company->id,
        'user_id' => $patientUser->id,
        'patient_id_string' => 'PAT-1138',
        'gender' => 'Female',
        'age' => 45,
        'age_type' => 'Years',
    ]);

    // 1. Call API to get SSO redirect URL
    $response = $this->postJson('/api/v1/patient/login', [
        'patient_id' => 'PAT-1138',
        'phone' => '9876543210',
    ], [
        'X-Lab-Api-Key' => 'test_api_key_sso_12345',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $redirectUrl = $response->json('data.redirect_url');
    expect($redirectUrl)->toBeString();
    expect($redirectUrl)->toContain('/portal/auth/sso/consume?token=');

    // 2. Consume SSO token in browser
    $consumeResponse = $this->get($redirectUrl);
    $consumeResponse->assertRedirect(route('portal.dashboard'));

    // Verify session is authenticated as patient
    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($patientUser->id);

    // 3. Second attempt with same token must fail (single-use protection)
    Auth::logout();
    $reusedResponse = $this->get($redirectUrl);
    $reusedResponse->assertRedirect(route('portal.login'));
    expect(Auth::check())->toBeFalse();
});

test('staff member can authenticate via API and consume SSO token to reach software dashboard', function () {
    $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);

    $staffUser = User::create([
        'company_id' => $this->company->id,
        'name' => 'Dr. Rajesh Sharma',
        'email' => 'dr.rajesh@apexlab.com',
        'phone' => '9123456780',
        'password' => bcrypt('SecurePass123!'),
        'is_active' => true,
    ]);
    $staffUser->assignRole($doctorRole);

    // 1. Call staff login API
    $response = $this->postJson('/api/v1/staff/login', [
        'email' => 'dr.rajesh@apexlab.com',
        'password' => 'SecurePass123!',
    ], [
        'X-Lab-Api-Key' => 'test_api_key_sso_12345',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'data' => [
            'user_name' => 'Dr. Rajesh Sharma',
            'email' => 'dr.rajesh@apexlab.com',
        ],
    ]);

    $redirectUrl = $response->json('data.redirect_url');
    expect($redirectUrl)->toBeString();
    expect($redirectUrl)->toContain('/auth/sso/consume?token=');

    // 2. Consume SSO token in browser
    $consumeResponse = $this->get($redirectUrl);
    $consumeResponse->assertRedirect(route('partner.dashboard'));

    // Verify authenticated user
    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($staffUser->id);
});

test('staff can directly log in via POST form action', function () {
    $staffUser = User::create([
        'company_id' => $this->company->id,
        'name' => 'Lab Technician John',
        'email' => 'john.tech@apexlab.com',
        'phone' => '9888877777',
        'password' => bcrypt('TechPass789!'),
        'is_active' => true,
    ]);

    $response = $this->post('/auth/direct-login', [
        'api_key' => 'test_api_key_sso_12345',
        'login' => 'john.tech@apexlab.com',
        'password' => 'TechPass789!',
    ]);

    $response->assertRedirect(route('lab.dashboard'));
    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($staffUser->id);
});

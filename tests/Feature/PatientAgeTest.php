<?php

namespace Tests\Feature;

use App\Models\PatientProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientAgeTest extends TestCase
{
    use RefreshDatabase;
    public function test_age_display_and_text_accessors(): void
    {
        // 1. Child with Years and Months
        $p1 = new PatientProfile([
            'age' => 5,
            'age_type' => 'Years',
            'age_months' => 6,
        ]);
        $this->assertEquals('5 Y 6 M', $p1->age_display);
        $this->assertEquals('5 Yrs 6 Mos', $p1->age_text);

        // 2. Child with Years, Months and Days
        $p2 = new PatientProfile([
            'age' => 5,
            'age_type' => 'Years',
            'age_months' => 6,
            'age_days' => 10,
        ]);
        $this->assertEquals('5 Y 6 M 10 D', $p2->age_display);
        $this->assertEquals('5 Yrs 6 Mos 10 Days', $p2->age_text);

        // 3. Adult with only Years
        $p3 = new PatientProfile([
            'age' => 25,
            'age_type' => 'Years',
        ]);
        $this->assertEquals('25 Y', $p3->age_display);
        $this->assertEquals('25 Yrs', $p3->age_text);

        // 4. Infant with Months
        $p4 = new PatientProfile([
            'age' => 4,
            'age_type' => 'Months',
        ]);
        $this->assertEquals('4 M', $p4->age_display);
        $this->assertEquals('4 Mos', $p4->age_text);

        // 5. Infant with Months and Days
        $p5 = new PatientProfile([
            'age' => 4,
            'age_type' => 'Months',
            'age_days' => 12,
        ]);
        $this->assertEquals('4 M 12 D', $p5->age_display);
        $this->assertEquals('4 Mos 12 Days', $p5->age_text);

        // 6. Newborn with Days
        $p6 = new PatientProfile([
            'age' => 15,
            'age_type' => 'Days',
        ]);
        $this->assertEquals('15 D', $p6->age_display);
        $this->assertEquals('15 Days', $p6->age_text);
    }

    public function test_pos_manager_quick_add_patient_with_years_and_months(): void
    {
        $company = \App\Models\Company::first();
        if (!$company) {
            $company = \App\Models\Company::create(['name' => 'Test Lab', 'email' => 'lab@test.com', 'is_active' => true]);
        }
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'is_active' => true,
            ]);
        }

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create patients', 'guard_name' => 'web']);
        $user->givePermissionTo($perm);

        $this->actingAs($user);

        // Test PosManager quickAddPatient
        $component = new \App\Livewire\Lab\PosManager();
        $component->new_name = 'Child Patient';
        $component->new_phone = '9876543210';
        $component->new_age = 5;
        $component->new_age_months = 6;
        $component->new_gender = 'Male';
        $component->quickAddPatient();

        $createdUser = \App\Models\User::where('name', 'Child Patient')->latest()->first();
        $this->assertNotNull($createdUser);
        $this->assertNotNull($createdUser->patientProfile);
        $this->assertEquals(5, $createdUser->patientProfile->age);
        $this->assertEquals('Years', $createdUser->patientProfile->age_type);
        $this->assertEquals(6, $createdUser->patientProfile->age_months);
        $this->assertEquals('5 Y 6 M', $createdUser->patientProfile->age_display);
        $this->assertEquals('5 Yrs 6 Mos', $createdUser->patientProfile->age_text);
    }

    public function test_patient_manager_store_new_patient(): void
    {
        $company = \App\Models\Company::first();
        if (!$company) {
            $company = \App\Models\Company::create(['name' => 'Test Lab', 'email' => 'lab@test.com', 'is_active' => true]);
        }
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'is_active' => true,
            ]);
        }

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create patients', 'guard_name' => 'web']);
        $user->givePermissionTo($perm);

        $this->actingAs($user);

        $component = new \App\Livewire\Lab\PatientManager();
        $component->name = 'Jane Doe';
        $component->phone = '9876543211';
        $component->age = 28;
        $component->gender = 'Female';
        $component->store();

        $createdUser = \App\Models\User::where('name', 'Jane Doe')->first();
        $this->assertNotNull($createdUser);
        $this->assertNotNull($createdUser->patientProfile);
        $this->assertStringStartsWith('PAT', $createdUser->patientProfile->patient_id_string);
    }
}

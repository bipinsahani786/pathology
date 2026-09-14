<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\GlobalTest;
use App\Models\LabTest;
use App\Models\Company;
use App\Models\Department;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dept = Department::where('name', 'Serology & Immunology')->first() 
            ?? Department::where('name', 'like', '%Serology%')->first();
        $deptId = $dept ? $dept->id : 3;

        $parameters = [
            [
                'name' => 'S.Typhi-O',
                'short_code' => 'TO',
                'input_type' => 'widal_slide',
                'range_type' => 'flexible',
                'unit' => '',
                'method' => 'Slide Agglutination',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320', '1:640'],
                'ranges' => [
                    [
                        'gender' => 'Both',
                        'age_min' => 0,
                        'age_max' => 120,
                        'age_unit' => 'Years',
                        'normal_value' => 'Negative',
                        'display_range' => 'Negative (< 1:80)'
                    ]
                ],
                'formula' => '',
            ],
            [
                'name' => 'S.Typhi-H',
                'short_code' => 'TH',
                'input_type' => 'widal_slide',
                'range_type' => 'flexible',
                'unit' => '',
                'method' => 'Slide Agglutination',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320', '1:640'],
                'ranges' => [
                    [
                        'gender' => 'Both',
                        'age_min' => 0,
                        'age_max' => 120,
                        'age_unit' => 'Years',
                        'normal_value' => 'Negative',
                        'display_range' => 'Negative (< 1:80)'
                    ]
                ],
                'formula' => '',
            ],
            [
                'name' => 'S.Paratyphi-AH',
                'short_code' => 'AH',
                'input_type' => 'widal_slide',
                'range_type' => 'flexible',
                'unit' => '',
                'method' => 'Slide Agglutination',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320', '1:640'],
                'ranges' => [
                    [
                        'gender' => 'Both',
                        'age_min' => 0,
                        'age_max' => 120,
                        'age_unit' => 'Years',
                        'normal_value' => 'Negative',
                        'display_range' => 'Negative (< 1:80)'
                    ]
                ],
                'formula' => '',
            ],
            [
                'name' => 'S.Paratyphi-BH',
                'short_code' => 'BH',
                'input_type' => 'widal_slide',
                'range_type' => 'flexible',
                'unit' => '',
                'method' => 'Slide Agglutination',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320', '1:640'],
                'ranges' => [
                    [
                        'gender' => 'Both',
                        'age_min' => 0,
                        'age_max' => 120,
                        'age_unit' => 'Years',
                        'normal_value' => 'Negative',
                        'display_range' => 'Negative (< 1:80)'
                    ]
                ],
                'formula' => '',
            ],
        ];

        $globalTest = GlobalTest::updateOrCreate(
            ['test_code' => 'WIDAL_SLIDE'],
            [
                'name' => 'Widal Test (Slide Method)',
                'category' => $dept ? $dept->name : 'Serology & Immunology',
                'department_id' => $deptId,
                'method' => 'Slide Agglutination',
                'sample_type' => 'Clotted Blood (Serum)',
                'tat_hours' => 24,
                'mrp' => 250.00,
                'b2b_price' => 175.00,
                'is_active' => true,
                'is_default' => true,
                'description' => "*Agglutinin titer greater than 1 : 80 considered significant and usually suggestive of infection.\n* A single positive result has less significance than the rising agglutinin titer.",
                'default_parameters' => $parameters,
            ]
        );

        // Sync into all existing labs
        $companies = Company::all();
        foreach ($companies as $company) {
            LabTest::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'test_code' => 'WIDAL_SLIDE',
                ],
                [
                    'global_test_id' => $globalTest->id,
                    'name' => $globalTest->name,
                    'department_id' => $globalTest->department_id,
                    'method' => $globalTest->method,
                    'description' => $globalTest->description,
                    'parameters' => $parameters,
                    'mrp' => $globalTest->mrp,
                    'b2b_price' => $globalTest->b2b_price,
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe to leave data intact
    }
};

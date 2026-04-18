<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalTest;

class GlobalTestSeeder extends Seeder
{
    public function run(): void
    {
        $dataFiles = [
            'GlobalTests_Haematology',
            'GlobalTests_Biochemistry',
            'GlobalTests_Thyroid_Diabetes',
            'GlobalTests_Liver_Kidney',
            'GlobalTests_Lipid_Cardiac',
            'GlobalTests_Serology_Urine',
            'GlobalTests_Vitamins_Minerals',
            'GlobalTests_TumorMarkers_Hormones',
            'GlobalTests_Special',
        ];

        foreach ($dataFiles as $file) {
            $tests = require database_path("seeders/data/{$file}.php");
            foreach ($tests as $test) {
                // Find system department
                $department = \App\Models\Department::where('name', $test['category'] ?? 'Other')
                    ->where('is_system', true)
                    ->first();

                GlobalTest::updateOrCreate(
                    ['test_code' => $test['test_code']],
                    [
                        'name' => $test['name'],
                        'category' => $test['category'] ?? 'Other',
                        'department_id' => $department?->id,
                        'description' => $test['description'] ?? null,
                        'interpretation' => $test['interpretation'] ?? null,
                        'mrp' => $test['suggested_price'] ?? 0,
                        'method' => $test['method'] ?? null,
                        'sample_type' => $test['sample_type'] ?? null,
                        'tat_hours' => $test['tat_hours'] ?? 24,
                        'default_parameters' => $test['default_parameters'] ?? [],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}

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
                GlobalTest::updateOrCreate(
                    ['test_code' => $test['test_code']],
                    $test
                );
            }
        }
    }
}

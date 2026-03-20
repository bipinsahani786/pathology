<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class SystemDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Haematology',
            'Biochemistry',
            'Thyroid & Diabetes',
            'Liver & Kidney',
            'Lipid & Cardiac',
            'Serology & Urine',
            'Vitamins & Minerals',
            'Tumor Markers & Hormones',
            'Special',
            'Microbiology',
            'Immunology',
            'Histopathology',
            'Cytopathology',
            'Molecular Biology',
            'Radiology',
            'Other'
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['name' => $dept, 'is_system' => true],
                ['is_active' => true, 'company_id' => null]
            );
        }
    }
}

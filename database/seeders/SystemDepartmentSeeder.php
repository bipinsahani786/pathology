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
            'Serology & Immunology',
            'Clinical Pathology',
            'Microbiology',
            'Molecular Biology',
            'Histopathology',
            'Cytopathology',
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

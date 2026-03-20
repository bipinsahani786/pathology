<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tests = \App\Models\LabTest::whereNotNull('department')->whereNull('department_id')->get();

        foreach ($tests as $test) {
            $department = \App\Models\Department::firstOrCreate(
                [
                    'company_id' => $test->company_id,
                    'name' => $test->department
                ],
                [
                    'is_active' => true
                ]
            );

            $test->update(['department_id' => $department->id]);
        }
    }
}

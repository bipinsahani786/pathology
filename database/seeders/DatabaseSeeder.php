<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles Create
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $labAdminRole = Role::firstOrCreate(['name' => 'lab_admin']);
        $patientRole = Role::firstOrCreate(['name' => 'patient']);

        // 2. Super Admin Account 
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@zytrixon.com'], 
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), 
                'is_active' => true,
            ]
        );

        // 3. Assign Super Admin Role
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
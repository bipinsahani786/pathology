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
        // 1. Roles & Permissions
        $this->call(RoleSeeder::class);

        // 2. Super Admin Account 
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@zytrixon.com'], 
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), 
                'is_active' => true,
            ]
        );
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        // 3. Demo Lab Data (Company, Tests, Patients, Invoices, etc.)
        $this->call(DemoSeeder::class);
    }
}
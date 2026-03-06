<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Permissions List
        $permissions = [
            'manage lab',
            'manage staff',
            'manage patients',
            'generate report',
            'edit report',
            'view reports',
            'download report'
        ];

        // 2. Permissions Create
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Roles Create & Permissions Assign

        // Company Admin
        $companyAdmin = Role::firstOrCreate(['name' => 'company_admin']);
        $companyAdmin->syncPermissions([
            'manage lab', 
            'manage staff', 
            'manage patients', 
            'generate report', 
            'edit report', 
            'view reports', 
            'download report'
        ]);

        // Staff
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'manage patients', 
            'generate report', 
            'view reports', 
            'download report'
        ]);

        // Customer (Patient)
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->syncPermissions([
            'view reports', 
            'download report'
        ]);
        
        $this->command->info('Roles and Permissions synced successfully!');
    }
}
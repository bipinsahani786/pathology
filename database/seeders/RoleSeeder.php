<?php

namespace Database\Seeders;

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

        // 1. Permissions List
        $permissions = [
            // Admin Permissions
            'manage global_tests',
            'manage plans',
            'manage subscriptions',
            // Lab Permissions
            'manage lab_tests',
            'manage test_packages',
            'manage patients',
            'manage doctors',
            'manage agents',
            'manage collection_centers',
            'manage branches',
            'manage marketing',
            'manage payment_modes',
            'manage pos',
            'view reports',
            'generate reports',
            'download reports'
        ];

        // 2. Permissions Create
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Roles Create & Permissions Assign

        // Super Admin (System Owner)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions([
            'manage global_tests',
            'manage plans',
            'manage subscriptions'
        ]);

        // Lab Admin (Tenant Owner)
        $labAdmin = Role::firstOrCreate(['name' => 'lab_admin']);
        $labAdmin->syncPermissions([
            'manage lab_tests',
            'manage test_packages',
            'manage patients',
            'manage doctors',
            'manage agents',
            'manage collection_centers',
            'manage branches',
            'manage marketing',
            'manage payment_modes',
            'manage pos',
            'view reports',
            'generate reports',
            'download reports'
        ]);

        // Lab Staff
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'manage patients',
            'manage pos',
            'view reports',
            'generate reports',
            'download reports'
        ]);

        // Customer (Patient)
        $patient = Role::firstOrCreate(['name' => 'patient']);
        $patient->syncPermissions([
            'view reports', 
            'download reports'
        ]);
        
        $this->command->info('Roles and Permissions synced successfully!');
    }
}
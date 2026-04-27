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

        // 1. Module-based Granular Permissions
        $modules = [
            'patients',
            'doctors',
            'agents',
            'lab_tests',
            'test_packages',
            'departments',
            'invoices',
            'reports',
            'settlements',
            'branches',
            'collection_centers',
            'payment_modes',
            'marketing',
            'staff_roles',
            'settings',
            'pos',
            'wallets',
            'inventory',
            'equipment',
            'audit_logs',
            'support_tickets'
        ];
        $actions = ['view', 'create', 'edit', 'delete'];

        $granularPermissions = [];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $granularPermissions[] = "$action $module";
            }
        }

        // Additional Special Permissions
        $specialPermissions = [
            'manage global_tests', // Super Admin
            'manage plans',        // Super Admin
            'manage subscriptions', // Super Admin
            'generate reports',
            'download reports'
        ];

        $allPermissions = array_merge($granularPermissions, $specialPermissions);

        // 2. Permissions Create
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Roles Create & Permissions Assign

        // Super Admin (System Owner)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions([
            'manage global_tests',
            'manage plans',
            'manage subscriptions',
            'view support_tickets',
            'edit support_tickets',
            'delete support_tickets',
        ]);

        // Lab Admin (Tenant Owner)
        $labAdmin = Role::firstOrCreate(['name' => 'lab_admin']);
        // Lab Admin gets everything except super admin stuff
        $labAdmin->syncPermissions($granularPermissions);
        $labAdmin->givePermissionTo(['generate reports', 'download reports']);

        // Branch Admin (Siloed Tenant Manager)
        $branchAdmin = Role::firstOrCreate(['name' => 'branch_admin']);
        // Branch Admin: Deny branch management, but ALLOW settings (restricted view) and staff_roles
        $branchAdminPermissions = array_diff($granularPermissions, ['view branches', 'create branches', 'edit branches', 'delete branches']);
        $branchAdmin->syncPermissions($branchAdminPermissions);
        $branchAdmin->givePermissionTo(['generate reports', 'download reports']);

        // Lab Staff (Default Permissions)
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'view patients',
            'create patients',
            'edit patients',
            'view invoices',
            'create invoices',
            'view reports',
            'generate reports',
            'download reports',
            'view pos',
            'create pos',
            'view inventory',
            'create inventory'
        ]);

        // Collection Center User
        $collector = Role::firstOrCreate(['name' => 'collection_center']);
        $collector->syncPermissions([
            'view patients',
            'create patients',
            'edit patients',
            'view doctors',
            'create doctors',
            'view agents',
            'create agents',
            'view invoices',
            'create invoices',
            'edit invoices',
            'view reports',
            'generate reports',
            'download reports',
            'view pos',
            'create pos',
            'view marketing',
            'create marketing',
            'view inventory',
            'view support_tickets',
            'create support_tickets'
        ]);

        // Customer (Patient)
        $patient = Role::firstOrCreate(['name' => 'patient']);
        $patient->syncPermissions([
            // 'view reports', 
            'download reports'
        ]);

        // Doctor (Referral Partner)
        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $doctor->syncPermissions([
            // 'view reports',
            'download reports',
            'view support_tickets',
            'create support_tickets'
        ]);

        // Agent (Referral Partner)
        $agent = Role::firstOrCreate(['name' => 'agent']);
        $agent->syncPermissions([
            // 'view reports',
            'download reports',
            'view support_tickets',
            'create support_tickets'
        ]);

        $this->command->info('Roles and Permissions synced successfully!');
    }
}
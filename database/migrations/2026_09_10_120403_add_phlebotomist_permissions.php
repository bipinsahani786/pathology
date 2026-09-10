<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Phlebotomist management permissions (admin side)
        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => "$action phlebotomists"]);
        }

        // Home collection permissions
        Permission::firstOrCreate(['name' => 'view home_collections']);
        Permission::firstOrCreate(['name' => 'assign home_collections']);
        Permission::firstOrCreate(['name' => 'update home_collection_status']);
        Permission::firstOrCreate(['name' => 'add tests to home_collection']);

        // Assign to admin roles
        $adminRoles = ['super_admin', 'lab_admin', 'branch_admin'];
        foreach ($adminRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo([
                    'view phlebotomists',
                    'create phlebotomists',
                    'edit phlebotomists',
                    'delete phlebotomists',
                    'view home_collections',
                    'assign home_collections',
                    'update home_collection_status',
                    'add tests to home_collection',
                ]);
            }
        }

        // Phlebotomist role — create if not exists, give portal permissions
        $phlebotomistRole = Role::firstOrCreate(['name' => 'phlebotomist']);
        $phlebotomistRole->givePermissionTo([
            'update home_collection_status',
            'add tests to home_collection',
        ]);
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view phlebotomists', 'create phlebotomists', 'edit phlebotomists', 'delete phlebotomists',
            'view home_collections', 'assign home_collections',
            'update home_collection_status', 'add tests to home_collection',
        ];

        foreach ($permissions as $p) {
            Permission::where('name', $p)->delete();
        }

        Role::where('name', 'phlebotomist')->delete();
    }
};


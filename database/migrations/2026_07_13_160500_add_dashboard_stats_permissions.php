<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $actions = ['view', 'create', 'edit', 'delete'];
        foreach ($actions as $action) {
            Permission::firstOrCreate(['name' => "$action dashboard_stats"]);
        }

        // Give them to admin and staff roles
        $adminRoles = ['super_admin', 'lab_admin', 'branch_admin', 'staff', 'collection_center'];
        foreach ($adminRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo('view dashboard_stats');
                if (in_array($roleName, ['super_admin', 'lab_admin', 'branch_admin'])) {
                    $role->givePermissionTo(['create dashboard_stats', 'edit dashboard_stats', 'delete dashboard_stats']);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $actions = ['view', 'create', 'edit', 'delete'];
        foreach ($actions as $action) {
            $permission = Permission::where('name', "$action dashboard_stats")->first();
            if ($permission) {
                $permission->delete();
            }
        }
    }
};

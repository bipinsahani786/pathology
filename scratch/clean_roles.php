<?php

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$systemRoles = ['staff', 'lab_admin', 'collection_center', 'branch_admin', 'doctor', 'agent'];

echo "Starting role cleanup...\n";

DB::transaction(function() use ($systemRoles) {
    $roles = Role::all();
    
    foreach ($roles as $role) {
        // Regex to match e.g. lab_9_doctor, lab_2_staff, lab_2_collection_center
        if (preg_match('/^lab_\d+_(staff|lab_admin|collection_center|branch_admin|doctor|agent)$/', $role->name, $matches)) {
            $systemRoleName = $matches[1];
            echo "Found duplicate/renamed role: {$role->name} -> Should be: {$systemRoleName}\n";
            
            // Ensure original system role exists
            $systemRole = Role::firstOrCreate(['name' => $systemRoleName, 'guard_name' => 'web']);
            
            // Find all users who currently have this renamed role
            $users = User::role($role->name)->get();
            echo "  Moving " . $users->count() . " users to system role '{$systemRoleName}'\n";
            
            foreach ($users as $user) {
                // Assign system role
                if (!$user->hasRole($systemRoleName)) {
                    $user->assignRole($systemRoleName);
                }
                // Detach renamed role
                $user->removeRole($role->name);
            }
            
            // Finally delete the renamed/duplicate role
            $role->delete();
            echo "  Deleted role: {$role->name}\n";
        }
    }
});

echo "Role cleanup completed successfully!\n";

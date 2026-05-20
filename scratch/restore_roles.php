<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "Starting role restoration...\n";

// Ensure global roles exist
$doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
$patientRole = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
$agentRole = Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);
$ccRole = Role::firstOrCreate(['name' => 'collection_center', 'guard_name' => 'web']);
$staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

DB::transaction(function() use ($doctorRole, $patientRole, $agentRole, $ccRole, $staffRole) {
    $users = User::all();
    $fixedCount = 0;
    
    foreach ($users as $user) {
        $rolesCount = DB::table('model_has_roles')->where('model_id', $user->id)->count();
        
        if ($rolesCount === 0) {
            $assignedRole = null;
            
            // 1. Check Profiles
            if ($user->doctorProfile) {
                $user->assignRole($doctorRole);
                $assignedRole = 'doctor';
            } elseif ($user->patientProfile) {
                $user->assignRole($patientRole);
                $assignedRole = 'patient';
            } elseif ($user->agentProfile) {
                $user->assignRole($agentRole);
                $assignedRole = 'agent';
            } 
            // 2. Check Collection Center
            elseif ($user->collection_center_id) {
                $user->assignRole($ccRole);
                $assignedRole = 'collection_center';
            } 
            // 3. Name-based or Specific Doctor check
            elseif (
                preg_match('/^(dr|dr\.|dr\s|doctor)/i', $user->name) || 
                in_array($user->id, [137, 150, 154]) // Dr. Akhlakh, A Kumar FRCS, M P Yadav
            ) {
                $user->assignRole($doctorRole);
                $assignedRole = 'doctor';
            } 
            // 4. Specific Staff check
            elseif (in_array($user->id, [3, 135]) || strtolower($user->name) === 'reception') {
                $user->assignRole($staffRole);
                $assignedRole = 'staff';
            }
            
            if ($assignedRole) {
                $fixedCount++;
                echo "Restored User ID: {$user->id} | Name: {$user->name} -> Role: {$assignedRole}\n";
            } else {
                echo "WARNING: User ID: {$user->id} | Name: {$user->name} has no role and could not be auto-restored.\n";
            }
        }
    }
    
    echo "\nTotal users fixed: {$fixedCount}\n";
});

// Clear Spatie Permission Cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "Permission cache cleared.\n";

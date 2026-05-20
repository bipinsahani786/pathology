<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "Finding all users with no roles...\n";

$users = User::all();
$noRoleCount = 0;

foreach ($users as $user) {
    $roles = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->get();

    if ($roles->isEmpty()) {
        $noRoleCount++;
        echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
        echo "  Has doctorProfile: " . ($user->doctorProfile ? 'Yes' : 'No') . "\n";
        echo "  Has patientProfile: " . ($user->patientProfile ? 'Yes' : 'No') . "\n";
        echo "  Has agentProfile: " . ($user->agentProfile ? 'Yes' : 'No') . "\n";
        echo "  Collection Center ID: " . ($user->collection_center_id ?: 'None') . "\n";
    }
}

echo "\nTotal users with no roles: {$noRoleCount}\n";

<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "All users in Company 9:\n";
$users = User::where('company_id', 9)->get();
foreach ($users as $user) {
    $roles = DB::table('model_has_roles')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('model_has_roles.model_id', $user->id)
        ->pluck('roles.name')
        ->toArray();
        
    echo "ID: {$user->id} | Name: {$user->name} | Roles: [" . implode(', ', $roles) . "]\n";
}

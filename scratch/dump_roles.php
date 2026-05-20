<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "Dumping all roles in the database...\n";

$roles = Role::all();
foreach ($roles as $role) {
    $count = DB::table('model_has_roles')->where('role_id', $role->id)->count();
    echo "ID: {$role->id} | Name: {$role->name} | Guard: {$role->guard_name} | User Count: {$count}\n";
}

<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "Detailed analysis of users with no roles:\n";

$users = User::all();
foreach ($users as $user) {
    $rolesCount = DB::table('model_has_roles')->where('model_id', $user->id)->count();
    if ($rolesCount === 0) {
        $doctorProfile = DB::table('doctor_profiles')->where('user_id', $user->id)->first();
        $patientProfile = DB::table('patient_profiles')->where('user_id', $user->id)->first();
        $agentProfile = DB::table('agent_profiles')->where('user_id', $user->id)->first();
        
        echo "ID: {$user->id} | Company: {$user->company_id} | Name: {$user->name} | Email: {$user->email}\n";
        echo "  Doc Profile: " . ($doctorProfile ? "Yes (ID {$doctorProfile->id})" : "No") . "\n";
        echo "  Pat Profile: " . ($patientProfile ? "Yes (ID {$patientProfile->id})" : "No") . "\n";
        echo "  Agent Profile: " . ($agentProfile ? "Yes (ID {$agentProfile->id})" : "No") . "\n";
        echo "  CC ID: " . ($user->collection_center_id ?: "None") . "\n";
        echo "  Created At: {$user->created_at}\n";
    }
}

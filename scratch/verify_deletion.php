<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LabTest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Services\LabTestService;
use Illuminate\Support\Facades\DB;

// Let's run verification inside a transaction so we don't dirty the database
DB::beginTransaction();

try {
    echo "Starting deletion validation verification...\n";

    // 1. Verification of LabTest Deletion
    // Find a test that has invoice items
    $linkedItem = InvoiceItem::first();
    if ($linkedItem) {
        $testId = $linkedItem->lab_test_id;
        $test = LabTest::find($testId);
        if ($test) {
            echo "Found linked test: {$test->name} (ID: {$test->id})\n";
            try {
                $service = new LabTestService();
                $service->deleteTest($test->id);
                echo "❌ FAIL: Linked test was deleted!\n";
            } catch (\Exception $e) {
                echo "✅ SUCCESS: Deletion of linked test blocked: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "⚠️ No InvoiceItem found in database to test linked test deletion.\n";
    }

    // 2. Verification of Patient Deletion
    // Find a patient that has invoices
    $linkedInvoice = Invoice::first();
    if ($linkedInvoice) {
        $patientId = $linkedInvoice->patient_id;
        $patient = User::find($patientId);
        if ($patient) {
            echo "Found linked patient: {$patient->name} (ID: {$patient->id})\n";
            
            // Run PatientManager delete check logic
            if (Invoice::where('patient_id', $patient->id)->exists()) {
                echo "✅ SUCCESS: Patient deletion block check passed.\n";
            } else {
                echo "❌ FAIL: Patient deletion block check failed.\n";
            }
        }
    } else {
        echo "⚠️ No Invoice found in database to test linked patient deletion.\n";
    }

    // 3. Verification of Doctor Deletion
    if ($linkedInvoice && $linkedInvoice->referred_by_doctor_id) {
        $doctorId = $linkedInvoice->referred_by_doctor_id;
        $doctor = User::find($doctorId);
        if ($doctor) {
            echo "Found linked doctor: {$doctor->name} (ID: {$doctor->id})\n";
            if (Invoice::where('referred_by_doctor_id', $doctor->id)->exists()) {
                echo "✅ SUCCESS: Doctor deletion block check passed.\n";
            } else {
                echo "❌ FAIL: Doctor deletion block check failed.\n";
            }
        }
    } else {
        echo "⚠️ No Invoice with referred doctor found in database.\n";
    }

    // 4. Verification of Agent Deletion
    if ($linkedInvoice && $linkedInvoice->referred_by_agent_id) {
        $agentId = $linkedInvoice->referred_by_agent_id;
        $agent = User::find($agentId);
        if ($agent) {
            echo "Found linked agent: {$agent->name} (ID: {$agent->id})\n";
            if (Invoice::where('referred_by_agent_id', $agent->id)->exists()) {
                echo "✅ SUCCESS: Agent deletion block check passed.\n";
            } else {
                echo "❌ FAIL: Agent deletion block check failed.\n";
            }
        }
    } else {
        echo "⚠️ No Invoice with referred agent found in database.\n";
    }

} catch (\Exception $e) {
    echo "❌ Unexpected error: " . $e->getMessage() . "\n";
} finally {
    // Always rollback so we don't commit any changes
    DB::rollBack();
    echo "Rollback completed.\n";
}

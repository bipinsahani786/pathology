<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Models\LabTest;

echo "Starting historical invoice B2B cost & CC profit recalculation...\n";

$invoices = Invoice::where('status', '!=', 'Cancelled')->get();
echo "Found " . $invoices->count() . " active invoices in the database.\n";
$count = 0;

foreach ($invoices as $invoice) {
    $totalB2b = 0;
    foreach ($invoice->items as $item) {
        if ($item->lab_test_id) {
            $test = LabTest::find($item->lab_test_id);
            if ($test) {
                // If B2B price is not set, fallback to normal item price
                $b2b = $test->b2b_price > 0 ? (float)$test->b2b_price : (float)$item->price;
                $totalB2b += $b2b;
            }
        }
    }

    if ($invoice->collection_center_id) {
        $ccProfit = max(0, (float)$invoice->total_amount - $totalB2b);
    } else {
        $ccProfit = 0;
    }

    echo "Invoice #{$invoice->invoice_number}: CC ID = " . ($invoice->collection_center_id ?? 'None') . ", Total = {$invoice->total_amount}, B2B = {$invoice->total_b2b_amount}, CC Profit = {$invoice->cc_profit_amount}, Calculated B2B = {$totalB2b}, Calculated CC Profit = {$ccProfit}\n";

    // Only update if B2B amount or CC profit differs from saved values
    if (abs((float)$invoice->total_b2b_amount - $totalB2b) > 0.01 || abs((float)$invoice->cc_profit_amount - $ccProfit) > 0.01) {
        $invoice->update([
            'total_b2b_amount' => $totalB2b,
            'cc_profit_amount' => $ccProfit,
        ]);
        $count++;
    }
}

// Flush dashboard cache
try {
    \App\Livewire\Lab\Dashboard::flushCache();
    echo "Dashboard cache flushed successfully.\n";
} catch (\Exception $e) {
    echo "Failed to flush dashboard cache: " . $e->getMessage() . "\n";
}

echo "Successfully recalculated and updated {$count} invoices!\n";

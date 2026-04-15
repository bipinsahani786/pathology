<?php

use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Debugging for SWS (Collection Center)
$partner = User::where('name', 'SWS')->first();
if (!$partner) {
    echo "Partner SWS not found\n";
    exit;
}

echo "Partner Name: " . $partner->name . "\n";
echo "Partner ID: " . $partner->id . "\n";
echo "Collection Center ID: " . $partner->collection_center_id . "\n";

$startDate = '2026-04-01';
$endDate = '2026-04-15';

$invoices = Invoice::where('collection_center_id', $partner->collection_center_id)
    ->where('status', '!=', 'Cancelled')
    ->get();

echo "Total invoices for this CC: " . $invoices->count() . "\n";

foreach ($invoices as $inv) {
    echo "Invoice ID: " . $inv->id . ", Date: " . $inv->invoice_date . ", Total: " . $inv->total_amount . ", B2B: " . $inv->total_b2b_amount . ", Payment: " . $inv->payment_status . "\n";
}

$filtered = Invoice::where('collection_center_id', $partner->collection_center_id)
    ->where('status', '!=', 'Cancelled')
    ->whereBetween('invoice_date', [
        Carbon::parse($startDate)->startOfDay(), 
        Carbon::parse($endDate)->endOfDay()
    ])
    ->get();

echo "Invoices in date range ($startDate to $endDate): " . $filtered->count() . "\n";

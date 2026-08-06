<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixInvoiceItemNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:invoice-item-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores original test names for invoice items where test_name was overwritten during edit.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting invoice item names repair...');

        // Find how many items need fixing
        $affectedCount = DB::table('invoice_items')
            ->join('lab_tests', 'invoice_items.lab_test_id', '=', 'lab_tests.id')
            ->whereNotNull('invoice_items.lab_test_id')
            ->whereRaw('invoice_items.test_name != lab_tests.name')
            ->count();

        if ($affectedCount === 0) {
            $this->info('No corrupted invoice items found. Everything is already correct!');
            return 0;
        }

        $this->info("Found {$affectedCount} corrupted invoice item(s). Repairing...");

        // Safely update test_name to match master lab_tests.name
        $updated = DB::affectingStatement("
            UPDATE invoice_items
            JOIN lab_tests ON invoice_items.lab_test_id = lab_tests.id
            SET invoice_items.test_name = lab_tests.name
            WHERE invoice_items.lab_test_id IS NOT NULL
              AND invoice_items.test_name != lab_tests.name
        ");

        $this->info("Successfully restored original test names for {$updated} item(s)!");

        return 0;
    }
}

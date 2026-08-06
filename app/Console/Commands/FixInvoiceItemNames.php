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
    protected $signature = 'fix:invoice-item-names {--apply : Actually perform the update in the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Previews or restores original test names for corrupted invoice items.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apply = $this->option('apply');

        $this->info($apply ? 'Executing invoice item names repair...' : 'Previewing invoice item names to be fixed (Dry-Run Mode)...');
        $this->newLine();

        // Fetch affected items with invoice number and patient details for clear preview
        $affectedItems = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('lab_tests', 'invoice_items.lab_test_id', '=', 'lab_tests.id')
            ->whereNotNull('invoice_items.lab_test_id')
            ->whereRaw('invoice_items.test_name != lab_tests.name')
            ->select([
                'invoice_items.id as item_id',
                'invoices.invoice_number',
                'invoice_items.test_name as current_name',
                'lab_tests.name as correct_name',
            ])
            ->get();

        if ($affectedItems->isEmpty()) {
            $this->info('✅ No corrupted invoice items found. All test names are already correct!');
            return 0;
        }

        $headers = ['Item ID', 'Invoice No', 'Current (Corrupted) Name', 'Will Be Changed To'];
        $rows = [];

        foreach ($affectedItems as $item) {
            $rows[] = [
                $item->item_id,
                $item->invoice_number,
                $item->current_name,
                $item->correct_name,
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
        $this->info("Total affected items found: " . $affectedItems->count());

        if (!$apply) {
            $this->newLine();
            $this->warn('⚠️ THIS WAS JUST A PREVIEW (NO CHANGES WERE MADE TO THE DATABASE).');
            $this->info('To actually apply these changes, run:');
            $this->comment('php artisan fix:invoice-item-names --apply');
        } else {
            $updated = DB::affectingStatement("
                UPDATE invoice_items
                JOIN lab_tests ON invoice_items.lab_test_id = lab_tests.id
                SET invoice_items.test_name = lab_tests.name
                WHERE invoice_items.lab_test_id IS NOT NULL
                  AND invoice_items.test_name != lab_tests.name
            ");

            $this->newLine();
            $this->info("🎉 SUCCESS! Successfully restored original test names for {$updated} item(s).");
        }

        return 0;
    }
}

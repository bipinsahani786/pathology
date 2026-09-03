<?php

namespace App\Services;

use App\Models\InventoryBatch;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\LabTest;
use App\Models\TestReport;
use App\Models\Configuration;
use Illuminate\Support\Facades\Log;

class InventoryDeductionService
{
    /**
     * Auto-deduct inventory for an approved report.
     * If the report was previously deducted (e.g. tests were edited/re-approved),
     * old deductions are automatically reversed first before applying the new deduction.
     *
     * @param TestReport $report
     * @param int $branchId
     * @return array ['deducted' => [...], 'warnings' => [...], 'reverted' => [...]]
     */
    public function deductForReport(TestReport $report, int $branchId): array
    {
        $result = ['deducted' => [], 'warnings' => [], 'reverted' => []];

        // 1. If previously deducted, revert old deductions first to keep stock 100% accurate
        $reverted = $this->revertForReport($report);
        if (!empty($reverted)) {
            $result['reverted'] = $reverted;
        }

        // 2. Load setting: allow negative stock?
        $companyId = $report->company_id;
        $allowNegative = Configuration::getFor('inventory_allow_negative_stock', '0', $companyId, 'global') === '1';

        // 3. Load invoice items with lab tests and consumables
        $invoice = $report->invoice;
        if (!$invoice) {
            return $result;
        }

        $invoice->load('items.labTest.consumables.inventoryItem');

        // 4. Aggregate all consumables needed across all tests in this invoice
        // Key: inventory_item_id => ['item' => InventoryItem, 'qty' => float, 'tests' => []]
        $consumablesNeeded = [];

        foreach ($invoice->items as $item) {
            if (!$item->labTest) {
                continue;
            }

            // Gather all tests to inspect (handles standalone tests and packages)
            $testsToProcess = collect();

            // If it's a package, check both package's own consumables and linked tests' consumables
            if ($item->labTest->is_package && !empty($item->labTest->linked_test_ids)) {
                $linkedTests = LabTest::with('consumables.inventoryItem')
                    ->whereIn('id', $item->labTest->linked_test_ids)
                    ->get();
                $testsToProcess = $testsToProcess->merge($linkedTests);

                // If the package master itself has consumables mapped, include it too
                if ($item->labTest->consumables->isNotEmpty()) {
                    $testsToProcess->push($item->labTest);
                }
            } else {
                $testsToProcess->push($item->labTest);
            }

            foreach ($testsToProcess as $test) {
                if ($test->consumables->isEmpty()) {
                    continue; // No consumables mapped for this test
                }

                foreach ($test->consumables as $consumable) {
                    $invItem = $consumable->inventoryItem;
                    if (!$invItem) {
                        continue;
                    }

                    $qtyPerTest = (float) $consumable->quantity_per_test;
                    if ($qtyPerTest <= 0) {
                        continue;
                    }

                    $itemId = $invItem->id;
                    if (!isset($consumablesNeeded[$itemId])) {
                        $consumablesNeeded[$itemId] = [
                            'item'  => $invItem,
                            'qty'   => 0,
                            'tests' => [],
                        ];
                    }

                    $consumablesNeeded[$itemId]['qty'] += $qtyPerTest;
                    $consumablesNeeded[$itemId]['tests'][] = $test->name;
                }
            }
        }

        if (empty($consumablesNeeded)) {
            // No consumables mapped on any of the tests
            $report->update(['inventory_deducted' => false]);
            return $result;
        }

        // 5. Perform deduction for each aggregated inventory item
        foreach ($consumablesNeeded as $itemId => $data) {
            $invItem = $data['item'];
            $qtyNeeded = (float) $data['qty'];
            $testList = implode(', ', array_unique($data['tests']));

            // Find stock for this branch
            $stock = InventoryStock::where('branch_id', $branchId)
                ->where('item_id', $invItem->id)
                ->first();

            if (!$stock) {
                $result['warnings'][] = "{$invItem->name}: No stock record found for branch. Skipped.";
                continue;
            }

            $availableQty = (float) $stock->quantity;

            if ($availableQty <= 0 && !$allowNegative) {
                $result['warnings'][] = "{$invItem->name}: Stock is 0. Needed {$qtyNeeded} {$invItem->unit} for ({$testList}). Skipped.";
                continue;
            }

            if ($availableQty < $qtyNeeded && !$allowNegative) {
                // Deduct only what is available
                $actualDeduct = $availableQty;
                $result['warnings'][] = "{$invItem->name}: Needed {$qtyNeeded} {$invItem->unit} but only {$availableQty} available. Deducted {$actualDeduct}.";
            } else {
                $actualDeduct = $qtyNeeded;
            }

            // Deduct from stock total
            $stock->decrement('quantity', $actualDeduct);

            // Deduct from batches using FIFO (earliest expiry first)
            $deductedBatch = $this->deductFromBatches($stock, $actualDeduct);

            // Log the transaction
            InventoryTransaction::create([
                'branch_id'       => $branchId,
                'item_id'         => $invItem->id,
                'batch_id'        => $deductedBatch?->id,
                'type'            => 'out',
                'quantity'        => $actualDeduct,
                'source'          => 'report_consumption',
                'reference_id'    => $report->id,
                'performed_by_id' => auth()->id() ?? $report->approved_by,
                'remarks'         => "Auto-deducted for Report #{$report->id} (Tests: {$testList})",
            ]);

            $result['deducted'][] = [
                'item'     => $invItem->name,
                'quantity' => $actualDeduct,
                'unit'     => $invItem->unit,
                'tests'    => $testList,
            ];
        }

        // Mark report as deducted
        $report->update(['inventory_deducted' => true]);

        return $result;
    }

    /**
     * Revert any previously deducted inventory for a given report.
     * Restores stock and batch quantities.
     *
     * @param TestReport $report
     * @return array List of reverted items
     */
    public function revertForReport(TestReport $report): array
    {
        $reverted = [];

        $transactions = InventoryTransaction::where('source', 'report_consumption')
            ->where('reference_id', $report->id)
            ->get();

        foreach ($transactions as $txn) {
            $qty = (float) $txn->quantity;
            if ($qty <= 0) {
                $txn->delete();
                continue;
            }

            // Find stock and restore quantity
            $stock = InventoryStock::where('branch_id', $txn->branch_id)
                ->where('item_id', $txn->item_id)
                ->first();

            if ($stock) {
                $stock->increment('quantity', $qty);

                // Restore batch quantity if batch exists
                $batch = $txn->batch;
                if (!$batch) {
                    // Fallback to earliest active batch or latest created batch
                    $batch = $stock->batches()->orderBy('expiry_date', 'asc')->first();
                }

                if ($batch) {
                    $batch->increment('quantity', $qty);
                }
            }

            $itemName = $txn->item?->name ?? "Item #{$txn->item_id}";
            $reverted[] = [
                'item'     => $itemName,
                'quantity' => $qty,
            ];

            // Delete the old consumption transaction
            $txn->delete();
        }

        $report->update(['inventory_deducted' => false]);

        return $reverted;
    }

    /**
     * Deduct quantity from batches using FIFO (earliest expiry first).
     * Returns the primary batch deducted from.
     */
    private function deductFromBatches(InventoryStock $stock, float $quantity): ?InventoryBatch
    {
        $remaining = $quantity;
        $batches = $stock->batches()
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        $firstBatch = null;

        foreach ($batches as $batch) {
            if (!$firstBatch) {
                $firstBatch = $batch;
            }

            if ($remaining <= 0) {
                break;
            }

            if ($batch->quantity >= $remaining) {
                $batch->decrement('quantity', $remaining);
                $remaining = 0;
            } else {
                $remaining -= $batch->quantity;
                $batch->update(['quantity' => 0]);
            }
        }

        return $firstBatch;
    }
}

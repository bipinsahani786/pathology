<?php

namespace App\Livewire\Lab\Inventory;

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\InventorySupplier;
use App\Models\InventoryTransaction;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // View: 'list' | 'form' | 'detail'
    public string $view = 'list';

    // Form fields
    public $supplier_id;
    public $remarks;
    public array $lines = [];

    // Detail / Edit
    public ?string $selectedGroupId = null;
    public array $editLines = [];   // for editing batch metadata
    public $editRemarks;
    public $editSupplierId;

    public string $searchTerm = '';

    public function mount()
    {
        $this->addLine();
    }

    // ─── LIST ────────────────────────────────────────────────────────────────

    public function showForm()
    {
        $this->view = 'form';
        $this->reset(['supplier_id', 'remarks']);
        $this->lines = [];
        $this->addLine();
        $this->resetValidation();
    }

    public function showList()
    {
        $this->view = 'list';
        $this->selectedGroupId = null;
        $this->resetPage();
    }

    public function viewDetail(string $groupId)
    {
        $this->selectedGroupId = $groupId;

        // Load transactions for this group
        $txns = InventoryTransaction::with(['item', 'supplier', 'batch'])
            ->where('purchase_group_id', $groupId)
            ->get();

        $first = $txns->first();
        $this->editRemarks  = $first ? $first->remarks : '';
        $this->editSupplierId = $first ? $first->reference_id : '';

        // Load batch edit lines
        $this->editLines = $txns->map(function ($txn) {
            // Find batch: either direct relation or fallback lookup
            $batch = $txn->batch;
            if (!$batch) {
                $stock = InventoryStock::where('item_id', $txn->item_id)
                    ->where('branch_id', $txn->branch_id)
                    ->first();

                $batch = $stock ? ($stock->batches()
                    ->where('created_at', '>=', $txn->created_at->copy()->subSeconds(30))
                    ->where('created_at', '<=', $txn->created_at->copy()->addSeconds(30))
                    ->first() ?? $stock->batches()
                    ->whereDate('created_at', $txn->created_at->toDateString())
                    ->orderBy('created_at', 'desc')
                    ->first()) : null;

                if ($batch && !$txn->batch_id) {
                    $txn->update(['batch_id' => $batch->id]);
                }
            }

            return [
                'txn_id'         => $txn->id,
                'item_name'      => $txn->item->name ?? '-',
                'unit'           => $txn->item->unit ?? '',
                'quantity'       => (float) $txn->quantity,
                'batch_id'       => $batch?->id,
                'batch_number'   => $batch?->batch_number ?? '',
                'expiry_date'    => $batch?->expiry_date ? $batch->expiry_date->format('Y-m-d') : '',
                'purchase_price' => $batch?->purchase_price ?? 0,
                'mrp'            => $batch?->mrp ?? 0,
            ];
        })->toArray();

        $this->view = 'detail';
    }

    public function saveEdit()
    {
        $this->validate([
            'editLines.*.quantity'       => 'required|numeric|min:0.01',
            'editLines.*.batch_number'   => 'nullable|string|max:100',
            'editLines.*.expiry_date'    => 'nullable|date',
            'editLines.*.purchase_price' => 'nullable|numeric|min:0',
            'editLines.*.mrp'            => 'nullable|numeric|min:0',
        ], [
            'editLines.*.quantity.required' => 'Quantity is required.',
            'editLines.*.quantity.min'      => 'Quantity must be at least 0.01.',
        ]);

        $companyId = auth()->user()->company_id;
        $allowNegative = \App\Models\Configuration::getFor('inventory_allow_negative_stock', '0', $companyId, 'global') === '1';

        // Pre-validate stock availability if quantity is being reduced
        foreach ($this->editLines as $ei => $line) {
            $txn = InventoryTransaction::find($line['txn_id']);
            if (!$txn) continue;

            $oldQty = (float) $txn->quantity;
            $newQty = (float) $line['quantity'];
            $qtyDiff = $newQty - $oldQty;

            if ($qtyDiff < 0) {
                $reduction = abs($qtyDiff);
                $stock = InventoryStock::where('branch_id', $txn->branch_id)->where('item_id', $txn->item_id)->first();
                $currentStock = $stock ? (float) $stock->quantity : 0;

                if ($currentStock < $reduction && !$allowNegative) {
                    $this->addError("editLines.{$ei}.quantity", "Cannot reduce quantity by {$reduction}. Current available stock is {$currentStock} {$line['unit']}.");
                    return;
                }
            }
        }

        // Apply changes
        foreach ($this->editLines as $ei => $line) {
            $txn = InventoryTransaction::find($line['txn_id']);
            if (!$txn) continue;

            $oldQty = (float) $txn->quantity;
            $newQty = (float) $line['quantity'];
            $qtyDiff = $newQty - $oldQty;

            // 1. Adjust Branch Stock if quantity changed
            if ($qtyDiff != 0) {
                $stock = InventoryStock::firstOrCreate(
                    ['branch_id' => $txn->branch_id, 'item_id' => $txn->item_id],
                    ['quantity' => 0]
                );

                if ($qtyDiff > 0) {
                    $stock->increment('quantity', $qtyDiff);
                } else {
                    $stock->decrement('quantity', abs($qtyDiff));
                }

                $txn->update(['quantity' => $newQty]);
            }

            // 2. Adjust Batch
            $batch = null;
            if (!empty($line['batch_id'])) {
                $batch = InventoryBatch::find($line['batch_id']);
            }
            if (!$batch && $txn->batch_id) {
                $batch = InventoryBatch::find($txn->batch_id);
            }

            if ($batch) {
                $purchasePrice = is_numeric($line['purchase_price'] ?? null) ? (float) $line['purchase_price'] : 0;
                $mrp = is_numeric($line['mrp'] ?? null) ? (float) $line['mrp'] : 0;

                $newBatchQty = (float) $batch->quantity;
                if ($qtyDiff != 0) {
                    $newBatchQty = max(0, $newBatchQty + $qtyDiff);
                }

                $batch->update([
                    'quantity'       => $newBatchQty,
                    'batch_number'   => !empty($line['batch_number']) ? trim($line['batch_number']) : null,
                    'expiry_date'    => !empty($line['expiry_date']) ? $line['expiry_date'] : null,
                    'purchase_price' => $purchasePrice,
                    'mrp'            => $mrp,
                ]);
            }
        }

        // 3. Update remarks
        InventoryTransaction::where('purchase_group_id', $this->selectedGroupId)
            ->update(['remarks' => $this->editRemarks]);

        $this->dispatch('notify', [
            'type'    => 'success',
            'message' => 'Purchase details & stock adjusted successfully.',
        ]);

        // Refresh details
        $this->viewDetail($this->selectedGroupId);
    }

    // ─── ADD FORM ────────────────────────────────────────────────────────────

    public function addLine()
    {
        $this->lines[] = [
            'item_id'        => '',
            'quantity'       => '',
            'batch_number'   => '',
            'expiry_date'    => '',
            'purchase_price' => '',
            'mrp'            => '',
        ];
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        if (empty($this->lines)) {
            $this->addLine();
        }
    }

    public function store()
    {
        $this->validate([
            'supplier_id'            => 'required|exists:inventory_suppliers,id',
            'lines'                  => 'required|array|min:1',
            'lines.*.item_id'        => 'required|exists:inventory_items,id',
            'lines.*.quantity'       => 'required|numeric|min:0.01',
            'lines.*.purchase_price' => 'nullable|numeric|min:0',
            'lines.*.mrp'            => 'nullable|numeric|min:0',
        ], [
            'lines.*.item_id.required'  => 'Please select an item for each row.',
            'lines.*.quantity.required' => 'Quantity is required for each item.',
            'lines.*.quantity.min'      => 'Quantity must be greater than 0.',
        ]);

        $branchId  = auth()->user()->branch_id;
        $supplier  = InventorySupplier::find($this->supplier_id);
        $groupId   = (string) Str::uuid(); // unique ID for this purchase session

        foreach ($this->lines as $line) {
            $stock = InventoryStock::firstOrCreate(
                ['branch_id' => $branchId, 'item_id' => $line['item_id']],
                ['quantity'  => 0]
            );

            $stock->increment('quantity', (float) $line['quantity']);

            $purchasePrice = is_numeric($line['purchase_price'] ?? null) ? (float) $line['purchase_price'] : 0;
            $mrp = is_numeric($line['mrp'] ?? null) ? (float) $line['mrp'] : 0;

            $batch = InventoryBatch::create([
                'inventory_stock_id' => $stock->id,
                'batch_number'       => !empty($line['batch_number']) ? trim($line['batch_number']) : null,
                'expiry_date'        => !empty($line['expiry_date']) ? $line['expiry_date'] : null,
                'quantity'           => (float) $line['quantity'],
                'purchase_price'     => $purchasePrice,
                'mrp'                => $mrp,
            ]);

            InventoryTransaction::create([
                'branch_id'         => $branchId,
                'item_id'           => $line['item_id'],
                'batch_id'          => $batch->id,
                'type'              => 'in',
                'quantity'          => (float) $line['quantity'],
                'source'            => 'purchase',
                'reference_id'      => $this->supplier_id,
                'performed_by_id'   => auth()->id(),
                'remarks'           => $this->remarks,
                'purchase_group_id' => $groupId,
            ]);
        }

        $itemCount = count($this->lines);
        $this->dispatch('notify', [
            'type'    => 'success',
            'message' => "Stock received! {$itemCount} item(s) added from {$supplier->name}.",
        ]);

        $this->view = 'list';
        $this->resetPage();
    }

    // ─── RENDER ──────────────────────────────────────────────────────────────

    public function render()
    {
        $suppliers = InventorySupplier::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)->get();

        $items = InventoryItem::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)->orderBy('name')->get();

        // Purchase history: group by purchase_group_id
        $branchId = auth()->user()->branch_id;

        $purchasesQuery = InventoryTransaction::with(['supplier', 'performedBy'])
            ->where('branch_id', $branchId)
            ->where('source', 'purchase')
            ->whereNotNull('purchase_group_id');

        if ($this->searchTerm) {
            $purchasesQuery->whereHas('supplier', function ($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%');
            });
        }

        // Get one representative row per group (latest per group)
        $allGrouped = $purchasesQuery
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('purchase_group_id')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'group_id'       => $first->purchase_group_id,
                    'supplier'       => $first->supplier,
                    'items_count'    => $group->count(),
                    'total_qty'      => $group->sum('quantity'),
                    'performed_by'   => $first->performedBy,
                    'remarks'        => $first->remarks,
                    'created_at'     => $first->created_at,
                ];
            })
            ->values();

        // Manual pagination
        $page       = $this->getPage();
        $perPage    = 15;
        $total      = $allGrouped->count();
        $purchases  = $allGrouped->forPage($page, $perPage);

        // Detail view data
        $detailTransactions = null;
        if ($this->view === 'detail' && $this->selectedGroupId) {
            $detailTransactions = InventoryTransaction::with(['item', 'supplier'])
                ->where('purchase_group_id', $this->selectedGroupId)
                ->get();
        }

        return view('livewire.lab.inventory.purchase-manager', [
            'suppliers'          => $suppliers,
            'items'              => $items,
            'purchases'          => $purchases,
            'totalPurchases'     => $total,
            'perPage'            => $perPage,
            'detailTransactions' => $detailTransactions,
        ])->layout('layouts.app');
    }
}

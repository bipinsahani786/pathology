<?php

namespace App\Livewire\Lab\Inventory;

use App\Models\InventorySupplier;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use Livewire\Component;

class PurchaseManager extends Component
{
    public $supplier_id, $item_id, $quantity, $batch_number, $expiry_date, $purchase_price, $mrp;
    public $remarks;

    public function render()
    {
        $suppliers = InventorySupplier::where('company_id', auth()->user()->company_id)->where('is_active', true)->get();
        $items = InventoryItem::where('company_id', auth()->user()->company_id)->where('is_active', true)->get();

        return view('livewire.lab.inventory.purchase-manager', [
            'suppliers' => $suppliers,
            'items' => $items
        ])->layout('layouts.app');
    }

    public function store()
    {
        $this->validate([
            'supplier_id' => 'required|exists:inventory_suppliers,id',
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'purchase_price' => 'nullable|numeric|min:0',
        ]);

        $branchId = auth()->user()->branch_id;

        // Find or create stock record for this branch
        $stock = InventoryStock::firstOrCreate(
            ['branch_id' => $branchId, 'item_id' => $this->item_id],
            ['quantity' => 0]
        );

        // Update total quantity
        $stock->increment('quantity', $this->quantity);

        // Create Batch
        InventoryBatch::create([
            'inventory_stock_id' => $stock->id,
            'batch_number' => $this->batch_number,
            'expiry_date' => $this->expiry_date,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchase_price ?? 0,
            'mrp' => $this->mrp ?? 0,
        ]);

        // Log Transaction
        InventoryTransaction::create([
            'branch_id' => $branchId,
            'item_id' => $this->item_id,
            'type' => 'in',
            'quantity' => $this->quantity,
            'source' => 'purchase',
            'reference_id' => $this->supplier_id, // Store supplier ID as reference for now
            'performed_by_id' => auth()->id(),
            'remarks' => "Purchased from " . InventorySupplier::find($this->supplier_id)->name . ". " . $this->remarks,
        ]);

        session()->flash('success', 'Stock received successfully.');
        $this->reset(['item_id', 'quantity', 'batch_number', 'expiry_date', 'purchase_price', 'mrp', 'remarks']);
    }
}

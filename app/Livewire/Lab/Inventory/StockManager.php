<?php

namespace App\Livewire\Lab\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response;

class StockManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';
    public $selectedItem;
    public $isAdjustmentModalOpen = false;
    
    // Adjustment fields
    public $adjustment_type = 'in'; // in, out
    public $adjustment_quantity;
    public $adjustment_remarks;
    public $adjustment_batch_number;
    public $adjustment_expiry_date;

    public function render()
    {
        $branchId = auth()->user()->branch_id;
        
        $stocks = InventoryStock::with(['item', 'batches'])
            ->where('branch_id', $branchId)
            ->whereHas('item', function($query) {
                $query->where('name', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->paginate(15);

        return view('livewire.lab.inventory.stock-manager', [
            'stocks' => $stocks
        ])->layout('layouts.app');
    }

    public function openAdjustment($stockId)
    {
        $this->selectedItem = InventoryStock::with('item')->findOrFail($stockId);
        $this->resetAdjustmentFields();
        $this->isAdjustmentModalOpen = true;
    }

    public function resetAdjustmentFields()
    {
        $this->reset(['adjustment_type', 'adjustment_quantity', 'adjustment_remarks', 'adjustment_batch_number', 'adjustment_expiry_date']);
        $this->resetValidation();
    }

    public function storeAdjustment()
    {
        $this->validate([
            'adjustment_quantity' => 'required|numeric|min:0.01',
            'adjustment_type' => 'required|in:in,out',
            'adjustment_remarks' => 'required|string|max:255',
        ]);

        $stock = $this->selectedItem;
        
        // Update total quantity
        if ($this->adjustment_type == 'in') {
            $stock->increment('quantity', $this->adjustment_quantity);
            
            // Create a batch if provided
            if ($this->adjustment_batch_number) {
                InventoryBatch::create([
                    'inventory_stock_id' => $stock->id,
                    'batch_number' => $this->adjustment_batch_number,
                    'expiry_date' => $this->adjustment_expiry_date,
                    'quantity' => $this->adjustment_quantity,
                ]);
            }
        } else {
            if ($stock->quantity < $this->adjustment_quantity) {
                $this->addError('adjustment_quantity', 'Insufficient stock.');
                return;
            }
            $stock->decrement('quantity', $this->adjustment_quantity);
            
            // Deduct from batches (FIFO simple logic for now)
            $remainingToDeduct = $this->adjustment_quantity;
            $batches = $stock->batches()->where('quantity', '>', 0)->orderBy('expiry_date', 'asc')->get();
            
            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) break;
                
                if ($batch->quantity >= $remainingToDeduct) {
                    $batch->decrement('quantity', $remainingToDeduct);
                    $remainingToDeduct = 0;
                } else {
                    $remainingToDeduct -= $batch->quantity;
                    $batch->update(['quantity' => 0]);
                }
            }
        }

        // Log Transaction
        InventoryTransaction::create([
            'branch_id' => auth()->user()->branch_id,
            'item_id' => $stock->item_id,
            'type' => $this->adjustment_type,
            'quantity' => $this->adjustment_quantity,
            'source' => 'adjustment',
            'performed_by_id' => auth()->id(),
            'remarks' => $this->adjustment_remarks,
        ]);

        session()->flash('success', 'Stock adjusted successfully.');
        $this->isAdjustmentModalOpen = false;
    }

    public function exportStock()
    {
        $branchId = auth()->user()->branch_id;
        $stocks = InventoryStock::with(['item.category'])
            ->where('branch_id', $branchId)
            ->get();

        $csvFileName = 'inventory_stock_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Item Name', 'Category', 'Unit', 'Current Quantity', 'Min Stock Level', 'Status'];

        $callback = function() use($stocks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($stocks as $stock) {
                $status = ($stock->quantity <= $stock->item->min_stock_level) ? 'Low Stock' : 'In Stock';
                
                fputcsv($file, [
                    $stock->item->name,
                    $stock->item->category->name,
                    $stock->item->unit,
                    $stock->quantity,
                    $stock->item->min_stock_level,
                    $status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

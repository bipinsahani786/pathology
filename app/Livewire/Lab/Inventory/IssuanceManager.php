<?php

namespace App\Livewire\Lab\Inventory;

use App\Models\User;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class IssuanceManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $item_id, $issued_to_id, $quantity, $remarks;
    public $searchTerm = '';

    public function render()
    {
        $branchId = auth()->user()->branch_id;
        $companyId = auth()->user()->company_id;

        // Staff list for the company
        $staff = User::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Items with available stock in this branch
        $stocks = InventoryStock::with('item')
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->get();

        // History of issuances
        $history = InventoryTransaction::with(['item', 'issuedTo', 'performedBy'])
            ->where('branch_id', $branchId)
            ->where('source', 'consumption')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.lab.inventory.issuance-manager', [
            'staff' => $staff,
            'stocks' => $stocks,
            'history' => $history
        ])->layout('layouts.app');
    }

    public function store()
    {
        $this->validate([
            'item_id' => 'required',
            'issued_to_id' => 'required|exists:users,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $branchId = auth()->user()->branch_id;
        $stock = InventoryStock::where('branch_id', $branchId)->where('item_id', $this->item_id)->first();

        if (!$stock || $stock->quantity < $this->quantity) {
            $this->addError('quantity', 'Insufficient stock available.');
            return;
        }

        // Deduct stock
        $stock->decrement('quantity', $this->quantity);

        // Deduct from batches (FIFO)
        $remainingToDeduct = $this->quantity;
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

        // Log Transaction
        InventoryTransaction::create([
            'branch_id' => $branchId,
            'item_id' => $this->item_id,
            'type' => 'out',
            'quantity' => $this->quantity,
            'source' => 'consumption',
            'issued_to_id' => $this->issued_to_id,
            'performed_by_id' => auth()->id(),
            'remarks' => $this->remarks,
        ]);

        session()->flash('success', 'Items issued successfully.');
        $this->reset(['item_id', 'quantity', 'remarks']);
    }
}

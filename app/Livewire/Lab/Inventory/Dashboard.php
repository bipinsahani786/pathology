<?php

namespace App\Livewire\Lab\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function render()
    {
        $branchId = auth()->user()->branch_id;
        $companyId = auth()->user()->company_id;

        // 1. Total Items & Categories
        $stats = [
            'total_items' => InventoryItem::where('company_id', $companyId)->count(),
            'low_stock_count' => InventoryStock::where('branch_id', $branchId)
                ->whereHas('item', function($q) {
                    $q->whereColumn('inventory_stocks.quantity', '<=', 'inventory_items.min_stock_level');
                })->count(),
            'near_expiry_count' => InventoryBatch::whereHas('stock', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->where('quantity', '>', 0)
              ->where('expiry_date', '<=', now()->addDays(30))
              ->where('expiry_date', '>', now())
              ->count(),
            'expired_count' => InventoryBatch::whereHas('stock', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->where('quantity', '>', 0)
              ->where('expiry_date', '<=', now())
              ->count(),
        ];

        // 2. Low Stock Items
        $lowStockItems = InventoryStock::with('item')
            ->where('branch_id', $branchId)
            ->whereHas('item', function($q) {
                $q->whereColumn('inventory_stocks.quantity', '<=', 'inventory_items.min_stock_level');
            })
            ->take(10)
            ->get();

        // 3. Near Expiry Batches
        $nearExpiryBatches = InventoryBatch::with(['stock.item'])
            ->whereHas('stock', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('quantity', '>', 0)
            ->where('expiry_date', '<=', now()->addDays(60))
            ->orderBy('expiry_date', 'asc')
            ->take(10)
            ->get();

        // 4. Most Used Items (based on consumption transactions in last 30 days)
        $mostUsedItems = InventoryTransaction::select('item_id', DB::raw('SUM(quantity) as total_used'))
            ->where('branch_id', $branchId)
            ->where('source', 'consumption')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('item_id')
            ->orderByDesc('total_used')
            ->with('item')
            ->take(5)
            ->get();

        // 5. Recent Activity
        $recentActivity = InventoryTransaction::with(['item', 'performedBy'])
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('livewire.lab.inventory.dashboard', [
            'stats' => $stats,
            'lowStockItems' => $lowStockItems,
            'nearExpiryBatches' => $nearExpiryBatches,
            'mostUsedItems' => $mostUsedItems,
            'recentActivity' => $recentActivity,
        ])->layout('layouts.app');
    }
}

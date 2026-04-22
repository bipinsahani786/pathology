<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    protected $table = 'inventory_stocks';

    protected $fillable = [
        'branch_id',
        'item_id',
        'quantity',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class, 'inventory_stock_id');
    }

    /**
     * Get non-expired batches with quantity > 0
     */
    public function activeBatches()
    {
        return $this->batches()
            ->where('quantity', '>', 0)
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('expiry_date', 'asc');
    }
}

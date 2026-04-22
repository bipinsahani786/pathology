<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    protected $table = 'inventory_batches';

    protected $fillable = [
        'inventory_stock_id',
        'batch_number',
        'expiry_date',
        'quantity',
        'purchase_price',
        'mrp',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function stock()
    {
        return $this->belongsTo(InventoryStock::class, 'inventory_stock_id');
    }
}

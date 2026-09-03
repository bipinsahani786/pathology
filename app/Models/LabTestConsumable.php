<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTestConsumable extends Model
{
    protected $table = 'lab_test_consumables';

    protected $fillable = [
        'lab_test_id',
        'inventory_item_id',
        'quantity_per_test',
    ];

    protected $casts = [
        'quantity_per_test' => 'decimal:2',
    ];

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}

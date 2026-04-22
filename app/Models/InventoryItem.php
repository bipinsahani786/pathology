<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'unit',
        'min_stock_level',
        'description',
        'barcode',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(InventoryStock::class, 'item_id');
    }

    /**
     * Get stock for a specific branch
     */
    public function stockForBranch($branchId)
    {
        return $this->stocks()->where('branch_id', $branchId)->first();
    }
}

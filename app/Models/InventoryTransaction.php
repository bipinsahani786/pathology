<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use \App\Traits\Auditable;

    protected $table = 'inventory_transactions';

    protected $fillable = [
        'branch_id',
        'item_id',
        'batch_id',
        'type',
        'quantity',
        'source',
        'reference_id',
        'performed_by_id',
        'issued_to_id',
        'remarks',
        'purchase_group_id',
    ];

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }

    public function supplier()
    {
        return $this->belongsTo(InventorySupplier::class, 'reference_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }

    public function issuedTo()
    {
        return $this->belongsTo(User::class, 'issued_to_id');
    }
}

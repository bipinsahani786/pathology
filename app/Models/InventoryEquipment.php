<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryEquipment extends Model
{
    protected $table = 'inventory_equipment';

    protected $fillable = [
        'branch_id',
        'name',
        'model_number',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'last_service_date',
        'next_service_date',
        'last_calibration_date',
        'next_calibration_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'last_calibration_date' => 'date',
        'next_calibration_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

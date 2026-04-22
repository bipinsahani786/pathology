<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySupplier extends Model
{
    protected $table = 'inventory_suppliers';

    protected $fillable = [
        'company_id',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'gst_number',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

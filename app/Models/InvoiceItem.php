<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    
    protected $guarded = [];

    /**
     * The parent invoice this item belongs to.
     */
    public function invoice() 
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * The actual lab test or package master data.
     */
    public function labTest() 
    {
        return $this->belongsTo(LabTest::class);
    }
}

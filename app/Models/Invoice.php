<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use BelongsToCompany;
    // Allow all fields to be mass-assigned safely
    protected $guarded = []; 
    
    protected $casts = [
        'invoice_date' => 'datetime',
    ];

    /**
     * The collection center (branch) where this bill was generated.
     */
    public function collectionCenter() 
    {
        return $this->belongsTo(CollectionCenter::class);
    }

    /**
     * The patient (User) who this invoice belongs to.
     */
    public function patient() 
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * The staff member (User) who created this invoice.
     */
    public function creator() 
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The individual lab tests or packages included in this invoice.
     */
    public function items() 
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * The payment history/logs associated with this invoice.
     */
    public function payments() 
    {
        return $this->hasMany(Payment::class);
    }
    
    /**
     * The doctor who referred the patient for these tests (for commission tracking).
     */
    public function doctor() 
    {
        return $this->belongsTo(User::class, 'referred_by_doctor_id');
    }
}

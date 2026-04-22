<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToCompany, \App\Traits\Auditable;
    protected $guarded = [];

    /**
     * The invoice this payment is applied to.
     */
    public function invoice() 
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * The staff member (cashier/receptionist) who physically collected the money.
     */
    public function collectedBy() 
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * The method of payment used (e.g., Cash, UPI, Credit Card).
     */
    public function paymentMode() 
    {
        return $this->belongsTo(PaymentMode::class);
    }
}

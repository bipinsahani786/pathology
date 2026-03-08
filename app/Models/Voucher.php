<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'code', 'discount_type', 'discount_value', 
        'min_bill_amount', 'max_discount_amount', 'valid_from', 
        'valid_until', 'usage_limit', 'used_count', 'is_active'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_bill_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Helper logic to check if a voucher is currently valid
     */
    public function isValid()
    {
        if (!$this->is_active) return false;
        
        $today = now()->startOfDay();
        
        if ($this->valid_from && $today->lt($this->valid_from)) return false;
        if ($this->valid_until && $today->gt($this->valid_until)) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;

        return true;
    }
}

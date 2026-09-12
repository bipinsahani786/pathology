<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PhlebotomistCommissionSettlement extends Model
{
    use BelongsToCompany;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function phlebotomist()
    {
        return $this->belongsTo(User::class, 'phlebotomist_id');
    }

    public function settledBy()
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    public function homeCollections()
    {
        return $this->hasMany(HomeCollection::class, 'commission_settlement_id');
    }
}

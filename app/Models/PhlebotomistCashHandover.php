<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PhlebotomistCashHandover extends Model
{
    use \App\Traits\Auditable, BelongsToCompany;

    protected $guarded = [];

    protected $casts = [
        'amount'      => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function phlebotomist()
    {
        return $this->belongsTo(User::class, 'phlebotomist_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

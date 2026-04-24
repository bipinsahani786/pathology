<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesAgentPayout extends Model
{
    protected $fillable = [
        'sales_agent_id',
        'amount',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function salesAgent()
    {
        return $this->belongsTo(SalesAgent::class);
    }
}

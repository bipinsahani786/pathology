<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformInvoice extends Model
{
    protected $fillable = [
        'company_id',
        'plan_id',
        'amount',
        'status',
        'paid_at',
        'sales_agent_id',
        'agent_commission',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function salesAgent()
    {
        return $this->belongsTo(SalesAgent::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesAgent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'commission_rate',
        'status',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function platformInvoices()
    {
        return $this->hasMany(PlatformInvoice::class, 'sales_agent_id');
    }

    public function payouts()
    {
        return $this->hasMany(SalesAgentPayout::class, 'sales_agent_id');
    }

    public function getTotalEarningsAttribute()
    {
        return $this->platformInvoices()->where('status', 'paid')->sum('agent_commission');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payouts()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_earnings - $this->total_paid;
    }
}

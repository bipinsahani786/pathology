<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'status',
        'settings',
        'plan_id',
        'sales_agent_id',
        'referred_by',
        'trial_ends_at',
        'website',
        'gst_number',
        'tagline',
    ];

    protected $casts = [
        'settings' => 'array', // Automatically cast JSON to array
        'trial_ends_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function salesAgent()
    {
        return $this->belongsTo(SalesAgent::class);
    }
}

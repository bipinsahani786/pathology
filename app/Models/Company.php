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
        'api_key',
        'api_allowed_origin',
        'api_enabled',
    ];

    protected $casts = [
        'settings' => 'array', // Automatically cast JSON to array
        'trial_ends_at' => 'datetime',
        'api_enabled' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function salesAgent()
    {
        return $this->belongsTo(SalesAgent::class);
    }

    public function hasWebsiteApiFeature(): bool
    {
        return (bool) ($this->plan?->features['website_api'] ?? false);
    }

    public function webBookings()
    {
        return $this->hasMany(WebBooking::class);
    }
}

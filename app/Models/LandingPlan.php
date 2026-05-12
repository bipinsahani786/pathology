<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPlan extends Model
{
    protected $fillable = [
        'name',
        'price_text',
        'badge',
        'features',
        'cta_text',
        'cta_link',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

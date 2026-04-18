<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'name',
        'price',
        'duration_in_days',
        'features',
        'is_active',
        'landing_subtitle',
        'landing_features',
        'landing_badge',
        'landing_cta_text',
        'landing_sort_order',
        'show_on_landing',
    ];

    protected $casts = [
        'features' => 'array',
        'landing_features' => 'array',
        'is_active' => 'boolean',
        'show_on_landing' => 'boolean',
    ];

    public function scopeLanding($query)
    {
        return $query->where('is_active', true)
                     ->where('show_on_landing', true)
                     ->orderBy('landing_sort_order');
    }
}

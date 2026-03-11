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
    ];

    protected $casts = [
        'features' => 'array', // Automatically cast JSON to array
        'is_active' => 'boolean',
    ];
}

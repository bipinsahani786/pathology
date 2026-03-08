<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'name', 'price', 'discount_percentage', 
        'validity_days', 'color_code', 'description', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}

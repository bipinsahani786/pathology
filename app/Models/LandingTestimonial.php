<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingTestimonial extends Model
{
    protected $fillable = ['author_name', 'author_role', 'author_company', 'author_avatar', 'quote', 'rating', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

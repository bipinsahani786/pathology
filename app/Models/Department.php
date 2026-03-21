<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['company_id', 'name', 'is_active', 'is_system'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
                $builder->where(function ($query) {
                    $query->where('company_id', auth()->user()->company_id)
                          ->orWhere('is_system', true);
                });
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId)->orWhere('is_system', true);
    }

    public function globalTests()
    {
        return $this->hasMany(GlobalTest::class, 'department_id');
    }
}

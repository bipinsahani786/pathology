<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        // If the user is logged in and is not a super admin
        if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
            static::addGlobalScope('company', function (Builder $builder) {
                $builder->where('company_id', auth()->user()->company_id);
            });
        }
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
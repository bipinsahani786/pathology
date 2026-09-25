<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'contact_number',
        'address',
        'is_active',
    ];

    public function getPhoneAttribute()
    {
        return $this->contact_number;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

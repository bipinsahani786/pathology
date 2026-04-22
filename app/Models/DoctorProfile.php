<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use BelongsToCompany, \App\Traits\Auditable;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

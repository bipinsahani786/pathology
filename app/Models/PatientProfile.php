<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    use BelongsToCompany, \App\Traits\Auditable;
    protected $guarded = [];

    /**
     * The main user account associated with this medical profile.
     */
    public function user() 
    {
        return $this->belongsTo(User::class);
    }
}

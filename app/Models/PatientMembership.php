<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PatientMembership extends Model
{
    use BelongsToCompany;
    protected $table = 'patient_memberships';

    protected $fillable = [
        'patient_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
    ];

   

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    
}

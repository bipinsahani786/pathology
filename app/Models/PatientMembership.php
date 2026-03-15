<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PatientMembership extends Model
{
    use BelongsToCompany;
    protected $table = 'patient_memberships';

    protected $fillable = [
        'company_id',
        'patient_id',
        'membership_id',
        'amount_paid',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}

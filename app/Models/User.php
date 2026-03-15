<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'email_verified_at',
        'company_id',
        'branch_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the company (tenant) that owns the user.
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    /**
     * Get the branch that owns the user.
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    /**
     * Get the user details record associated with this user.
     */
    public function details()
    {
        return $this->hasOne(\App\Models\UserDetail::class, 'user_id');
    }

    // ==========================================
    // ROLE-BASED PROFILE RELATIONSHIPS
    // ==========================================

    /**
     * Get the patient profile associated with the user.
     */
    public function patientProfile() 
    {
        return $this->hasOne(PatientProfile::class);
    }

    /**
     * Get the doctor profile associated with the user.
     */
    public function doctorProfile() 
    {
        return $this->hasOne(DoctorProfile::class);
    }

    /**
     * Get the agent profile associated with the user.
     */
    public function agentProfile() 
    {
        return $this->hasOne(AgentProfile::class);
    }

    /**
     * Get the patient's membership records.
     */
    public function patientMemberships()
    {
        return $this->hasMany(PatientMembership::class, 'patient_id');
    }
}

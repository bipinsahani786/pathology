<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    use \App\Traits\Auditable, BelongsToCompany;

    protected $guarded = [];

    protected $appends = ['age_display', 'age_text'];

    /**
     * Get compact formatted age (e.g., "5 Y 6 M", "25 Y", "6 M", "12 D")
     */
    public function getAgeDisplayAttribute(): string
    {
        $years = $this->age_type === 'Years' ? (int) $this->age : 0;
        $months = $this->age_type === 'Months' ? (int) $this->age : (int) ($this->age_months ?? 0);
        $days = $this->age_type === 'Days' ? (int) $this->age : (int) ($this->age_days ?? 0);

        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' Y';
        }
        if ($months > 0) {
            $parts[] = $months . ' M';
        }
        if ($days > 0) {
            $parts[] = $days . ' D';
        }

        if (empty($parts)) {
            return ($this->age ?? 0) . ' ' . ($this->age_type === 'Months' ? 'M' : ($this->age_type === 'Days' ? 'D' : 'Y'));
        }

        return implode(' ', $parts);
    }

    /**
     * Get detailed formatted age text (e.g., "5 Yrs 6 Mos", "25 Yrs", "6 Mos", "12 Days")
     */
    public function getAgeTextAttribute(): string
    {
        $years = $this->age_type === 'Years' ? (int) $this->age : 0;
        $months = $this->age_type === 'Months' ? (int) $this->age : (int) ($this->age_months ?? 0);
        $days = $this->age_type === 'Days' ? (int) $this->age : (int) ($this->age_days ?? 0);

        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ($years === 1 ? ' Yr' : ' Yrs');
        }
        if ($months > 0) {
            $parts[] = $months . ($months === 1 ? ' Mo' : ' Mos');
        }
        if ($days > 0) {
            $parts[] = $days . ($days === 1 ? ' Day' : ' Days');
        }

        if (empty($parts)) {
            return ($this->age ?? 0) . ' ' . ($this->age_type ?? 'Yrs');
        }

        return implode(' ', $parts);
    }

    /**
     * The main user account associated with this medical profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

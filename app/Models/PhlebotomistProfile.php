<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PhlebotomistProfile extends Model
{
    use \App\Traits\Auditable, BelongsToCompany;

    protected $guarded = [];

    protected $casts = [
        'is_available'        => 'boolean',
        'commission_per_visit'=> 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this phlebotomist is free for the given date + time slot.
     */
    public function isAvailableForSlot(string $date, ?string $slotStart, ?string $slotEnd): bool
    {
        if (! $this->is_available) {
            return false;
        }

        if (! $slotStart) {
            return true; // No specific slot — assume available
        }

        return ! HomeCollection::where('phlebotomist_id', $this->user_id)
            ->where('scheduled_date', $date)
            ->whereNotIn('status', ['Cancelled'])
            ->where('scheduled_slot_start', $slotStart)
            ->exists();
    }
}


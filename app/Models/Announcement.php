<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_dismissible' => 'boolean',
        'priority'       => 'integer',
        'starts_at'      => 'datetime',
        'expires_at'     => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function dismissals()
    {
        return $this->hasMany(AnnouncementDismissal::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to only currently active and scheduled announcements.
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', $now);
            });
    }

    /**
     * Scope to exclude announcements that have been dismissed by the given user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereDoesntHave('dismissals', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // ==========================================
    // ACCESSORS & HELPERS
    // ==========================================

    /**
     * Get default feather icon based on type if custom icon not specified.
     */
    public function getResolvedIconAttribute(): string
    {
        if (! empty($this->icon)) {
            return $this->icon;
        }

        return match ($this->type) {
            'warning' => 'feather-alert-triangle',
            'danger'  => 'feather-alert-octagon',
            'success' => 'feather-check-circle',
            default   => 'feather-bell',
        };
    }

    /**
     * Get theme color styling tokens.
     */
    public function getThemeColorAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'warning',
            'danger'  => 'danger',
            'success' => 'success',
            default   => 'primary',
        };
    }

    /**
     * Check if announcement is currently expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

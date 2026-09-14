<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitStatusLog extends Model
{
    /**
     * Immutable log — only created_at, no updated_at.
     */
    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'latitude'   => 'decimal:7',
        'longitude'  => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (! $model->created_at) {
                $model->created_at = now();
            }
        });
    }

    public function homeCollection()
    {
        return $this->belongsTo(HomeCollection::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}


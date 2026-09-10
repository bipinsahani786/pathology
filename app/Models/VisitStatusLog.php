<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitStatusLog extends Model
{
    /**
     * Immutable log — only created_at, no updated_at.
     */
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'latitude'   => 'decimal:7',
        'longitude'  => 'decimal:7',
    ];

    public function homeCollection()
    {
        return $this->belongsTo(HomeCollection::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}


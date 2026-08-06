<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineResultLog extends Model
{
    protected $fillable = [
        'machine_integration_id',
        'company_id',
        'sample_id',
        'invoice_id',
        'raw_data',
        'parsed_data',
        'status',
        'error_message',
        'imported_at',
        'imported_by',
    ];

    protected $casts = [
        'parsed_data' => 'array',
        'imported_at' => 'datetime',
    ];

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────

    public function machine()
    {
        return $this->belongsTo(MachineIntegration::class, 'machine_integration_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeMatched($query)
    {
        return $query->where('status', 'matched');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    /**
     * Mark this log as imported.
     */
    public function markImported(int $userId): void
    {
        $this->update([
            'status'      => 'imported',
            'imported_at' => now(),
            'imported_by' => $userId,
        ]);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'matched'   => 'blue',
            'imported'  => 'green',
            'unmatched' => 'gray',
            'failed'    => 'red',
            'simulated' => 'purple',
            default     => 'gray',
        };
    }
}

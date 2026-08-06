<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MachineIntegration extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'branch_id',
        'name',
        'brand',
        'machine_type',
        'connection_type',
        'port_or_ip',
        'baud_rate',
        'tcp_port',
        'protocol',
        'test_mapping',
        'api_token',
        'is_active',
        'last_seen_at',
        'notes',
    ];

    protected $casts = [
        'test_mapping'   => 'array',
        'is_active'      => 'boolean',
        'last_seen_at'   => 'datetime',
        'baud_rate'      => 'integer',
        'tcp_port'       => 'integer',
    ];

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────

    public function resultLogs()
    {
        return $this->hasMany(MachineResultLog::class);
    }

    public function pendingLogs()
    {
        return $this->hasMany(MachineResultLog::class)->where('status', 'pending');
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    /**
     * Generate a fresh unique API token for Bridge Agent authentication.
     */
    public static function generateToken(): string
    {
        $prefix = 'mach_';
        return $prefix . Str::random(40);
    }

    /**
     * Check if this machine has been seen recently (within last 10 minutes).
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) <= 10;
    }

    /**
     * Get human-readable connection status.
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->last_seen_at) {
            return 'Never Connected';
        }
        if ($this->isOnline()) {
            return 'Online — ' . $this->last_seen_at->diffForHumans();
        }
        return 'Offline — last seen ' . $this->last_seen_at->diffForHumans();
    }

    /**
     * Get color badge for status.
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->last_seen_at) return 'gray';
        return $this->isOnline() ? 'green' : 'red';
    }

    /**
     * Map machine parameter name to our system's parameter name.
     * Falls back to the original name if no mapping defined.
     */
    public function mapParam(string $machineParamName): string
    {
        $mapping = $this->test_mapping ?? [];
        return $mapping[$machineParamName] ?? $machineParamName;
    }

    /**
     * Machine type label for display.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->machine_type) {
            'biochemistry' => '🔬 Biochemistry',
            'hematology'   => '🩸 Hematology (CBC)',
            'electrolyte'  => '⚗️ Electrolyte',
            default        => '🔧 Other',
        };
    }

    // ─────────────────────────────────────────
    // Boot
    // ─────────────────────────────────────────

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->api_token)) {
                $model->api_token = self::generateToken();
            }
        });
    }
}

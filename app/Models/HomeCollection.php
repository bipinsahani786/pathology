<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HomeCollection extends Model
{
    use BelongsToCompany;

    protected $guarded = [];

    protected $casts = [
        'scheduled_date'  => 'date',
        'assigned_at'     => 'datetime',
        'en_route_at'     => 'datetime',
        'arrived_at'      => 'datetime',
        'collected_at'    => 'datetime',
        'dispatched_at'   => 'datetime',
        'received_at'     => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function phlebotomist()
    {
        return $this->belongsTo(User::class, 'phlebotomist_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function commissionSettlement()
    {
        return $this->belongsTo(PhlebotomistCommissionSettlement::class, 'commission_settlement_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(VisitStatusLog::class)->orderBy('id', 'desc');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Google Maps link (lat/lng preferred, address fallback).
     */
    public function getGoogleMapsLinkAttribute(): string
    {
        if ($this->collection_lat && $this->collection_lng) {
            return "https://maps.google.com/?q={$this->collection_lat},{$this->collection_lng}";
        }

        return 'https://maps.google.com/?q=' . urlencode($this->collection_address ?? '');
    }

    /**
     * Google Maps turn-by-turn navigation link.
     */
    public function getNavigationLinkAttribute(): string
    {
        $dest = ($this->collection_lat && $this->collection_lng)
            ? "{$this->collection_lat},{$this->collection_lng}"
            : urlencode($this->collection_address ?? '');

        return "https://www.google.com/maps/dir/?api=1&destination={$dest}&travelmode=driving";
    }

    /**
     * Formatted time slot for display.
     */
    public function getScheduledSlotLabelAttribute(): string
    {
        if (! $this->scheduled_slot_start) {
            return $this->scheduled_date?->format('d M Y') ?? '';
        }

        $start = date('h:i A', strtotime($this->scheduled_slot_start));
        $end   = $this->scheduled_slot_end
            ? date('h:i A', strtotime($this->scheduled_slot_end))
            : '';

        return $this->scheduled_date?->format('d M Y') . ' ' . $start . ($end ? ' - ' . $end : '');
    }

    // ==========================================
    // BUSINESS LOGIC
    // ==========================================

    /**
     * Transition visit to a new status with full audit trail.
     * Syncs invoice sample_status automatically.
     */
    public function transitionStatus(
        string $newStatus,
        int $userId,
        ?float $lat = null,
        ?float $lng = null,
        ?string $notes = null
    ): void {
        $oldStatus = $this->status;

        // Timestamp fields mapped to status
        $timestampMap = [
            'Assigned'   => 'assigned_at',
            'En Route'   => 'en_route_at',
            'Arrived'    => 'arrived_at',
            'Collected'  => 'collected_at',
            'Dispatched' => 'dispatched_at',
            'Received'   => 'received_at',
        ];

        $updateData = ['status' => $newStatus];

        if (isset($timestampMap[$newStatus])) {
            $updateData[$timestampMap[$newStatus]] = now();
        }

        // Store GPS at collection point
        if ($lat && $lng && $newStatus === 'Collected') {
            $updateData['collected_lat'] = $lat;
            $updateData['collected_lng'] = $lng;
        }

        if ($newStatus === 'Cancelled') {
            $updateData['cancelled_by'] = $userId;
        }

        $this->update($updateData);

        // Audit log
        VisitStatusLog::create([
            'home_collection_id' => $this->id,
            'from_status'        => $oldStatus,
            'to_status'          => $newStatus,
            'changed_by'         => $userId,
            'latitude'           => $lat,
            'longitude'          => $lng,
            'notes'              => $notes,
        ]);

        // Sync invoice sample_status
        $sampleStatusMap = [
            'Collected'  => 'Sample Collected',
            'Dispatched' => 'Dispatched',
            'Received'   => 'Received',
        ];

        if (isset($sampleStatusMap[$newStatus])) {
            $this->invoice()->update([
                'sample_status'      => $sampleStatusMap[$newStatus],
                'sample_collected_at'=> $newStatus === 'Collected' ? now() : null,
            ]);
        }
    }
}


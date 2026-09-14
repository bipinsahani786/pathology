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
        'scheduled_date'        => 'date',
        'assigned_at'           => 'datetime',
        'en_route_at'           => 'datetime',
        'arrived_at'            => 'datetime',
        'collected_at'          => 'datetime',
        'dispatched_at'         => 'datetime',
        'received_at'           => 'datetime',
        'is_commission_settled' => 'boolean',
        'commission_amount'     => 'decimal:2',
        'commission_settled_at' => 'datetime',
        'collection_lat'        => 'decimal:7',
        'collection_lng'        => 'decimal:7',
        'collected_lat'         => 'decimal:7',
        'collected_lng'         => 'decimal:7',
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

    /**
     * Bootstrap badge color theme based on visit status.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Pending'    => 'warning',
            'Assigned'   => 'primary',
            'En Route'   => 'info',
            'Arrived'    => 'warning',
            'Collected'  => 'success',
            'Dispatched' => 'teal',
            'Received'   => 'success',
            'Cancelled'  => 'danger',
            default      => 'secondary',
        };
    }

    /**
     * Icon associated with current visit status.
     */
    public function getStatusBadgeIconAttribute(): string
    {
        return match ($this->status) {
            'Pending'    => 'feather-clock',
            'Assigned'   => 'feather-user-check',
            'En Route'   => 'feather-navigation',
            'Arrived'    => 'feather-map-pin',
            'Collected'  => 'feather-check-circle',
            'Dispatched' => 'feather-send',
            'Received'   => 'feather-package',
            'Cancelled'  => 'feather-x-circle',
            default      => 'feather-activity',
        };
    }

    /**
     * Patient-friendly headline for current status.
     */
    public function getPatientStatusHeadlineAttribute(): string
    {
        return match ($this->status) {
            'Pending'    => 'Order Placed — Phlebotomist Assignment in Progress',
            'Assigned'   => 'Phlebotomist Assigned — Scheduled for Your Visit',
            'En Route'   => 'Phlebotomist is On The Way to Your Address',
            'Arrived'    => 'Phlebotomist Has Arrived at Your Doorstep',
            'Collected'  => 'Sample Collected Successfully — Transferring to Lab',
            'Dispatched' => 'Sample Dispatched to Laboratory',
            'Received'   => 'Sample Received at Testing Lab — Processing Underway',
            'Cancelled'  => 'Home Collection Request Cancelled',
            default      => 'Home Collection Status: ' . $this->status,
        };
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
            'created_at'         => now(),
        ]);

        // Sync invoice sample_status & preserve timestamps
        $sampleStatusMap = [
            'Collected'  => 'Sample Collected',
            'Dispatched' => 'Dispatched',
            'Received'   => 'Received',
        ];

        if (isset($sampleStatusMap[$newStatus])) {
            $invoiceUpdate = [
                'sample_status' => $sampleStatusMap[$newStatus],
            ];

            if ($newStatus === 'Collected' && ! $this->invoice?->sample_collected_at) {
                $invoiceUpdate['sample_collected_at'] = now();
            }

            if ($newStatus === 'Received' && ! $this->invoice?->sample_received_at) {
                $invoiceUpdate['sample_received_at'] = now();
            }

            $this->invoice()->update($invoiceUpdate);
        }
    }
}


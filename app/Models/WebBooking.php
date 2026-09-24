<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebBooking extends Model
{
    use HasFactory;

    protected $table = 'web_bookings';

    protected $fillable = [
        'company_id',
        'branch_id',
        'booking_reference',
        'patient_name',
        'patient_phone',
        'patient_email',
        'patient_gender',
        'patient_age',
        'patient_age_unit',
        'collection_type',
        'collection_address',
        'preferred_date',
        'preferred_time_slot',
        'items',
        'subtotal',
        'discount',
        'total_amount',
        'status',
        'notes',
        'invoice_id',
        'source_ip',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public static function generateReference(): string
    {
        $datePart = now()->format('Ymd');
        $randomPart = strtoupper(substr(uniqid(), -5));
        return "WB-{$datePart}-{$randomPart}";
    }
}

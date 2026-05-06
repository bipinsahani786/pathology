<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TestReport extends Model
{
    use BelongsToCompany, \App\Traits\Auditable;

    protected $fillable = [
        'company_id',
        'invoice_id',
        'patient_id',
        'status',
        'pdf_path',
        'comments',
        'approved_by',
        'approved_at',
        'report_date',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'report_date' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function results()
    {
        return $this->hasMany(ReportResult::class);
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TestReport extends Model
{
    use \App\Traits\Auditable, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'invoice_id',
        'patient_id',
        'status',
        'pdf_path',
        'outsourced_pdf_path',
        'outsourced_lab_name',
        'outsourced_crop_top',
        'outsourced_crop_bottom',
        'comments',
        'approved_by',
        'approved_at',
        'report_date',
        'inventory_deducted',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'outsourced_crop_top' => 'integer',
        'outsourced_crop_bottom' => 'integer',
        'outsourced_pdf_path' => 'array',
        'report_date' => 'datetime',
        'inventory_deducted' => 'boolean',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportResult extends Model
{
    protected $fillable = [
        'test_report_id',
        'lab_test_id',
        'parameter_name',
        'short_code',
        'result_value',
        'status',
        'is_highlighted',
        'reference_range',
        'unit',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
    ];

    public function testReport()
    {
        return $this->belongsTo(TestReport::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }
}

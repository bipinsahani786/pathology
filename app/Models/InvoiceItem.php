<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_package' => 'boolean',
        'report_options' => 'array',
    ];

    /**
     * The parent invoice this item belongs to.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * The actual lab test or package master data.
     */
    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    /**
     * Check if this line item requires parameter result entry.
     */
    public function hasParameters(): bool
    {
        if (!$this->lab_test_id) {
            return false;
        }

        return $this->labTest ? $this->labTest->hasParameters() : false;
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use \App\Traits\Auditable, BelongsToCompany;

    protected $fillable = [
        'company_id', 'global_test_id', 'department_id', 'test_code', 'name', 'method', 'department',
        'mrp', 'b2b_price', 'sample_type', 'tat_hours', 'parameters', 'is_active', 'description', 'interpretation', 'is_package', 'linked_test_ids',
        'show_method_on_report', 'show_interpretation_on_report', 'show_note_on_report',
    ];

    protected $casts = [
        'department_id' => 'integer',
        'mrp' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'is_active' => 'boolean',
        'show_method_on_report' => 'boolean',
        'show_interpretation_on_report' => 'boolean',
        'show_note_on_report' => 'boolean',
        'parameters' => 'array',
        'is_package' => 'boolean',
        'linked_test_ids' => 'array',
    ];

    public function dept()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Check if the test has at least one valid parameter (excluding headings).
     * For packages, checks if any linked test has parameters.
     */
    public function hasParameters(): bool
    {
        if ($this->is_package) {
            if (empty($this->linked_test_ids) || !is_array($this->linked_test_ids)) {
                return false;
            }
            $linked = static::whereIn('id', $this->linked_test_ids)->get();
            foreach ($linked as $lt) {
                if ($lt->hasParameters()) {
                    return true;
                }
            }
            return false;
        }

        if (empty($this->parameters) || !is_array($this->parameters)) {
            return false;
        }

        foreach ($this->parameters as $p) {
            $inputType = is_array($p) ? ($p['input_type'] ?? 'numeric') : 'numeric';
            $paramName = is_array($p) ? trim($p['name'] ?? '') : trim((string) $p);
            if ($inputType !== 'heading' && $paramName !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Inventory consumables mapped to this test for auto-deduction.
     */
    public function consumables()
    {
        return $this->hasMany(LabTestConsumable::class);
    }

    protected static function booted()
    {
        static::saved(function ($test) {
            \Illuminate\Support\Facades\Cache::forget("company_tests_{$test->company_id}");
        });

        static::deleted(function ($test) {
            \Illuminate\Support\Facades\Cache::forget("company_tests_{$test->company_id}");
        });
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'global_test_id', 'department_id', 'test_code', 'name', 'department',
        'mrp', 'b2b_price', 'sample_type', 'tat_hours', 'parameters', 'is_active', 'description' , 'interpretation', 'is_package', 'linked_test_ids',
    ];

    protected $casts = [
        'department_id' => 'integer',
        'mrp' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'is_active' => 'boolean',
        'parameters' => 'array', 
        'is_package' => 'boolean',  
        'linked_test_ids' => 'array',     
    ];

    public function dept()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

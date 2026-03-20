<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalTest extends Model
{
   use HasFactory;

    protected $guarded = []; 

  
    protected $casts = [
        'default_parameters' => 'array',
        'is_active' => 'boolean',
        'department_id' => 'integer',
    ];

    public function dept()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

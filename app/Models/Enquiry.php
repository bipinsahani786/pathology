<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'lab_name', 'lab_city',
        'message', 'enquiry_type', 'status', 'admin_notes',
    ];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('enquiry_type', $type);
    }
}

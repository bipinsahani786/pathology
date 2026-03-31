<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CollectionCenter extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'name',
        'center_code',
        'address',
        'is_main_lab',
        'is_active',
    ];

    /**
     * Get the branch that owns the collection center.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user associated with the collection center.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php
 
namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use BelongsToCompany;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user who received this settlement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoices linked to this settlement.
     */
    public function invoices()
    {
        if ($this->type === 'Doctor') {
            return $this->hasMany(Invoice::class, 'doctor_settlement_id');
        } elseif ($this->type === 'Agent') {
            return $this->hasMany(Invoice::class, 'agent_settlement_id');
        } else {
            return $this->hasMany(Invoice::class, 'cc_settlement_id');
        }
    }
}

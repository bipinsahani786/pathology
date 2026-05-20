<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use \App\Traits\Auditable, BelongsToCompany;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'support_ticket_id');
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use BelongsToCompany;
    protected $guarded = [];
}

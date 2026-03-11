<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CollectionCenter extends Model
{
    use BelongsToCompany;
    protected $guarded = [];
}

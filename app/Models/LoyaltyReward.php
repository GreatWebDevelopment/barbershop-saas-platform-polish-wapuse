<?php

namespace App\Models;

use App\Models\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    use BelongsToShop;

    protected $guarded = [];
}

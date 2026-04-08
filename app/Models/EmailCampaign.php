<?php

namespace App\Models;

use App\Models\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use BelongsToShop;

    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}

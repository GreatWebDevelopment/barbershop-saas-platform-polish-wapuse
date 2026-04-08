<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = [
        'branding' => 'array',
        'settings' => 'array',
    ];

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}

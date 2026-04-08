<?php

namespace App\Models;

use App\Models\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceAddOn extends Model
{
    use BelongsToShop;

    protected $guarded = [];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_add_on_mappings');
    }
}

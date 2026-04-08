<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Shop;

trait BelongsToShop
{
    public static function bootBelongsToShop(): void
    {
        static::addGlobalScope('shop', function (Builder $builder) {
            if ($user = auth()->user()) {
                $builder->where($builder->getModel()->getTable() . '.shop_id', $user->shop_id);
            }
        });

        static::creating(function (Model $model) {
            if (! $model->shop_id && ($user = auth()->user())) {
                $model->shop_id = $user->shop_id;
            }
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}

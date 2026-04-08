<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QueueEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'called_at' => 'datetime',
        'service_started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(QueueNotification::class);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'called', 'in_service']);
    }

    public static function generateQueueNumber(int $shopId): string
    {
        $today = now()->format('Ymd');
        $count = static::where('shop_id', $shopId)
            ->whereDate('created_at', today())
            ->count();

        return strtoupper(substr(dechex($shopId), 0, 1)) . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}

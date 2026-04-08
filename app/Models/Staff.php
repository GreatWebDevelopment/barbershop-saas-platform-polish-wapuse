<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staff';
    protected $guarded = [];

    protected $casts = [
        'specialties' => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    public function currentQueueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class, 'current_queue_entry_id');
    }

    public function scopeOnDuty($query)
    {
        return $query->where('queue_status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('queue_status', 'active')->whereNull('current_queue_entry_id');
    }
}

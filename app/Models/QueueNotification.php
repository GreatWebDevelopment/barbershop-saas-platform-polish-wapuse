<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueNotification extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToShop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use BelongsToShop;

    protected $table = 'staff';
    protected $guarded = [];

    protected $casts = [
        'specialties' => 'array',
        'availability_schedule' => 'array',
    ];

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

    /**
     * Check if staff is available at a given datetime for a given duration.
     * Checks weekly schedule AND existing appointments.
     */
    public function isAvailableAt(Carbon $datetime, int $durationMinutes): bool
    {
        $schedule = $this->availability_schedule;
        if (! $schedule) {
            return false;
        }

        $dayName = strtolower($datetime->format('l'));
        $daySchedule = $schedule[$dayName] ?? null;

        if (! $daySchedule || empty($daySchedule['enabled'])) {
            return false;
        }

        $requestedStart = $datetime->format('H:i');
        $requestedEnd = $datetime->copy()->addMinutes($durationMinutes)->format('H:i');

        // Check if requested time falls within any working block
        $blocks = $daySchedule['blocks'] ?? [];
        $withinBlock = false;
        foreach ($blocks as $block) {
            if ($requestedStart >= $block['start'] && $requestedEnd <= $block['end']) {
                $withinBlock = true;
                break;
            }
        }

        if (! $withinBlock) {
            return false;
        }

        // Check for break conflicts
        $breaks = $daySchedule['breaks'] ?? [];
        foreach ($breaks as $break) {
            if ($requestedStart < $break['end'] && $requestedEnd > $break['start']) {
                return false;
            }
        }

        // Check for existing appointment conflicts (with 15 min buffer)
        $bufferMinutes = 15;
        $conflicting = $this->appointments()
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '<', $datetime->copy()->addMinutes($durationMinutes + $bufferMinutes))
            ->where('ends_at', '>', $datetime->copy()->subMinutes($bufferMinutes))
            ->exists();

        return ! $conflicting;
    }
}

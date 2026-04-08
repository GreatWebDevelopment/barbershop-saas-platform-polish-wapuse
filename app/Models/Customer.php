<?php

namespace App\Models;

use App\Models\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, BelongsToShop, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'last_visit_at' => 'datetime',
        'total_spent' => 'decimal:2',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_by_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Customer::class, 'referred_by_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function preferredStylist(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'preferred_stylist_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

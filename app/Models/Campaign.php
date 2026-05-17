<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED         = 'approved';
    const STATUS_LIVE             = 'live';
    const STATUS_COMPLETED        = 'completed';
    const STATUS_REJECTED         = 'rejected';

    const ARTWORK_FEE = 1300.00;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'objective',
        'date_from',
        'date_to',
        'artwork',
        'needs_admin_artwork',
        'artwork_fee',
        'total_impressions',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'date_from'           => 'date',
        'date_to'             => 'date',
        'needs_admin_artwork' => 'boolean',
        'artwork_fee'         => 'decimal:2',
        'total_impressions'   => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Number of days the campaign spans (inclusive). */
    public function getDaysAttribute(): int
    {
        return $this->date_from->diffInDays($this->date_to) + 1;
    }
}

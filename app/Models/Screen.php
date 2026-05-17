<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screen extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'screen_type',
        'width',
        'height',
        'daily_impressions',
        'description',
        'price_per_day',
        'min_booking_days',
        'rotation_duration',
        'active_days',
        'display_from',
        'display_to',
        'is_247',
        'blackout_dates',
        'street_address',
        'landmark',
        'latitude',
        'longitude',
        'district',
        'city',
        'photos',
        'cr_document',
        'municipality_permit',
        'approval_status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'active_days'  => 'array',
        'photos'       => 'array',
        'is_247'       => 'boolean',
        'reviewed_at'  => 'datetime',
        'price_per_day' => 'decimal:2',
        'width'        => 'decimal:2',
        'height'       => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

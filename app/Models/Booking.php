<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED         = 'approved';
    const STATUS_REJECTED         = 'rejected';
    const STATUS_LIVE             = 'live';
    const STATUS_COMPLETED        = 'completed';

    const COMMISSION_RATE = 0.09; // 9%

    protected $fillable = [
        'campaign_id',
        'screen_id',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'sale_price',
        'commission',
        'net_earned',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'sale_price'  => 'decimal:2',
        'commission'  => 'decimal:2',
        'net_earned'  => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function screen(): BelongsTo
    {
        return $this->belongsTo(Screen::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Calculate and set financial fields based on screen price and campaign days. */
    public static function calcFinancials(Screen $screen, int $days): array
    {
        $salePrice  = round($screen->price_per_day * $days, 2);
        $commission = round($salePrice * self::COMMISSION_RATE, 2);
        $netEarned  = round($salePrice - $commission, 2);

        return [
            'sale_price' => $salePrice,
            'commission' => $commission,
            'net_earned' => $netEarned,
        ];
    }
}

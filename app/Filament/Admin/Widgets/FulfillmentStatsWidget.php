<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use App\Models\Campaign;
use App\Models\WithdrawRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Scoped to the Fulfillment page only (getHeaderWidgets()) — kept out of the main
 * Dashboard's auto-discovered widget list via $isDiscovered = false.
 */
class FulfillmentStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $pendingCount = WithdrawRequest::where('status', WithdrawRequest::STATUS_PENDING)->count();

        // Platform's 10% commission across captured campaigns, plus artwork fees
        // (a platform-only service charge, never split with the screen-owning company).
        $commission = Booking::whereHas('campaign', fn ($q) => $q->where('payment_status', Campaign::PAYMENT_STATUS_PAID))
            ->sum('commission');
        $artworkFees = Campaign::where('payment_status', Campaign::PAYMENT_STATUS_PAID)->sum('artwork_fee');

        return [
            Stat::make('Pending Withdrawals', $pendingCount)
                ->description('Awaiting admin review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success'),

            Stat::make('Platform Net Earnings', number_format((float) $commission + (float) $artworkFees, 2) . ' SAR')
                ->description('10% commission + artwork fees, all-time')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}

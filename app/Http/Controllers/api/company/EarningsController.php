<?php

namespace App\Http\Controllers\api\company;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Campaign;
use App\Models\WithdrawRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    /**
     * GET /api/company/earnings
     *
     * Owner-facing earnings summary. All revenue figures are scoped to bookings on
     * this company's screens whose campaign payment has been captured (payment_status
     * = paid) — a booking existing doesn't mean the client actually paid yet.
     */
    public function index(Request $request): JsonResponse
    {
        $company = $request->user();

        $capturedBookings = fn () => Booking::whereHas('screen', fn ($q) => $q->where('company_id', $company->id))
            ->whereHas('campaign', fn ($q) => $q->where('payment_status', Campaign::PAYMENT_STATUS_PAID));

        $totalRevenue = $capturedBookings()->sum('sale_price');   // 100%
        $netEarnings  = $capturedBookings()->sum('net_earned');   // 90%
        $platformFees = $capturedBookings()->sum('commission');   // 10%

        $pendingPayout = WithdrawRequest::where('company_id', $company->id)
            ->where('status', WithdrawRequest::STATUS_PENDING)
            ->sum('amount');

        // --- Monthly Earnings Trend (grouped bar/column chart), last 6 months ---
        // Bucketed by when the campaign's payment was actually captured (Payment.paid_at),
        // not booking creation — that's when the revenue was realized.
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);

            $monthBookings = $capturedBookings()
                ->whereHas('campaign.payment', function ($q) use ($monthDate) {
                    $q->whereMonth('paid_at', $monthDate->month)
                        ->whereYear('paid_at', $monthDate->year);
                });

            $monthlyTrend[] = [
                'month'          => $monthDate->format('M Y'),
                'gross_revenue'  => (float) (clone $monthBookings)->sum('sale_price'),
                'net_earnings'   => (float) (clone $monthBookings)->sum('net_earned'),
                'platform_fees'  => (float) (clone $monthBookings)->sum('commission'),
            ];
        }

        // --- Earnings by Screen (bar chart) ---
        $earningsByScreen = Booking::selectRaw('screens.id as screen_id, screens.name as screen_name, SUM(bookings.net_earned) as net_earned')
            ->join('screens', 'bookings.screen_id', '=', 'screens.id')
            ->join('campaigns', 'bookings.campaign_id', '=', 'campaigns.id')
            ->where('screens.company_id', $company->id)
            ->where('campaigns.payment_status', Campaign::PAYMENT_STATUS_PAID)
            ->groupBy('screens.id', 'screens.name')
            ->orderByDesc('net_earned')
            ->get()
            ->map(fn ($row) => [
                'screen_id'   => $row->screen_id,
                'screen_name' => $row->screen_name,
                'net_earned'  => (float) $row->net_earned,
            ]);

        // --- Payout History (most recent; full/filterable list at /company/withdraw-requests) ---
        $payoutHistory = WithdrawRequest::where('company_id', $company->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'balance'              => (float) $company->balance,
            'total_revenue'        => (float) $totalRevenue,
            'net_earnings'         => (float) $netEarnings,
            'pending_payout'       => (float) $pendingPayout,
            'platform_fees'        => (float) $platformFees,
            'monthly_earnings_trend' => $monthlyTrend,
            'earnings_by_screen'   => $earningsByScreen,
            'payout_history'       => $payoutHistory,
        ]);
    }
}

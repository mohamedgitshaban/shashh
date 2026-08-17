<?php

namespace App\Http\Controllers\api\company;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Screen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/company/dashboard
     * 
     * Company dashboard summary including active screens, revenue, 
     * recent bookings, and screen availability list.
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->id;

        $bookingsBase = Booking::whereHas('screen', fn ($q) => $q->where('company_id', $companyId));

        // --- Active Screens Section ---
        $liveBookingsScreensCount = (clone $bookingsBase)
            ->where('status', Booking::STATUS_LIVE)
            ->distinct('screen_id')
            ->count('screen_id');

        $pendingApprovalsScreensCount = (clone $bookingsBase)
            ->where('status', Booking::STATUS_PENDING_APPROVAL)
            ->distinct('screen_id')
            ->count('screen_id');

        $netEarnedThisMonth = (clone $bookingsBase)
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->sum('net_earned');

        // --- Revenue This Month Section ---
        $grossRevenueThisMonth = (clone $bookingsBase)
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->sum('sale_price');

        // Bar Chart - net_earned for the last 6 months
        $revenueLast6Months = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $sum = (clone $bookingsBase)
                ->whereMonth('approved_at', $monthDate->month)
                ->whereYear('approved_at', $monthDate->year)
                ->sum('net_earned');

            $revenueLast6Months[] = [
                'month' => $monthDate->format('M Y'),
                'net_earned' => (float) $sum,
            ];
        }

        // --- Recent Bookings ---
        // Get last 5 overall bookings
        $recentBookings = (clone $bookingsBase)
            ->with(['screen:id,name,city', 'campaign:id,client_id,title,date_from,date_to', 'campaign.client:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($b) => $this->formatBooking($b));

        // --- Pending Content Review ---
        // Get last 3 bookings with status pending_approval
        $pendingContentReview = (clone $bookingsBase)
            ->with(['screen:id,name,city', 'campaign:id,client_id,title,date_from,date_to', 'campaign.client:id,name'])
            ->where('status', Booking::STATUS_PENDING_APPROVAL)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($b) => $this->formatBooking($b));

        // --- Screens List & Stats ---
        $allScreens = Screen::where('company_id', $companyId)->get();
        
        // Find screen IDs that currently have an active/pending booking
        $unavailableScreenIds = (clone $bookingsBase)
            ->whereIn('status', [Booking::STATUS_LIVE, Booking::STATUS_APPROVED, Booking::STATUS_PENDING_APPROVAL])
            ->pluck('screen_id')
            ->toArray();

        // Screen models use 'in_review' enum value for what user calls 'pending_approval' (awaiting admin review)
        $screensPendingApprovalCount = $allScreens->where('approval_status', 'in_review')->count();
        $screensAvailableCount = $allScreens->whereNotIn('id', $unavailableScreenIds)->count();

        $screensList = $allScreens->map(function ($s) use ($unavailableScreenIds) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'city' => $s->city,
                'type' => $s->screen_type,
                'approval_status' => $s->approval_status,
                'is_available' => !in_array($s->id, $unavailableScreenIds)
            ];
        });

        return response()->json([
            'active_screens' => [
                'live_bookings' => $liveBookingsScreensCount,
                'pending_approvals' => $pendingApprovalsScreensCount,
                'net_earned' => (float) $netEarnedThisMonth,
            ],
            'revenue_this_month' => [
                'gross_revenue' => (float) $grossRevenueThisMonth,
                'net_earnings' => (float) $netEarnedThisMonth,
                'last_six_months' => $revenueLast6Months,
            ],
            'recent_bookings' => $recentBookings,
            'pending_content_review' => $pendingContentReview,
            'screens_summary' => [
                'total_count' => $allScreens->count(),
                'pending_approval' => $screensPendingApprovalCount,
                'available' => $screensAvailableCount,
                'list' => $screensList,
            ]
        ]);
    }

    private function formatBooking(Booking $b): array
    {
        $days = 0;
        if ($b->campaign && $b->campaign->date_from && $b->campaign->date_to) {
            $days = $b->campaign->date_from->diffInDays($b->campaign->date_to) + 1;
        }

        return [
            'order_id'   => $b->id,
            'screen'     => $b->screen->name ?? null,
            'client'     => $b->campaign->client->name ?? null,
            'campaign'   => $b->campaign->title ?? null,
            'start'      => $b->campaign->date_from ? $b->campaign->date_from->toDateString() : null,
            'end'        => $b->campaign->date_to ? $b->campaign->date_to->toDateString() : null,
            'days'       => $days,
            'sale_price' => (float) $b->sale_price,
            'status'     => $b->status,
            'commission' => (float) $b->commission,
            'net_earned' => (float) $b->net_earned,
        ];
    }
}

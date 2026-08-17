<?php

namespace App\Http\Controllers\api\client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/client/dashboard/stats
     *
     * Returns a summary for the authenticated client:
     * - Active campaigns (live only)
     * - Total screens booked
     * - Total impressions
     * - Total spend
     */
    public function stats(Request $request): JsonResponse
    {
        $clientId = $request->user()->id;

        $campaignsBase = Campaign::where('client_id', $clientId);

        $activeCampaigns = (clone $campaignsBase)
            ->where('status', Campaign::STATUS_LIVE)
            ->count();

        $totalImpressions = (clone $campaignsBase)->sum('total_impressions');

        $bookingsBase = Booking::whereHas('campaign', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });

        $totalScreensBooked = (clone $bookingsBase)
            ->distinct('screen_id')
            ->count('screen_id');

        $totalSpend = (clone $bookingsBase)->sum('sale_price');

        return response()->json([
            'active_campaigns' => (int) $activeCampaigns,
            'total_screens_booked' => (int) $totalScreensBooked,
            'total_impressions' => (int) $totalImpressions,
            'total_spend' => (float) $totalSpend,
        ]);
    }
}

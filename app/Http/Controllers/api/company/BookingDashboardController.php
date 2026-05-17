<?php

namespace App\Http\Controllers\api\company;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BookingDashboardController extends Controller
{
    /**
     * GET /api/company/bookings/stats
     *
     * Returns summary counts for the dashboard header.
     */
    public function stats(Request $request): JsonResponse
    {
        $companyId = $request->user()->id;

        $base = Booking::whereHas('screen', fn ($q) => $q->where('company_id', $companyId));

        return response()->json([
            'total'     => (clone $base)->count(),
            'live'      => (clone $base)->where('status', Booking::STATUS_LIVE)->count(),
            'upcoming'  => (clone $base)->where('status', Booking::STATUS_APPROVED)->count(),
            'completed' => (clone $base)->where('status', Booking::STATUS_COMPLETED)->count(),
        ]);
    }

    /**
     * GET /api/company/bookings
     *
     * All ad reservations across this company's screens.
     * Optional filter: ?status=live|approved|pending_approval|rejected|completed
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with([
            'screen:id,name,city',
            'campaign:id,client_id,title,date_from,date_to',
            'campaign.client:id,name',
        ])
            ->whereHas('screen', fn ($q) => $q->where('company_id', $request->user()->id))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query->get()->map(function (Booking $b) {
            $days = $b->campaign->date_from->diffInDays($b->campaign->date_to) + 1;

            return [
                'order_id'   => $b->id,
                'screen'     => $b->screen->name,
                'client'     => $b->campaign->client->name,
                'campaign'   => $b->campaign->title,
                'start'      => $b->campaign->date_from->toDateString(),
                'end'        => $b->campaign->date_to->toDateString(),
                'days'       => $days,
                'sale_price' => (float) $b->sale_price,
                'status'     => $b->status,
                'commission' => (float) $b->commission,
                'net_earned' => (float) $b->net_earned,
            ];
        });

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * GET /api/company/bookings/export
     *
     * Download bookings as CSV.
     */
    public function export(Request $request)
    {
        $query = Booking::with([
            'screen:id,name',
            'campaign:id,client_id,title,date_from,date_to',
            'campaign.client:id,name',
        ])
            ->whereHas('screen', fn ($q) => $q->where('company_id', $request->user()->id))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query->get();

        $headers = ['Screen', 'Order ID', 'Client', 'Campaign', 'Start', 'End', 'Days', 'Sale Price', 'Status', 'Commission', 'Net Earned'];

        $rows = $bookings->map(function (Booking $b) {
            $days = $b->campaign->date_from->diffInDays($b->campaign->date_to) + 1;
            return [
                $b->screen->name,
                $b->id,
                $b->campaign->client->name,
                $b->campaign->title,
                $b->campaign->date_from->toDateString(),
                $b->campaign->date_to->toDateString(),
                $days,
                number_format((float) $b->sale_price, 2),
                $b->status,
                number_format((float) $b->commission, 2),
                number_format((float) $b->net_earned, 2),
            ];
        });

        $csv  = implode(',', array_map(fn ($h) => '"' . $h . '"', $headers)) . "\n";
        $csv .= $rows->map(fn ($r) => implode(',', array_map(fn ($c) => '"' . $c . '"', $r)))->implode("\n");

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings.csv"',
        ]);
    }
}

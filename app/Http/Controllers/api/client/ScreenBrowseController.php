<?php

namespace App\Http\Controllers\api\client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Screen;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScreenBrowseController extends Controller
{
    /**
     * GET /api/client/screens
     *
     * Query params:
     *   city        (string)  – filter by screen city
     *   company_id  (integer) – filter by company
     *   date_from   (date)    – start of requested booking period
     *   date_to     (date)    – end   of requested booking period
     *
     * Response adds to each screen:
     *   is_available       (bool|null) – true/false when date range given, null otherwise
     *   next_available_from (date|null) – first free date after any blocking bookings
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date|after_or_equal:date_from',
            'city'       => 'nullable|string|max:100',
            'company_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->input('date_from')) : null;
        $dateTo   = $request->filled('date_to')   ? Carbon::parse($request->input('date_to'))   : null;

        // ── Build screen query ────────────────────────────────────────────
        $query = Screen::with('company:id,company_name')
            ->where('approval_status', 'approved');

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->integer('company_id'));
        }

        $screens = $query->latest()->get([
            'id', 'company_id', 'name', 'screen_type',
            'width', 'height', 'daily_impressions', 'description',
            'price_per_day', 'min_booking_days', 'rotation_duration',
            'active_days', 'display_from', 'display_to', 'is_247',
            'street_address', 'landmark', 'latitude', 'longitude',
            'district', 'city', 'photos',
        ]);

        // ── Load all active bookings for these screens (one query) ────────
        // Active = statuses that actually block availability
        $blockingStatuses = [
            Booking::STATUS_PENDING_APPROVAL,
            Booking::STATUS_APPROVED,
            Booking::STATUS_LIVE,
        ];

        $bookingsByScreen = Booking::with('campaign:id,date_from,date_to')
            ->whereIn('screen_id', $screens->pluck('id'))
            ->whereIn('status', $blockingStatuses)
            ->whereHas('campaign', fn ($q) => $q->whereDate('date_to', '>=', now()))
            ->get()
            ->groupBy('screen_id');

        // ── Annotate each screen with availability info ───────────────────
        $result = $screens->map(function (Screen $screen) use ($bookingsByScreen, $dateFrom, $dateTo) {
            $screenBookings = $bookingsByScreen->get($screen->id, collect());

            $isAvailable       = null;   // null when no date range is given
            $nextAvailableFrom = null;

            if ($dateFrom && $dateTo) {
                // A booking blocks the requested range when:
                //   booking_date_from <= requested_date_to
                //   AND booking_date_to >= requested_date_from
                $overlapping = $screenBookings->filter(function ($booking) use ($dateFrom, $dateTo) {
                    $bFrom = Carbon::parse($booking->campaign->date_from);
                    $bTo   = Carbon::parse($booking->campaign->date_to);

                    return $bFrom->lte($dateTo) && $bTo->gte($dateFrom);
                });

                $isAvailable = $overlapping->isEmpty();

                if (! $isAvailable) {
                    // Next free date = day after the latest blocking booking ends
                    $latestEnd         = $overlapping
                        ->map(fn ($b) => Carbon::parse($b->campaign->date_to))
                        ->max();
                    $nextAvailableFrom = $latestEnd->copy()->addDay()->toDateString();
                }
            } else {
                // No date range requested – surface the earliest "screen is fully free" date
                // so clients can see booking gaps at a glance
                if ($screenBookings->isNotEmpty()) {
                    $latestEnd         = $screenBookings
                        ->map(fn ($b) => Carbon::parse($b->campaign->date_to))
                        ->max();
                    $nextAvailableFrom = $latestEnd->copy()->addDay()->toDateString();
                }
                // is_available stays null (no range to evaluate)
            }

            $data = $screen->toArray();
            $data['is_available']        = $isAvailable;
            $data['next_available_from'] = $nextAvailableFrom;

            return $data;
        });

        return response()->json(['screens' => $result]);
    }
}


<?php

namespace App\Http\Controllers\api\client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Campaign;
use App\Models\Screen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    /** GET /api/client/campaigns */
    public function index(Request $request): JsonResponse
    {
        $campaigns = Campaign::with(['bookings.screen:id,name,city,photos'])
            ->where('client_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['campaigns' => $campaigns]);
    }

    /** GET /api/client/campaigns/{id} */
    public function show(Request $request, int $id): JsonResponse
    {
        $campaign = Campaign::with(['bookings.screen:id,name,city,photos'])
            ->where('client_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['campaign' => $campaign]);
    }

    /**
     * POST /api/client/campaigns
     *
     * Body (multipart/form-data):
     *   Section 1 – title, description, objective, date_from, date_to
     *   Section 2 – screen_ids[] (array)
     *   Section 4 – artwork (file, nullable), needs_admin_artwork (boolean)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // Section 1
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'objective'           => 'required|string|max:100',
            'date_from'           => 'required|date|after_or_equal:today',
            'date_to'             => 'required|date|after_or_equal:date_from',

            // Section 2
            'screen_ids'          => 'required|array|min:1',
            'screen_ids.*'        => 'integer|exists:screens,id',

            // Section 4
            'artwork'             => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:51200',
            'needs_admin_artwork' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Validate that selected screens are approved
        $screens = Screen::whereIn('id', $data['screen_ids'])
            ->where('approval_status', 'approved')
            ->get();

        if ($screens->count() !== count($data['screen_ids'])) {
            return response()->json([
                'message' => 'One or more selected screens are not available.',
            ], 422);
        }

        // Validate availability: reject screens that have blocking bookings in the requested range
        $dateFrom = \Carbon\Carbon::parse($data['date_from']);
        $dateTo   = \Carbon\Carbon::parse($data['date_to']);

        $blockingStatuses = [
            Booking::STATUS_PENDING_APPROVAL,
            Booking::STATUS_APPROVED,
            Booking::STATUS_LIVE,
        ];

        $conflictingScreenIds = Booking::whereIn('screen_id', $screens->pluck('id'))
            ->whereIn('status', $blockingStatuses)
            ->whereHas('campaign', function ($q) use ($dateFrom, $dateTo) {
                $q->whereDate('date_from', '<=', $dateTo)
                  ->whereDate('date_to',   '>=', $dateFrom);
            })
            ->pluck('screen_id')
            ->unique()
            ->values();

        if ($conflictingScreenIds->isNotEmpty()) {
            $conflictingNames = $screens
                ->whereIn('id', $conflictingScreenIds->toArray())
                ->pluck('name');

            return response()->json([
                'message'            => 'One or more screens are already booked for the selected dates.',
                'unavailable_screens' => $conflictingNames,
            ], 422);
        }
        $needsAdminArtwork = (bool) ($data['needs_admin_artwork'] ?? false);
        $artworkFee        = $needsAdminArtwork ? Campaign::ARTWORK_FEE : 0.00;

        $artworkPath = null;
        if ($request->hasFile('artwork')) {
            $artworkPath = $request->file('artwork')->store('campaigns/artwork');
        }

        $days = $dateFrom->diffInDays($dateTo) + 1;

        DB::beginTransaction();
        try {
            $campaign = Campaign::create([
                'client_id'           => $request->user()->id,
                'title'               => $data['title'],
                'description'         => $data['description'] ?? null,
                'objective'           => $data['objective'],
                'date_from'           => $data['date_from'],
                'date_to'             => $data['date_to'],
                'artwork'             => $artworkPath,
                'needs_admin_artwork' => $needsAdminArtwork,
                'artwork_fee'         => $artworkFee,
                'status'              => Campaign::STATUS_PENDING_APPROVAL,
            ]);

            foreach ($screens as $screen) {
                $financials = Booking::calcFinancials($screen, $days);

                Booking::create([
                    'campaign_id' => $campaign->id,
                    'screen_id'   => $screen->id,
                    'status'      => Booking::STATUS_PENDING_APPROVAL,
                    ...$financials,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create campaign.'], 500);
        }

        return response()->json([
            'message'  => 'Campaign submitted successfully. Awaiting company approval.',
            'campaign' => $campaign->load('bookings.screen:id,name,city'),
        ], 201);
    }
}

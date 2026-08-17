<?php

namespace App\Http\Controllers\api\client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Campaign;
use App\Models\Payment;
use App\Models\Screen;
use App\Services\TapPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function __construct(private readonly TapPaymentService $tapPaymentService)
    {
    }

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
     * POST /api/client/campaigns/{id}/pay
     *
     * Re-initiates a Tap charge for a campaign whose payment hasn't been captured yet
     * (e.g. the client abandoned checkout or a previous charge failed/declined).
     */
    public function pay(Request $request, int $id): JsonResponse
    {
        $campaign = Campaign::with('payment')
            ->where('client_id', $request->user()->id)
            ->findOrFail($id);

        if ($campaign->payment_status === Campaign::PAYMENT_STATUS_PAID) {
            return response()->json(['message' => 'Campaign is already paid.'], 422);
        }

        $payment = $campaign->payment ?? Payment::create([
            'campaign_id' => $campaign->id,
            'client_id'   => $request->user()->id,
            'amount'      => $campaign->total_amount,
            'currency'    => config('services.tap.currency', 'SAR'),
            'status'      => Payment::STATUS_PENDING,
        ]);

        try {
            $charge = $this->tapPaymentService->createCharge(
                $payment,
                $request->user(),
                "Campaign payment - {$campaign->title}"
            );

            $payment->update([
                'tap_charge_id' => $charge['id'] ?? null,
                'status'        => $this->tapPaymentService->mapStatus($charge['status'] ?? ''),
                'payment_url'   => $charge['transaction']['url'] ?? null,
                'meta'          => $charge,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to re-initiate Tap payment for campaign.', [
                'campaign_id' => $campaign->id,
                'payment_id'  => $payment->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Failed to initiate payment. Please try again.'], 502);
        }

        return response()->json([
            'message' => 'Campaign submitted successfully. Complete payment to proceed.',
            'campaign'     => $campaign,
            'payment'     => $payment,
            'payment_url' => $payment->payment_url,
        ]);
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

        $campaign->load('bookings.screen:id,name,city');

        // Campaign + Booking rows are committed at this point. Payment is initiated as a
        // separate step so that a Tap outage never prevents the campaign/booking from
        // being recorded; the client can retry payment via POST /campaigns/{id}/pay.
        $payment = Payment::create([
            'campaign_id' => $campaign->id,
            'client_id'   => $request->user()->id,
            'amount'      => $campaign->total_amount,
            'currency'    => config('services.tap.currency', 'SAR'),
            'status'      => Payment::STATUS_PENDING,
        ]);

        try {
            $charge = $this->tapPaymentService->createCharge(
                $payment,
                $request->user(),
                "Campaign payment - {$campaign->title}"
            );

            $payment->update([
                'tap_charge_id' => $charge['id'] ?? null,
                'status'        => $this->tapPaymentService->mapStatus($charge['status'] ?? ''),
                'payment_url'   => $charge['transaction']['url'] ?? null,
                'meta'          => $charge,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to initiate Tap payment for campaign.', [
                'campaign_id' => $campaign->id,
                'payment_id'  => $payment->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'message'  => 'Campaign submitted, but payment could not be initiated. Please retry payment.',
                'campaign' => $campaign,
                'payment'  => $payment,
            ], 201);
        }

        return response()->json([
            'message'     => 'Campaign submitted successfully. Complete payment to proceed.',
            'campaign'    => $campaign,
            'payment'     => $payment,
            'payment_url' => $payment->payment_url,
        ], 201);
    }
}

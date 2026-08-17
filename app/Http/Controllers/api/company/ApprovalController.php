<?php

namespace App\Http\Controllers\api\company;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    /**
     * GET /api/company/approvals
     *
     * Returns all bookings (for this company's screens) that are pending approval, rejected, or approved.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with([
            'screen:id,name,city,photos,price_per_day',
            'campaign:id,client_id,title,description,objective,date_from,date_to,total_impressions,needs_admin_artwork,artwork_fee,artwork',
            'campaign.client:id,name,email,phone',
        ])
            ->whereHas('screen', fn($q) => $q->where('company_id', $request->user()->id))
            ->where('status', Booking::STATUS_PENDING_APPROVAL)
            ->latest()
            ->get();
        $rejectedBookings = Booking::with([
            'screen:id,name,city,photos,price_per_day',
            'campaign:id,client_id,title,description,objective,date_from,date_to,total_impressions,needs_admin_artwork,artwork_fee,artwork',
            'campaign.client:id,name,email,phone',
        ])
            ->whereHas('screen', fn($q) => $q->where('company_id', $request->user()->id))
            ->where('status', Booking::STATUS_REJECTED)
            ->latest()
            ->get();
        $approvedBookings = Booking::with([
            'screen:id,name,city,photos,price_per_day',
            'campaign:id,client_id,title,description,objective,date_from,date_to,total_impressions,needs_admin_artwork,artwork_fee,artwork',
            'campaign.client:id,name,email,phone',
        ])
            ->whereHas('screen', fn($q) => $q->where('company_id', $request->user()->id))
            ->whereNotIn('status', [Booking::STATUS_PENDING_APPROVAL, Booking::STATUS_REJECTED])
            ->latest()
            ->get();
        return response()->json(['pending_approvals' => $bookings, 'rejected_approvals' => $rejectedBookings, 'approved_approvals' => $approvedBookings]);
    }

    /**
     * POST /api/company/approvals/{booking}/approve
     */
    public function approve(Request $request, int $bookingId): JsonResponse
    {
        $booking = Booking::with('screen', 'campaign')->findOrFail($bookingId);

        if ($booking->screen->company_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($booking->status !== Booking::STATUS_PENDING_APPROVAL) {
            return response()->json(['message' => 'Booking is not pending approval.'], 422);
        }

        $booking->update([
            'status'      => Booking::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        // If all bookings for this campaign are approved → mark campaign approved
        $campaign      = $booking->campaign;
        $pendingExists = $campaign->bookings()
            ->where('status', Booking::STATUS_PENDING_APPROVAL)
            ->exists();

        if (! $pendingExists) {
            $campaign->update(['status' => Campaign::STATUS_APPROVED]);
        }

        return response()->json(['message' => 'Booking approved.', 'booking' => $booking]);
    }

    /**
     * POST /api/company/approvals/{booking}/reject
     *
     * Body: reason (string, required)
     */
    public function reject(Request $request, int $bookingId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::with('screen', 'campaign')->findOrFail($bookingId);

        if ($booking->screen->company_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($booking->status !== Booking::STATUS_PENDING_APPROVAL) {
            return response()->json(['message' => 'Booking is not pending approval.'], 422);
        }

        $booking->update([
            'status'           => Booking::STATUS_REJECTED,
            'rejection_reason' => $request->input('reason'),
        ]);

        // Mark the campaign as rejected
        $booking->campaign->update([
            'status'           => Campaign::STATUS_REJECTED,
            'rejection_reason' => $request->input('reason'),
        ]);

        return response()->json(['message' => 'Booking rejected.', 'booking' => $booking]);
    }
}

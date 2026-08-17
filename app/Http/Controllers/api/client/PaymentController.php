<?php

namespace App\Http\Controllers\api\client;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Payment;
use App\Models\User;
use App\Services\TapPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private readonly TapPaymentService $tapPaymentService)
    {
    }

    /**
     * GET /api/payments/callback
     *
     * Browser-facing redirect target Tap sends the client back to after they finish
     * (or abandon) the hosted payment page. Not authenticated — Tap only appends
     * `tap_id`. We re-fetch the charge from Tap (server-to-server) rather than trust
     * the query string, then bounce the browser to the SPA with the outcome.
     */
    public function callback(Request $request): RedirectResponse
    {
        $chargeId = $request->query('tap_id');
        $frontendUrl = rtrim((string) config('services.tap.frontend_redirect_url'), '/');

        if (! $chargeId) {
            return redirect()->away("{$frontendUrl}?status=error&reason=missing_charge_id");
        }

        $payment = Payment::where('tap_charge_id', $chargeId)->first();

        if (! $payment) {
            return redirect()->away("{$frontendUrl}?status=error&reason=payment_not_found");
        }

        try {
            $charge = $this->tapPaymentService->retrieveCharge($chargeId);
            $this->applyChargeUpdate($payment, $charge);
        } catch (\Throwable $e) {
            Log::error('Failed to confirm Tap charge on callback.', [
                'tap_charge_id' => $chargeId,
                'error'         => $e->getMessage(),
            ]);

            return redirect()->away("{$frontendUrl}?status=error&reason=confirmation_failed&campaign_id={$payment->campaign_id}");
        }

        return redirect()->away(
            "{$frontendUrl}?status={$payment->status}&campaign_id={$payment->campaign_id}"
        );
    }

    /**
     * POST /api/payments/webhook
     *
     * Server-to-server notification from Tap. Must be verified via the `hashstring`
     * header (HMAC-SHA256 over an ordered field concatenation, keyed with the Secret
     * API Key) before the payload is trusted — this is the only endpoint that should
     * ever move a payment into a final captured/failed state.
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $hashstring = $request->header('hashstring');

        if (! $this->tapPaymentService->verifyWebhookSignature($payload, $hashstring)) {
            Log::warning('Rejected Tap webhook with invalid signature.', [
                'charge_id' => $payload['id'] ?? null,
            ]);

            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $chargeId = $payload['id'] ?? null;
        $payment = $chargeId ? Payment::where('tap_charge_id', $chargeId)->first() : null;

        if (! $payment) {
            Log::warning('Tap webhook for unknown charge.', ['charge_id' => $chargeId]);

            return response()->json(['message' => 'Unknown charge.'], 404);
        }

        $this->applyChargeUpdate($payment, $payload);

        return response()->json(['message' => 'Webhook processed.']);
    }

    /**
     * Persist the latest charge status onto the Payment + parent Campaign, and — the
     * first time (and only the first time) a charge reaches CAPTURED — credit each
     * screen-owning company's balance with its 90% share.
     *
     * Both the webhook and the browser callback funnel through here, so this locks the
     * Payment row for the duration of the transaction to make the capture transition
     * (and the balance credit it triggers) happen exactly once even if both requests
     * land at nearly the same time.
     */
    private function applyChargeUpdate(Payment $payment, array $charge): void
    {
        $status = $this->tapPaymentService->mapStatus($charge['status'] ?? '');

        DB::transaction(function () use ($payment, $charge, $status) {
            /** @var Payment $locked */
            $locked = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $wasCaptured = $locked->status === Payment::STATUS_CAPTURED;

            $locked->update([
                'tap_charge_id' => $charge['id'] ?? $locked->tap_charge_id,
                'status'        => $status,
                'meta'          => $charge,
                'paid_at'       => $status === Payment::STATUS_CAPTURED ? ($locked->paid_at ?? now()) : $locked->paid_at,
            ]);

            $locked->campaign()->update([
                'payment_status' => $status === Payment::STATUS_CAPTURED ? Campaign::PAYMENT_STATUS_PAID
                    : (in_array($status, [Payment::STATUS_FAILED, Payment::STATUS_DECLINED, Payment::STATUS_CANCELLED], true)
                        ? Campaign::PAYMENT_STATUS_FAILED
                        : Campaign::PAYMENT_STATUS_UNPAID),
            ]);

            if ($status === Payment::STATUS_CAPTURED && ! $wasCaptured) {
                $this->creditCompanyBalances($locked);
            }
        });

        // Refresh the caller's instance so it reflects what was just committed.
        $payment->refresh();
    }

    /**
     * Credit each screen-owning company's balance with the sum of its bookings'
     * net_earned (90% share) for this campaign. A campaign can span screens from
     * multiple companies, so the split is computed per company, not per campaign.
     * Artwork fees are a platform-only service charge and are not split here.
     */
    private function creditCompanyBalances(Payment $payment): void
    {
        $campaign = $payment->campaign()->with('bookings.screen')->first();

        if (! $campaign) {
            return;
        }

        $creditsByCompany = $campaign->bookings
            ->groupBy(fn ($booking) => $booking->screen?->company_id)
            ->filter(fn ($bookings, $companyId) => ! is_null($companyId));

        foreach ($creditsByCompany as $companyId => $bookings) {
            User::whereKey($companyId)->increment('balance', $bookings->sum('net_earned'));
        }
    }
}

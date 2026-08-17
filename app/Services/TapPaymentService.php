<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TapPaymentService
{
    private string $secretKey;
    private string $baseUrl;
    private string $currency;

    public function __construct()
    {
        $this->secretKey = (string) config('services.tap.secret_key');
        $this->baseUrl   = rtrim((string) config('services.tap.base_url'), '/');
        $this->currency   = (string) config('services.tap.currency', 'SAR');
    }

    /**
     * Create a Tap charge for the given payment and return Tap's response payload.
     * Tap returns a hosted payment page URL at `transaction.url` that the client
     * must be redirected to in order to complete the payment.
     *
     * @throws RuntimeException on API failure
     */
    public function createCharge(Payment $payment, User $client, string $description): array
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->post("{$this->baseUrl}/v2/charges", [
                'amount'      => (float) $payment->amount,
                'currency'    => $this->currency,
                'threeDSecure' => true,
                'save_card'   => false,
                'description' => $description,
                'metadata'    => [
                    'payment_id'  => $payment->id,
                    'campaign_id' => $payment->campaign_id,
                ],
                'reference'   => [
                    'transaction' => 'txn_'.$payment->id,
                    'order'       => 'ord_'.$payment->campaign_id,
                ],
                'receipt'     => [
                    'email' => true,
                    'sms'   => false,
                ],
                'customer'    => [
                    'first_name' => $client->name,
                    'email'      => $client->email,
                    'phone'      => [
                        'country_code' => '966',
                        'number'       => preg_replace('/\D/', '', (string) $client->phone),
                    ],
                ],
                'source'      => [
                    'id' => 'src_all',
                ],
                'post'        => [
                    'url' => route('payments.webhook'),
                ],
                'redirect'    => [
                    'url' => route('payments.callback'),
                ],
            ]);

        if ($response->failed()) {
            Log::error('Tap charge creation failed', [
                'payment_id' => $payment->id,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);

            throw new RuntimeException('Failed to initiate payment with Tap.');
        }

        return $response->json();
    }

    /** Retrieve a charge by its Tap charge id, to confirm status server-side. */
    public function retrieveCharge(string $chargeId): array
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->get("{$this->baseUrl}/v2/charges/{$chargeId}");

        if ($response->failed()) {
            Log::error('Tap charge retrieval failed', [
                'charge_id' => $chargeId,
                'status'    => $response->status(),
                'body'      => $response->body(),
            ]);

            throw new RuntimeException('Failed to retrieve payment status from Tap.');
        }

        return $response->json();
    }

    /** Map a Tap charge status to our internal Payment status. */
    public function mapStatus(string $tapStatus): string
    {
        return match (strtoupper($tapStatus)) {
            'CAPTURED'                        => Payment::STATUS_CAPTURED,
            'DECLINED', 'RESTRICTED'          => Payment::STATUS_DECLINED,
            'CANCELLED', 'VOID'               => Payment::STATUS_CANCELLED,
            'FAILED', 'ABANDONED', 'TIMEDOUT' => Payment::STATUS_FAILED,
            default                           => Payment::STATUS_INITIATED,
        };
    }

    /**
     * Validate the `hashstring` header sent with Tap webhooks.
     *
     * Tap builds an HMAC-SHA256 signature over a fixed, ordered concatenation of
     * fields from the charge payload, keyed with your Secret API Key. Field order
     * matters and must match Tap's documentation exactly.
     *
     * @see https://developers.tap.company/docs/webhook
     */
    public function verifyWebhookSignature(array $payload, ?string $hashstringHeader): bool
    {
        if (empty($hashstringHeader)) {
            return false;
        }

        $id                = $payload['id'] ?? '';
        $amount            = $payload['amount'] ?? '';
        $currency          = $payload['currency'] ?? '';
        $gatewayReference  = $payload['reference']['gateway'] ?? '';
        $paymentReference  = $payload['reference']['payment'] ?? '';
        $status            = $payload['status'] ?? '';
        $created           = $payload['transaction']['created'] ?? ($payload['created'] ?? '');

        $toBeHashedString = 'x_id'.$id
            .'x_amount'.$amount
            .'x_currency'.$currency
            .'x_gateway_reference'.$gatewayReference
            .'x_payment_reference'.$paymentReference
            .'x_status'.$status
            .'x_created'.$created;

        $computedHash = hash_hmac('sha256', $toBeHashedString, $this->secretKey);

        return hash_equals($computedHash, $hashstringHeader);
    }
}

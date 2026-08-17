<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillCapturedPayments extends Command
{
    protected $signature   = 'payments:backfill-captured {--dry-run : Preview the changes without writing them}';
    protected $description = 'Data fix: for campaigns marked payment_status=paid without a genuinely captured Payment '
        . '(e.g. flipped directly in the DB rather than via the real Tap flow), create/complete the Payment row and '
        . 'credit the owning companies\' balances so earnings totals, monthly trend, balance, and payout history line up.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $campaigns = Campaign::where('payment_status', Campaign::PAYMENT_STATUS_PAID)
            ->with(['payment', 'bookings.screen', 'client'])
            ->get()
            ->filter(fn (Campaign $c) => ! $c->payment
                || $c->payment->status !== Payment::STATUS_CAPTURED
                || is_null($c->payment->paid_at));

        if ($campaigns->isEmpty()) {
            $this->info('Nothing to backfill — every paid campaign already has a captured payment.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%d paid campaign(s) missing a captured payment record.%s',
            $campaigns->count(),
            $dryRun ? ' (dry run — no changes will be written)' : ''
        ));

        $creditedByCompany = [];
        $paymentsCreated   = 0;
        $paymentsCompleted = 0;

        foreach ($campaigns as $campaign) {
            // Best-effort paid_at: approximate to when the payment would realistically have
            // happened — the earliest booking approval for this campaign, falling back to
            // the campaign's creation date. Keeps the monthly trend roughly aligned with the
            // dashboard's approved_at-based bucketing instead of dumping everything into "now".
            $earliestApproval = $campaign->bookings->pluck('approved_at')->filter()->min();
            $paidAt = $earliestApproval ?? $campaign->created_at ?? now();

            $this->line(sprintf(
                '  Campaign #%d "%s" — %s payment, paid_at → %s',
                $campaign->id,
                $campaign->title,
                $campaign->payment ? 'completing existing' : 'creating new',
                $paidAt->toDateString()
            ));

            if ($dryRun) {
                if (! $campaign->payment) {
                    $paymentsCreated++;
                } else {
                    $paymentsCompleted++;
                }

                foreach ($this->creditsForCampaign($campaign) as $companyId => $amount) {
                    $creditedByCompany[$companyId] = ($creditedByCompany[$companyId] ?? 0) + $amount;
                }

                continue;
            }

            DB::transaction(function () use ($campaign, $paidAt, &$creditedByCompany, &$paymentsCreated, &$paymentsCompleted) {
                $payment = $campaign->payment;

                if ($payment) {
                    $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                } else {
                    $payment = Payment::create([
                        'campaign_id' => $campaign->id,
                        'client_id'   => $campaign->client_id,
                        'amount'      => $campaign->total_amount,
                        'currency'    => config('services.tap.currency', 'SAR'),
                        'status'      => Payment::STATUS_PENDING,
                    ]);
                    $paymentsCreated++;
                }

                $wasCaptured = $payment->status === Payment::STATUS_CAPTURED && ! is_null($payment->paid_at);

                if (! $wasCaptured) {
                    $payment->update([
                        'status'  => Payment::STATUS_CAPTURED,
                        'paid_at' => $payment->paid_at ?? $paidAt,
                        'meta'    => array_merge($payment->meta ?? [], [
                            'backfilled'    => true,
                            'backfilled_at' => now()->toISOString(),
                            'note'          => 'Data fix: payment_status was already "paid" without a real Tap capture.',
                        ]),
                    ]);
                    $paymentsCompleted++;
                }

                if (! $wasCaptured) {
                    foreach ($this->creditsForCampaign($campaign) as $companyId => $amount) {
                        User::whereKey($companyId)->increment('balance', $amount);
                        $creditedByCompany[$companyId] = ($creditedByCompany[$companyId] ?? 0) + $amount;
                    }
                }
            });
        }

        $this->newLine();
        $this->info(sprintf(
            '%s: %d payment(s) created, %d completed, %d compan%s credited.',
            $dryRun ? 'Would backfill' : 'Backfilled',
            $paymentsCreated,
            $paymentsCompleted,
            count($creditedByCompany),
            count($creditedByCompany) === 1 ? 'y' : 'ies'
        ));

        foreach ($creditedByCompany as $companyId => $amount) {
            $this->line(sprintf('  Company #%d %s %.2f', $companyId, $dryRun ? 'would be credited' : 'credited', $amount));
        }

        return self::SUCCESS;
    }

    /** Per-company net_earned credit for a campaign's bookings, keyed by company_id. */
    private function creditsForCampaign(Campaign $campaign): array
    {
        return $campaign->bookings
            ->groupBy(fn ($booking) => $booking->screen?->company_id)
            ->filter(fn ($bookings, $companyId) => ! is_null($companyId))
            ->map(fn ($bookings) => (float) $bookings->sum('net_earned'))
            ->all();
    }
}

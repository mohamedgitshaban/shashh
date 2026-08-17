<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessCampaigns extends Command
{
    protected $signature   = 'campaigns:process';
    protected $description = 'Activate, accumulate impressions, and complete campaigns daily.';

    public function handle(): int
    {
        $today = Carbon::today();

        DB::transaction(function () use ($today) {
            // 1. Complete campaigns whose end date has passed
            $expired = Campaign::where('status', Campaign::STATUS_LIVE)
                ->whereDate('date_to', '<', $today)
                ->get();

            foreach ($expired as $campaign) {
                $campaign->update(['status' => Campaign::STATUS_COMPLETED]);
                $campaign->bookings()
                    ->where('status', Booking::STATUS_LIVE)
                    ->update(['status' => Booking::STATUS_COMPLETED]);
            }

            // 2. Activate approved campaigns that have reached their start date
            $starting = Campaign::where('status', Campaign::STATUS_APPROVED)
                ->whereDate('date_from', '<=', $today)
                ->whereDate('date_to', '>=', $today)
                ->get();

            foreach ($starting as $campaign) {
                $campaign->update(['status' => Campaign::STATUS_LIVE]);
                $campaign->bookings()
                    ->where('status', Booking::STATUS_APPROVED)
                    ->update(['status' => Booking::STATUS_LIVE]);
            }

            // 3. Accumulate daily impressions for all live campaigns
            $liveCampaigns = Campaign::where('status', Campaign::STATUS_LIVE)
                ->with(['bookings' => fn ($q) => $q->where('status', Booking::STATUS_LIVE)->with('screen:id,daily_impressions')])
                ->get();

            foreach ($liveCampaigns as $campaign) {
                $daily = $campaign->bookings->sum(fn ($b) => (int) ($b->screen->daily_impressions ?? 0));
                if ($daily > 0) {
                    $campaign->increment('total_impressions', $daily);
                }
            }
        });

        $this->info('Campaigns processed successfully for ' . $today->toDateString());

        return self::SUCCESS;
    }
}

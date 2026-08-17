<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Campaign;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $screen   = Screen::where('approval_status', 'approved')->inRandomOrder()->first()
                    ?? Screen::factory()->approved()->create();

        $campaign = Campaign::inRandomOrder()->first()
                    ?? Campaign::factory()->create();

        $dateFrom = $campaign->date_from;
        $dateTo   = $campaign->date_to;
        $days     = $dateFrom->diffInDays($dateTo) + 1;

        $financials = Booking::calcFinancials($screen, $days);

        return [
            'campaign_id'      => $campaign->id,
            'screen_id'        => $screen->id,
            'status'           => Booking::STATUS_PENDING_APPROVAL,
            'rejection_reason' => null,
            'approved_by'      => null,
            'approved_at'      => null,
            ...$financials,
        ];
    }

    // ── Status states ──────────────────────────────────────────────────────

    public function pendingApproval(): static
    {
        return $this->state(fn () => [
            'status'           => Booking::STATUS_PENDING_APPROVAL,
            'rejection_reason' => null,
            'approved_by'      => null,
            'approved_at'      => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'           => Booking::STATUS_APPROVED,
            'rejection_reason' => null,
            'approved_by'      => User::where('type', 'company')->inRandomOrder()->value('id'),
            'approved_at'      => now()->subDays(fake()->numberBetween(1, 5)),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'           => Booking::STATUS_REJECTED,
            'rejection_reason' => fake()->sentence(),
            'approved_by'      => null,
            'approved_at'      => null,
        ]);
    }

    public function live(): static
    {
        return $this->state(fn () => [
            'status'           => Booking::STATUS_LIVE,
            'rejection_reason' => null,
            'approved_by'      => User::where('type', 'company')->inRandomOrder()->value('id'),
            'approved_at'      => now()->subDays(fake()->numberBetween(2, 10)),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'           => Booking::STATUS_COMPLETED,
            'rejection_reason' => null,
            'approved_by'      => User::where('type', 'company')->inRandomOrder()->value('id'),
            'approved_at'      => now()->subDays(fake()->numberBetween(30, 90)),
        ]);
    }

    /**
     * Attach this booking to a specific campaign and derive financials automatically.
     */
    public function forCampaign(Campaign $campaign): static
    {
        return $this->state(function () use ($campaign) {
            $screen = Screen::where('approval_status', 'approved')->inRandomOrder()->first();
            $days   = $campaign->date_from->diffInDays($campaign->date_to) + 1;

            return [
                'campaign_id' => $campaign->id,
                'screen_id'   => $screen->id,
                ...(Booking::calcFinancials($screen, $days)),
            ];
        });
    }
}

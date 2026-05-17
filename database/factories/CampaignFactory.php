<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    private static array $titles = [
        'Ramadan Mega Sale',       'National Day Celebration',
        'New Branch Grand Opening','Summer Collection Launch',
        'Eid Al-Fitr Offers',      'Back to School Campaign',
        'Premium Car Showroom',    'Digital Banking Awareness',
        'Real Estate Expo',        'Luxury Watch Collection',
        'Tech Conference Promo',   'Healthcare Month',
        'Food Festival Announcement','Travel Season Deals',
        'Winter Fashion Drop',     'Fitness Club Launch',
        'University Enrollment',   'Smart Home Expo',
        'Coffee Brand Awareness',  'Electric Vehicle Launch',
    ];

    private static array $objectives = [
        'Brand Awareness', 'Product Launch', 'Seasonal Promotion',
        'Event Announcement', 'Store Opening', 'App Download', 'Sale Promotion',
    ];

    public function definition(): array
    {
        $dateFrom = fake()->dateTimeBetween('-30 days', '+30 days');
        $dateTo   = fake()->dateTimeBetween($dateFrom, '+60 days');

        $needsAdminArtwork = fake()->boolean(30);

        return [
            'client_id'           => Client::factory(),
            'title'               => fake()->randomElement(self::$titles) . ' ' . fake()->year(),
            'description'         => fake()->sentences(2, true),
            'objective'           => fake()->randomElement(self::$objectives),
            'date_from'           => $dateFrom,
            'date_to'             => $dateTo,
            'artwork'             => $needsAdminArtwork ? null : 'campaigns/artwork/sample.jpg',
            'needs_admin_artwork' => $needsAdminArtwork,
            'artwork_fee'         => $needsAdminArtwork ? Campaign::ARTWORK_FEE : 0.00,
            'total_impressions'   => 0,
            'status'              => Campaign::STATUS_PENDING_APPROVAL,
            'rejection_reason'    => null,
        ];
    }

    // ── Status states ─────────────────────────────────────────────────────

    public function pendingApproval(): static
    {
        return $this->state(fn () => [
            'date_from'        => now()->addDays(fake()->numberBetween(3, 20))->toDateString(),
            'date_to'          => now()->addDays(fake()->numberBetween(25, 50))->toDateString(),
            'status'           => Campaign::STATUS_PENDING_APPROVAL,
            'total_impressions'=> 0,
            'rejection_reason' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'date_from'        => now()->addDays(fake()->numberBetween(1, 10))->toDateString(),
            'date_to'          => now()->addDays(fake()->numberBetween(15, 45))->toDateString(),
            'status'           => Campaign::STATUS_APPROVED,
            'total_impressions'=> 0,
            'rejection_reason' => null,
        ]);
    }

    public function live(): static
    {
        $startOffset = fake()->numberBetween(-15, -1);
        $endOffset   = fake()->numberBetween(5, 30);

        return $this->state(fn () => [
            'date_from'        => now()->addDays($startOffset)->toDateString(),
            'date_to'          => now()->addDays($endOffset)->toDateString(),
            'status'           => Campaign::STATUS_LIVE,
            'total_impressions'=> fake()->numberBetween(5000, 500000),
        ]);
    }

    public function completed(): static
    {
        $endOffset   = fake()->numberBetween(-60, -5);
        $startOffset = $endOffset - fake()->numberBetween(7, 30);

        return $this->state(fn () => [
            'date_from'        => now()->addDays($startOffset)->toDateString(),
            'date_to'          => now()->addDays($endOffset)->toDateString(),
            'status'           => Campaign::STATUS_COMPLETED,
            'total_impressions'=> fake()->numberBetween(50000, 2000000),
            'rejection_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'date_from'        => now()->addDays(fake()->numberBetween(3, 20))->toDateString(),
            'date_to'          => now()->addDays(fake()->numberBetween(25, 50))->toDateString(),
            'status'           => Campaign::STATUS_REJECTED,
            'total_impressions'=> 0,
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    /** Requires admin artwork (adds 1,300 SAR fee). */
    public function withAdminArtwork(): static
    {
        return $this->state(fn () => [
            'artwork'             => null,
            'needs_admin_artwork' => true,
            'artwork_fee'         => Campaign::ARTWORK_FEE,
        ]);
    }

    /** Client provides their own artwork. */
    public function withClientArtwork(): static
    {
        return $this->state(fn () => [
            'artwork'             => 'campaigns/artwork/sample.jpg',
            'needs_admin_artwork' => false,
            'artwork_fee'         => 0.00,
        ]);
    }
}

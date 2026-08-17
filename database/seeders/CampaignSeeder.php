<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Screen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Ensure clients exist ──────────────────────────────────────
        if (Client::count() < 5) {
            Client::factory(5)->create();
        }

        // ── 2. Guard: need approved screens ─────────────────────────────
        if (Screen::where('approval_status', 'approved')->doesntExist()) {
            $this->command->warn('No approved screens found. Run ScreenSeeder first.');
            return;
        }

        // ── 3. Seed campaigns by status ──────────────────────────────────
        $this->seedWithBookings('pendingApproval', 7);
        $this->seedWithBookings('approved',        5);
        $this->seedWithBookings('live',             7);
        $this->seedWithBookings('completed',       11);
        $this->seedWithBookings('rejected',         3);

        $this->command->info(sprintf(
            'Seeded %d campaigns and %d bookings.',
            Campaign::count(),
            Booking::count()
        ));
    }

    /**
     * Create $count campaigns using the named factory state,
     * then attach 1–3 bookings with a matching booking status.
     */
    private function seedWithBookings(string $state, int $count): void
    {
        $bookingState = match ($state) {
            'pendingApproval' => 'pendingApproval',
            'approved'        => 'approved',
            'live'            => 'live',
            'completed'       => 'completed',
            'rejected'        => 'rejected',
        };

        Campaign::factory()
            ->count($count)
            ->$state()
            ->create()
            ->each(function (Campaign $campaign) use ($bookingState) {
                $screenCount = fake()->numberBetween(1, 3);

                Booking::factory()
                    ->count($screenCount)
                    ->$bookingState()
                    ->forCampaign($campaign)
                    ->create();
            });
    }
}


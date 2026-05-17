<?php

namespace Database\Factories;

use App\Models\Screen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Screen>
 */
class ScreenFactory extends Factory
{
    protected $model = Screen::class;

    public function definition(): array
    {
        $is247 = $this->faker->boolean(20);

        $screenTypes = [
            'LED Billboard',
            'Digital Display',
            'Static Billboard',
            'Street Furniture',
            'Transit Display',
            'Indoor Screen',
        ];

        $cities = ['Riyadh', 'Jeddah', 'Mecca', 'Medina', 'Dammam', 'Khobar', 'Tabuk', 'Abha'];
        $districts = ['Al Olaya', 'Al Malaz', 'Al Hamra', 'Al Rawdah', 'Al Nuzha', 'Al Murjanah', 'Al Zahra'];

        return [
            'name'               => $this->faker->words(3, true) . ' Screen',
            'screen_type'        => $this->faker->randomElement($screenTypes),
            'width'              => $this->faker->randomFloat(2, 2, 20),
            'height'             => $this->faker->randomFloat(2, 1, 10),
            'daily_impressions'  => $this->faker->numberBetween(500, 50000),
            'description'        => $this->faker->sentence(12),

            'price_per_day'      => $this->faker->randomFloat(2, 100, 5000),
            'min_booking_days'   => $this->faker->randomElement([1, 3, 7, 14, 30]),
            'rotation_duration'  => $this->faker->randomElement([5, 10, 15, 20, 30, 60]),

            'active_days'        => $is247
                ? ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
                : $this->faker->randomElements(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], $this->faker->numberBetween(3, 6)),
            'display_from'       => $is247 ? '00:00' : $this->faker->randomElement(['06:00', '07:00', '08:00', '09:00']),
            'display_to'         => $is247 ? '23:59' : $this->faker->randomElement(['21:00', '22:00', '23:00', '23:59']),
            'is_247'             => $is247,
            'blackout_dates'     => null,

            'street_address'     => $this->faker->streetAddress(),
            'landmark'           => $this->faker->boolean(60) ? $this->faker->company() : null,
            'latitude'           => $this->faker->latitude(17.0, 32.0),
            'longitude'          => $this->faker->longitude(36.0, 56.0),
            'district'           => $this->faker->randomElement($districts),
            'city'               => $this->faker->randomElement($cities),

            'photos'             => null,
            'cr_document'        => null,
            'municipality_permit' => null,

            'approval_status'    => $this->faker->randomElement(['in_review', 'approved', 'rejected']),
            'rejection_reason'   => null,
            'reviewed_by'        => null,
            'reviewed_at'        => null,
        ];
    }

    public function inReview(): static
    {
        return $this->state([
            'approval_status' => 'in_review',
            'rejection_reason' => null,
            'reviewed_by'     => null,
            'reviewed_at'     => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'approval_status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by'     => User::where('type', 'admin')->inRandomOrder()->value('id'),
            'reviewed_at'     => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'approval_status'  => 'rejected',
            'rejection_reason' => $this->faker->sentence(8),
            'reviewed_by'      => User::where('type', 'admin')->inRandomOrder()->value('id'),
            'reviewed_at'      => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}

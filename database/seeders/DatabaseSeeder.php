<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GulfCitySeeder::class,  // Gulf countries cities
            ScreenSeeder::class,    // Companies + approved screens
            CampaignSeeder::class,  // Clients + campaigns + bookings
        ]);
    }
}

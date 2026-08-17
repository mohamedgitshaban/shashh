<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ScreenSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one admin exists for reviewed_by FK
        $admin = User::where('type', 'admin')->first();
        if (! $admin) {
            $admin = User::create([
                'name'     => 'Super Admin',
                'email'    => 'admin@shashh.com',
                'password' => Hash::make('password'),
                'type'     => 'admin',
            ]);
        }

        // Create 5 company users if none exist
        $companies = Company::all();
        if ($companies->count() < 5) {
            $needed = 5 - $companies->count();
            for ($i = 1; $i <= $needed; $i++) {
                Company::create([
                    'name'             => fake()->company(),
                    'email'            => fake()->unique()->safeEmail(),
                    'password'         => Hash::make('password'),
                    'phone'            => fake()->phoneNumber(),
                    'company_name'     => fake()->company() . ' Media',
                    'company_address'  => fake()->address(),
                    'vat_number'       => fake()->numerify('3##########3'),
                    'cr'               => fake()->numerify('10########'),
                    'approval_status'  => 'approved',
                ]);
            }
            $companies = Company::all();
        }

        // Seed screens for each company
        foreach ($companies as $company) {
            // 2 in_review screens
            Screen::factory(2)
                ->inReview()
                ->create(['company_id' => $company->id]);

            // 3 approved screens
            Screen::factory(3)
                ->approved()
                ->create(['company_id' => $company->id]);

            // 1 rejected screen
            Screen::factory(1)
                ->rejected()
                ->create(['company_id' => $company->id]);
        }

        $total = Screen::count();
        $this->command->info("Seeded {$total} screens across {$companies->count()} companies.");
    }
}

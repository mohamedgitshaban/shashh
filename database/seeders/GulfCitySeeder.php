<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GulfCitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            // Saudi Arabia
            ['name' => 'Riyadh',          'country' => 'Saudi Arabia'],
            ['name' => 'Jeddah',          'country' => 'Saudi Arabia'],
            ['name' => 'Mecca',           'country' => 'Saudi Arabia'],
            ['name' => 'Medina',          'country' => 'Saudi Arabia'],
            ['name' => 'Dammam',          'country' => 'Saudi Arabia'],
            ['name' => 'Khobar',          'country' => 'Saudi Arabia'],
            ['name' => 'Dhahran',         'country' => 'Saudi Arabia'],
            ['name' => 'Jubail',          'country' => 'Saudi Arabia'],
            ['name' => 'Tabuk',           'country' => 'Saudi Arabia'],
            ['name' => 'Abha',            'country' => 'Saudi Arabia'],
            ['name' => 'Taif',            'country' => 'Saudi Arabia'],
            ['name' => 'Qatif',           'country' => 'Saudi Arabia'],
            ['name' => 'Hofuf',           'country' => 'Saudi Arabia'],
            ['name' => 'Najran',          'country' => 'Saudi Arabia'],
            ['name' => 'Yanbu',           'country' => 'Saudi Arabia'],
            ['name' => 'Hail',            'country' => 'Saudi Arabia'],
            ['name' => 'Khamis Mushait',  'country' => 'Saudi Arabia'],
            ['name' => 'Buraidah',        'country' => 'Saudi Arabia'],
            ['name' => 'Sakaka',          'country' => 'Saudi Arabia'],
            ['name' => 'Arar',            'country' => 'Saudi Arabia'],
            ['name' => 'Jizan',           'country' => 'Saudi Arabia'],

            // United Arab Emirates
            ['name' => 'Dubai',           'country' => 'UAE'],
            ['name' => 'Abu Dhabi',       'country' => 'UAE'],
            ['name' => 'Sharjah',         'country' => 'UAE'],
            ['name' => 'Ajman',           'country' => 'UAE'],
            ['name' => 'Ras Al Khaimah',  'country' => 'UAE'],
            ['name' => 'Fujairah',        'country' => 'UAE'],
            ['name' => 'Umm Al Quwain',   'country' => 'UAE'],
            ['name' => 'Al Ain',          'country' => 'UAE'],

            // Kuwait
            ['name' => 'Kuwait City',     'country' => 'Kuwait'],
            ['name' => 'Hawalli',         'country' => 'Kuwait'],
            ['name' => 'Salmiya',         'country' => 'Kuwait'],
            ['name' => 'Farwaniya',       'country' => 'Kuwait'],
            ['name' => 'Jahra',           'country' => 'Kuwait'],
            ['name' => 'Ahmadi',          'country' => 'Kuwait'],
            ['name' => 'Mangaf',          'country' => 'Kuwait'],

            // Qatar
            ['name' => 'Doha',            'country' => 'Qatar'],
            ['name' => 'Al Wakrah',       'country' => 'Qatar'],
            ['name' => 'Al Khor',         'country' => 'Qatar'],
            ['name' => 'Al Rayyan',       'country' => 'Qatar'],
            ['name' => 'Umm Salal',       'country' => 'Qatar'],
            ['name' => 'Lusail',          'country' => 'Qatar'],

            // Bahrain
            ['name' => 'Manama',          'country' => 'Bahrain'],
            ['name' => 'Muharraq',        'country' => 'Bahrain'],
            ['name' => 'Riffa',           'country' => 'Bahrain'],
            ['name' => 'Hamad Town',      'country' => 'Bahrain'],
            ['name' => 'Isa Town',        'country' => 'Bahrain'],
            ['name' => 'Sitra',           'country' => 'Bahrain'],
            ['name' => 'Budaiya',         'country' => 'Bahrain'],

            // Oman
            ['name' => 'Muscat',          'country' => 'Oman'],
            ['name' => 'Salalah',         'country' => 'Oman'],
            ['name' => 'Sohar',           'country' => 'Oman'],
            ['name' => 'Nizwa',           'country' => 'Oman'],
            ['name' => 'Sur',             'country' => 'Oman'],
            ['name' => 'Rustaq',          'country' => 'Oman'],
            ['name' => 'Ibri',            'country' => 'Oman'],
            ['name' => 'Barka',           'country' => 'Oman'],
            ['name' => 'Seeb',            'country' => 'Oman'],
        ];

        $now = now();
        $rows = array_map(fn ($c) => array_merge($c, [
            'created_at' => $now,
            'updated_at' => $now,
        ]), $cities);

        DB::table('cities')->insertOrIgnore($rows);
    }
}

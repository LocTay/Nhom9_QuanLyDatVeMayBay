<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

class SkyBookingAirlineSeeder extends Seeder
{
    public function run(): void
    {
        Airline::updateOrCreate(
            ['code' => 'SB'],
            [
                'name' => 'SkyBooking Airlines',
                'country' => 'Việt Nam',
                'is_active' => true,
            ]
        );
    }
}
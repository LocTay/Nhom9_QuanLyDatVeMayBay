<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        $sanBay = DB::table('airports')->pluck('id', 'code');

        $chuyenBay = [
            [
                'flight_number' => 'SB101',
                'departure' => 'SGN',
                'arrival' => 'HAN',
                'departure_time' => '2026-11-05 08:00:00',
                'arrival_time' => '2026-11-05 10:10:00',
                'price' => 1500000,
                'available_seats' => 120,
            ],
            [
                'flight_number' => 'SB102',
                'departure' => 'HAN',
                'arrival' => 'SGN',
                'departure_time' => '2026-11-05 13:00:00',
                'arrival_time' => '2026-11-05 15:10:00',
                'price' => 1600000,
                'available_seats' => 100,
            ],
            [
                'flight_number' => 'SB103',
                'departure' => 'SGN',
                'arrival' => 'CXR',
                'departure_time' => '2026-11-05 09:00:00',
                'arrival_time' => '2026-11-05 10:05:00',
                'price' => 850000,
                'available_seats' => 80,
            ],
            [
                'flight_number' => 'SB104',
                'departure' => 'CXR',
                'arrival' => 'SGN',
                'departure_time' => '2026-11-05 16:00:00',
                'arrival_time' => '2026-11-05 17:05:00',
                'price' => 900000,
                'available_seats' => 75,
            ],
            [
                'flight_number' => 'SB105',
                'departure' => 'SGN',
                'arrival' => 'DAD',
                'departure_time' => '2026-11-05 11:30:00',
                'arrival_time' => '2026-11-05 12:50:00',
                'price' => 1100000,
                'available_seats' => 90,
            ],
            [
                'flight_number' => 'SB106',
                'departure' => 'DAD',
                'arrival' => 'PQC',
                'departure_time' => '2026-11-06 14:00:00',
                'arrival_time' => '2026-11-06 15:40:00',
                'price' => 1200000,
                'available_seats' => 60,
            ],
        ];

        foreach ($chuyenBay as $chuyen) {
            if (!isset($sanBay[$chuyen['departure']]) ||
                !isset($sanBay[$chuyen['arrival']])) {
                throw new \RuntimeException(
                    'Không tìm thấy mã sân bay trong bảng airports.'
                );
            }

            DB::table('flights')->updateOrInsert(
                ['flight_number' => $chuyen['flight_number']],
                [
                    'departure_airport_id' => $sanBay[$chuyen['departure']],
                    'arrival_airport_id' => $sanBay[$chuyen['arrival']],
                    'departure_time' => $chuyen['departure_time'],
                    'arrival_time' => $chuyen['arrival_time'],
                    'price' => $chuyen['price'],
                    'available_seats' => $chuyen['available_seats'],
                    'status' => 'scheduled',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

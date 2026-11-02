<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirportSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('airports')->insert([
            [
                'code' => 'SGN',
                'name' => 'Cảng hàng không quốc tế Tân Sơn Nhất',
                'city' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'HAN',
                'name' => 'Cảng hàng không quốc tế Nội Bài',
                'city' => 'Hà Nội',
                'country' => 'Việt Nam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CXR',
                'name' => 'Cảng hàng không quốc tế Cam Ranh',
                'city' => 'Khánh Hòa',
                'country' => 'Việt Nam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DAD',
                'name' => 'Cảng hàng không quốc tế Đà Nẵng',
                'city' => 'Đà Nẵng',
                'country' => 'Việt Nam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PQC',
                'name' => 'Cảng hàng không quốc tế Phú Quốc',
                'city' => 'An Giang',
                'country' => 'Việt Nam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

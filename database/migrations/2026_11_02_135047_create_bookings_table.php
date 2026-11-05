<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng bookings đã được tạo bởi migration trước đó.
        // Bổ sung các cột của cấu trúc đặt vé mới.
        if (!Schema::hasColumn('bookings', 'pnr_code')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('pnr_code')->nullable()->unique();
            });
        }

        if (!Schema::hasColumn('bookings', 'passenger_count')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->unsignedInteger('passenger_count')->default(1);
            });
        }

        // Chuyển dữ liệu cũ nếu đã tồn tại.
        if (
            Schema::hasColumn('bookings', 'booking_code') &&
            Schema::hasColumn('bookings', 'number_of_seats')
        ) {
            DB::table('bookings')
                ->whereNull('pnr_code')
                ->orderBy('id')
                ->chunkById(100, function ($bookings) {
                    foreach ($bookings as $booking) {
                        DB::table('bookings')
                            ->where('id', $booking->id)
                            ->update([
                                'pnr_code' => $booking->booking_code,
                                'passenger_count' => $booking->number_of_seats,
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['pnr_code', 'passenger_count']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Thêm cột loại vé và hạng vé
            $table->string('trip_type')->default('one_way')->after('passenger_count');
            $table->string('flight_class')->default('Phổ thông')->after('trip_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Xóa cột nếu rollback migration
            $table->dropColumn(['trip_type', 'flight_class']);
        });
    }
};
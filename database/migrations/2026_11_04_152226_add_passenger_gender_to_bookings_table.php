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
            // Thêm cột passenger_gender (cho phép null để tránh lỗi với các dữ liệu cũ)
            $table->string('passenger_gender', 10)->nullable()->after('passenger_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Xóa cột nếu rollback migration
            $table->dropColumn('passenger_gender');
        });
    }
};
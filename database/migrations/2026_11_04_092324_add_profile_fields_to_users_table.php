<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm thông tin cá nhân vào bảng users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
        });
    }

    /**
     * Xóa các cột khi rollback migration.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'date_of_birth',
                'gender',
            ]);
        });
    }
};

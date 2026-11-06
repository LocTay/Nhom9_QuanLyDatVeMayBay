<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('refund_status', 50)->nullable();
            $table->text('cancellation_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'refund_status',
                'cancellation_reason',
            ]);
        });
    }
};

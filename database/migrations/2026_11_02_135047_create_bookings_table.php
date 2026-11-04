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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->string('pnr_code')->unique(); // Mã đặt chỗ (VD: HD72KS)
        $table->foreignId('flight_id')->constrained('flights')->onDelete('cascade');
        $table->string('passenger_name');
        $table->string('passenger_email');
        $table->string('passenger_phone');
        $table->integer('passenger_count')->default(1);
        $table->decimal('total_price', 15, 2);
        $table->string('status')->default('pending'); // pending, paid, cancelled
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

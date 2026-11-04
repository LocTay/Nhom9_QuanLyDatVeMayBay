<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();

            $table->string('flight_number', 20)->unique();

            $table->foreignId('departure_airport_id')
                ->constrained('airports')
                ->restrictOnDelete();

            $table->foreignId('arrival_airport_id')
                ->constrained('airports')
                ->restrictOnDelete();

            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');

            $table->decimal('price', 12, 2);
            $table->unsignedInteger('available_seats')->default(0);

            $table->string('status')->default('scheduled');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};

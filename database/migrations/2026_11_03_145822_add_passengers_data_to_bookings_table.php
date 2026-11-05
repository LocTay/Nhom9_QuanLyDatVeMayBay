<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::table('bookings', function (Blueprint $table) {
        $table->json('passengers_data')->nullable()->after('passenger_name');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn('passengers_data');
    });
    }
};


<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FlightController;

Route::get('/', function () {
    $sanBays = DB::table('airports')
        ->orderBy('city')
        ->get();

    return view('home', compact('sanBays'));
});

Route::get('/flights', [FlightController::class, 'index'])
    ->name('flights.search');

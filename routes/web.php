<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\BookingController; // Thêm dòng gọi Controller đặt vé

Route::get('/', function () {
    $sanBays = DB::table('airports')
        ->orderBy('city')
        ->get();

    return view('home', compact('sanBays'));
});

Route::get('/flights', [FlightController::class, 'index'])
    ->name('flights.search');

// === BỔ SUNG CÁC ROUTE ĐẶT VÉ CỦA THÀNH VIÊN 2 ===
Route::get('/booking/create/{flight_id}', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/success/{id}', [BookingController::class, 'success'])->name('booking.success');
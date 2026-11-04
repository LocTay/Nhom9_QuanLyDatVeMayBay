
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\AdminAirportController;

/*
Route::middleware(['role:admin'])->group(function () {
    // Route dành cho Admin
});

Route::middleware(['role:staff,admin'])->group(function () {
    // Route dành cho Staff và Admin
});
*/

Route::get('/', function () {
    $sanBays = DB::table('airports')
        ->orderBy('city')
        ->get();

    return view('home', compact('sanBays'));
});

Route::get('/flights', [FlightController::class, 'index'])
    ->name('flights.search');

use App\Http\Controllers\PaymentController;

// Tra cứu vé
Route::get('/tra-cuu-ve', [PaymentController::class, 'showLookupForm'])->name('ticket.lookup.form');
Route::post('/tra-cuu-ve', [PaymentController::class, 'lookup'])->name('ticket.lookup');

// Xem chi tiết vé
Route::get('/ve/{booking_code}', [PaymentController::class, 'showDetail'])->name('ticket.detail');

// Mô phỏng thanh toán
Route::post('/ve/{booking_code}/thanh-toan', [PaymentController::class, 'processPayment'])->name('ticket.payment');

// Hủy vé
Route::post('/ve/{booking_code}/huy', [PaymentController::class, 'cancelTicket'])->name('ticket.cancel');

// Quản lý sân bay - chỉ Admin được truy cập
Route::middleware(['role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('airports', AdminAirportController::class)
            ->except(['show']);
    });
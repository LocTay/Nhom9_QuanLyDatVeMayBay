
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

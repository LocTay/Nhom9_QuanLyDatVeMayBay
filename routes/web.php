<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\FlightController;
use App\Http\Controllers\BookingController; // Thêm dòng gọi Controller đặt vé

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminAirportController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminFlightController;

// Trang chu
Route::get('/', function () {
    $sanBays = DB::table('airports')
        ->orderBy('city')
        ->get();

    return view('home', compact('sanBays'));
});
// Trang lien he
Route::get('/lien-he', [ContactController::class, 'index'])
    ->name('contact');

// Xu ly gui thong tin lien he
Route::post('/lien-he', [ContactController::class, 'send'])
    ->name('contact.send');
// Tìm kiếm chuyến bay
Route::get('/flights', [FlightController::class, 'index'])
    ->name('flights.search');

// BỔ SUNG CÁC ROUTE ĐẶT VÉ CỦA THÀNH VIÊN 2
Route::get('/booking/create/{flight_id}', [BookingController::class, 'create'])
    ->name('booking.create');

Route::post('/booking/store', [BookingController::class, 'store'])
    ->name('booking.store');

Route::get('/booking/success/{id}', [BookingController::class, 'success'])
    ->name('booking.success');


// Dang ky
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

// Dang nhap
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Quen mat khau
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email');

// Dat lai mat khau
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

// Tra cuu ve
Route::get('/tra-cuu-ve', [PaymentController::class, 'showLookupForm'])
    ->name('ticket.lookup.form');

Route::post('/tra-cuu-ve', [PaymentController::class, 'lookup'])
    ->name('ticket.lookup');



// xem lich su dat ve
Route::middleware(['auth'])->group(function () {
    Route::get('/lich-su-dat-ve', [PaymentController::class, 'history'])->name('ticket.history');
});

// Xem chi tiet ve
Route::get('/ve/{booking_code}', [PaymentController::class, 'showDetail'])
    ->name('ticket.detail');

// Thanh toan
Route::post('/ve/{booking_code}/thanh-toan', [PaymentController::class, 'processPayment'])
    ->name('ticket.payment');

// Huy ve
Route::post('/ve/{booking_code}/huy', [PaymentController::class, 'cancelTicket'])
    ->name('ticket.cancel');

// Thong tin tai khoan
Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/tai-khoan/chinh-sua', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/tai-khoan', [ProfileController::class, 'update'])
        ->name('profile.update');
});
// Quan ly san bay - chi Admin
Route::middleware(['role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('airports', AdminAirportController::class)
            ->except(['show']);

        Route::resource('flights', AdminFlightController::class)
            ->except(['show']);
    });

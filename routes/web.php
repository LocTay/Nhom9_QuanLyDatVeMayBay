<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
// Trang chủ
Route::get('/', function () {
    $sanBays = DB::table('airports')
        ->orderBy('city')
        ->get();

    return view('home', compact('sanBays'));
});

// Tra cứu chuyến bay
Route::get('/flights', [FlightController::class, 'index'])
    ->name('flights.search');

// Đăng ký
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

// Đăng nhập (sẽ hoàn thiện ở bước tiếp theo)
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

 // Quên mật khẩu
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email');

// Đặt lại mật khẩu
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

// Tra cứu vé
Route::get('/tra-cuu-ve', [PaymentController::class, 'showLookupForm'])
    ->name('ticket.lookup.form');

Route::post('/tra-cuu-ve', [PaymentController::class, 'lookup'])
    ->name('ticket.lookup');

// Xem chi tiết vé
Route::get('/ve/{booking_code}', [PaymentController::class, 'showDetail'])
    ->name('ticket.detail');

// Mô phỏng thanh toán
Route::post('/ve/{booking_code}/thanh-toan', [PaymentController::class, 'processPayment'])
    ->name('ticket.payment');

// Hủy vé
Route::post('/ve/{booking_code}/huy', [PaymentController::class, 'cancelTicket'])
    ->name('ticket.cancel');
// Trang thông tin tài khoản
// Trang thông tin và chỉnh sửa tài khoản
Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/tai-khoan/chinh-sua', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/tai-khoan', [ProfileController::class, 'update'])
        ->name('profile.update');
});
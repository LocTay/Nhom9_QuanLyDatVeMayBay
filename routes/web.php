<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AdminAirlineController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminRefundController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminInvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminAirportController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminFlightController;

// Trang chủ
Route::get('/', function () {
    $sanBays = DB::table('airports')
        ->orderBy('city')
        ->get();

    return view('home', compact('sanBays'));
});

// Trang liên hệ
Route::get('/lien-he', [ContactController::class, 'index'])
    ->name('contact');

Route::post('/lien-he', [ContactController::class, 'send'])
    ->name('contact.send');

// Tìm kiếm chuyến bay
Route::get('/flights', [FlightController::class, 'index'])
    ->name('flights.search');

// Đặt vé
Route::get('/booking/create/{flight_id}', [BookingController::class, 'create'])
    ->name('booking.create');

Route::post('/booking/store', [BookingController::class, 'store'])
    ->name('booking.store');

Route::get('/booking/success/{id}', [BookingController::class, 'success'])
    ->name('booking.success');

// Đăng ký
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

// Đăng nhập
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

// Lịch sử đặt vé
Route::middleware(['auth'])->group(function () {
    Route::get('/lich-su-dat-ve', [PaymentController::class, 'history'])
        ->name('ticket.history');
});

// Chi tiết vé
Route::get('/ve/{booking_code}', [PaymentController::class, 'showDetail'])
    ->name('ticket.detail');

// Thanh toán
Route::post('/ve/{booking_code}/thanh-toan', [PaymentController::class, 'processPayment'])
    ->name('ticket.payment');

// Hủy vé
Route::post('/ve/{booking_code}/huy', [PaymentController::class, 'cancelTicket'])
    ->name('ticket.cancel');

// Thông tin tài khoản
Route::middleware(['auth'])->group(function () {
    Route::get('/tai-khoan', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/tai-khoan/chinh-sua', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/tai-khoan', [ProfileController::class, 'update'])
        ->name('profile.update');
});

// Quản lý sân bay, chuyến bay, hóa đơn và hoàn tiền - chỉ Admin
Route::middleware(['role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Quản lý khách hàng
        Route::get('/customers', [AdminCustomerController::class, 'index'])
            ->name('customers.index');

        Route::get('/customers/{user}', [AdminCustomerController::class, 'show'])
            ->name('customers.show');
        Route::resource('airports', AdminAirportController::class)
            ->except(['show']);

        Route::resource('flights', AdminFlightController::class)
            ->except(['show']);

        // Quản lý hóa đơn
        Route::get('/invoices', [AdminInvoiceController::class, 'index'])
            ->name('invoices.index');

        Route::get('/invoices/{booking}', [AdminInvoiceController::class, 'show'])
            ->name('invoices.show');

        // Quản lý yêu cầu hoàn tiền
        Route::get('/refunds', [AdminRefundController::class, 'index'])
            ->name('refunds.index');

        Route::get('/refunds/{booking}', [AdminRefundController::class, 'show'])
            ->name('refunds.show');

        Route::post('/refunds/{booking}/complete', [AdminRefundController::class, 'complete'])
            ->name('refunds.complete');

        // Quản lý hãng hàng không
        Route::resource('airlines', AdminAirlineController::class)
            ->except(['show']);

        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Duyệt thanh toán chuyển khoản
        Route::post('/ve/{booking_code}/duyet-thanh-toan', [PaymentController::class, 'adminApprovePayment'])
            ->name('ticket.approve');

        // Hủy đơn đang chờ Admin duyệt
        Route::post('/ve/{booking_code}/huy-cho-duyet', [PaymentController::class, 'adminCancelPendingApproval'])
            ->name('ticket.cancel-pending');
    });

// Trang thanh toán QR
Route::get('/ve/{booking_code}/thanh-toan', [PaymentController::class, 'showPaymentPage'])
    ->name('ticket.payment.page');

// Khách hàng xác nhận chuyển khoản
Route::post('/ve/{booking_code}/xac-nhan-chuyen-khoan', [PaymentController::class, 'submitTransfer'])
    ->name('ticket.payment.submit');

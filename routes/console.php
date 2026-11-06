<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Lệnh mặc định của Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tự động hủy đơn chờ duyệt thanh toán đã quá hạn 2 phút
Schedule::command('bookings:expire-pending-approvals')
    ->everyMinute()
    ->withoutOverlapping();

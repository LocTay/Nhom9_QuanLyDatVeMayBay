<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\BookingApprovalService;

class ExpirePendingApprovals extends Command
{
    protected $signature = 'bookings:expire-pending-approvals';

    protected $description = 'Tu dong huy don chua thanh toan va don het han cho Admin duyet';

    public function handle(BookingApprovalService $approvalService): int
    {
        $now = now();

        // 1. Tìm đơn vừa đặt nhưng chưa thanh toán quá 2 phút.
        $unpaidBookings = Booking::whereIn('status', ['pending', 'failed'])
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', $now)
            ->pluck('booking_code');

        $cancelledUnpaid = 0;

        foreach ($unpaidBookings as $bookingCode) {
            $result = $approvalService->cancelExpiredUnpaidBooking(
                $bookingCode
            );

            if ($result === 'cancelled') {
                $cancelledUnpaid++;
            }
        }

        // 2. Tìm đơn đã xác nhận chuyển khoản nhưng Admin chưa duyệt.
        $approvalBookings = Booking::where('status', 'pending_approval')
            ->whereNotNull('approval_expires_at')
            ->where('approval_expires_at', '<=', $now)
            ->pluck('booking_code');

        $cancelledApproval = 0;

        foreach ($approvalBookings as $bookingCode) {
            $result = $approvalService->cancelPendingApproval(
                $bookingCode,
                'Đơn tự động hủy do quá 2 phút chờ Admin duyệt thanh toán.'
            );

            if ($result === 'cancelled') {
                $cancelledApproval++;
            }
        }

        $this->info(
            "Đơn chưa thanh toán tự hủy: {$cancelledUnpaid}"
        );

        $this->info(
            "Đơn quá hạn chờ Admin duyệt tự hủy: {$cancelledApproval}"
        );

        return self::SUCCESS;
    }
}
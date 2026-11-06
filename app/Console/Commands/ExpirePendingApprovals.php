<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\BookingApprovalService;

class ExpirePendingApprovals extends Command
{
    protected $signature = 'bookings:expire-pending-approvals';

    protected $description = 'Tự động hủy các đơn chờ duyệt thanh toán đã quá 2 phút';

    public function handle(BookingApprovalService $approvalService): int
    {
        // Tìm các đơn đang chờ duyệt và đã hết hạn
        $bookingCodes = Booking::where('status', 'pending_approval')
            ->whereNotNull('approval_expires_at')
            ->where('approval_expires_at', '<=', now())
            ->pluck('booking_code');

        $cancelled = 0;

        foreach ($bookingCodes as $bookingCode) {
            // Gọi service để hủy đơn và hoàn lại ghế
            $result = $approvalService->cancelPendingApproval(
                $bookingCode,
                'Đơn tự động hủy do quá 2 phút chờ Admin duyệt thanh toán.'
            );

            if ($result === 'cancelled') {
                $cancelled++;

                $this->info("Đã tự động hủy đơn: {$bookingCode}");
            }
        }

        $this->info("Hoàn tất. Số đơn đã tự động hủy: {$cancelled}");

        return self::SUCCESS;
    }
}

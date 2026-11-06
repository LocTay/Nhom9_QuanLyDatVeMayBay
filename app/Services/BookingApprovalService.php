<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Support\Facades\DB;

class BookingApprovalService
{
    /**
     * Admin duyệt thanh toán khi đơn vẫn còn hạn.
     */
    public function approvePendingApproval(string $bookingCode): string
    {
        return DB::transaction(function () use ($bookingCode) {
            $booking = Booking::where('booking_code', $bookingCode)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                return 'not_found';
            }

            if ($booking->status !== 'pending_approval') {
                return 'not_pending';
            }

            if (
                !$booking->approval_expires_at ||
                $booking->approval_expires_at->lte(now())
            ) {
                $this->cancelLockedBooking(
                    $booking,
                    'Đơn đã hết thời hạn chờ Admin duyệt thanh toán.'
                );

                return 'expired';
            }

            $booking->status = 'paid';
            $booking->payment_time = now();
            $booking->approval_expires_at = null;
            $booking->save();

            return 'approved';
        });
    }

    /**
     * Hủy đơn đang chờ duyệt.
     */
    public function cancelPendingApproval(
        string $bookingCode,
        string $reason
    ): string {
        return DB::transaction(function () use ($bookingCode, $reason) {
            $booking = Booking::where('booking_code', $bookingCode)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                return 'not_found';
            }

            if ($booking->status !== 'pending_approval') {
                return 'not_pending';
            }

            $this->cancelLockedBooking($booking, $reason);

            return 'cancelled';
        });
    }

    /**
     * Chỉ hoàn ghế khi đơn thực sự chuyển từ pending_approval sang cancelled.
     * Việc khóa đơn và chuyến bay giúp tránh hoàn ghế hai lần.
     */
    private function cancelLockedBooking(
        Booking $booking,
        string $reason
    ): void {
        $flight = Flight::whereKey($booking->flight_id)
            ->lockForUpdate()
            ->first();

        if ($flight) {
            $seats = $booking->passenger_count
                ?? $booking->number_of_seats
                ?? 1;

            $flight->increment('available_seats', $seats);
        }

        $booking->status = 'cancelled';
        $booking->cancellation_reason = $reason;
        $booking->approval_expires_at = null;
        $booking->save();
    }
}

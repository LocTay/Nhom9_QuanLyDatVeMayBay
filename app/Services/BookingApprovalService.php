<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Support\Facades\DB;

class BookingApprovalService
{
    /**
     * Hủy đơn vừa đặt nhưng chưa thanh toán khi quá thời hạn.
     */
    public function cancelExpiredUnpaidBooking(string $bookingCode): string
    {
        return DB::transaction(function () use ($bookingCode) {
            $booking = Booking::where('booking_code', $bookingCode)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                return 'not_found';
            }

            if (!in_array($booking->status, ['pending', 'failed'], true)) {
                return 'not_unpaid';
            }

            if (
                !$booking->payment_expires_at ||
                $booking->payment_expires_at->gt(now())
            ) {
                return 'not_expired';
            }

            $this->cancelLockedBooking(
                $booking,
                'Đơn tự động hủy do khách hàng chưa thanh toán trong 2 phút.'
            );

            return 'cancelled';
        });
    }

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
            $booking->payment_expires_at = null;
            $booking->approval_expires_at = null;
            $booking->save();

            return 'approved';
        });
    }

    /**
     * Hủy đơn đang chờ Admin duyệt.
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
     * Hủy đơn và hoàn ghế trong cùng transaction.
     * Khóa booking giúp ngăn hoàn ghế hai lần.
     */
    private function cancelLockedBooking(
        Booking $booking,
        string $reason
    ): void {
        $flight = Flight::whereKey($booking->flight_id)
            ->lockForUpdate()
            ->first();

        if ($flight) {
            $seats = (int) (
                $booking->passenger_count
                ?? $booking->number_of_seats
                ?? 1
            );

            if ($seats > 0) {
                $flight->increment('available_seats', $seats);
            }
        }

        $booking->status = 'cancelled';
        $booking->cancellation_reason = $reason;
        $booking->payment_expires_at = null;
        $booking->approval_expires_at = null;
        $booking->save();
    }
}
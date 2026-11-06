<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Flight;
use App\Services\BookingApprovalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // 1. Hiển thị form tra cứu
    public function showLookupForm()
    {
        return view('ticket.lookup');
    }

    // 2. Xử lý tìm kiếm mã đặt chỗ
    public function lookup(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string',
        ], [
            'booking_code.required' => 'Vui lòng nhập mã đặt chỗ!'
        ]);

        $code = strtoupper(trim($request->booking_code));

        $booking = Booking::where('booking_code', $code)->first();

        if (!$booking) {
            return redirect()->back()->with(
                'error',
                'Không tìm thấy mã đặt chỗ ' . $code . ' trên hệ thống!'
            );
        }

        return redirect()->route('ticket.detail', [
            'booking_code' => $booking->pnr_code ?: $booking->booking_code
        ]);
    }

    // 3. Hiển thị chi tiết vé
    public function showDetail($booking_code)
    {
        $booking = Booking::with([
            'flight.departureAirport',
            'flight.arrivalAirport'
        ])
            ->where(function ($query) use ($booking_code) {
                $query->where('pnr_code', $booking_code)
                    ->orWhere('booking_code', $booking_code);
            })
            ->firstOrFail();

        return view('ticket.detail', compact('booking'));
    }

    // 4. Xử lý mô phỏng thanh toán
    public function processPayment(Request $request, $booking_code)
    {
        $booking = Booking::where('pnr_code', $booking_code)
            ->orWhere('booking_code', $booking_code)
            ->firstOrFail();

        if ($booking->status === 'paid') {
            return redirect()->back()->with(
                'info',
                'Vé này đã được thanh toán trước đó.'
            );
        }

        if (in_array($booking->status, ['cancelled', 'pending_approval'], true)) {
            return redirect()->back()->with(
                'error',
                'Vé không thể thực hiện thanh toán ở trạng thái hiện tại!'
            );
        }

        $paymentStatus = $request->input('payment_status', 'success');

        if ($paymentStatus === 'success') {
            $booking->status = 'paid';
            $booking->payment_time = now();
            $booking->save();

            return redirect()->back()->with(
                'success',
                'Thanh toán thành công! Vé của bạn đã được xác nhận.'
            );
        }

        $booking->status = 'failed';
        $booking->save();

        return redirect()->back()->with(
            'error',
            'Thanh toán thất bại! Vui lòng thử lại.'
        );
    }

    // 5. Quy tắc hủy vé hiện có
    public function cancelTicket(Request $request, $booking_code)
    {
        $booking = Booking::with('flight')
            ->where('pnr_code', $booking_code)
            ->firstOrFail();

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with(
                'info',
                'Vé này đã được hủy từ trước.'
            );
        }

        if (!$booking->flight) {
            return redirect()->back()->with(
                'error',
                'Không tìm thấy thông tin chuyến bay liên quan đến vé này.'
            );
        }

        return redirect()->back()->with(
            'error',
            'Chức năng hủy vé theo chính sách hiện chưa được hoàn thiện.'
        );
    }

    // 6. Lịch sử đặt vé
    public function history()
    {
        $bookings = Booking::with([
            'flight.departureAirport',
            'flight.arrivalAirport'
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ticket.history', compact('bookings'));
    }

    // 7. Trang QR thanh toán
    public function showPaymentPage($booking_code)
    {
        $booking = Booking::with([
            'flight.departureAirport',
            'flight.arrivalAirport'
        ])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        if ($booking->status === 'paid') {
            return redirect()->route('ticket.detail', $booking_code)
                ->with('info', 'Vé này đã được thanh toán thành công.');
        }

        if ($booking->status === 'cancelled') {
            return redirect()->route('ticket.detail', $booking_code)
                ->with('error', 'Vé này đã bị hủy.');
        }

        return view('ticket.payment', compact('booking'));
    }

    // 8. Khách xác nhận đã chuyển khoản
    public function submitTransfer($booking_code)
    {
        $result = DB::transaction(function () use ($booking_code) {
            $booking = Booking::where('booking_code', $booking_code)
                ->lockForUpdate()
                ->firstOrFail();

            if ($booking->status === 'paid') {
                return 'paid';
            }

            if ($booking->status === 'cancelled') {
                return 'cancelled';
            }

            if ($booking->status === 'pending_approval') {
                return 'already_pending';
            }

            if (!in_array($booking->status, ['pending', 'failed'], true)) {
                return 'invalid_status';
            }

            $booking->status = 'pending_approval';
            $booking->payment_method = 'bank_transfer';
            $booking->payment_confirmed_at = now();
            $booking->approval_expires_at = now()->addMinutes(2);
            $booking->save();

            return 'submitted';
        });

        if ($result === 'paid') {
            return redirect()->route('ticket.detail', $booking_code)
                ->with('info', 'Vé này đã được thanh toán.');
        }

        if ($result === 'cancelled') {
            return redirect()->route('ticket.detail', $booking_code)
                ->with('error', 'Vé đã bị hủy, không thể xác nhận chuyển khoản.');
        }

        if ($result === 'already_pending') {
            return redirect()->route('ticket.detail', $booking_code)
                ->with('info', 'Đơn của bạn đang chờ Admin duyệt thanh toán.');
        }

        if ($result === 'invalid_status') {
            return redirect()->route('ticket.detail', $booking_code)
                ->with('error', 'Trạng thái vé không hợp lệ để xác nhận chuyển khoản.');
        }

        return redirect()->route('ticket.detail', $booking_code)
            ->with('success', 'Đã ghi nhận xác nhận chuyển khoản. Đơn đang chờ Admin duyệt trong 2 phút.');
    }

    // 9. Admin duyệt thanh toán
    public function adminApprovePayment(
        $booking_code,
        BookingApprovalService $approvalService
    ) {
        $result = $approvalService->approvePendingApproval($booking_code);

        if ($result === 'approved') {
            return redirect()->back()->with(
                'success',
                'Đã duyệt thanh toán cho mã vé ' . $booking_code . '.'
            );
        }

        if ($result === 'expired') {
            return redirect()->back()->with(
                'error',
                'Đơn đã hết hạn 2 phút và đã được hủy, ghế đã được hoàn lại.'
            );
        }

        if ($result === 'not_found') {
            abort(404);
        }

        return redirect()->back()->with(
            'error',
            'Đơn không còn ở trạng thái chờ duyệt.'
        );
    }

    // 10. Admin chủ động hủy đơn chờ duyệt
    public function adminCancelPendingApproval(
        $booking_code,
        BookingApprovalService $approvalService
    ) {
        $result = $approvalService->cancelPendingApproval(
            $booking_code,
            'Admin đã hủy đơn trong thời gian chờ duyệt chuyển khoản.'
        );

        if ($result === 'cancelled') {
            return redirect()->back()->with(
                'success',
                'Đã hủy đơn và hoàn lại số ghế đã giữ.'
            );
        }

        if ($result === 'not_found') {
            abort(404);
        }

        return redirect()->back()->with(
            'error',
            'Đơn không còn ở trạng thái chờ duyệt.'
        );
    }
}
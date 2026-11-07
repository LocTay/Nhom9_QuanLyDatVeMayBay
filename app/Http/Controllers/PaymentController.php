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
    
// 4. Xử lý mô phỏng thanh toán
public function processPayment(
    Request $request,
    $booking_code,
    BookingApprovalService $approvalService
) {
    $result = DB::transaction(function () use ($request, $booking_code) {
        $booking = Booking::where(function ($query) use ($booking_code) {
            $query->where('pnr_code', $booking_code)
                ->orWhere('booking_code', $booking_code);
        })
            ->lockForUpdate()
            ->firstOrFail();

        if ($booking->status === 'paid') {
            return 'already_paid';
        }

        if ($booking->status === 'cancelled') {
            return 'cancelled';
        }

        if ($booking->status === 'pending_approval') {
            return 'pending_approval';
        }

        if (!in_array($booking->status, ['pending', 'failed'], true)) {
            return 'invalid_status';
        }

        // Kiểm tra thời hạn thanh toán 2 phút
        if (
            $booking->payment_expires_at &&
            $booking->payment_expires_at->lte(now())
        ) {
            return 'expired_unpaid';
        }

        $paymentStatus = $request->input('payment_status', 'success');

        if ($paymentStatus === 'success') {
            $booking->status = 'paid';
            $booking->payment_time = now();
            $booking->payment_expires_at = null;
            $booking->save();

            return 'paid_now';
        }

        $booking->status = 'failed';
        $booking->save();

        return 'failed';
    });

    // Đơn hết hạn phải được hủy và hoàn ghế
    if ($result === 'expired_unpaid') {
        $approvalService->cancelExpiredUnpaidBooking($booking_code);

        return redirect()->back()->with(
            'error',
            'Đơn đã quá thời hạn thanh toán 2 phút và đã bị hủy.'
        );
    }

    if ($result === 'already_paid') {
        return redirect()->back()->with(
            'info',
            'Vé này đã được thanh toán trước đó.'
        );
    }

    if ($result === 'cancelled') {
        return redirect()->back()->with(
            'error',
            'Vé đã bị hủy, không thể thanh toán.'
        );
    }

    if ($result === 'pending_approval') {
        return redirect()->back()->with(
            'info',
            'Đơn đang chờ Admin duyệt chuyển khoản.'
        );
    }

    if ($result === 'invalid_status') {
        return redirect()->back()->with(
            'error',
            'Trạng thái vé không hợp lệ để thanh toán.'
        );
    }

    if ($result === 'failed') {
        return redirect()->back()->with(
            'error',
            'Thanh toán thất bại! Vui lòng thử lại.'
        );
    }

    return redirect()->back()->with(
        'success',
        'Thanh toán thành công! Vé của bạn đã được xác nhận.'
    );
}


    // 5. Quy tắc hủy vé hiện có
    
public function cancelTicket(Request $request, $booking_code)
{
    $result = DB::transaction(function () use ($booking_code) {

        // Khóa đơn đặt vé để tránh xử lý hủy nhiều lần
        $booking = Booking::where('pnr_code', $booking_code)
            ->lockForUpdate()
            ->firstOrFail();

        // Chỉ chủ sở hữu được hủy nếu đơn gắn với tài khoản
        // Đơn đặt vé của khách không đăng nhập được tra cứu bằng PNR
        if (
            $booking->user_id !== null
            && (int) $booking->user_id !== (int) Auth::id()
        ) {
            return 'forbidden';
        }

        // Không hoàn ghế nếu vé đã bị hủy trước đó
        if ($booking->status === 'cancelled') {
            return 'already_cancelled';
        }

        // Đơn đang chờ Admin duyệt phải xử lý theo quy trình riêng
        if ($booking->status === 'pending_approval') {
            return 'pending_approval';
        }

        // Khóa chuyến bay liên quan
        $flight = $booking->flight()
            ->lockForUpdate()
            ->first();

        if (!$flight) {
            return 'flight_not_found';
        }

        // Xác định số ghế cần hoàn trả
        $seatCount = (int) (
            $booking->passenger_count
            ?: $booking->number_of_seats
            ?: 1
        );

        if ($seatCount < 1) {
            return 'invalid_seat_count';
        }

        // Cập nhật trạng thái đơn
        $booking->status = 'cancelled';
        $booking->cancellation_reason = 'Khách hàng chủ động hủy vé';
        $booking->approval_expires_at = null;
        $booking->save();

        // Hoàn trả số ghế đã đặt
        $flight->increment('available_seats', $seatCount);

        return 'success';
    });

    // Trả thông báo tương ứng
    switch ($result) {
        case 'success':
            return redirect()->back()->with(
                'success',
                'Hủy vé thành công. Số ghế đã được hoàn trả.'
            );

        case 'already_cancelled':
            return redirect()->back()->with(
                'info',
                'Vé này đã được hủy từ trước.'
            );

        case 'forbidden':
            abort(403, 'Bạn không có quyền hủy đặt vé này.');

        case 'pending_approval':
            return redirect()->back()->with(
                'error',
                'Đơn đang chờ Admin duyệt, không thể hủy bằng chức năng này.'
            );

        case 'flight_not_found':
            return redirect()->back()->with(
                'error',
                'Không tìm thấy chuyến bay liên quan đến vé này.'
            );

        default:
            return redirect()->back()->with(
                'error',
                'Không thể thực hiện hủy vé. Vui lòng kiểm tra lại thông tin.'
            );
    }
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
    
// 8. Khách xác nhận đã chuyển khoản
public function submitTransfer(
    $booking_code,
    BookingApprovalService $approvalService
) {
    $result = DB::transaction(function () use ($booking_code) {
        $booking = Booking::where(function ($query) use ($booking_code) {
            $query->where('booking_code', $booking_code)
                ->orWhere('pnr_code', $booking_code);
        })
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

        // Không được xác nhận chuyển khoản sau khi đơn hết hạn
        if (
            $booking->payment_expires_at &&
            $booking->payment_expires_at->lte(now())
        ) {
            return 'expired_unpaid';
        }

        // Chuyển sang quy trình chờ Admin duyệt riêng
        $booking->status = 'pending_approval';
        $booking->payment_method = 'bank_transfer';
        $booking->payment_confirmed_at = now();
        $booking->approval_expires_at = now()->addMinutes(2);

        // Kết thúc thời hạn thanh toán ban đầu
        $booking->payment_expires_at = null;

        $booking->save();

        return 'submitted';
    });

    if ($result === 'expired_unpaid') {
        $approvalService->cancelExpiredUnpaidBooking($booking_code);

        return redirect()->route('ticket.detail', $booking_code)
            ->with(
                'error',
                'Đơn đã hết hạn thanh toán và đã được tự động hủy.'
            );
    }

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
        ->with(
            'success',
            'Đã ghi nhận xác nhận chuyển khoản. Đơn đang chờ Admin duyệt trong 2 phút.'
        );
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
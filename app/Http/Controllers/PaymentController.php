<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

        $booking = Booking::where('pnr_code', $code)
            ->orWhere('booking_code', $code)
            ->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'Không tìm thấy mã đặt chỗ ' . $code . ' trên hệ thống!');
        }

        return redirect()->route('ticket.detail', [
            'booking_code' => $booking->pnr_code ?: $booking->booking_code
        ]);
    }

    // 3. Hiển thị chi tiết vé & chuyến bay, hành khách
    public function showDetail($booking_code)
    {
        $booking = Booking::with(['flight.departureAirport', 'flight.arrivalAirport'])
            ->where(function ($query) use ($booking_code) {
                $query->where('pnr_code', $booking_code)
                    ->orWhere('booking_code', $booking_code);
            })
            ->firstOrFail();

        return view('ticket.detail', compact('booking'));
    }

    // 4. Xử lý mô phỏng thanh toán (pending -> paid / failed / pending)
    public function processPayment(Request $request, $booking_code)
    {
        $booking = Booking::where('pnr_code', $booking_code)
            ->orWhere('booking_code', $booking_code)->firstOrFail();

        if ($booking->status === 'paid') {
            return redirect()->back()->with('info', 'Vé này đã được thanh toán trước đó.');
        }

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'Vé này đã bị hủy, không thể thực hiện thanh toán!');
        }

        $paymentStatus = $request->input('payment_status', 'success');

        if ($paymentStatus === 'success') {
            $booking->status = 'paid';
            $booking->payment_time = now();
            $booking->payment_method = $request->input('payment_method', 'Mô phỏng VNPay/MoMo');
            $booking->save();

            return redirect()->back()->with('success', 'Thanh toán thành công! Vé của bạn đã được xác nhận.');
        } elseif ($paymentStatus === 'pending') {
            $booking->status = 'pending';
            $booking->save();

            return redirect()->back()->with('warning', 'Vé đang ở trạng thái chờ thanh toán.');
        } else {
            $booking->status = 'failed';
            $booking->save();
            return redirect()->back()->with('error', 'Thanh toán thất bại! Vui lòng thử lại.');
        }
    }

    // 5. Quy tắc hủy vé (Hủy trước 24h, cộng trả lại ghế trống & mô phỏng hoàn tiền)
    public function cancelTicket(Request $request, $booking_code)
    {
        $booking = Booking::with('flight')->where('pnr_code', $booking_code)->firstOrFail();

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('info', 'Vé này đã được hủy từ trước.');
        }

        // Kiểm tra an toàn xem dữ liệu chuyến bay có tồn tại hay không
        if (!$booking->flight) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin chuyến bay liên quan đến vé này.');
        }

        // Kiểm tra an toàn giờ khởi hành
        if (!empty($booking->flight->departure_time)) {
            $departureTime = Carbon::parse($booking->flight->departure_time);
            if (Carbon::now()->diffInHours($departureTime, false) < 24) {
                return redirect()->back()->with('error', 'Không thể hủy vé! Quy định hệ thống chỉ hỗ trợ hủy trước giờ khởi hành 24 tiếng.');
            }
        }

        // Mô phỏng trạng thái hoàn tiền nếu vé đã thanh toán trước đó
        if ($booking->status === 'paid') {
            $booking->refund_status = 'pending_refund'; // Chờ hoàn tiền
        } else {
            $booking->refund_status = 'no_refund';
        }

        // Đổi trạng thái vé & lưu lý do
        $booking->status = 'cancelled';
        $booking->cancellation_reason = $request->input('reason', 'Khách hàng yêu cầu hủy vé');
        $booking->save();


        // Hoàn trả số ghế trống cho chuyến bay
        if ($booking->flight) {
            $seatsToReturn = $booking->passenger_count
                ?? $booking->number_of_seats
                ?? 1;

            $booking->flight->increment('available_seats', $seatsToReturn);
        }

        $msg = ($booking->refund_status === 'pending_refund')
            ? 'Hủy vé thành công! Yêu cầu hoàn tiền của bạn đã được gửi và đang xử lý.'
            : 'Hủy đặt chỗ thành công! Ghế trống đã được cập nhật lại cho chuyến bay.';

        return redirect()->back()->with('success', $msg);
    }

    // 6. Xem lịch sử đặt vé của khách hàng đang đăng nhập
    public function history()
    {
        $bookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();


        return view('ticket.history', compact('bookings'));
    }
}

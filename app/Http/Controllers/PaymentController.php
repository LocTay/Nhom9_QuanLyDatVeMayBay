<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

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
            return redirect()->back()->with('error', 'Không tìm thấy mã đặt chỗ ' . $code . ' trên hệ thống!');
        }

        return redirect()->route('ticket.detail', ['booking_code' => $booking->booking_code]);
    }

    // 3. Hiển thị chi tiết vé & chuyến bay, hành khách
    public function showDetail($booking_code)
    {
        $booking = Booking::with(['flight.departureAirport', 'flight.arrivalAirport'])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        return view('ticket.detail', compact('booking'));
    }

    // 4. Xử lý mô phỏng thanh toán (pending -> paid / failed)
    public function processPayment(Request $request, $booking_code)
    {
        $booking = Booking::where('booking_code', $booking_code)->firstOrFail();

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
            $booking->save();

            return redirect()->back()->with('success', 'Thanh toán thành công! Vé của bạn đã được xác nhận.');
        } else {
            $booking->status = 'failed';
            $booking->save();

            return redirect()->back()->with('error', 'Thanh toán thất bại! Vui lòng thử lại.');
        }
    }

    // 5. Quy tắc hủy vé (Hủy trước 24h & cộng trả lại ghế trống)
    public function cancelTicket($booking_code)
    {
        $booking = Booking::with('flight')->where('booking_code', $booking_code)->firstOrFail();

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('info', 'Vé này đã được hủy từ trước.');
        }

        // Kiểm tra giờ khởi hành (hủy trước 24h)
        $departureTime = Carbon::parse($booking->flight->departure_time);
        if (Carbon::now()->diffInHours($departureTime, false) < 24) {
            return redirect()->back()->with('error', 'Không thể hủy vé! Quy định hệ thống chỉ hỗ trợ hủy trước giờ khởi hành 24 tiếng.');
        }

        // Đổi trạng thái vé
        $booking->status = 'cancelled';
        $booking->save();

        // Hoàn trả số ghế trống cho chuyến bay
        if ($booking->flight) {
            $booking->flight->increment('available_seats', $booking->number_of_seats);
        }

        return redirect()->back()->with('success', 'Hủy vé thành công! Ghế trống đã được cập nhật lại cho chuyến bay.');
    }
}
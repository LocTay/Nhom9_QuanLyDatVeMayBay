<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Flight;
use Carbon\Carbon;
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
            return redirect()->back()->with('error', 'Không tìm thấy mã đặt chỗ ' . $code . ' trên hệ thống!');
        }

        return redirect()->route('ticket.detail', ['booking_code' => $booking->booking_code]);
    }

    // 3. Hiển thị chi tiết vé (Đã bỏ load relation 'passengers' vì lấy từ cột JSON)
    public function showDetail($booking_code)
    {
        $booking = Booking::with(['flight.departureAirport', 'flight.arrivalAirport'])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        return view('ticket.detail', compact('booking'));
    }

    // 4. Xử lý mô phỏng thanh toán
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
    public function cancelTicket(Request $request, $booking_code)
    {
        $booking = Booking::with('flight')->where('booking_code', $booking_code)->firstOrFail();

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('info', 'Vé này đã được hủy từ trước.');
        }

        // Kiểm tra an toàn dữ liệu chuyến bay
        if (!$booking->flight) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin chuyến bay liên quan đến vé này.');
        }

        // Kiểm tra điều kiện thời gian (Hủy trước 24h)
        if (!empty($booking->flight->departure_time)) {
            $departureTime = Carbon::parse($booking->flight->departure_time);
            
            if (Carbon::now()->isAfter($departureTime)) {
                return redirect()->back()->with('error', 'Không thể hủy vé cho chuyến bay đã cất cánh!');
            }

            if (Carbon::now()->diffInHours($departureTime, false) < 24) {
                return redirect()->back()->with('error', 'Không thể hủy vé! Hệ thống chỉ hỗ trợ hủy trước giờ khởi hành tối thiểu 24 tiếng.');
            }
        }

        $seatsToRestore = max((int)($booking->number_of_seats ?? 1), 1);

        // Thực thi Transaction để cập nhật CSDL chính xác
        DB::transaction(function () use ($booking, $seatsToRestore) {
            // 1. Cập nhật trạng thái vé
            $booking->status = 'cancelled';
            $booking->save();

            // 2. Trực tiếp tăng số ghế khả dụng trong bảng flights
            if ($booking->flight_id) {
                Flight::where('id', $booking->flight_id)->increment('available_seats', $seatsToRestore);
            }
        });

        return redirect()->back()->with('success', "Hủy vé thành công! Đã hoàn trả lại {$seatsToRestore} ghế trống cho chuyến bay.");
    }



    // 6. Lịch sử đặt vé của tài khoản đang đăng nhập
    public function history()
    {
        $bookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ticket.history', compact('bookings'));
    }

    // Trang hiển thị QR thanh toán
    public function showPaymentPage($booking_code)
    {
        $booking = Booking::with(['flight.departureAirport', 'flight.arrivalAirport'])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        if ($booking->status === 'paid') {
            return redirect()->route('ticket.detail', $booking_code)->with('info', 'Vé này đã được thanh toán thành công.');
        }

        return view('ticket.payment', compact('booking'));
    }

    // Khách hàng bấm "Xác nhận chuyển khoản" -> Đổi trạng thái thành "pending_approval"
    public function submitTransfer($booking_code)
    {
        $booking = Booking::where('booking_code', $booking_code)->firstOrFail();

        if ($booking->status === 'cancelled') {
            return redirect()->route('ticket.detail', $booking_code)->with('error', 'Vé đã bị hủy, không thể thanh toán!');
        }

        $booking->status = 'pending_approval'; // Đang chờ duyệt
        $booking->save();

        return redirect()->route('ticket.detail', $booking_code)
            ->with('info', 'Hệ thống đã ghi nhận thông tin! Đơn đặt vé của bạn đang chờ Admin xác nhận thanh toán.');
    }

    // Mô phỏng Admin duyệt thanh toán -> Đổi trạng thái thành "paid" (Thành công)
    public function adminApprovePayment($booking_code)
    {
        $booking = Booking::where('booking_code', $booking_code)->firstOrFail();

        $booking->status = 'paid';
        $booking->payment_time = now();
        $booking->save();

        return redirect()->back()->with('success', 'Admin đã duyệt thanh toán thành công cho mã vé ' . $booking_code);
    }
}
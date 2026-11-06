<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;
use App\Models\Booking;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{

    public function create(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);
        
        // 1. Kiểm tra ràng buộc: Chặn ngay nếu còn dưới 3 tiếng nữa là bay hoặc đã bay
        $departureTime = Carbon::parse($flight->departure_time);
        if (Carbon::now()->addHours(3)->greaterThan($departureTime)) {
            return redirect()->route('flights.search') 
                ->with('error', 'Chuyến bay này đã khởi hành hoặc còn dưới 3 tiếng nữa là khởi hành, không thể đặt vé.');
        }

        // 2. Nhận thêm thông tin loại vé và hạng vé từ URL
        $tripType = $request->query('trip_type', 'one_way');
        $flightClass = $request->query('flight_class', 'pho_thong');
        $ngayVe = $request->query('ngay_ve');
        $passengerCount = $request->query('passenger_count', 1);

        // Lấy tất cả các booking của chuyến bay này để gom ghế đã đặt
        $existingBookings = \App\Models\Booking::where('flight_id', $id)->get();
        $bookedSeats = [];

        foreach ($existingBookings as $booking) {
            if (!empty($booking->passengers_data) && is_array($booking->passengers_data)) {
                foreach ($booking->passengers_data as $p) {
                    if (!empty($p['seat'])) {
                        $bookedSeats[] = $p['seat'];
                    }
                }
            }
        }

        // Đếm số ghế đã đặt thực tế cho từng khoang (A, B, C)
        $bookedCounts = ['A' => 0, 'B' => 0, 'C' => 0];
        foreach ($bookedSeats as $seat) {
            $prefix = substr($seat, 0, 1);
            if (isset($bookedCounts[$prefix])) {
                $bookedCounts[$prefix]++;
            }
        }

        // Quy định số lượng cố định cho khoang A và B
        $maxA = 10; 
        $maxB = 10; 

        // Khoang Phổ thông (C) linh động theo tổng số ghế trống thực tế của chuyến bay
        $totalAvailableFlightSeats = $flight->available_seats;
        $maxC = max(0, $totalAvailableFlightSeats - $maxA - $maxB);

        $maxSeats = [
            'A' => $maxA,
            'B' => $maxB,
            'C' => $maxC > 0 ? $maxC : 80, 
        ];

        $remainingSeats = [
            'A' => max(0, $maxSeats['A'] - $bookedCounts['A']),
            'B' => max(0, $maxSeats['B'] - $bookedCounts['B']),
            'C' => max(0, $maxSeats['C'] - $bookedCounts['C']),
        ];

        // 3. Tính toán hệ số hạng vé và hệ số khứ hồi chuẩn xác khớp với trang tìm kiếm
        $classMultiplier = 1.0;
        switch ($flightClass) {
            case 'pho_thong': 
                $classMultiplier = 1.0; 
                break;
            case 'pho_thong_dac_biet': 
                $classMultiplier = 1.4; 
                break;
            case 'thuong_gia': 
            case 'Thương gia': 
                $classMultiplier = 2.2; 
                break;
            default: 
                $classMultiplier = 1.0; 
                break;
        }

        $isRoundTrip = ($tripType === 'round_trip' || $tripType === 'khu_hoi');
        $tripFactor = 1.0;
        if ($isRoundTrip) {
            switch ($flightClass) {
                case 'pho_thong': 
                    $tripFactor = 1.95; 
                    break;
                case 'pho_thong_dac_biet': 
                    $tripFactor = 1.90; 
                    break;
                case 'thuong_gia': 
                case 'Thương gia': 
                    $tripFactor = 1.85; 
                    break;
                default: 
                    $tripFactor = 1.90; 
                    break;
            }
        }

        // Tính tiền tạm tính chính xác 100% so với ngoài trang tìm kiếm
        $subTotal = $flight->price * $classMultiplier * $tripFactor * $passengerCount;

        // Định nghĩa danh sách các mức ưu đãi hệ thống
        $availableVouchers = [
            [
                'code' => 'GIAMGIA5',
                'title' => 'Giảm 5% cho đơn từ 2.000.000 VNĐ',
                'min_spend' => 2000000,
                'discount_percent' => 5,
            ],
            [
                'code' => 'GIAMGIA10',
                'title' => 'Giảm 10% cho đơn từ 5.000.000 VNĐ',
                'min_spend' => 5000000,
                'discount_percent' => 10,
            ],
        ];

        return view('booking.create', compact(
            'flight', 
            'tripType', 
            'flightClass', 
            'ngayVe', 
            'bookedSeats', 
            'remainingSeats', 
            'maxSeats',
            'subTotal',          
            'availableVouchers'  
        ));
    }
    public function store(Request $request)
    {
        $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => ['required', 'string', 'regex:/^0[0-9]{9,10}$/'],
            'passenger_count' => 'required|integer|min:1',
            'trip_type' => 'required|string',
            'flight_class' => 'required|string',
        ], [
            'passenger_phone.required' => 'Vui lòng nhập số điện thoại.',
            'passenger_phone.regex' => 'Số điện thoại không hợp lệ (phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số).',
        ]);

        // Lấy thông tin chuyến bay và kiểm tra tổng số ghế trống còn lại của chuyến
        $flight = Flight::findOrFail($request->flight_id);
        // Kiểm tra: Thời gian hiện tại cộng thêm 3 tiếng phải nhỏ hơn thời gian khởi hành của chuyến bay
        $departureTime = Carbon::parse($flight->departure_time);

        if (Carbon::now()->addHours(3)->greaterThan($departureTime)) {
            return redirect()->back()
                ->withErrors(['flight_id' => 'Chuyến bay này đã khởi hành hoặc còn dưới 3 tiếng nữa là khởi hành, không thể đặt vé.'])
                ->withInput();
        }
        if ($flight->available_seats < $request->passenger_count) {
            return back()->withErrors(['passenger_count' => 'Chuyến bay không còn đủ số ghế trống cho số lượng hành khách này.'])->withInput();
        }

        // Chuẩn hóa loại chuyến: Chấp nhận cả 'round_trip' lẫn 'khu_hoi'
        $tripTypeInput = $request->trip_type;
        $isRoundTrip = ($tripTypeInput == 'round_trip' || $tripTypeInput == 'khu_hoi');
        $normalizedTripType = $isRoundTrip ? 'round_trip' : 'one_way';

        // RÀNG BUỘC VÉ KHỨ HỒI TRONG CONTROLLER
        if ($normalizedTripType === 'round_trip') {
            $ngayVe = $request->input('ngay_ve');
            if (empty($ngayVe)) {
                return back()->withErrors(['trip_type' => 'Vé khứ hồi bắt buộc phải có ngày về hợp lệ.'])->withInput();
            }

            $departureAirportId = $flight->departure_airport_id;
            $arrivalAirportId = $flight->arrival_airport_id;

            $hasReturnFlight = Flight::where('departure_airport_id', $arrivalAirportId)
                ->where('arrival_airport_id', $departureAirportId)
                ->whereDate('departure_time', $ngayVe)
                ->where('status', 'scheduled')
                ->where('available_seats', '>=', $request->passenger_count)
                ->exists();

            if (!$hasReturnFlight) {
                return back()->withErrors(['passenger_count' => 'Rất tiếc, vào ngày về đã chọn không có chuyến bay chiều ngược lại. Không thể đặt vé khứ hồi!'])->withInput();
            }
        }

        // Chuẩn hóa mảng hành khách và tự động gán hành khách số 1 từ người đại diện
        $passengersInput = $request->input('passengers', []);
        if (empty($passengersInput[0]['name'])) {
            $passengersInput[0]['name'] = $request->passenger_name;
        }
        if (empty($passengersInput[0]['gender'])) {
            $passengersInput[0]['gender'] = $request->passenger_gender;
        }

        // Xác định tiền tố mã ghế theo hạng vé thực tế
        $flightClass = $request->flight_class;
        $prefix = 'C'; 
        if ($flightClass === 'thuong_gia') {
            $prefix = 'A';
        } elseif ($flightClass === 'pho_thong_dac_biet') {
            $prefix = 'B';
        }

        // 1. Quét toàn bộ các đơn hàng đã đặt trước đó của chuyến bay này để gom các ghế đã có người ngồi
        $existingBookings = Booking::where('flight_id', $request->flight_id)->get();
        $bookedSeats = [];
        foreach ($existingBookings as $bk) {
            if (!empty($bk->passengers_data) && is_array($bk->passengers_data)) {
                foreach ($bk->passengers_data as $p) {
                    if (!empty($p['seat'])) {
                        $bookedSeats[] = $p['seat'];
                    }
                }
            }
        }

        // 2. Nhận dữ liệu ghế do người dùng tự chọn từ sơ đồ trực quan và kiểm tra an toàn
        $passengersWithSeats = [];
        foreach ($passengersInput as $index => $passenger) {
            $selectedSeat = $passenger['seat'] ?? null;

            if ($selectedSeat && in_array($selectedSeat, $bookedSeats)) {
                return back()->withErrors(['passenger_count' => "Ghế {$selectedSeat} đã có người đặt. Vui lòng chọn ghế khác!"])->withInput();
            }

            $passengersWithSeats[] = [
                'name' => $passenger['name'] ?? $request->passenger_name,
                'gender' => $passenger['gender'] ?? 'Nam',
                'seat' => $selectedSeat,
            ];

            if ($selectedSeat) {
                $bookedSeats[] = $selectedSeat;
            }
        }

        // Tạo mã PNR ngẫu nhiên
        $pnrCode = strtoupper(Str::random(6));
        
        // 1. Hệ số hạng vé (Class Multiplier)
        $classMultiplier = 1.0;
        switch ($flightClass) {
            case 'pho_thong': 
                $classMultiplier = 1.0; 
                break;
            case 'pho_thong_dac_biet': 
                $classMultiplier = 1.4; 
                break;
            case 'thuong_gia': 
                $classMultiplier = 2.2; 
                break;
            default: 
                $classMultiplier = 1.0; 
                break;
        }

        // 2. Tính hệ số khứ hồi hoặc giữ nguyên nếu là vé một chiều
        $tripFactor = 1.0;
        if ($isRoundTrip) {
            switch ($flightClass) {
                case 'pho_thong': 
                    $tripFactor = 1.95; 
                    break;
                case 'pho_thong_dac_biet': 
                    $tripFactor = 1.90; 
                    break;
                case 'thuong_gia': 
                    $tripFactor = 1.85; 
                    break;
                default: 
                    $tripFactor = 1.90; 
                    break;
            }
        }

        // 3. Tính tổng tiền tạm tính (Subtotal) = Giá cơ bản * Hệ số hạng vé * Hệ số khứ hồi * Số lượng hành khách
        $passengerCount = $request->passenger_count ?? 1;
        $subTotal = $flight->price * $classMultiplier * $tripFactor * $passengerCount;

        // 4. Xử lý mã giảm giá dựa trên lựa chọn của khách và kiểm tra điều kiện an toàn
        $discountAmount = 0;
        $selectedVoucher = $request->input('selected_voucher');

        if ($selectedVoucher === 'GIAMGIA10' && $subTotal >= 5000000) {
            $discountAmount = $subTotal * 0.10;
        } elseif ($selectedVoucher === 'GIAMGIA5' && $subTotal >= 2000000) {
            $discountAmount = $subTotal * 0.05;
        }

        // Tổng tiền cuối cùng sau khi đã trừ ưu đãi
        $finalPrice = $subTotal - $discountAmount;
        
        // Trừ số lượng ghế tồn của chuyến bay trong database
        $flight->decrement('available_seats', $request->passenger_count);

        // Lưu vào database
        $booking = Booking::create([
            'user_id' => auth()->id(),  
            'flight_id' => $request->flight_id,
            'passenger_name' => $request->passenger_name,
            'passenger_email' => $request->passenger_email,
            'passenger_phone' => $request->passenger_phone,
            'passenger_gender' => $request->passenger_gender,
            'passenger_count' => $request->passenger_count,
            'number_of_seats' => $request->passenger_count,
            'trip_type' => $normalizedTripType, 
            'flight_class' => $flightClass,
            'booking_code' => $pnrCode,
            'total_price' => $finalPrice, // Đổi từ $totalPrice thành $finalPrice cho khớp biến đã tính ở trên
            'passengers_data' => $passengersWithSeats,
            'ngay_ve' => $request->input('ngay_ve'),
        ]);

        return redirect()->route('booking.success', ['id' => $booking->id, 'ngay_ve' => $request->input('ngay_ve')]);
    }
    // Trang thông báo đặt vé thành công
    public function success($id)
    {
        // Load kèm theo flight và các quan hệ sân bay đi/đến của flight đó
        $booking = Booking::with('flight.departureAirport', 'flight.arrivalAirport')->findOrFail($id);

        return view('booking.success', compact('booking'));
    }
}
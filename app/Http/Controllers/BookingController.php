<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;
use App\Models\Booking;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);
        
        // Nhận thêm thông tin loại vé và hạng vé từ URL
        $tripType = $request->query('trip_type', 'one_way');
        $flightClass = $request->query('flight_class', 'Phổ thông');
        $ngayVe = $request->query('ngay_ve');

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
        $maxC = max(0, $totalAvailableFlightSeats - ($maxA - $bookedCounts['A']) - ($maxB - $bookedCounts['B']));

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

        return view('booking.create', compact('flight', 'tripType', 'flightClass', 'ngayVe', 'bookedSeats', 'remainingSeats', 'maxSeats'));    
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
        
        // Tính hệ số nhân theo chính sách giá vé khứ hồi / một chiều
        $multiplier = 1; 

        if ($isRoundTrip) {
            switch ($flightClass) {
                case 'pho_thong':
                    $multiplier = 1.95; 
                    break;
                case 'pho_thong_dac_biet':
                    $multiplier = 1.90; 
                    break;
                case 'thuong_gia':
                    $multiplier = 1.85; 
                    break;
                default:
                    $multiplier = 1.90; 
                    break;
            }
        }

        // Tính tổng tiền = Giá vé cơ bản * Số lượng hành khách * Hệ số hạng vé
        $totalPrice = $flight->price * $request->passenger_count * $multiplier;

        // Trừ số lượng ghế tồn của chuyến bay trong database
        $flight->decrement('available_seats', $request->passenger_count);

        // Lưu vào database
        $booking = Booking::create([
            'flight_id' => $request->flight_id,
            'passenger_name' => $request->passenger_name,
            'passenger_email' => $request->passenger_email,
            'passenger_phone' => $request->passenger_phone,
            'passenger_gender' => $request->passenger_gender,
            'passenger_count' => $request->passenger_count,
            'trip_type' => $normalizedTripType, 
            'flight_class' => $flightClass,
            'pnr_code' => $pnrCode,
            'total_price' => $totalPrice,
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
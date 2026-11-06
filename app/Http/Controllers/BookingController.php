<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);

        if (Carbon::now()->addHours(3)->greaterThan(Carbon::parse($flight->departure_time))) {
            return redirect()->route('flights.search')
                ->with('error', 'Chuyến bay này đã khởi hành hoặc còn dưới 3 tiếng nữa là khởi hành, không thể đặt vé.');
        }

        $tripType = $request->query('trip_type', 'one_way');
        $flightClass = $request->query('flight_class', 'pho_thong');
        $ngayVe = $request->query('ngay_ve');
        $passengerCount = $request->query('passenger_count', 1);

        // Vé đã hủy không còn giữ ghế.
        $existingBookings = Booking::where('flight_id', $id)
            ->where('status', '!=', 'cancelled')
            ->get();
        $bookedSeats = [];

        foreach ($existingBookings as $booking) {
            foreach ($booking->passengers_data ?? [] as $passenger) {
                if (!empty($passenger['seat'])) {
                    $bookedSeats[] = $passenger['seat'];
                }
            }
        }

        $bookedCounts = ['A' => 0, 'B' => 0, 'C' => 0];
        foreach ($bookedSeats as $seat) {
            $prefix = substr($seat, 0, 1);
            if (isset($bookedCounts[$prefix])) {
                $bookedCounts[$prefix]++;
            }
        }

        $maxA = 10;
        $maxB = 10;
        $maxC = max(0, $flight->available_seats - $maxA - $maxB);
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
        $validated = $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => ['required', 'string', 'regex:/^0[0-9]{9,10}$/'],
            'passenger_count' => 'required|integer|min:1',
            'trip_type' => 'required|string',
            'flight_class' => 'required|string',
            'ngay_ve' => 'nullable|date',
        ], [
            'passenger_phone.required' => 'Vui lòng nhập số điện thoại.',
            'passenger_phone.regex' => 'Số điện thoại không hợp lệ (phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số).',
        ]);

        $flight = Flight::findOrFail($validated['flight_id']);
        $passengerCount = (int) $validated['passenger_count'];

        if (Carbon::now()->addHours(3)->greaterThan(Carbon::parse($flight->departure_time))) {
            return back()->withErrors([
                'flight_id' => 'Chuyến bay này đã khởi hành hoặc còn dưới 3 tiếng nữa là khởi hành, không thể đặt vé.',
            ])->withInput();
        }

        if ($flight->available_seats < $passengerCount) {
            return back()->withErrors([
                'passenger_count' => 'Chuyến bay không còn đủ số ghế trống cho số lượng hành khách này.',
            ])->withInput();
        }

        $isRoundTrip = in_array($validated['trip_type'], ['round_trip', 'khu_hoi'], true);
        $normalizedTripType = $isRoundTrip ? 'round_trip' : 'one_way';

        if ($isRoundTrip) {
            $ngayVe = $request->input('ngay_ve');
            if (empty($ngayVe)) {
                return back()->withErrors([
                    'trip_type' => 'Vé khứ hồi bắt buộc phải có ngày về hợp lệ.',
                ])->withInput();
            }

            $hasReturnFlight = Flight::where('departure_airport_id', $flight->arrival_airport_id)
                ->where('arrival_airport_id', $flight->departure_airport_id)
                ->whereDate('departure_time', $ngayVe)
                ->where('status', 'scheduled')
                ->where('available_seats', '>=', $passengerCount)
                ->exists();

            if (!$hasReturnFlight) {
                return back()->withErrors([
                    'passenger_count' => 'Rất tiếc, vào ngày về đã chọn không có chuyến bay chiều ngược lại. Không thể đặt vé khứ hồi!',
                ])->withInput();
            }
        }

        $passengersInput = $request->input('passengers', []);
        if (empty($passengersInput[0]['name'])) {
            $passengersInput[0]['name'] = $request->passenger_name;
        }
        if (empty($passengersInput[0]['gender'])) {
            $passengersInput[0]['gender'] = $request->passenger_gender;
        }

        $flightClass = $validated['flight_class'];
        $prefix = match ($flightClass) {
            'thuong_gia' => 'A',
            'pho_thong_dac_biet' => 'B',
            default => 'C',
        };

        $passengersWithSeats = [];
        $selectedSeats = [];
        foreach ($passengersInput as $passenger) {
            $selectedSeat = $passenger['seat'] ?? null;
            if ($selectedSeat && in_array($selectedSeat, $selectedSeats, true)) {
                return back()->withErrors([
                    'passenger_count' => "Ghế {$selectedSeat} đã được chọn nhiều lần. Vui lòng chọn ghế khác!",
                ])->withInput();
            }

            if ($selectedSeat) {
                $selectedSeats[] = $selectedSeat;
            }

            $passengersWithSeats[] = [
                'name' => $passenger['name'] ?? $request->passenger_name,
                'gender' => $passenger['gender'] ?? 'Nam',
                'seat' => $selectedSeat,
            ];
        }

        // Chính sách giá mới được cập nhật trên upstream/main.
        $classMultiplier = match ($flightClass) {
            'pho_thong_dac_biet' => 1.4,
            'thuong_gia' => 2.2,
            default => 1.0,
        };
        $tripFactor = $isRoundTrip ? match ($flightClass) {
            'pho_thong' => 1.95,
            'pho_thong_dac_biet' => 1.90,
            'thuong_gia' => 1.85,
            default => 1.90,
        } : 1.0;

        $pnrCode = strtoupper(Str::random(6));

        // Cả tạo booking và cập nhật số ghế đều nằm trong cùng transaction.
        $booking = DB::transaction(function () use (
            $request, $validated, $passengerCount, $normalizedTripType,
            $flightClass, $classMultiplier, $tripFactor,
            $pnrCode, $passengersWithSeats
        ) {
            $lockedFlight = Flight::whereKey($validated['flight_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (Carbon::now()->addHours(3)->greaterThan(Carbon::parse($lockedFlight->departure_time))) {
                throw ValidationException::withMessages([
                    'flight_id' => 'Chuyến bay không còn đủ thời gian để đặt vé.',
                ]);
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

    public function success($id)
    {
        $booking = Booking::with('flight.departureAirport', 'flight.arrivalAirport')
            ->findOrFail($id);

        return view('booking.success', compact('booking'));
    }
}

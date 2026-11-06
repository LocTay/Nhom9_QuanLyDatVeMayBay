<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;
use App\Models\Booking;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    // Trang nhập thông tin đặt vé
    public function create(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);

        // Không cho đặt vé nếu chuyến bay đã khởi hành
        // hoặc còn dưới 3 tiếng nữa là khởi hành
        $departureTime = Carbon::parse($flight->departure_time);

        if (Carbon::now()->addHours(3)->greaterThan($departureTime)) {
            return redirect()->route('flights.search')
                ->with('error', 'Chuyến bay này đã khởi hành hoặc còn dưới 3 tiếng nữa là khởi hành, không thể đặt vé.');
        }

        // Nhận thông tin từ URL
        $tripType = $request->query('trip_type', 'one_way');
        $flightClass = $request->query('flight_class', 'pho_thong');
        $ngayVe = $request->query('ngay_ve');
        $passengerCount = (int) $request->query('passenger_count', 1);

        // Lấy danh sách ghế đã đặt
        $existingBookings = Booking::where('flight_id', $id)
            ->whereNotIn('status', ['cancelled', 'refund_pending'])
            ->get();

        $bookedSeats = [];

        foreach ($existingBookings as $booking) {
            if (is_array($booking->passengers_data)) {
                foreach ($booking->passengers_data as $passenger) {
                    if (!empty($passenger['seat'])) {
                        $bookedSeats[] = $passenger['seat'];
                    }
                }
            }
        }

        // Đếm số ghế đã đặt theo từng khoang
        $bookedCounts = [
            'A' => 0,
            'B' => 0,
            'C' => 0,
        ];

        foreach ($bookedSeats as $seat) {
            $prefix = substr($seat, 0, 1);

            if (isset($bookedCounts[$prefix])) {
                $bookedCounts[$prefix]++;
            }
        }

        // Số ghế quy định cho từng khoang
        $maxA = 10;
        $maxB = 10;

        $totalAvailableFlightSeats = $flight->available_seats;
        $maxC = max(0, $totalAvailableFlightSeats - $maxA - $maxB);

        $maxSeats = [
            'A' => $maxA,
            'B' => $maxB,
            'C' => $maxC > 0 ? $maxC : 80,
        ];

        // Số ghế còn lại theo từng khoang
        $remainingSeats = [
            'A' => max(0, $maxSeats['A'] - $bookedCounts['A']),
            'B' => max(0, $maxSeats['B'] - $bookedCounts['B']),
            'C' => max(0, $maxSeats['C'] - $bookedCounts['C']),
        ];

        // Hệ số hạng vé
        $classMultiplier = match ($flightClass) {
            'pho_thong_dac_biet' => 1.4,
            'thuong_gia', 'Thương gia' => 2.2,
            default => 1.0,
        };

        // Kiểm tra loại vé khứ hồi
        $isRoundTrip = in_array(
            $tripType,
            ['round_trip', 'khu_hoi'],
            true
        );

        // Hệ số khứ hồi
        $tripFactor = 1.0;

        if ($isRoundTrip) {
            $tripFactor = match ($flightClass) {
                'pho_thong' => 1.95,
                'pho_thong_dac_biet' => 1.90,
                'thuong_gia', 'Thương gia' => 1.85,
                default => 1.90,
            };
        }

        // Tính tiền tạm tính
        $subTotal = $flight->price
            * $classMultiplier
            * $tripFactor
            * $passengerCount;

        // Danh sách mã giảm giá
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

    // Xử lý đặt vé
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validated = $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => [
                'required',
                'string',
                'regex:/^0[0-9]{9,10}$/',
            ],
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

        // Kiểm tra thời gian khởi hành
        if (Carbon::now()->addHours(3)->greaterThan(
            Carbon::parse($flight->departure_time)
        )) {
            return back()
                ->withErrors([
                    'flight_id' => 'Chuyến bay đã khởi hành hoặc còn dưới 3 tiếng nữa là khởi hành, không thể đặt vé.',
                ])
                ->withInput();
        }

        // Kiểm tra số ghế còn trống
        if ($flight->available_seats < $passengerCount) {
            return back()
                ->withErrors([
                    'passenger_count' => 'Chuyến bay không còn đủ số ghế trống cho số lượng hành khách này.',
                ])
                ->withInput();
        }

        // Chuẩn hóa loại chuyến bay
        $isRoundTrip = in_array(
            $validated['trip_type'],
            ['round_trip', 'khu_hoi'],
            true
        );

        $normalizedTripType = $isRoundTrip
            ? 'round_trip'
            : 'one_way';

        // Kiểm tra chuyến bay chiều về
        if ($isRoundTrip) {
            $ngayVe = $request->input('ngay_ve');

            if (empty($ngayVe)) {
                return back()
                    ->withErrors([
                        'trip_type' => 'Vé khứ hồi bắt buộc phải có ngày về hợp lệ.',
                    ])
                    ->withInput();
            }

            $hasReturnFlight = Flight::where(
                'departure_airport_id',
                $flight->arrival_airport_id
            )
                ->where(
                    'arrival_airport_id',
                    $flight->departure_airport_id
                )
                ->whereDate('departure_time', $ngayVe)
                ->where('status', 'scheduled')
                ->where('available_seats', '>=', $passengerCount)
                ->exists();

            if (!$hasReturnFlight) {
                return back()
                    ->withErrors([
                        'passenger_count' => 'Rất tiếc, vào ngày về đã chọn không có chuyến bay chiều ngược lại. Không thể đặt vé khứ hồi!',
                    ])
                    ->withInput();
            }
        }

        // Chuẩn hóa thông tin hành khách
        $passengersInput = $request->input('passengers', []);

        if (!is_array($passengersInput)) {
            $passengersInput = [];
        }

        $flightClass = $validated['flight_class'];

        // Hệ số hạng vé
        $classMultiplier = match ($flightClass) {
            'pho_thong_dac_biet' => 1.4,
            'thuong_gia', 'Thương gia' => 2.2,
            default => 1.0,
        };

        // Hệ số khứ hồi
        $tripFactor = 1.0;

        if ($isRoundTrip) {
            $tripFactor = match ($flightClass) {
                'pho_thong' => 1.95,
                'pho_thong_dac_biet' => 1.90,
                'thuong_gia', 'Thương gia' => 1.85,
                default => 1.90,
            };
        }

        // Chuẩn hóa dữ liệu hành khách và ghế
        $passengersWithSeats = [];

        for ($i = 0; $i < $passengerCount; $i++) {
            $passenger = $passengersInput[$i] ?? [];

            $passengersWithSeats[] = [
                'name' => $passenger['name']
                    ?? ($i === 0 ? $request->passenger_name : $request->passenger_name),

                'gender' => $passenger['gender']
                    ?? ($i === 0 ? $request->passenger_gender : 'Nam'),

                'seat' => $passenger['seat'] ?? null,
            ];
        }

        // Tạo mã đặt vé
        $pnrCode = strtoupper(Str::random(6));

        // Tạo booking và trừ ghế trong cùng transaction
        $booking = DB::transaction(function () use (
            $request,
            $validated,
            $passengerCount,
            $normalizedTripType,
            $flightClass,
            $classMultiplier,
            $tripFactor,
            $pnrCode,
            $passengersWithSeats
        ) {
            // Khóa chuyến bay để tránh hai người đặt cùng lúc
            $lockedFlight = Flight::whereKey($validated['flight_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Kiểm tra lại thời gian khởi hành
            if (Carbon::now()->addHours(3)->greaterThan(
                Carbon::parse($lockedFlight->departure_time)
            )) {
                throw ValidationException::withMessages([
                    'flight_id' => 'Chuyến bay không còn đủ thời gian để đặt vé.',
                ]);
            }

            // Kiểm tra lại số ghế còn trống
            if ($lockedFlight->available_seats < $passengerCount) {
                throw ValidationException::withMessages([
                    'passenger_count' => 'Chuyến bay không còn đủ số ghế trống.',
                ]);
            }

            // Lấy danh sách ghế đã được đặt
            $occupiedSeats = Booking::where(
                'flight_id',
                $lockedFlight->id
            )
                ->whereNotIn('status', ['cancelled', 'refund_pending'])
                ->get()
                ->flatMap(function ($existingBooking) {
                    return collect(
                        $existingBooking->passengers_data ?? []
                    )->pluck('seat');
                })
                ->filter()
                ->all();

            // Kiểm tra ghế trùng
            $selectedSeats = [];

            foreach ($passengersWithSeats as $passenger) {
                $seat = $passenger['seat'];

                if (empty($seat)) {
                    continue;
                }

                if (
                    in_array($seat, $selectedSeats, true)
                    || in_array($seat, $occupiedSeats, true)
                ) {
                    throw ValidationException::withMessages([
                        'passenger_count' => "Ghế {$seat} đã được đặt hoặc được chọn nhiều lần. Vui lòng chọn ghế khác.",
                    ]);
                }

                $selectedSeats[] = $seat;
            }

            // Tính tổng tiền
            $subTotal = $lockedFlight->price
                * $classMultiplier
                * $tripFactor
                * $passengerCount;

            // Áp dụng mã giảm giá
            $discountAmount = 0;
            $selectedVoucher = $request->input('selected_voucher');

            if (
                $selectedVoucher === 'GIAMGIA10'
                && $subTotal >= 5000000
            ) {
                $discountAmount = $subTotal * 0.10;
            } elseif (
                $selectedVoucher === 'GIAMGIA5'
                && $subTotal >= 2000000
            ) {
                $discountAmount = $subTotal * 0.05;
            }

            $finalPrice = $subTotal - $discountAmount;

            // Lưu thông tin đặt vé
            $newBooking = Booking::create([
                'user_id' => Auth::id(),
                'flight_id' => $lockedFlight->id,
                'passenger_name' => $request->passenger_name,
                'passenger_email' => $request->passenger_email,
                'passenger_phone' => $request->passenger_phone,
                'passenger_gender' => $request->passenger_gender,
                'passenger_count' => $passengerCount,
                'number_of_seats' => $passengerCount,
                'trip_type' => $normalizedTripType,
                'flight_class' => $flightClass,
                'pnr_code' => $pnrCode,
                'booking_code' => $pnrCode,
                'total_price' => $finalPrice,
                'passengers_data' => $passengersWithSeats,
            ]);

            // Chỉ trừ ghế sau khi tạo booking thành công
            $lockedFlight->decrement(
                'available_seats',
                $passengerCount
            );

            return $newBooking;
        });

        // Chuyển đến trang đặt vé thành công
        return redirect()->route('booking.success', [
            'id' => $booking->id,
            'ngay_ve' => $request->input('ngay_ve'),
        ]);
    }

    // Trang thông báo đặt vé thành công
    public function success($id)
    {
        $booking = Booking::with(
            'flight.departureAirport',
            'flight.arrivalAirport'
        )->findOrFail($id);

        return view('booking.success', compact('booking'));
    }
}

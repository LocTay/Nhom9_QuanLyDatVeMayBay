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
        $flightClass = $request->query('flight_class', 'Phổ thông');
        $ngayVe = $request->query('ngay_ve');

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

        return view('booking.create', compact(
            'flight', 'tripType', 'flightClass', 'ngayVe',
            'bookedSeats', 'remainingSeats', 'maxSeats'
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

            if ($lockedFlight->available_seats < $passengerCount) {
                throw ValidationException::withMessages([
                    'passenger_count' => 'Chuyến bay không còn đủ số ghế trống.',
                ]);
            }

            $occupiedSeats = Booking::where('flight_id', $lockedFlight->id)
                ->where('status', '!=', 'cancelled')
                ->get()
                ->flatMap(fn (Booking $booking) => collect($booking->passengers_data ?? [])->pluck('seat'))
                ->filter()
                ->all();

            foreach ($passengersWithSeats as $passenger) {
                if (!empty($passenger['seat']) && in_array($passenger['seat'], $occupiedSeats, true)) {
                    throw ValidationException::withMessages([
                        'passenger_count' => 'Ghế vừa được người khác đặt. Vui lòng chọn lại.',
                    ]);
                }
            }

            $totalPrice = $lockedFlight->price * $classMultiplier * $tripFactor * $passengerCount;

            $newBooking = Booking::create([
                'flight_id' => $lockedFlight->id,
                'passenger_name' => $request->passenger_name,
                'passenger_email' => $request->passenger_email,
                'passenger_phone' => $request->passenger_phone,
                'passenger_gender' => $request->passenger_gender,
                'passenger_count' => $passengerCount,
                'trip_type' => $normalizedTripType,
                'flight_class' => $flightClass,
                'pnr_code' => $pnrCode,
                'total_price' => $totalPrice,
                'passengers_data' => $passengersWithSeats,
            ]);

            $lockedFlight->decrement('available_seats', $passengerCount);

            return $newBooking;
        });

        return redirect()->route('booking.success', [
            'id' => $booking->id,
            'ngay_ve' => $request->input('ngay_ve'),
        ]);
    }

    public function success($id)
    {
        $booking = Booking::with('flight.departureAirport', 'flight.arrivalAirport')
            ->findOrFail($id);

        return view('booking.success', compact('booking'));
    }
}

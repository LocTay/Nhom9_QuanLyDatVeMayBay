<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminFlightController extends Controller
{
    // Danh sách chuyến bay
    public function index()
    {
        $flights = Flight::with([
            'airline',
            'departureAirport',
            'arrivalAirport',
        ])
            ->orderByDesc('departure_time')
            ->paginate(10);

        return view('admin.flights.index', compact('flights'));
    }

    // Form thêm chuyến bay
    public function create()
    {
        $airports = Airport::orderBy('city')->get();

        return view('admin.flights.create', compact('airports'));
    }

    // Lưu chuyến bay mới
    public function store(Request $request)
    {
        $request->merge([
            'flight_number' => strtoupper(trim($request->flight_number ?? '')),
        ]);

        // Không nhận lựa chọn hãng từ người dùng.
        $request->request->remove('airline_id');

        $validated = $request->validate($this->rules());

        // Tự động gán tất cả chuyến bay mới cho SkyBooking.
        $skyBooking = Airline::where('code', 'SB')
            ->where('is_active', true)
            ->firstOrFail();

        $validated['airline_id'] = $skyBooking->id;

        Flight::create($validated);

        return redirect()
            ->route('admin.flights.index')
            ->with('success', 'Thêm chuyến bay thành công.');
    }
    // Form chỉnh sửa
    public function edit(Flight $flight)
    {
        $airports = Airport::orderBy('city')->get();

        return view('admin.flights.edit', compact(
            'flight',
            'airports'
        ));
    }
    // Cập nhật chuyến bay
    public function update(Request $request, Flight $flight)
    {
        $request->merge([
            'flight_number' => strtoupper(trim($request->flight_number ?? '')),
        ]);

        // Không cho thay đổi hãng thông qua form chỉnh sửa.
        $request->request->remove('airline_id');

        $validated = $request->validate($this->rules($flight));

        // Nếu chuyến bay đã có booking, bảo vệ thông tin vé đã bán
        if ($flight->bookings()->exists()) {
            $protectedFields = [
                'flight_number',
                'airline_id',
                'departure_airport_id',
                'arrival_airport_id',
                'departure_time',
                'arrival_time',
                'price',
                'available_seats',
            ];

            foreach ($protectedFields as $field) {
                $oldValue = $flight->getAttribute($field);

                // Request cũ không gửi airline_id:
                // giữ nguyên hãng hiện tại thay vì gây lỗi.
                $newValue = array_key_exists($field, $validated)
                    ? $validated[$field]
                    : $oldValue;

                if ($field === 'departure_time' || $field === 'arrival_time') {
                    $oldValue = \Carbon\Carbon::parse($oldValue)->format('Y-m-d H:i');
                    $newValue = \Carbon\Carbon::parse($newValue)->format('Y-m-d H:i');
                }

                if ((string) $oldValue !== (string) $newValue) {
                    return back()
                        ->withErrors([
                            $field => 'Không thể thay đổi thông tin này vì chuyến bay đã có đơn đặt vé.',
                        ])
                        ->withInput();
                }
            }
        }

        // Tương thích với các request cũ không gửi airline_id.
        // Nếu thiếu trường này, giữ nguyên hãng đang được gán.
        $validated['airline_id'] = $flight->airline_id;

        $flight->update($validated);

        return redirect()
            ->route('admin.flights.index')
            ->with('success', 'Cập nhật chuyến bay thành công.');
    }


    // Xóa chuyến bay
    public function destroy(Flight $flight)
    {
        if ($flight->bookings()->exists()) {
            return redirect()
                ->route('admin.flights.index')
                ->with(
                    'error',
                    'Không thể xóa chuyến bay đã có đơn đặt vé.'
                );
        }

        $flight->delete();

        return redirect()
            ->route('admin.flights.index')
            ->with('success', 'Xóa chuyến bay thành công.');
    }

    // Quy tắc kiểm tra dữ liệu
    private function rules(?Flight $flight = null): array
    {
        return [
            'flight_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('flights', 'flight_number')
                    ->ignore($flight?->id),
            ],
            'airline_id' => [
                'nullable',
                Rule::exists('airlines', 'id')->where(function ($query) use ($flight) {
                    $query->where(function ($q) use ($flight) {
                        $q->where('is_active', true);

                        if ($flight?->airline_id !== null) {
                            $q->orWhere('id', $flight->airline_id);
                        }
                    });
                }),
            ],
            'departure_airport_id' => [
                'required',
                'exists:airports,id',
                'different:arrival_airport_id',
            ],
            'arrival_airport_id' => [
                'required',
                'exists:airports,id',
            ],
            'departure_time' => [
                'required',
                'date',
            ],
            'arrival_time' => [
                'required',
                'date',
                'after:departure_time',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'available_seats' => [
                'required',
                'integer',
                'min:0',
            ],
            'status' => [
                'required',
                Rule::in([
                    'scheduled',
                    'delayed',
                    'cancelled',
                    'completed',
                ]),
            ],
        ];
    }
}

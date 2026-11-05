<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAirportController extends Controller
{
    // Danh sách sân bay
    public function index()
    {
        $airports = Airport::orderBy('city')->paginate(10);

        return view('admin.airports.index', compact('airports'));
    }

    // Hiển thị form thêm sân bay
    public function create()
    {
        return view('admin.airports.create');
    }

    // Lưu sân bay mới
    public function store(Request $request)
    {
        $request->merge([
            'code' => strtoupper(trim($request->code ?? '')),
        ]);

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:3', 'alpha', 'unique:airports,code'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
        ]);

        Airport::create($validated);

        return redirect()
            ->route('admin.airports.index')
            ->with('success', 'Thêm sân bay thành công.');
    }

    // Hiển thị form sửa sân bay
    public function edit(Airport $airport)
    {
        return view('admin.airports.edit', compact('airport'));
    }

    // Cập nhật sân bay
    public function update(Request $request, Airport $airport)
    {
        $request->merge([
            'code' => strtoupper(trim($request->code ?? '')),
        ]);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'size:3',
                'alpha',
                Rule::unique('airports', 'code')->ignore($airport->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
        ]);

        $airport->update($validated);

        return redirect()
            ->route('admin.airports.index')
            ->with('success', 'Cập nhật sân bay thành công.');
    }

    // Xóa sân bay
    public function destroy(Airport $airport)
    {
        $hasFlights = $airport->departureFlights()->exists()
            || $airport->arrivalFlights()->exists();

        if ($hasFlights) {
            return redirect()
                ->route('admin.airports.index')
                ->with('error', 'Không thể xóa sân bay đang được sử dụng trong chuyến bay.');
        }

        $airport->delete();

        return redirect()
            ->route('admin.airports.index')
            ->with('success', 'Xóa sân bay thành công.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAirlineController extends Controller
{
    public function index(Request $request)
    {
        $query = Airline::query()->withCount('flights');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $airlines = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalAirlines = Airline::count();
        $activeAirlines = Airline::where('is_active', true)->count();
        $inactiveAirlines = Airline::where('is_active', false)->count();

        return view('admin.airlines.index', compact(
            'airlines',
            'totalAirlines',
            'activeAirlines',
            'inactiveAirlines'
        ));
    }

    public function create()
    {
        return view('admin.airlines.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'code' => strtoupper(trim($request->input('code', ''))),
        ]);

        $validated = $request->validate($this->rules());

        Airline::create($validated);

        return redirect()
            ->route('admin.airlines.index')
            ->with('success', 'Thêm hãng hàng không thành công.');
    }

    public function edit(Airline $airline)
    {
        return view('admin.airlines.edit', compact('airline'));
    }

    public function update(Request $request, Airline $airline)
    {
        $request->merge([
            'code' => strtoupper(trim($request->input('code', ''))),
        ]);

        $validated = $request->validate($this->rules($airline));

        $airline->update($validated);

        return redirect()
            ->route('admin.airlines.index')
            ->with('success', 'Cập nhật hãng hàng không thành công.');
    }

    public function destroy(Airline $airline)
    {
        if ($airline->flights()->exists()) {
            return redirect()
                ->route('admin.airlines.index')
                ->with(
                    'error',
                    'Không thể xóa hãng hàng không đang có chuyến bay.'
                );
        }

        $airline->delete();

        return redirect()
            ->route('admin.airlines.index')
            ->with('success', 'Xóa hãng hàng không thành công.');
    }

    private function rules(?Airline $airline = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'min:2',
                'max:3',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('airlines', 'code')->ignore($airline?->id),
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'country' => [
                'nullable',
                'string',
                'max:100',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }
}

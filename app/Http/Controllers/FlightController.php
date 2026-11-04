<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $diemDi = $request->input('diem_di');
        $diemDen = $request->input('diem_den');
        $ngayDi = $request->input('ngay_di');

        $chuyenBays = collect();

        if ($diemDi && $diemDen && $ngayDi) {
            $chuyenBays = DB::table('flights')
                ->join(
                    'airports as san_bay_di',
                    'flights.departure_airport_id',
                    '=',
                    'san_bay_di.id'
                )
                ->join(
                    'airports as san_bay_den',
                    'flights.arrival_airport_id',
                    '=',
                    'san_bay_den.id'
                )
                ->where('san_bay_di.code', $diemDi)
                ->where('san_bay_den.code', $diemDen)
                ->whereDate('flights.departure_time', $ngayDi)
                ->where('flights.status', 'scheduled')
                ->where('flights.available_seats', '>', 0)
                ->select(
                    'flights.*',
                    'san_bay_di.city as thanh_pho_di',
                    'san_bay_di.code as ma_san_bay_di',
                    'san_bay_den.city as thanh_pho_den',
                    'san_bay_den.code as ma_san_bay_den'
                )
                ->orderBy('flights.departure_time')
                ->get();
        }

        return view('flights', compact(
            'chuyenBays',
            'diemDi',
            'diemDen',
            'ngayDi'
        ));
    }
}

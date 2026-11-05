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
        
        // Lấy thông tin loại chuyến và ngày về từ request của trang chủ
        $loaiChuyen = $request->input('loai_chuyen', $request->input('trip_type', 'mot_chieu'));
        $ngayVe = $request->input('ngay_ve');
        
        // Bắt số lượng hành khách từ trang chủ (name="hanh_khach"), mặc định là 1 nếu không có
        $passengerCount = $request->input('hanh_khach', 1);

        $chuyenBays = collect();
        $chuyenBayVes = collect(); // Thêm biến chứa danh sách chuyến bay chiều về

        if ($diemDi && $diemDen && $ngayDi) {
            // Truy vấn danh sách chuyến bay chiều đi
            $query = DB::table('flights')
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
                ->where('flights.available_seats', '>=', $passengerCount)
                ->select(
                    'flights.*',
                    'san_bay_di.city as thanh_pho_di',
                    'san_bay_di.code as ma_san_bay_di',
                    'san_bay_den.city as thanh_pho_den',
                    'san_bay_den.code as ma_san_bay_den'
                )
                ->orderBy('flights.departure_time');

            $chuyenBays = $query->get();

            // RÀNG BUỘC VÉ KHỨ HỒI
            $isRoundTrip = ($loaiChuyen === 'khu_hoi' || $loaiChuyen === 'round_trip');

            if ($isRoundTrip) {
                if (empty($ngayVe)) {
                    $chuyenBays = collect();
                } else {
                    // 1. LẤY DANH SÁCH CÁC CHUYẾN BAY CHIỀU VỀ THỰC TẾ
                    $chuyenBayVes = DB::table('flights')
                        ->join('airports as san_bay_di', 'flights.departure_airport_id', '=', 'san_bay_di.id')
                        ->join('airports as san_bay_den', 'flights.arrival_airport_id', '=', 'san_bay_den.id')
                        ->where('san_bay_di.code', $diemDen) // Đảo chiều: xuất phát từ điểm đến cũ
                        ->where('san_bay_den.code', $diemDi) // Về lại điểm đi cũ
                        ->whereDate('flights.departure_time', $ngayVe)
                        ->where('flights.status', 'scheduled')
                        ->where('flights.available_seats', '>=', $passengerCount)
                        ->select(
                            'flights.*',
                            'san_bay_di.city as thanh_pho_di',
                            'san_bay_di.code as ma_san_bay_di',
                            'san_bay_den.city as thanh_pho_den',
                            'san_bay_den.code as ma_san_bay_den'
                        )
                        ->orderBy('flights.departure_time')
                        ->get();

                    // 2. Chỉ giữ lại chuyến đi nếu ngày về thực sự có ít nhất 1 chuyến bay chiều về phù hợp
                    if ($chuyenBayVes->isEmpty()) {
                        $chuyenBays = collect();
                    }
                }
            }
        }

        return view('flights', compact(
            'chuyenBays',
            'chuyenBayVes', // Truyền thêm biến này sang view để hiển thị
            'diemDi',
            'diemDen',
            'ngayDi',
            'ngayVe',
            'loaiChuyen',
            'passengerCount'
        ));
    }
}
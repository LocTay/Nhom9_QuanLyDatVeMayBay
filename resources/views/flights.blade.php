<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm chuyến bay - SkyBooking</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Cập nhật nền gradient sang trọng, đồng bộ toàn hệ thống */
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            color: #24324a;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        p {
            color: #e2e8f0;
        }

        .flight {
            background: #ffffff;
            padding: 24px;
            margin: 18px 0;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .flight:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .route {
            font-size: 19px;
            font-weight: bold;
            color: #0f2027;
        }

        .muted {
            color: #64748b;
            margin-top: 6px;
            font-size: 14px;
        }

        .price {
            color: #e65b2a;
            font-size: 22px;
            font-weight: bold;
        }

        .empty {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            font-size: 16px;
            color: #475569;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #6fb1fc;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .back:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .flight {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <a class="back" href="/">← Quay lại trang chủ</a>

        <h1>Kết quả tìm chuyến bay</h1>
        @if(session('error'))
<div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px 20px; margin: 15px 0; border-radius: 8px; font-weight: 500; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif

        <p>
            Hành trình:
            <strong>{{ $diemDi ?? '---' }}</strong>
            →
            <strong>{{ $diemDen ?? '---' }}</strong>
        </p>

        <p>Ngày đi: <strong>{{ $ngayDi ?? 'Chưa chọn' }}</strong></p>

        @if (!$diemDi || !$diemDen || !$ngayDi)
            <div class="empty">
                Vui lòng quay lại trang chủ và nhập đầy đủ điểm đi,
                điểm đến và ngày khởi hành.
            </div>
        @elseif ($chuyenBays->isEmpty())
            <div class="empty">
                Không tìm thấy chuyến bay phù hợp.
                Bạn hãy thử chọn ngày khác hoặc hành trình khác.
            </div>
        @else
            <p>Tìm thấy {{ $chuyenBays->count() }} chuyến bay.</p>

            @foreach ($chuyenBays as $chuyenBay)
                <div class="flight d-flex justify-content-between align-items-center p-3 mb-3 border rounded bg-white shadow-sm">
                    <div>
                        <div class="route fw-bold text-primary mb-1">
                            {{ $chuyenBay->ma_san_bay_di }}
                            ({{ $chuyenBay->thanh_pho_di }})
                            →
                            {{ $chuyenBay->ma_san_bay_den }}
                            ({{ $chuyenBay->thanh_pho_den }})
                        </div>

                        <div class="muted text-muted small">
                            Mã chuyến: {{ $chuyenBay->flight_number }}
                        </div>

                        <div class="muted text-muted small">
                            Khởi hành:
                            {{ \Carbon\Carbon::parse($chuyenBay->departure_time)->format('H:i d/m/Y') }}
                        </div>

                        <div class="muted text-muted small">
                            Hạ cánh:
                            {{ \Carbon\Carbon::parse($chuyenBay->arrival_time)->format('H:i d/m/Y') }}
                        </div>

                        <div class="muted text-muted small">
                            Số ghế còn lại: {{ $chuyenBay->available_seats }}
                        </div>
                        @if(request('loai_chuyen') == 'khu_hoi' || request('trip_type') == 'round_trip')
                            <div class="mt-3 p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0d6efd;">
                                <div class="fw-bold text-primary mb-2" style="font-size: 14px;">
<i class="bi bi-arrow-left-right"></i> Chiều về ({{ $chuyenBay->thanh_pho_den }} → {{ $chuyenBay->thanh_pho_di }} - Ngày: {{ \Carbon\Carbon::parse(request('ngay_ve'))->format('d/m/Y') }}):
                                </div>

                                @if(isset($chuyenBayVes) && $chuyenBayVes->isNotEmpty())
                                    @foreach($chuyenBayVes as $chuyenVe)
                                        <div class="p-2 mb-2 bg-white rounded border shadow-sm" style="font-size: 13px;">
                                            <div class="fw-bold text-dark">Mã chuyến: {{ $chuyenVe->flight_number }}</div>
                                            <div class="text-muted">Khởi hành dự kiến: {{ \Carbon\Carbon::parse($chuyenVe->departure_time)->format('H:i d/m/Y') }}</div>
                                            <div class="text-muted">Hạ cánh dự kiến: {{ \Carbon\Carbon::parse($chuyenVe->arrival_time)->format('H:i d/m/Y') }}</div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-danger small">Không tìm thấy chuyến bay chiều về phù hợp.</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="d-flex flex-column align-items-end text-end" style="min-width: 180px;">
                        @php
                            $rawTrip = request('loai_chuyen', request('trip_type', 'mot_chieu'));
                            $rawClass = request('hang_ve', request('flight_class', 'pho_thong'));
                            
                            // Lấy số lượng hành khách từ Controller truyền sang hoặc lấy từ request('hanh_khach')
                            $passengerCount = $passengerCount ?? request('hanh_khach', 1);
                            
                            // 1. Hệ số hạng vé (Class Multiplier)
                            $classMultiplier = 1.0;
                            switch ($rawClass) {
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

                            $isRoundTrip = ($rawTrip == 'round_trip' || $rawTrip == 'khu_hoi');
if (!$isRoundTrip) {
                                // Vé một chiều: Giá cơ bản * Hệ số hạng vé * Số lượng hành khách
                                $finalPrice = $chuyenBay->price * $classMultiplier * $passengerCount;
                            } else {
                                // Vé khứ hồi: Nhân thêm hệ số ưu đãi khứ hồi (Hạng cao cấp giảm nhiều hơn)
                                $roundTripFactor = 1.95; // Phổ thông
                                if ($rawClass === 'pho_thong_dac_biet') {
                                    $roundTripFactor = 1.90; // Phổ thông đặc biệt
                                } elseif ($rawClass === 'thuong_gia') {
                                    $roundTripFactor = 1.85; // Thương gia
                                }

                                $finalPrice = $chuyenBay->price * $classMultiplier * $roundTripFactor * $passengerCount;
                            }


                            // KIỂM TRA HẠN MỨC GHẾ CHO KHOANG A VÀ B (Tối đa 10 ghế)
                            $isSoldOut = false;
                            if ($rawClass === 'thuong_gia' || $rawClass === 'pho_thong_dac_biet') {
                                $bookedForThisClass = \App\Models\Booking::where('flight_id', $chuyenBay->id)
                                    ->where('flight_class', $rawClass)
                                    ->get()
                                    ->sum(function($bk) {
                                        return $bk->passenger_count;
                                    });

                                if (($bookedForThisClass + $passengerCount) > 10) {
                                    $isSoldOut = true; 
                                }
                            }
                        @endphp

                        <!-- Giá tiền hiển thị chuẩn theo chính sách khứ hồi / một chiều -->
                        <div class="price mb-1 fw-bold fs-5 text-danger">
                            {{ number_format($finalPrice, 0, ',', '.') }} đ
                        </div>
                        
                        <!-- Badge hạng vé và loại vé -->
                        <div class="mb-2" style="font-size: 13px;">
                            <span class="badge bg-info text-dark">
                                @php
                                    $classMap = [
                                        'pho_thong' => 'Phổ thông',
                                        'pho_thong_dac_biet' => 'Phổ thông đặc biệt',
                                        'thuong_gia' => 'Thương gia',
                                        'hang_nhat' => 'Hạng nhất',
                                    ];
                                    $displayClass = $classMap[$rawClass] ?? ucwords(str_replace(['_', '-'], ' ', $rawClass));
                                @endphp
{{ $displayClass }}
                            </span>
                            
                            <span class="text-muted ms-1">,</span>

                            <span class="badge bg-secondary ms-1">
                                {{ $isRoundTrip ? 'Khứ hồi' : 'Một chiều' }}
                            </span>
                        </div>

                        <!-- Nút Đặt vé hoặc Hiển thị Hết vé khoang nếu vượt quá 10 ghế -->
@if($isSoldOut)
    <button class="btn btn-secondary btn-sm mt-1 w-100" disabled style="padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: not-allowed;">
        Hết vé khoang này
    </button>
@else
    @auth
        {{-- Đã đăng nhập: Cho phép bấm đặt vé bình thường --}}
        <a href="{{ route('booking.create', [
            'flight_id' => $chuyenBay->id, 
            'trip_type' => $rawTrip, 
            'loai_chuyen' => $rawTrip, 
            'flight_class' => $rawClass,
            'hang_ve' => $rawClass,
            'passenger_count' => $passengerCount,
            'ngay_ve' => $ngayVe
        ]) }}" class="btn btn-primary btn-sm mt-1 w-100" style="background: #1455a3; color: white; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block;">
            Đặt vé
        </a>
    @else
        {{-- Chưa đăng nhập: Chuyển hướng sang trang đăng nhập kèm thông báo nhỏ --}}
        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm mt-1 w-100" onclick="alert('Vui lòng đăng nhập để tiến hành đặt vé!');" style="border: 1px solid #1455a3; color: #1455a3; background: #fff; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block;">
            Đăng nhập để đặt vé
        </a>
    @endauth
@endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm chuyến bay - SkyBooking</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #082b55;
            --blue: #0756a5;
            --gold: #d6ad55;
            --bg: #f3f6fa;
            --text: #23344d;
            --muted: #7b8798;
            --white: #ffffff;
            --border: #e5eaf1;
            --danger: #dc4545;
            --success: #16865b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Be Vietnam Pro', 'Segoe UI', sans-serif;
        }

        a {
            text-decoration: none;
        }

        .topbar {
            background: var(--navy);
            color: white;
            padding: 17px 24px;
            box-shadow: 0 3px 12px rgba(8, 43, 85, .15);
        }

        .topbar-inner {
            max-width: 1120px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .brand {
            color: white;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .brand span {
            color: var(--gold);
        }

        .back-link {
            color: white;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,.35);
            border-radius: 8px;
            padding: 9px 14px;
            transition: .2s;
        }

        .back-link:hover {
            background: var(--gold);
            color: var(--navy);
            border-color: var(--gold);
        }

        .hero {
            background: linear-gradient(120deg, #082b55, #0756a5);
            color: white;
            padding: 38px 20px 65px;
        }

        .hero-inner {
            max-width: 1120px;
            margin: auto;
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: 29px;
            font-weight: 800;
        }

        .hero p {
            margin: 0;
            color: #dce8f5;
            font-size: 14px;
        }

        .page-container {
            max-width: 1120px;
            margin: -35px auto 50px;
            padding: 0 20px;
            position: relative;
        }

        .search-summary {
            background: white;
            border-radius: 15px;
            padding: 21px 24px;
            box-shadow: 0 8px 25px rgba(8, 43, 85, .09);
            margin-bottom: 25px;
            border: 1px solid var(--border);
        }

        .summary-title {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            margin-bottom: 12px;
        }

        .summary-route {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 18px;
            font-weight: 800;
            color: var(--navy);
        }

        .summary-arrow {
            color: var(--gold);
            font-size: 22px;
        }

        .summary-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 13px;
            color: var(--muted);
            font-size: 13px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 25px 0 15px;
        }

        .section-heading h2 {
            color: var(--navy);
            font-size: 19px;
            margin: 0;
        }

        .result-count {
            background: #e9f1fa;
            color: var(--blue);
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .flight-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 23px;
            margin-bottom: 17px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 220px;
            gap: 22px;
            box-shadow: 0 5px 17px rgba(8, 43, 85, .05);
            transition: transform .2s, box-shadow .2s;
        }

        .flight-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(8, 43, 85, .10);
        }

        .route {
            color: var(--navy);
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .flight-number {
            color: var(--blue);
            background: #edf4fb;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 13px;
        }

        .flight-info {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 24px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        .flight-info strong {
            color: var(--text);
        }

        .return-box {
            margin-top: 18px;
            padding: 15px;
            background: #f7f9fc;
            border: 1px solid var(--border);
            border-left: 4px solid var(--gold);
            border-radius: 10px;
        }

        .return-title {
            color: var(--navy);
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .return-flight {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 11px;
            margin-bottom: 8px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.9;
        }

        .return-flight:last-child {
            margin-bottom: 0;
        }

        .return-flight strong {
            color: var(--navy);
        }

        .price-area {
            border-left: 1px solid var(--border);
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            text-align: right;
        }

        .price-label {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .price {
            color: var(--danger);
            font-size: 23px;
            font-weight: 800;
            white-space: nowrap;
        }

        .price-unit {
            color: var(--muted);
            font-size: 11px;
            margin-top: 3px;
        }

        .badges {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 6px;
            margin: 13px 0;
        }

        .badge {
            display: inline-block;
            border-radius: 20px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-class {
            color: var(--navy);
            background: #fff3d6;
            border: 1px solid #f1dfb5;
        }

        .badge-trip {
            color: var(--blue);
            background: #eaf2fb;
        }

        .booking-button {
            display: block;
            width: 100%;
            text-align: center;
            background: var(--navy);
            color: white;
            border-radius: 8px;
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 700;
            transition: .2s;
        }

        .booking-button:hover {
            background: var(--gold);
            color: var(--navy);
        }

        .disabled-button {
            display: block;
            width: 100%;
            background: #aab2bd;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 11px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: not-allowed;
        }

        .empty {
            background: white;
            padding: 35px 20px;
            border-radius: 14px;
            text-align: center;
            color: var(--muted);
            border: 1px solid var(--border);
            box-shadow: 0 5px 17px rgba(8, 43, 85, .05);
            line-height: 1.8;
        }

        .empty strong {
            display: block;
            color: var(--navy);
            font-size: 17px;
            margin-bottom: 7px;
        }

        .alert {
            background: #fff0f0;
            color: #a52c2c;
            border: 1px solid #f3cccc;
            border-radius: 9px;
            padding: 13px 16px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .footer {
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            padding: 20px;
        }

        @media (max-width: 800px) {
            .flight-card {
                grid-template-columns: 1fr;
            }

            .price-area {
                border-left: 0;
                border-top: 1px solid var(--border);
                padding: 17px 0 0;
                align-items: flex-start;
                text-align: left;
            }

            .badges {
                justify-content: flex-start;
            }

            .booking-button,
            .disabled-button {
                max-width: 250px;
            }
        }

        @media (max-width: 500px) {
            .topbar {
                padding: 14px;
            }

            .brand {
                font-size: 17px;
            }

            .hero {
                padding: 30px 16px 55px;
            }

            .hero h1 {
                font-size: 23px;
            }

            .page-container {
                padding: 0 12px;
            }

            .search-summary,
            .flight-card {
                padding: 17px;
            }

            .summary-route {
                font-size: 16px;
            }

            .flight-info {
                flex-direction: column;
                gap: 5px;
            }

            .price {
                font-size: 21px;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <div class="topbar-inner">
        <a href="/" class="brand">SKY<span>BOOKING</span></a>
        <a href="/" class="back-link">← Quay lại trang chủ</a>
    </div>
</header>

<section class="hero">
    <div class="hero-inner">
        <h1>Kết quả tìm chuyến bay</h1>
        <p>Lựa chọn hành trình phù hợp và đặt vé cùng SkyBooking.</p>
    </div>
</section>

<main class="page-container">

    @if(session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    <section class="search-summary">
        <div class="summary-title">Thông tin hành trình</div>

        <div class="summary-route">
            <span>{{ $diemDi ?? '---' }}</span>
            <span class="summary-arrow">→</span>
            <span>{{ $diemDen ?? '---' }}</span>
        </div>

        <div class="summary-details">
            <span>Ngày đi: <strong>{{ $ngayDi ?? 'Chưa chọn' }}</strong></span>
            <span>Ngày về: <strong>{{ $ngayVe ?? 'Không có' }}</strong></span>
            <span>Hành khách: <strong>{{ $passengerCount ?? 1 }}</strong></span>
            <span>Loại vé:
                <strong>
                    {{ in_array($loaiChuyen ?? '', ['khu_hoi', 'round_trip']) ? 'Khứ hồi' : 'Một chiều' }}
                </strong>
            </span>
        </div>
    </section>

    @if (!$diemDi || !$diemDen || !$ngayDi)

        <div class="empty">
            <strong>Chưa có thông tin tìm kiếm</strong>
            Vui lòng quay lại trang chủ và nhập đầy đủ điểm đi, điểm đến và ngày khởi hành.
        </div>

    @elseif ($chuyenBays->isEmpty())

        <div class="empty">
            <strong>Không tìm thấy chuyến bay phù hợp</strong>
            Bạn hãy thử chọn ngày khác hoặc hành trình khác.
        </div>

    @else

        <div class="section-heading">
            <h2>Danh sách chuyến bay chiều đi</h2>
            <span class="result-count">{{ $chuyenBays->count() }} chuyến bay</span>
        </div>

        @foreach ($chuyenBays as $chuyenBay)

            @php
                $rawTrip = $loaiChuyen ?? request('loai_chuyen', request('trip_type', 'mot_chieu'));
                $rawClass = request('hang_ve', request('flight_class', 'pho_thong'));

                $passengerCount = $passengerCount ?? request('hanh_khach', 1);

                // Giữ nguyên hệ số giá theo hạng vé của nhóm.
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
                    $finalPrice = $chuyenBay->price * $classMultiplier * $passengerCount;
                } else {
                    $roundTripFactor = 1.95;

                    if ($rawClass === 'pho_thong_dac_biet') {
                        $roundTripFactor = 1.90;
                    } elseif ($rawClass === 'thuong_gia') {
                        $roundTripFactor = 1.85;
                    }

                    $finalPrice = $chuyenBay->price * $classMultiplier * $roundTripFactor * $passengerCount;
                }

                // Giữ nguyên giới hạn 10 ghế của nhóm cho hai hạng vé này.
                $isSoldOut = false;

                if ($rawClass === 'thuong_gia' || $rawClass === 'pho_thong_dac_biet') {
                    $bookedForThisClass = \App\Models\Booking::where('flight_id', $chuyenBay->id)
                        ->where('flight_class', $rawClass)
                        ->get()
                        ->sum(function ($bk) {
                            return $bk->passenger_count;
                        });

                    if (($bookedForThisClass + $passengerCount) > 10) {
                        $isSoldOut = true;
                    }
                }

                $classMap = [
                    'pho_thong' => 'Phổ thông',
                    'pho_thong_dac_biet' => 'Phổ thông đặc biệt',
                    'thuong_gia' => 'Thương gia',
                    'hang_nhat' => 'Hạng nhất',
                ];

                $displayClass = $classMap[$rawClass] ?? ucwords(str_replace(['_', '-'], ' ', $rawClass));
            @endphp

            <article class="flight-card">

                <div class="flight-main">

                    <div class="route">
                        {{ $chuyenBay->ma_san_bay_di }}
                        ({{ $chuyenBay->thanh_pho_di }})
                        →
                        {{ $chuyenBay->ma_san_bay_den }}
                        ({{ $chuyenBay->thanh_pho_den }})
                    </div>

                    <div class="flight-number">
                        Mã chuyến bay: {{ $chuyenBay->flight_number }}
                    </div>

                    <div class="flight-info">
                        <span>
                            Khởi hành:
                            <strong>{{ \Carbon\Carbon::parse($chuyenBay->departure_time)->format('H:i d/m/Y') }}</strong>
                        </span>

                        <span>
                            Hạ cánh:
                            <strong>{{ \Carbon\Carbon::parse($chuyenBay->arrival_time)->format('H:i d/m/Y') }}</strong>
                        </span>

                        <span>
                            Số ghế còn lại:
                            <strong>{{ $chuyenBay->available_seats }}</strong>
                        </span>
                    </div>

                    @if($isRoundTrip)

                        <div class="return-box">
                            <div class="return-title">
                                Chuyến bay chiều về:
                                {{ $chuyenBay->thanh_pho_den }}
                                → {{ $chuyenBay->thanh_pho_di }}
                                @if($ngayVe)
                                    - Ngày {{ \Carbon\Carbon::parse($ngayVe)->format('d/m/Y') }}
                                @endif
                            </div>

                            @if(isset($chuyenBayVes) && $chuyenBayVes->isNotEmpty())

                                @foreach($chuyenBayVes as $chuyenVe)

                                    <div class="return-flight">
                                        <strong>Mã chuyến: {{ $chuyenVe->flight_number }}</strong><br>

                                        Khởi hành:
                                        {{ \Carbon\Carbon::parse($chuyenVe->departure_time)->format('H:i d/m/Y') }}
                                        <br>

                                        Hạ cánh:
                                        {{ \Carbon\Carbon::parse($chuyenVe->arrival_time)->format('H:i d/m/Y') }}
                                        <br>

                                        Số ghế còn lại: {{ $chuyenVe->available_seats }}
                                    </div>

                                @endforeach

                            @else
                                <div style="color:#dc4545;font-size:12px;">
                                    Không tìm thấy chuyến bay chiều về phù hợp.
                                </div>
                            @endif
                        </div>

                    @endif

                </div>

                <div class="price-area">

                    <div class="price-label">Giá vé dự kiến</div>

                    <div class="price">
                        {{ number_format($finalPrice, 0, ',', '.') }} đ
                    </div>

                    <div class="price-unit">
                        Cho {{ $passengerCount }} hành khách
                    </div>

                    <div class="badges">
                        <span class="badge badge-class">{{ $displayClass }}</span>

                        <span class="badge badge-trip">
                            {{ $isRoundTrip ? 'Khứ hồi' : 'Một chiều' }}
                        </span>
                    </div>

                    @if($isSoldOut)

                        <button class="disabled-button" disabled>
                            Hết vé khoang này
                        </button>

                    @else

                        <a href="{{ route('booking.create', [
                            'flight_id' => $chuyenBay->id,
                            'trip_type' => $rawTrip,
                            'loai_chuyen' => $rawTrip,
                            'flight_class' => $rawClass,
                            'hang_ve' => $rawClass,
                            'passenger_count' => $passengerCount,
                            'ngay_ve' => $ngayVe
                        ]) }}" class="booking-button">
                            Đặt vé ngay →
                        </a>

                    @endif

                </div>

            </article>

        @endforeach

    @endif

</main>

<footer class="footer">
    © {{ date('Y') }} SkyBooking. Hệ thống đặt vé máy bay.
</footer>

</body>
</html>

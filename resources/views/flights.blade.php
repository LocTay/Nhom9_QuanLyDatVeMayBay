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
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #24324a;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            color: #1455a3;
        }

        .flight {
            background: white;
            padding: 20px;
            margin: 16px 0;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0000000d;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .route {
            font-size: 18px;
            font-weight: bold;
        }

        .muted {
            color: #6b778c;
            margin-top: 8px;
        }

        .price {
            color: #e65b2a;
            font-size: 20px;
            font-weight: bold;
        }

        .empty {
            background: white;
            padding: 20px;
            border-radius: 12px;
        }

        .back {
            display: inline-block;
            margin-bottom: 15px;
            color: #1455a3;
            text-decoration: none;
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
                <div class="flight">
                    <div>
                        <div class="route">
                            {{ $chuyenBay->ma_san_bay_di }}
                            ({{ $chuyenBay->thanh_pho_di }})
                            →
                            {{ $chuyenBay->ma_san_bay_den }}
                            ({{ $chuyenBay->thanh_pho_den }})
                        </div>

                        <div class="muted">
                            Mã chuyến: {{ $chuyenBay->flight_number }}
                        </div>

                        <div class="muted">
                            Khởi hành:
                            {{ \Carbon\Carbon::parse($chuyenBay->departure_time)->format('H:i d/m/Y') }}
                        </div>

                        <div class="muted">
                            Hạ cánh:
                            {{ \Carbon\Carbon::parse($chuyenBay->arrival_time)->format('H:i d/m/Y') }}
                        </div>

                        <div class="muted">
                            Số ghế còn lại: {{ $chuyenBay->available_seats }}
                        </div>
                    </div>

                    <div class="price">
                        {{ number_format($chuyenBay->price, 0, ',', '.') }} đ
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt vé thành công - SkyBooking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            /* Nền chuyển màu mềm mại chủ đề hàng không */
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .ticket-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        .ticket-header {
            background: linear-gradient(45deg, #0052d4, #4364f7, #6fb1fc);
            color: white;
        }
        .pnr-box {
            background-color: #f8f9fa;
            border: 2px dashed #0d6efd;
            border-radius: 12px;
        }
        .info-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        .info-value {
            font-weight: 600;
            color: #212529;
        }
    </style>
</head>
<body class="py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            
            <div class="card ticket-card">
                <!-- Header thông báo -->
                <div class="ticket-header p-4 text-center">
                    <div class="mb-2">
                        <i class="bi bi-check-circle-fill display-4 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-1">ĐẶT VÉ THÀNH CÔNG!</h3>
                    <p class="mb-0 opacity-75">Quý khách vui lòng lưu hoặc chụp màn hình thông tin vé bên dưới</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Khung Mã PNR nổi bật -->
                    <div class="pnr-box p-3 text-center mb-4">
                        <span class="info-label d-block mb-1">MÃ ĐẶT CHỖ (PNR)</span>
                        <h1 class="text-primary fw-bold mb-0" style="letter-spacing: 3px;">{{ $booking->pnr_code }}</h1>
                    </div>

                    <!-- Thông tin chiều đi -->
                    <h6 class="text-primary fw-bold text-uppercase border-bottom pb-2 mb-3">
                        <i class="bi bi-airplane-engines me-2"></i>Thông tin chuyến bay (Chiều đi)
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <span class="info-label d-block">Tên sân bay đi & đến</span>
                            <span class="info-value fs-5 text-primary">
                                {{ $booking->flight->departureAirport->name ?? 'Sân bay đi' }} 
                                <i class="bi bi-arrow-right mx-1"></i> 
                                {{ $booking->flight->arrivalAirport->name ?? 'Sân bay đến' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="info-label d-block">Mã chuyến bay</span>
                            <span class="info-value fs-5">{{ $booking->flight->flight_number ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="info-label d-block">Loại vé & Hạng vé</span>
                            <span class="info-value text-primary">
                                @php
                                    $rawTrip = $booking->trip_type ?? 'one_way';
                                    $rawClass = $booking->flight_class ?? 'Phổ thông';

                                    $classMap = [
                                        'pho_thong' => 'Phổ thông',
                                        'pho_thong_dac_biet' => 'Phổ thông đặc biệt',
                                        'thuong_gia' => 'Thương gia',
                                        'hang_nhat' => 'Hạng nhất',
                                    ];
                                    $displayClass = $classMap[$rawClass] ?? ucwords(str_replace(['_', '-'], ' ', $rawClass));
                                    $isRoundTrip = ($rawTrip == 'round_trip' || $rawTrip == 'khu_hoi');
                                @endphp
                                {{ $isRoundTrip ? 'Khứ hồi' : 'Một chiều' }} • {{ $displayClass }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="info-label d-block">Số lượng vé</span>
                            <span class="info-value fs-5">{{ $booking->passenger_count ?? 1 }} hành khách</span>
                        </div>
                        <div class="col-6">
                            <span class="info-label d-block">Thời gian khởi hành</span>
                            <span class="info-value">{{ $booking->flight->departure_time ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="info-label d-block">Thời gian dự kiến đến</span>
                            <span class="info-value">{{ $booking->flight->arrival_time ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- THÊM ĐOẠN NÀY ĐỂ HIỂN THỊ CHIỀU VỀ NẾU LÀ VÉ KHỨ HỒI -->
                    @if($isRoundTrip)
                        @php
                            // Lấy ngày về được truyền từ request hoặc lưu trong session/database
                            $ngayVeBooking = request('ngay_ve');

                            // Tìm chuyến bay chiều về ngược lại và khớp chính xác ngày về (nếu có)
                            $returnQuery = \App\Models\Flight::where('departure_airport_id', $booking->flight->arrival_airport_id)
                                ->where('arrival_airport_id', $booking->flight->departure_airport_id)
                                ->where('status', 'scheduled');

                            if (!empty($ngayVeBooking)) {
                                $returnQuery->whereDate('departure_time', $ngayVeBooking);
                            }

                            $returnFlight = $returnQuery->orderBy('departure_time')->first();
                        @endphp

                        <h6 class="text-success fw-bold text-uppercase border-bottom pb-2 mb-3 mt-4">
                            <i class="bi bi-airplane-fill me-2"></i>Thông tin chuyến bay (Chiều về)
                        </h6>
                        <div class="row g-3 mb-4 p-3 bg-light rounded border border-success">
                            <div class="col-12">
                                <span class="info-label d-block">Tên sân bay về & đến</span>
                                <span class="info-value fs-5 text-success">
                                    {{ $booking->flight->arrivalAirport->name ?? 'Sân bay về' }} 
                                    <i class="bi bi-arrow-right mx-1"></i> 
                                    {{ $booking->flight->departureAirport->name ?? 'Sân bay đến' }}
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="info-label d-block">Mã chuyến bay khứ hồi</span>
                                <span class="info-value fs-5">{{ $returnFlight->flight_number ?? 'Sắp xếp tự động' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="info-label d-block">Thời gian khởi hành (khứ hồi)</span>
                                <span class="info-value">
                                    {{ $returnFlight ? \Carbon\Carbon::parse($returnFlight->departure_time)->format('H:i d/m/Y') : ($ngayVeBooking ? \Carbon\Carbon::parse($ngayVeBooking)->format('d/m/Y') : 'Theo lịch trình') }}
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="info-label d-block">Thời gian dự kiến đến(khứ hồi)</span>
                                <span class="info-value">
                                    {{ $returnFlight ? \Carbon\Carbon::parse($returnFlight->arrival_time)->format('H:i d/m/Y') : 'Theo lịch trình' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Thông tin liên hệ (Người đại diện) -->
                    <h6 class="text-primary fw-bold text-uppercase border-bottom pb-2 mb-3">
                        <i class="bi bi-person-lines-fill me-2"></i>Thông tin liên hệ (Người đại diện)
                    </h6>
                    <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                        <div class="col-md-4">
                            <span class="info-label d-block">Họ và tên</span>
                            <span class="info-value text-primary fw-bold">
                                {{ $booking->passenger_name }} <span class="text-dark fw-normal">({{ $booking->passenger_gender ?? 'N/A' }})</span>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <span class="info-label d-block">Số điện thoại</span>
                            <span class="info-value">{{ $booking->passenger_phone ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-4">
                            <span class="info-label d-block">Email nhận vé</span>
                            <span class="info-value">{{ $booking->passenger_email ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Danh sách hành khách bay & Ghế ngồi -->
                    <h6 class="text-primary fw-bold text-uppercase border-bottom pb-2 mb-3">
                        <i class="bi bi-people-fill me-2"></i>Danh sách hành khách bay ({{ $booking->passenger_count }} vé)
                    </h6>
                    <div class="mb-4">
                        <div class="list-group">
                            @if(!empty($booking->passengers_data) && is_array($booking->passengers_data))
                                @foreach($booking->passengers_data as $index => $passenger)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <i class="bi bi-person-fill text-secondary me-2"></i>
                                            <strong>Hành khách {{ $index + 1 }}:</strong> {{ $passenger['name'] ?? 'N/A' }}
                                            <span class="badge bg-secondary ms-2">{{ $passenger['gender'] ?? 'N/A' }}</span>                                            
                                            @if(!empty($passenger['seat']))
                                                <span class="ms-2 badge bg-success">Ghế: {{ $passenger['seat'] }}</span>
                                            @endif
                                        </div>
                                        <span class="badge bg-primary rounded-pill">Đã xác nhận</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="list-group-item py-2">
                                    <i class="bi bi-person-fill text-secondary me-2"></i> {{ $booking->passenger_name }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tổng tiền thanh toán -->
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mb-4 border">
                        <span class="fw-bold text-secondary">Tổng tiền thanh toán</span>
                        <span class="fs-4 fw-bold text-danger">{{ number_format($booking->total_price) }} VND</span>
                    </div>

                    <!-- Nút bấm hành động -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="/" class="btn btn-outline-secondary px-4 py-2">
                            <i class="bi bi-house-door me-1"></i> Về trang chủ
                        </a>
                        <button onclick="window.print()" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-printer me-1"></i> In / Tải vé
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
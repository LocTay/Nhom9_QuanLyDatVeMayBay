<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết vé - {{ $booking->booking_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Arial, sans-serif; color: #243b53; background: #f2f6fb; }
        .site-header { background: linear-gradient(120deg, #071b3a, #0750a0); padding: 17px 0; box-shadow: 0 3px 14px rgba(0, 0, 0, .12); }
        .brand { color: #fff; text-decoration: none; font-size: 22px; font-weight: 800; letter-spacing: .7px; }
        .brand i { color: #55c7ff; margin-right: 9px; }
        .page-wrap { width: min(100% - 30px, 1000px); margin: 32px auto 50px; }
        .back-link { display: inline-block; margin-bottom: 22px; border-radius: 9px; font-weight: 600; }
        .page-title { color: #123e73; font-weight: 800; margin-bottom: 5px; }
        .page-description { color: #718096; margin-bottom: 24px; }
        .ticket-card, .action-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 28px rgba(30, 60, 90, .08); }
        .ticket-card { margin-bottom: 22px; }
        .ticket-header { color: #fff; background: linear-gradient(120deg, #0879c9, #0750a0); padding: 24px 28px; }
        .booking-label { color: rgba(255,255,255,.78); font-size: 12px; font-weight: 700; letter-spacing: 1px; }
        .booking-code { font-size: 27px; font-weight: 800; letter-spacing: 2px; margin-top: 4px; }
        .status-badge { display: inline-block; padding: 8px 14px; border-radius: 30px; font-size: 13px; font-weight: 700; white-space: nowrap; }
        .status-paid { background: #d1fae5; color: #047857; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-failed { background: #fee2e2; color: #b91c1c; }
        .status-cancelled { background: #e5e7eb; color: #374151; }
        .ticket-body { padding: 28px; }
        .section-title { color: #0750a0; font-size: 15px; font-weight: 800; padding-bottom: 12px; margin-bottom: 18px; border-bottom: 2px solid #eaf1f8; }
        .section-title i { margin-right: 8px; color: #1683c7; }
        .flight-route { background: #f4f9ff; border: 1px solid #e1edf9; border-radius: 13px; padding: 20px 12px; margin-bottom: 26px; }
        .airport-code { color: #0750a0; font-size: 30px; line-height: 1.2; font-weight: 800; }
        .airport-city { color: #70849a; font-size: 13px; margin-top: 5px; }
        .route-line { display: flex; align-items: center; justify-content: center; color: #1683c7; padding: 0 8px; }
        .route-line .line { height: 2px; background: #b5d9f5; flex: 1; }
        .route-line i { margin: 0 9px; font-size: 19px; }
        .info-item { display: flex; align-items: flex-start; gap: 11px; margin-bottom: 17px; }
        .info-icon { width: 36px; height: 36px; flex: 0 0 36px; display: flex; align-items: center; justify-content: center; color: #0879c9; background: #eaf5ff; border-radius: 9px; }
        .info-content { min-width: 0; }
        .info-content small { display: block; color: #8796a8; font-size: 12px; margin-bottom: 3px; }
        .info-content strong { display: block; color: #243b53; font-size: 14px; font-weight: 650; overflow-wrap: anywhere; }
        .flight-number { display: inline-block; color: #0750a0; background: #dff1ff; border-radius: 7px; padding: 5px 10px; font-weight: 800; }
        .price-box { background: #fff7ed; border: 1px solid #ffdfc2; border-radius: 12px; padding: 18px 20px; margin-top: 8px; }
        .price-label { color: #9a6b40; font-size: 12px; font-weight: 700; }
        .total-price { color: #e65332; font-size: 26px; font-weight: 800; margin-top: 4px; }
        .action-card { padding: 24px 28px; }
        .action-title { color: #123e73; font-weight: 800; margin-bottom: 18px; }
        .action-buttons { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; }
        .payment-form { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; }
        .payment-label { color: #64748b; font-size: 13px; font-weight: 700; }
        .btn-action { border-radius: 9px; padding: 10px 16px; font-weight: 650; }
        .site-footer { background: #071b3a; color: #cbdced; text-align: center; padding: 19px 12px; font-size: 13px; }
        .alert { border-radius: 10px; }
        @media (min-width: 768px) {
            .flight-column { border-right: 1px solid #e7edf4; padding-right: 25px; }
            .passenger-column { padding-left: 25px; }
        }
        @media (max-width: 767.98px) {
            .page-wrap { margin-top: 22px; }
            .ticket-header, .ticket-body { padding: 20px; }
            .booking-code { font-size: 23px; }
            .status-wrap { text-align: left !important; margin-top: 15px; }
            .airport-code { font-size: 24px; }
            .airport-city { font-size: 11px; }
            .route-line i { margin: 0 5px; font-size: 16px; }
            .action-card { padding: 20px; }
            .action-buttons { align-items: stretch; flex-direction: column; }
            .action-buttons form { width: 100%; }
            .payment-form { width: 100%; }
            .payment-form button { flex: 1; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="brand" href="{{ route('ticket.lookup.form') }}">
            <i class="fas fa-plane-departure"></i> SKY TRAVEL
        </a>
        <span class="text-white small">
            <a href="{{ url('/') }}" class="btn btn-primary btn-action">
                <i class="fas fa-home me-2"></i> Quay lại trang chủ
            </a>
        </span>
    </div>
</header>

<main class="page-wrap">
    <a href="{{ route('ticket.lookup.form') }}" class="btn btn-outline-primary back-link">
        <i class="fas fa-arrow-left me-2"></i> Quay lại tra cứu
    </a>

    <h3 class="page-title">Chi tiết vé máy bay</h3>
    <p class="page-description">Thông tin đặt chỗ, chuyến bay và hành khách của bạn.</p>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>{{ session('info') }}</div>
    @endif

    <section class="ticket-card">
        <div class="ticket-header">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="booking-label">BOOKING CODE / MÃ ĐẶT CHỖ</div>
                    <div class="booking-code">{{ $booking->pnr_code }}</div>
                    <div class="small text-white-50 mt-2">
                        <i class="fas fa-plane me-1"></i> SKY TRAVEL E-TICKET
                    </div>
                </div>

                <div class="col-md-5 text-md-end status-wrap">
                    @if($booking->status === 'paid')
                        <span class="status-badge status-paid"><i class="fas fa-check-circle me-1"></i> Đã thanh toán</span>
                    @elseif($booking->status === 'pending')
                        <span class="status-badge status-pending"><i class="fas fa-clock me-1"></i> Chờ thanh toán</span>
                    @elseif($booking->status === 'failed')
                        <span class="status-badge status-failed"><i class="fas fa-times-circle me-1"></i> Thanh toán thất bại</span>
                    @elseif($booking->status === 'cancelled')
                        <span class="status-badge status-cancelled"><i class="fas fa-ban me-1"></i> Đã hủy vé</span>
                    @else
                        <span class="status-badge status-pending">Chưa xác định trạng thái</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="ticket-body">
            <h6 class="section-title"><i class="fas fa-plane"></i> THÔNG TIN CHUYẾN BAY</h6>

            <div class="flight-route">
                <div class="row align-items-center text-center">
                    <div class="col-4">
                        <div class="airport-code">{{ $booking->flight->departureAirport->code ?? '---' }}</div>
                        <div class="airport-city">{{ $booking->flight->departureAirport->city ?? '' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="route-line">
                            <span class="line"></span>
                            <i class="fas fa-plane"></i>
                            <span class="line"></span>
                        </div>
                        <div class="small text-muted mt-2">{{ $booking->flight->flight_number ?? 'N/A' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="airport-code">{{ $booking->flight->arrivalAirport->code ?? '---' }}</div>
                        <div class="airport-city">{{ $booking->flight->arrivalAirport->city ?? '' }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- CỘT CHUYẾN BAY -->
                <div class="col-md-6 flight-column">
                    <h6 class="section-title"><i class="fas fa-plane-departure"></i> CHI TIẾT HÀNH TRÌNH</h6>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-content">
                            <small>Điểm khởi hành</small>
                            <strong>{{ $booking->flight->departureAirport->name ?? 'N/A' }} ({{ $booking->flight->departureAirport->code ?? '---' }}) - {{ $booking->flight->departureAirport->city ?? '' }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-pin"></i></div>
                        <div class="info-content">
                            <small>Điểm đến</small>
                            <strong>{{ $booking->flight->arrivalAirport->name ?? 'N/A' }} ({{ $booking->flight->arrivalAirport->code ?? '---' }}) - {{ $booking->flight->arrivalAirport->city ?? '' }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                        <div class="info-content">
                            <small>Giờ cất cánh</small>
                            <strong>{{ isset($booking->flight->departure_time) ? \Carbon\Carbon::parse($booking->flight->departure_time)->format('H:i - d/m/Y') : 'N/A' }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                        <div class="info-content">
                            <small>Giờ hạ cánh</small>
                            <strong>{{ isset($booking->flight->arrival_time) ? \Carbon\Carbon::parse($booking->flight->arrival_time)->format('H:i - d/m/Y') : 'N/A' }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-fingerprint"></i></div>
                        <div class="info-content">
                            <small>Số hiệu chuyến bay</small>
                            <strong><span class="flight-number">{{ $booking->flight->flight_number ?? 'N/A' }}</span></strong>
                        </div>
                    </div>

                    <!-- BỔ SUNG HẠNG VÉ -->
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-couch"></i></div>
                        <div class="info-content">
                            <small>Hạng ghế</small>
                            <strong>
                                @if(($booking->flight_class ?? '') === 'thuong_gia')
                                    Thương gia
                                @elseif(($booking->flight_class ?? '') === 'pho_thong_dac_biet')
                                    Phổ thông đặc biệt
                                @else
                                    Phổ thông
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- CỘT HÀNH KHÁCH (LẤY TỪ SQL DẠNG JSON TRONG PASSENGERS_DATA) -->
                <div class="col-md-6 passenger-column">
                    <h6 class="section-title"><i class="fas fa-user"></i> THÔNG TIN HÀNH KHÁCH</h6>

                    @php
                        $passengers = is_array($booking->passengers_data) 
                            ? $booking->passengers_data 
                            : json_decode($booking->passengers_data ?? '[]', true);
                    @endphp

                    @if(!empty($passengers) && count($passengers) > 0)
                        @foreach($passengers as $index => $p)
                            @if(count($passengers) > 1)
                                <div class="fw-bold text-primary mb-2 mt-2">Hành khách {{ $index + 1 }}</div>
                            @endif

                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-user-circle"></i></div>
                                <div class="info-content">
                                    <small>Họ và tên</small>
                                    <strong>{{ $p['name'] ?? $booking->passenger_name }}</strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-venus-mars"></i></div>
                                <div class="info-content">
                                    <small>Giới tính</small>
                                    <strong>{{ $p['gender'] ?? $booking->passenger_gender ?? 'Chưa cập nhật' }}</strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-chair"></i></div>
                                <div class="info-content">
                                    <small>Vị trí ghế</small>
                                    <strong><span class="flight-number">{{ $p['seat'] ?? 'Tự động xếp ghế' }}</span></strong>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Nếu không có mảng JSON, hiển thị thông tin ở bảng chính -->
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-user-circle"></i></div>
                            <div class="info-content">
                                <small>Họ và tên</small>
                                <strong>{{ $booking->passenger_name }}</strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-venus-mars"></i></div>
                            <div class="info-content">
                                <small>Giới tính</small>
                                <strong>{{ $booking->passenger_gender ?? 'Chưa cập nhật' }}</strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-chair"></i></div>
                            <div class="info-content">
                                <small>Vị trí ghế</small>
                                <strong><span class="flight-number">Chưa chọn ghế</span></strong>
                            </div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-content">
                            <small>Email liên hệ</small>
                            <strong>{{ $booking->passenger_email }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone"></i></div>
                        <div class="info-content">
                            <small>Số điện thoại</small>
                            <strong>{{ $booking->passenger_phone }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-users"></i></div>
                        <div class="info-content">
                            <small>Số lượng ghế đặt</small>
                            <strong>{{ $booking->number_of_seats }} ghế</strong>
                        </div>
                    </div>

                    @if($booking->payment_time)
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="info-content">
                                <small>Thời gian thanh toán</small>
                                <strong>{{ \Carbon\Carbon::parse($booking->payment_time)->format('H:i - d/m/Y') }}</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="price-box">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="price-label"><i class="fas fa-receipt me-1"></i> TỔNG THANH TOÁN</div>
                        <div class="total-price">{{ number_format($booking->total_price) }} VNĐ</div>
                    </div>
                    <div class="text-end text-warning">
                        <i class="fas fa-shield-alt fa-2x"></i>
                        <div class="small text-muted mt-1">SKY TRAVEL</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KHỐI THAO TÁC VÉ -->
    <section class="action-card">
    <h6 class="action-title"><i class="fas fa-cogs text-primary me-2"></i> THAO TÁC VÉ MÁY BAY</h6>
    <div class="action-buttons">
        <!-- Nút Hủy vé -->
        @if($booking->status !== 'cancelled')
            <form action="{{ route('ticket.cancel', $booking->booking_code) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy vé máy bay này?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-action">
                    <i class="fas fa-ban me-2"></i> Hủy vé máy bay
                </button>
                <button onclick="window.print()" class="btn btn-primary px-4 py-2">
                    <i class="bi bi-printer me-1"></i> In / Tải vé
                </button>
            </form>
        @else
            <div class="text-muted small"><i class="fas fa-info-circle me-1"></i> Vé này đã được hủy.</div>
        @endif

        <!-- Khu vực Thanh toán / Trạng thái -->
        @if(in_array(($booking->status ?? ''), ['pending', 'failed']))
    <!-- Chuyển hướng sang giao diện thanh toán QR -->
    <a href="{{ route('ticket.payment.page', $booking->booking_code) }}" class="btn btn-success btn-action">
        <i class="fas fa-credit-card me-2"></i> Thanh toán
    </a>
@elseif(($booking->status ?? '') === 'pending_approval')
    <!-- Hiển thị khi khách đã bấm Xác nhận chuyển khoản, đang chờ Admin duyệt -->
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="text-info fw-bold">
            <i class="fas fa-hourglass-half me-1"></i> Đang chờ duyệt...
        </span>

        
    </div>
@elseif(($booking->status ?? '') === 'paid')
    <!-- Đã thanh toán thành công -->
    <span class="text-success fw-bold">
        <i class="fas fa-check-circle me-2"></i> Vé đã được thanh toán thành công!
    </span>
@endif
    </div>
</section>
</main>

<footer class="site-footer">
    <div>© {{ date('Y') }} SKY TRAVEL - Hệ thống quản lý và tra cứu vé máy bay</div>
</footer>

</body>
</html>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Vé - {{ $booking->booking_code }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            color: #243b53;
        }

        /* Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, #071b3a, #0750a0);
            padding: 17px 0;
            box-shadow: 0 3px 15px rgba(0,0,0,0.12);
        }

        .navbar-brand {
            color: white !important;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .navbar-brand i {
            color: #55c7ff;
            margin-right: 10px;
        }

        /* Main */
        .main-container {
            max-width: 1050px;
            margin: auto;
            padding: 35px 15px 55px;
        }

        .back-btn {
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            margin-bottom: 22px;
        }

        .page-title {
            font-weight: 800;
            color: #123e73;
            margin-bottom: 7px;
        }

        .page-subtitle {
            color: #8192a5;
            font-size: 14px;
            margin-bottom: 25px;
        }

        /* Ticket */
        .ticket-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 35px rgba(30,60,90,0.09);
            margin-bottom: 25px;
        }

        .ticket-header {
            background: linear-gradient(135deg, #0879c9, #0750a0);
            color: white;
            padding: 25px 30px;
        }

        .ticket-header h5 {
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .booking-code {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #ffffff;
        }

        .status-badge {
            display: inline-block;
            padding: 9px 17px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            margin-top: 12px;
        }

        .status-paid {
            background: #d1fae5;
            color: #047857;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-cancelled {
            background: #e5e7eb;
            color: #374151;
        }

        .ticket-body {
            padding: 30px;
        }

        .section-title {
            color: #0750a0;
            font-size: 16px;
            font-weight: 800;
            padding-bottom: 13px;
            margin-bottom: 22px;
            border-bottom: 2px solid #eaf1f8;
        }

        .section-title i {
            margin-right: 9px;
            color: #1683c7;
        }

        /* Flight route */
        .flight-route {
            background: #f4f9ff;
            border: 1px solid #e1edf9;
            border-radius: 15px;
            padding: 22px 15px;
            margin-bottom: 25px;
        }

        .airport-code {
            font-size: 32px;
            font-weight: 800;
            color: #0750a0;
            letter-spacing: 1px;
        }

        .airport-name {
            font-size: 13px;
            color: #70849a;
            margin-top: 3px;
        }

        .route-line {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1683c7;
            padding: 0 10px;
        }

        .route-line .line {
            height: 2px;
            background: #b5d9f5;
            flex: 1;
            position: relative;
        }

        .route-line i {
            font-size: 21px;
            margin: 0 10px;
            transform: rotate(0deg);
        }

        /* Information */
        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 18px;
            gap: 12px;
        }

        .info-icon {
            min-width: 38px;
            height: 38px;
            background: #eaf5ff;
            color: #0879c9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-content small {
            display: block;
            color: #8a9bad;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .info-content strong {
            color: #243b53;
            font-size: 14px;
            word-break: break-word;
        }

        .flight-number {
            background: #dff1ff;
            color: #0750a0;
            padding: 7px 13px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 15px;
        }

        /* Price */
        .price-box {
            background: linear-gradient(135deg, #fff7ed, #fff1e6);
            border: 1px solid #ffdfc2;
            border-radius: 14px;
            padding: 20px;
            margin-top: 10px;
        }

        .price-box small {
            color: #9a6b40;
            font-weight: 600;
        }

        .total-price {
            color: #e65332;
            font-size: 27px;
            font-weight: 800;
            margin-top: 5px;
        }

        /* Action */
        .action-card {
            background: white;
            border-radius: 18px;
            padding: 25px 30px;
            box-shadow: 0 8px 35px rgba(30,60,90,0.07);
        }

        .action-title {
            font-weight: 800;
            color: #123e73;
            margin-bottom: 20px;
        }

        .btn-action {
            border-radius: 10px;
            padding: 11px 20px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .payment-area {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .payment-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin-right: 5px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background: #071b3a;
            color: #cbdced;
            font-size: 13px;
        }

        .alert {
            border-radius: 12px;
        }

        @media(max-width: 767px) {
            .ticket-header {
                padding: 22px 20px;
            }

            .ticket-body {
                padding: 22px 18px;
            }

            .airport-code {
                font-size: 25px;
            }

            .airport-name {
                font-size: 11px;
            }

            .route-line {
                padding: 0 5px;
            }

            .route-line i {
                font-size: 16px;
                margin: 0 5px;
            }

            .ticket-header .text-end {
                text-align: left !important;
                margin-top: 15px;
            }

            .action-card {
                padding: 22px 18px;
            }

            .payment-area {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="{{ route('ticket.lookup.form') }}">
            <i class="fas fa-plane-departure"></i> SKY TRAVEL
        </a>

        <span class="text-white small">
            <i class="fas fa-ticket-alt me-2"></i>
            Vé máy bay điện tử
        </span>
    </div>
</nav>

<!-- Main -->
<div class="main-container">

    <a href="{{ route('ticket.lookup.form') }}" class="btn btn-outline-primary back-btn">
        <i class="fas fa-arrow-left me-2"></i> Quay lại tra cứu
    </a>

    <h3 class="page-title">Chi tiết vé máy bay</h3>
    <p class="page-subtitle">
        Xem thông tin đặt chỗ, hành khách và trạng thái thanh toán của bạn.
    </p>

    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}
        </div>
    @endif

    <!-- Ticket Card -->
    <div class="ticket-card">

        <!-- Header -->
        <div class="ticket-header">
            <div class="row align-items-center">

                <div class="col-md-7">
                    <div class="small mb-2 text-white-50">
                        BOOKING CODE / MÃ ĐẶT CHỖ
                    </div>

                    <div class="booking-code">
                        {{ $booking->booking_code }}
                    </div>

                    <div class="mt-2 small text-white-50">
                        <i class="fas fa-plane me-1"></i>
                        SKY TRAVEL E-TICKET
                    </div>
                </div>

                <div class="col-md-5 text-end">

                    @if($booking->status === 'paid')
                        <span class="status-badge status-paid">
                            <i class="fas fa-check-circle me-1"></i>
                            Đã thanh toán
                        </span>

                    @elseif($booking->status === 'pending')
                        <span class="status-badge status-pending">
                            <i class="fas fa-clock me-1"></i>
                            Chờ thanh toán
                        </span>

                    @elseif($booking->status === 'failed')
                        <span class="status-badge status-failed">
                            <i class="fas fa-times-circle me-1"></i>
                            Thanh toán thất bại
                        </span>

                    @elseif($booking->status === 'cancelled')
                        <span class="status-badge status-cancelled">
                            <i class="fas fa-ban me-1"></i>
                            Đã hủy vé
                        </span>
                    @endif

                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="ticket-body">

            <!-- Flight Information -->
            <h6 class="section-title">
                <i class="fas fa-plane"></i>
                THÔNG TIN CHUYẾN BAY
            </h6>

            <div class="flight-route">

                <div class="row align-items-center text-center">

                    <div class="col-4">
                        <div class="airport-code">
                            {{ $booking->flight->departureAirport->code }}
                        </div>

                        <div class="airport-name">
                            {{ $booking->flight->departureAirport->city }}
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="route-line">
                            <div class="line"></div>
                            <i class="fas fa-plane"></i>
                            <div class="line"></div>
                        </div>

                        <div class="small text-muted mt-2">
                            {{ $booking->flight->flight_number }}
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="airport-code">
                            {{ $booking->flight->arrivalAirport->code }}
                        </div>

                        <div class="airport-name">
                            {{ $booking->flight->arrivalAirport->city }}
                        </div>
                    </div>

                </div>

            </div>

            <div class="row g-4">

                <!-- Flight Details -->
                <div class="col-md-6 border-md-end">

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-plane-departure"></i>
                        </div>

                        <div class="info-content">
                            <small>Điểm khởi hành</small>
                            <strong>
                                {{ $booking->flight->departureAirport->name }}
                                ({{ $booking->flight->departureAirport->code }})
                                - {{ $booking->flight->departureAirport->city }}
                            </strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-plane-arrival"></i>
                        </div>

                        <div class="info-content">
                            <small>Điểm đến</small>
                            <strong>
                                {{ $booking->flight->arrivalAirport->name }}
                                ({{ $booking->flight->arrivalAirport->code }})
                                - {{ $booking->flight->arrivalAirport->city }}
                            </strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div class="info-content">
                            <small>Giờ cất cánh</small>
                            <strong>
                                {{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('H:i - d/m/Y') }}
                            </strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div class="info-content">
                            <small>Giờ hạ cánh</small>
                            <strong>
                                {{ \Carbon\Carbon::parse($booking->flight->arrival_time)->format('H:i - d/m/Y') }}
                            </strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-fingerprint"></i>
                        </div>

                        <div class="info-content">
                            <small>Số hiệu chuyến bay</small>
                            <strong>
                                <span class="flight-number">
                                    {{ $booking->flight->flight_number }}
                                </span>
                            </strong>
                        </div>
                    </div>

                </div>

                <!-- Passenger Details -->
                <div class="col-md-6">

                    <h6 class="section-title">
                        <i class="fas fa-user"></i>
                        THÔNG TIN HÀNH KHÁCH
                    </h6>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>

                        <div class="info-content">
                            <small>Họ và tên</small>
                            <strong>{{ $booking->passenger_name }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>

                        <div class="info-content">
                            <small>Email</small>
                            <strong>{{ $booking->passenger_email }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>

                        <div class="info-content">
                            <small>Số điện thoại</small>
                            <strong>{{ $booking->passenger_phone }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-chair"></i>
                        </div>

                        <div class="info-content">
                            <small>Số lượng ghế</small>
                            <strong>{{ $booking->number_of_seats }} ghế</strong>
                        </div>
                    </div>

                    @if($booking->payment_time)
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>

                            <div class="info-content">
                                <small>Thời gian thanh toán</small>
                                <strong>
                                    {{ \Carbon\Carbon::parse($booking->payment_time)->format('H:i - d/m/Y') }}
                                </strong>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Total Price -->
            <div class="price-box">
                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    <div>
                        <small>
                            <i class="fas fa-receipt me-1"></i>
                            TỔNG THANH TOÁN
                        </small>

                        <div class="total-price">
                            {{ number_format($booking->total_price) }} VNĐ
                        </div>
                    </div>

                    <div class="text-end mt-2">
                        <i class="fas fa-shield-alt fa-2x text-warning"></i>
                        <div class="small text-muted mt-1">
                            SKY TRAVEL
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Action Card -->
    <div class="action-card">

        <h6 class="action-title">
            <i class="fas fa-cogs text-primary me-2"></i>
            THAO TÁC VÉ MÁY BAY
        </h6>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <!-- Cancel Ticket -->
            @if($booking->status !== 'cancelled')

                <form
                    action="{{ route('ticket.cancel', $booking->booking_code) }}"
                    method="POST"
                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy vé máy bay này?')"
                >
                    @csrf

                    <button type="submit" class="btn btn-outline-danger btn-action">
                        <i class="fas fa-ban me-2"></i>
                        Hủy vé máy bay
                    </button>
                </form>

            @else

                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Vé này đã được hủy.
                </div>

            @endif

            <!-- Payment Simulation -->
            @if(in_array($booking->status, ['pending', 'failed']))

                <form
                    action="{{ route('ticket.payment', $booking->booking_code) }}"
                    method="POST"
                    class="payment-area"
                >
                    @csrf

                    <span class="payment-label">
                        Mô phỏng thanh toán:
                    </span>

                    <button
                        type="submit"
                        name="payment_status"
                        value="success"
                        class="btn btn-success btn-action"
                    >
                        <i class="fas fa-check me-1"></i>
                        Thành công
                    </button>

                    <button
                        type="submit"
                        name="payment_status"
                        value="failed"
                        class="btn btn-danger btn-action"
                    >
                        <i class="fas fa-times me-1"></i>
                        Thất bại
                    </button>

                </form>

            @endif

            @if($booking->status === 'paid')
                <span class="text-success fw-bold">
                    <i class="fas fa-check-circle me-2"></i>
                    Vé đã được thanh toán thành công!
                </span>
            @endif

        </div>

    </div>

</div>

<!-- Footer -->
<footer class="footer">
    <div>
        © {{ date('Y') }} SKY TRAVEL - Hệ thống quản lý và tra cứu vé máy bay
    </div>

    <div class="mt-2">
        <i class="fas fa-plane me-1"></i>
        Chúc bạn có một chuyến bay an toàn và vui vẻ!
    </div>
</footer>

</body>
</html>

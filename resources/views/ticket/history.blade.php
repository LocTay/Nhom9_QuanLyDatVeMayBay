<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt vé - SKY TRAVEL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #243b53;
            background: #f2f6fb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .site-header {
            background: linear-gradient(120deg, #071b3a, #0750a0);
            padding: 17px 0;
            box-shadow: 0 3px 14px rgba(0, 0, 0, .12);
        }
        .brand {
            color: #fff;
            text-decoration: none;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: .7px;
        }
        .brand i { color: #55c7ff; margin-right: 9px; }
        .page-wrap {
            width: min(100% - 30px, 1100px);
            margin: 32px auto 50px;
            flex: 1;
        }
        .page-title {
            color: #123e73;
            font-weight: 800;
            margin-bottom: 5px;
        }
        .page-description {
            color: #718096;
            margin-bottom: 24px;
        }
        .history-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(30, 60, 90, .08);
            padding: 24px;
        }
        .table-custom {
            margin-bottom: 0;
            vertical-align: middle;
        }
        .table-custom th {
            background: #f4f9ff;
            color: #0750a0;
            font-weight: 700;
            font-size: 13px;
            border-bottom: 2px solid #e1edf9;
            padding: 14px 16px;
            white-space: nowrap;
        }
        .table-custom td {
            padding: 16px;
            color: #243b53;
            font-size: 14px;
        }
        .booking-code-badge {
            font-weight: 800;
            color: #0750a0;
            background: #eaf5ff;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .status-paid { background: #d1fae5; color: #047857; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-failed { background: #fee2e2; color: #b91c1c; }
        .status-cancelled { background: #e5e7eb; color: #374151; }
        .btn-view {
            background: #0879c9;
            color: #fff;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-view:hover {
            background: #0750a0;
            color: #fff;
        }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        .empty-icon {
            font-size: 50px;
            color: #b5d9f5;
            margin-bottom: 15px;
        }
        .site-footer {
            background: #071b3a;
            color: #cbdced;
            text-align: center;
            padding: 19px 12px;
            font-size: 13px;
            margin-top: auto;
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
                <i class="fas fa-home me-2"></i> Trang chủ
            </a>
        </span>
    </div>
</header>

<main class="page-wrap">
    <h3 class="page-title"><i class="fas fa-history text-primary me-2"></i>Lịch sử đặt vé của bạn</h3>
    <p class="page-description">Quản lý và tra cứu lại tất cả chuyến bay bạn đã thực hiện đặt chỗ.</p>

    <div class="history-card">
        @if(isset($bookings) && count($bookings) > 0)
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>MÃ ĐẶT CHỖ</th>
                            <th>HÀNH TRÌNH</th>
                            <th>SỐ HIỆU</th>
                            <th>NGÀY BAY</th>
                            <th>TỔNG TIỀN</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-end">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <span class="booking-code-badge">{{ $booking->booking_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $booking->flight->departureAirport->code ?? '---' }}</strong>
                                    <i class="fas fa-long-arrow-alt-right mx-1 text-primary"></i>
                                    <strong>{{ $booking->flight->arrivalAirport->code ?? '---' }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $booking->flight->flight_number ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    {{ isset($booking->flight->departure_time) ? \Carbon\Carbon::parse($booking->flight->departure_time)->format('H:i - d/m/Y') : 'N/A' }}
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ number_format($booking->total_price ?? 0) }} VNĐ
                                </td>
                                <td>
                                    @if(($booking->status ?? '') === 'paid')
                                        <span class="status-badge status-paid">Đã thanh toán</span>
                                    @elseif(($booking->status ?? '') === 'pending')
                                        <span class="status-badge status-pending">Chờ thanh toán</span>
                                    @elseif(($booking->status ?? '') === 'failed')
                                        <span class="status-badge status-failed">Thất bại</span>
                                    @elseif(($booking->status ?? '') === 'cancelled')
                                        <span class="status-badge status-cancelled">Đã hủy</span>
                                    @else
                                        <span class="status-badge status-pending">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('ticket.detail', ['booking_code' => $booking->booking_code]) }}" class="btn-view">
                                        <i class="fas fa-eye me-1"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-ticket-alt"></i></div>
                <h5 class="fw-bold text-secondary">Bạn chưa có đơn đặt vé nào</h5>
                <p class="text-muted small">Hãy tìm kiếm chuyến bay và đặt vé ngay hôm nay.</p>
                <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                    <i class="fas fa-search me-1"></i> Tìm chuyến bay
                </a>
            </div>
        @endif
    </div>
</main>

<footer class="site-footer">
    <div>© {{ date('Y') }} SKY TRAVEL - Hệ thống quản lý và tra cứu vé máy bay</div>
</footer>
</body>
</html>
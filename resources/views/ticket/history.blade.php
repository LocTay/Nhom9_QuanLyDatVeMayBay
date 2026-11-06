<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt vé - SkyBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fb;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .card-history {
            border: none;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: #062b55;
            color: white;
            border: none;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-navy"><i class="fas fa-history me-2"></i> Lịch sử đặt vé của bạn</h3>
            <a href="{{ auth()->check() && auth()->user()->role === 'admin'
    ? route('admin.dashboard')
    : url('/') }}" class="btn btn-outline-primary"><i class="fas fa-home me-1"></i> Trang chủ</a>
        </div>

        <div class="card card-history p-4">
            @if($bookings->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">Bạn chưa có lịch sử đặt vé nào trên hệ thống.</p>
                <a href="{{ url('/#datve') }}" class="btn btn-primary">Đặt vé ngay</a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã đặt chỗ</th>
                            <th>Hành trình</th>
                            <th>Chuyến bay</th>
                            <th>Ngày khởi hành</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td><strong class="text-primary">{{ $booking->booking_code }}</strong></td>
                            <td>
                                {{ $booking->flight->departureAirport->code ?? 'N/A' }}
                                <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                {{ $booking->flight->arrivalAirport->code ?? 'N/A' }}
                            </td>
                            <td><span class="badge bg-secondary">{{ $booking->flight->flight_number ?? 'N/A' }}</span></td>
                            <td>
                                {{ isset($booking->flight->departure_time) ? \Carbon\Carbon::parse($booking->flight->departure_time)->format('H:i d/m/Y') : 'N/A' }}
                            </td>
                            <td><strong class="text-danger">{{ number_format($booking->total_price) }} VNĐ</strong></td>
                            <td>
                                @if($booking->status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                                @elseif($booking->status === 'pending')
                                <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                @elseif($booking->status === 'cancelled')
                                <span class="badge bg-secondary">Đã hủy</span>
                                @else
                                <span class="badge bg-danger">Thất bại</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ticket.detail', $booking->booking_code) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye me-1"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</body>

</html>
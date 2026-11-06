@extends('admin.airports.layout')

@section('title', 'Tổng quan hệ thống')
@section('topbar-title', 'Trang chủ Admin')

@section('content')

<style>
    .dashboard-heading {
        margin-bottom: 28px;
    }

    .dashboard-heading h1 {
        color: #06264b;
        font-size: 28px;
        margin-bottom: 8px;
    }

    .dashboard-heading p {
        color: #77869a;
        font-size: 14px;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .dashboard-stat {
        background: #fff;
        border: 1px solid #e4eaf2;
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 8px 25px rgba(6,38,75,.04);
    }

    .dashboard-stat .label {
        font-size: 13px;
        color: #77869a;
        margin-bottom: 12px;
    }

    .dashboard-stat .value {
        font-size: 27px;
        font-weight: bold;
        color: #06264b;
    }

    .dashboard-section {
        background: #fff;
        border: 1px solid #e4eaf2;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .dashboard-section h2 {
        font-size: 18px;
        color: #06264b;
        margin-bottom: 20px;
    }

    .dashboard-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
    }

    .dashboard-link {
        display: block;
        padding: 20px;
        background: #f4f7fb;
        border: 1px solid #e4eaf2;
        border-radius: 10px;
        color: #06264b;
        font-weight: bold;
        text-decoration: none;
        transition: .2s;
    }

    .dashboard-link:hover {
        border-color: #e8bd62;
        background: #fff9ec;
    }

    .dashboard-link small {
        display: block;
        margin-top: 8px;
        font-weight: normal;
        color: #77869a;
    }

    .dashboard-table-wrap {
        overflow-x: auto;
    }
</style>

<div class="dashboard-heading">
    <h1>Tổng quan hệ thống SkyBooking</h1>
    <p>
        Theo dõi hoạt động đặt vé, doanh thu
        và tình trạng quản lý hệ thống.
    </p>
</div>

<div class="dashboard-stats">

    <div class="dashboard-stat">
        <div class="label">Tổng sân bay</div>
        <div class="value">{{ number_format($totalAirports) }}</div>
    </div>

    <div class="dashboard-stat">
        <div class="label">Tổng chuyến bay</div>
        <div class="value">{{ number_format($totalFlights) }}</div>
    </div>

    <div class="dashboard-stat">
        <div class="label">Tổng lượt đặt vé</div>
        <div class="value">{{ number_format($totalBookings) }}</div>
    </div>

    <div class="dashboard-stat">
        <div class="label">Khách hàng</div>
        <div class="value">{{ number_format($totalCustomers) }}</div>
    </div>

    <div class="dashboard-stat">
        <div class="label">Đơn đã thanh toán</div>
        <div class="value">{{ number_format($paidBookings) }}</div>
    </div>

    <div class="dashboard-stat">
        <div class="label">Đơn chờ thanh toán</div>
        <div class="value">{{ number_format($pendingBookings) }}</div>
    </div>

    <div class="dashboard-stat">
        <div class="label">Yêu cầu chờ hoàn tiền</div>
        <div class="value">{{ number_format($pendingRefunds) }}</div>
    </div>

    <div class="dashboard-stat">
        <div class="label">Doanh thu từ đơn đã thanh toán</div>
        <div class="value" style="font-size:23px;">
            {{ number_format($totalRevenue, 0, ',', '.') }} đ
        </div>
    </div>

</div>

<div class="dashboard-section">
    <h2>Quản lý nhanh</h2>

    <div class="dashboard-links">

        <a href="{{ route('admin.airports.index') }}"
           class="dashboard-link">
            Quản lý sân bay
            <small>Danh sách và cập nhật sân bay</small>
        </a>

        <a href="{{ route('admin.flights.index') }}"
           class="dashboard-link">
            Quản lý chuyến bay
            <small>Lịch trình, giá vé và số ghế</small>
        </a>

        <a href="{{ route('admin.invoices.index') }}"
           class="dashboard-link">
            Quản lý hóa đơn
            <small>Đơn đặt vé và thanh toán</small>
        </a>

        <a href="{{ route('admin.refunds.index') }}"
           class="dashboard-link">
            Quản lý hoàn tiền
            <small>Tiếp nhận và xử lý hoàn tiền</small>
        </a>

    </div>
</div>

<div class="dashboard-section">
    <h2>Đặt vé gần đây</h2>

    <div class="dashboard-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã đặt vé</th>
                    <th>Khách hàng</th>
                    <th>Chuyến bay</th>
                    <th>Số tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($recentBookings as $booking)
                    <tr>
                        <td>
                            <a href="{{ route('admin.invoices.show', $booking) }}">
                                {{ $booking->pnr_code ?: $booking->booking_code }}
                            </a>
                        </td>

                        <td>{{ $booking->passenger_name }}</td>

                        <td>
                            {{ $booking->flight?->flight_number ?? 'N/A' }}
                        </td>

                        <td>
                            {{ number_format($booking->total_price, 0, ',', '.') }} đ
                        </td>

                        <td>{{ $booking->status }}</td>

                        <td>
                            {{ $booking->created_at?->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">
                            Chưa có đơn đặt vé nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

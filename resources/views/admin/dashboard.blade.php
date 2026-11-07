@extends('admin.airports.layout')

@section('title', 'Tổng quan hệ thống')
@section('topbar-title', 'Trang chủ Admin')

@section('content')

<style>
    .dashboard-heading {
        position: relative;
        overflow: hidden;
        padding: 35px;
        margin-bottom: 25px;
        border-radius: 18px;
        background:
            radial-gradient(circle at 90% 0%,
                rgba(83, 155, 205, .35), transparent 35%),
            linear-gradient(115deg,
                #05264a 0%, #084779 65%, #126b9f 100%);
        color: white;
        box-shadow: 0 12px 32px rgba(6, 43, 85, .12);
    }

    .dashboard-heading::after {
        content: "";
        position: absolute;
        width: 270px;
        height: 270px;
        right: -45px;
        top: -130px;
        border: 1px solid rgba(255, 255, 255, .15);
        border-radius: 50%;
        pointer-events: none;
    }

    .dashboard-heading h1 {
        color: white;
        font-size: clamp(23px, 3vw, 32px);
        font-weight: 800;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    .dashboard-heading p {
        color: #d5e5f3;
        font-size: 12px;
        line-height: 1.9;
        max-width: 650px;
    }

    .dashboard-kicker {
        display: inline-block;
        padding: 8px 13px;
        margin-bottom: 17px;
        border: 1px solid rgba(215, 174, 88, .4);
        background: rgba(255, 255, 255, .06);
        border-radius: 20px;
        color: #f2dba5;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .dashboard-stat {
        position: relative;
        min-width: 0;
        padding: 24px;
        background: white;
        border: 1px solid #e4eaf2;
        border-radius: 14px;
        box-shadow: 0 8px 25px rgba(6, 38, 75, .04);
        transition: transform .2s, box-shadow .2s;
    }

    .dashboard-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(6, 38, 75, .09);
    }

    .dashboard-stat::before {
        content: "";
        position: absolute;
        top: 0;
        left: 24px;
        right: 24px;
        height: 3px;
        background: #d7ae58;
        border-radius: 0 0 4px 4px;
    }

    .dashboard-stat .label {
        color: #77869a;
        font-size: 11px;
        line-height: 1.7;
        margin-bottom: 15px;
    }

    .dashboard-stat .value {
        color: #062b55;
        font-size: clamp(20px, 2vw, 27px);
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .dashboard-section {
        background: white;
        border: 1px solid #e4eaf2;
        border-radius: 15px;
        padding: 26px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(6, 38, 75, .04);
    }

    .dashboard-section h2 {
        color: #062b55;
        font-size: 19px;
        font-weight: 700;
        margin-bottom: 22px;
    }

    .dashboard-links {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
    }

    .dashboard-link {
        display: block;
        padding: 21px;
        border: 1px solid #e4eaf2;
        border-radius: 12px;
        background: #f7f9fc;
        color: #062b55;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.6;
        transition: .2s;
    }

    .dashboard-link:hover {
        border-color: #d7ae58;
        background: #fff9ec;
        transform: translateY(-2px);
    }

    .dashboard-link small {
        display: block;
        margin-top: 10px;
        color: #77869a;
        font-size: 10px;
        font-weight: 400;
        line-height: 1.8;
    }

    .dashboard-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .dashboard-table-wrap table {
        min-width: 720px;
        width: 100%;
        border-collapse: collapse;
    }

    .dashboard-table-wrap th {
        background: #f4f7fb;
        color: #062b55;
        font-size: 10px;
        padding: 15px 12px;
    }

    .dashboard-table-wrap td {
        font-size: 11px;
        padding: 16px 12px;
        border-bottom: 1px solid #e4eaf2;
    }

    .dashboard-table-wrap td a {
        color: #1267a5;
        font-weight: 700;
    }

    .dashboard-table-wrap td a:hover {
        text-decoration: underline;
    }

    @media (max-width: 1250px) {

        .dashboard-stats,
        .dashboard-links {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .dashboard-heading {
            padding: 25px 20px;
        }

        .dashboard-stats,
        .dashboard-links {
            grid-template-columns: 1fr;
        }

        .dashboard-stat,
        .dashboard-section {
            padding: 20px;
        }
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 7px 11px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-paid {
        background: #e7f7ee;
        color: #16834a;
    }

    .status-pending {
        background: #fff4dc;
        color: #a76c08;
    }

    .status-cancelled,
    .status-failed {
        background: #fdecee;
        color: #bf3346;
    }

    .status-refund {
        background: #f1e8ff;
        color: #8438c0;
    }

    .status-default {
        background: #edf1f6;
        color: #566a80;
    }
</style>


<div class="dashboard-heading">
    <div class="dashboard-kicker">
        ✦ SKYBOOKING ADMIN
    </div>

    <h1>
        Trung tâm quản trị
        <span style="color:#d7ae58;">SkyBooking</span>
    </h1>

    <p>
        Tổng quan hoạt động hệ thống đặt vé máy bay.
        Theo dõi chuyến bay, khách hàng, doanh thu
        và xử lý các yêu cầu hoàn tiền một cách tập trung.
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

                    <td>
                        @php
                        $statuses = [
                        'paid' => ['Đã thanh toán', 'paid'],
                        'pending' => ['Chờ thanh toán', 'pending'],
                        'cancelled' => ['Đã hủy', 'cancelled'],
                        'failed' => ['Thất bại', 'failed'],
                        'refund_pending' => ['Chờ hoàn tiền', 'refund'],
                        ];

                        $status = $statuses[$booking->status]
                        ?? [$booking->status, 'default'];
                        @endphp

                        <span class="status-badge status-{{ $status[1] }}">
                            {{ $status[0] }}
                        </span>
                    </td>

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
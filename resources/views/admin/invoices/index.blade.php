@extends('admin.airports.layout')

@section('title', 'Quản lý hóa đơn')
@section('topbar-title', 'Quản lý hóa đơn')

@section('content')
@php
$statusLabels = [
'pending' => 'Chờ thanh toán',
'pending_approval' => 'Chờ Admin duyệt',
'paid' => 'Đã thanh toán',
'failed' => 'Thất bại',
'cancelled' => 'Đã hủy',
'refund_pending' => 'Chờ hoàn tiền',
];
@endphp

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / HÓA ĐƠN</small>
    <h1>Quản lý hóa đơn</h1>
    <p>
        Theo dõi đơn đặt vé, trạng thái thanh toán
        và doanh thu của hệ thống SkyBooking.
    </p>
</div>

@if($pendingApprovalBookings > 0)
<div class="alert alert-warning" style="margin-bottom:20px;">
    <strong>
        Có {{ $pendingApprovalBookings }} đơn đang chờ duyệt thanh toán!
    </strong>
    <p style="margin-top:8px;">
        Vui lòng kiểm tra và xử lý trước khi hết thời hạn 2 phút.
    </p>
</div>
@endif

<div class="finance-stats">
    <div class="finance-stat">
        <div class="finance-stat-label">Tổng đơn đặt vé</div>
        <div class="finance-stat-value">
            {{ number_format($totalBookings) }}
        </div>
        <div class="finance-stat-note">Toàn bộ đơn đặt vé</div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Đã thanh toán</div>
        <div class="finance-stat-value">
            {{ number_format($paidBookings) }}
        </div>
        <div class="finance-stat-note">Đơn có trạng thái paid</div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Chờ thanh toán</div>
        <div class="finance-stat-value">
            {{ number_format($pendingBookings) }}
        </div>
        <div class="finance-stat-note">Đơn đang chờ xử lý</div>
    </div>
    <div class="finance-stat">
        <div class="finance-stat-label">
            Chờ duyệt thanh toán
        </div>

        <div class="finance-stat-value">
            {{ number_format($pendingApprovalBookings) }}
        </div>

        <div class="finance-stat-note">
            Đơn đang chờ Admin xác nhận
        </div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">
            Doanh thu từ đơn đã thanh toán
        </div>
        <div class="finance-stat-value" style="font-size:21px;">
            {{ number_format($totalRevenue, 0, ',', '.') }} đ
        </div>
        <div class="finance-stat-note">
            Chưa trừ hoàn tiền
        </div>
    </div>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Tìm kiếm hóa đơn</h2>
            <p>Lọc danh sách theo thông tin khách hàng hoặc trạng thái.</p>
        </div>
    </div>

    <form method="GET"
        action="{{ route('admin.invoices.index') }}"
        class="finance-filters">

        <div class="finance-field">
            <label for="invoice-search">Mã đặt vé, tên hoặc email</label>
            <input
                id="invoice-search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nhập mã đặt vé hoặc thông tin khách hàng">
        </div>

        <div class="finance-field">
            <label for="invoice-status">Trạng thái</label>
            <select id="invoice-status" name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}"
                    @selected(request('status')===$value)>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="finance-filter-actions">
            <button type="submit" class="btn btn-primary">
                Tìm kiếm
            </button>
            <a href="{{ route('admin.invoices.index') }}"
                class="btn btn-secondary">
                Đặt lại
            </a>
        </div>
    </form>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Danh sách hóa đơn</h2>
            <p>
                Có {{ number_format($bookings->total()) }}
                kết quả phù hợp.
            </p>
        </div>
    </div>

    <div class="finance-table-wrap">
        <table class="finance-table">
            <thead>
                <tr>
                    <th>Mã đặt vé</th>
                    <th>Khách hàng</th>
                    <th>Chuyến bay</th>
                    <th>Số khách</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($bookings as $booking)
                <tr>
                    <td>
                        <span class="finance-code">
                            {{ $booking->pnr_code ?: $booking->booking_code }}
                        </span>
                    </td>

                    <td>
                        <span class="finance-person">
                            {{ $booking->passenger_name }}
                        </span>
                        <span class="finance-subtext">
                            {{ $booking->passenger_email }}
                        </span>
                    </td>

                    <td>
                        {{ $booking->flight?->flight_number ?? 'Không xác định' }}
                    </td>

                    <td>
                        {{ $booking->passenger_count ?? $booking->number_of_seats }}
                    </td>

                    <td>
                        <span class="finance-amount">
                            {{ number_format($booking->total_price, 0, ',', '.') }} đ
                        </span>
                    </td>

                    <td>
                        <span class="finance-badge finance-badge-{{ $booking->status }}">
                            {{ $statusLabels[$booking->status] ?? $booking->status }}
                        </span>
                    </td>

                    <td>
                        <a href="{{ route('admin.invoices.show', $booking->id) }}"
                            class="btn btn-primary">
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="finance-empty">
                        Không tìm thấy hóa đơn phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($bookings->hasPages())
    <div class="finance-pagination">
        {{ $bookings->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
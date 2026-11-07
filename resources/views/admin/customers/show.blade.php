@extends('admin.airports.layout')

@section('title', 'Chi tiết khách hàng')
@section('topbar-title', 'Chi tiết khách hàng')

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
    <small>QUẢN TRỊ HỆ THỐNG / KHÁCH HÀNG / CHI TIẾT</small>
    <h1>{{ $user->name }}</h1>
    <p>Thông tin tài khoản khách hàng #{{ $user->id }}.</p>
</div>

<div class="finance-detail-grid">
    <div class="finance-detail-card">
        <h2>Thông tin cá nhân</h2>

        <div class="finance-detail-row">
            <span>Họ và tên</span>
            <strong>{{ $user->name }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Email</span>
            <strong>{{ $user->email }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Số điện thoại</span>
            <strong>{{ $user->phone ?: 'Chưa cập nhật' }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Ngày sinh</span>
            <strong>
                {{ $user->date_of_birth
                    ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y')
                    : 'Chưa cập nhật' }}
            </strong>
        </div>

        <div class="finance-detail-row">
            <span>Giới tính</span>
            <strong>{{ $user->gender ?: 'Chưa cập nhật' }}</strong>
        </div>
    </div>

    <div class="finance-detail-card">
        <h2>Thông tin tài khoản</h2>

        <div class="finance-detail-row">
            <span>Mã tài khoản</span>
            <strong>#{{ $user->id }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Vai trò</span>
            <strong>Khách hàng</strong>
        </div>

        <div class="finance-detail-row">
            <span>Ngày đăng ký</span>
            <strong>
                {{ $user->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}
            </strong>
        </div>

        <div class="finance-detail-row">
            <span>Xác minh email</span>
            <strong>
                {{ $user->email_verified_at ? 'Đã xác minh' : 'Chưa xác minh' }}
            </strong>
        </div>

        <div class="finance-detail-row">
            <span>Tổng đơn đặt vé</span>
            <strong>{{ number_format($totalBookings) }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Giá trị đơn đã thanh toán</span>
            <strong>
                {{ number_format($totalPaid, 0, ',', '.') }} đ
            </strong>
        </div>
    </div>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Lịch sử đặt vé</h2>
            <p>
                Các đơn đặt vé liên kết với tài khoản khách hàng này.
            </p>
        </div>
    </div>

    <div class="finance-table-wrap">
        <table class="finance-table">
            <thead>
                <tr>
                    <th>Mã đặt vé</th>
                    <th>Chuyến bay</th>
                    <th>Ngày đặt</th>
                    <th>Số khách</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <span class="finance-code">
                                {{ $booking->pnr_code ?: $booking->booking_code }}
                            </span>
                        </td>

                        <td>
                            <span class="finance-person">
                                {{ $booking->flight?->flight_number ?? 'Không xác định' }}
                            </span>
                            <span class="finance-subtext">
                                {{ $booking->flight?->departureAirport?->city ?? '?' }}
                                →
                                {{ $booking->flight?->arrivalAirport?->city ?? '?' }}
                            </span>
                        </td>

                        <td>
                            {{ $booking->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}
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
                            <a href="{{ route('admin.invoices.show', $booking) }}"
                               class="btn btn-primary">
                                Xem hóa đơn
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="finance-empty">
                            Chưa có đơn đặt vé liên kết với tài khoản này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div class="finance-pagination">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

<div class="finance-actions">
    <a href="{{ route('admin.customers.index') }}"
       class="btn btn-secondary">
        ← Quay lại danh sách
    </a>
</div>
@endsection

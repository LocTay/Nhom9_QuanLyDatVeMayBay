@extends('admin.airports.layout')

@section('title', 'Quản lý hoàn tiền')
@section('topbar-title', 'Quản lý hoàn tiền')

@section('content')
@php
    $statusLabels = [
        'pending_refund' => 'Chờ hoàn tiền',
        'completed' => 'Đã hoàn tiền',
        'no_refund' => 'Không hoàn tiền',
    ];
@endphp

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / HOÀN TIỀN</small>
    <h1>Quản lý hoàn tiền</h1>
    <p>
        Theo dõi, tìm kiếm và xác nhận xử lý
        các yêu cầu hoàn tiền trong hệ thống SkyBooking.
    </p>
</div>

<div class="finance-stats" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
    <div class="finance-stat">
        <div class="finance-stat-label">Yêu cầu đang chờ</div>
        <div class="finance-stat-value">
            {{ number_format($pendingCount) }}
        </div>
        <div class="finance-stat-note">
            Các yêu cầu chưa hoàn tất
        </div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Yêu cầu đã hoàn tất</div>
        <div class="finance-stat-value">
            {{ number_format($completedCount) }}
        </div>
        <div class="finance-stat-note">
            Đã xác nhận hoàn tiền trên hệ thống
        </div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Tổng giá trị đơn chờ hoàn</div>
        <div class="finance-stat-value" style="font-size:21px;">
            {{ number_format($pendingAmount, 0, ',', '.') }} đ
        </div>
        <div class="finance-stat-note">
            Tổng giá trị đặt vé của yêu cầu đang chờ
        </div>
    </div>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Tìm kiếm yêu cầu hoàn tiền</h2>
            <p>Lọc theo mã đặt vé, khách hàng hoặc trạng thái hoàn tiền.</p>
        </div>
    </div>

    <form method="GET"
          action="{{ route('admin.refunds.index') }}"
          class="finance-filters">

        <div class="finance-field">
            <label for="refund-search">Mã đặt vé, tên hoặc email</label>
            <input
                id="refund-search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nhập mã đặt vé hoặc thông tin khách hàng"
            >
        </div>

        <div class="finance-field">
            <label for="refund-status">Trạng thái hoàn tiền</label>
            <select id="refund-status" name="refund_status">
                <option value="">Tất cả trạng thái</option>

                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}"
                        @selected(request('refund_status') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="finance-filter-actions">
            <button type="submit" class="btn btn-primary">
                Tìm kiếm
            </button>

            <a href="{{ route('admin.refunds.index') }}"
               class="btn btn-secondary">
                Đặt lại
            </a>
        </div>
    </form>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Danh sách yêu cầu hoàn tiền</h2>
            <p>
                Có {{ number_format($refunds->total()) }}
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
                    <th>Giá trị đơn</th>
                    <th>Trạng thái hoàn tiền</th>
                    <th>Ngày cập nhật</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($refunds as $booking)
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
                            <span class="finance-amount">
                                {{ number_format($booking->total_price, 0, ',', '.') }} đ
                            </span>
                        </td>

                        <td>
                            <span class="finance-badge finance-badge-{{ $booking->refund_status }}">
                                {{ $statusLabels[$booking->refund_status] ?? $booking->refund_status }}
                            </span>
                        </td>

                        <td>
                            {{ $booking->updated_at?->format('d/m/Y H:i') ?? 'Chưa có' }}
                        </td>

                        <td>
                            <a href="{{ route('admin.refunds.show', $booking) }}"
                               class="btn btn-primary">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="finance-empty">
                            Chưa có yêu cầu hoàn tiền phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($refunds->hasPages())
        <div class="finance-pagination">
            {{ $refunds->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

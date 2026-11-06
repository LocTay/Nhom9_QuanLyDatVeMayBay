@extends('admin.airports.layout')

@section('title', 'Chi tiết hoàn tiền')
@section('topbar-title', 'Chi tiết hoàn tiền')

@section('content')
@php
    $code = $booking->pnr_code ?: $booking->booking_code;

    $refundLabels = [
        'pending_refund' => 'Chờ hoàn tiền',
        'completed' => 'Đã hoàn tiền',
        'no_refund' => 'Không hoàn tiền',
    ];

    $bookingLabels = [
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thất bại',
        'cancelled' => 'Đã hủy',
        'refund_pending' => 'Chờ hoàn tiền',
    ];
@endphp

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / HOÀN TIỀN / CHI TIẾT</small>
    <h1>Chi tiết yêu cầu hoàn tiền</h1>
    <p>Mã đặt vé: {{ $code }}</p>
</div>

<div class="finance-detail-grid">
    <div class="finance-detail-card">
        <h2>Thông tin khách hàng</h2>

        <div class="finance-detail-row">
            <span>Họ tên</span>
            <strong>{{ $booking->passenger_name }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Email</span>
            <strong>{{ $booking->passenger_email }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Số điện thoại</span>
            <strong>{{ $booking->passenger_phone ?: 'Chưa có' }}</strong>
        </div>

        <div class="finance-detail-row">
            <span>Số hành khách</span>
            <strong>
                {{ $booking->passenger_count ?? $booking->number_of_seats ?? 1 }}
            </strong>
        </div>
    </div>

    <div class="finance-detail-card">
        <h2>Thông tin chuyến bay</h2>

        <div class="finance-detail-row">
            <span>Mã chuyến bay</span>
            <strong>
                {{ $booking->flight?->flight_number ?? 'Không xác định' }}
            </strong>
        </div>

        <div class="finance-detail-row">
            <span>Điểm đi</span>
            <strong>
                {{ $booking->flight?->departureAirport?->city ?? 'Không xác định' }}
            </strong>
        </div>

        <div class="finance-detail-row">
            <span>Điểm đến</span>
            <strong>
                {{ $booking->flight?->arrivalAirport?->city ?? 'Không xác định' }}
            </strong>
        </div>

        <div class="finance-detail-row">
            <span>Khởi hành</span>
            <strong>
                {{ $booking->flight?->departure_time
                    ? \Carbon\Carbon::parse($booking->flight->departure_time)->format('d/m/Y H:i')
                    : 'Không xác định' }}
            </strong>
        </div>
    </div>
</div>

<div class="finance-detail-card">
    <h2>Thông tin yêu cầu hoàn tiền</h2>

    <div class="finance-detail-row">
        <span>Mã đặt vé</span>
        <strong>{{ $code }}</strong>
    </div>

    <div class="finance-detail-row">
        <span>Trạng thái vé</span>
        <strong>
            {{ $bookingLabels[$booking->status] ?? $booking->status }}
        </strong>
    </div>

    <div class="finance-detail-row">
        <span>Trạng thái hoàn tiền</span>
        <strong>
            <span class="finance-badge finance-badge-{{ $booking->refund_status }}">
                {{ $refundLabels[$booking->refund_status] ?? $booking->refund_status }}
            </span>
        </strong>
    </div>

    <div class="finance-detail-row">
        <span>Lý do hủy</span>
        <strong>{{ $booking->cancellation_reason ?: 'Không có' }}</strong>
    </div>

    <div class="finance-detail-row">
        <span>Thời gian cập nhật</span>
        <strong>
            {{ $booking->updated_at?->format('d/m/Y H:i') ?? 'Chưa có' }}
        </strong>
    </div>

    <div class="finance-detail-row">
        <span>Giá trị đơn đặt vé</span>
        <strong class="finance-total">
            {{ number_format($booking->total_price, 0, ',', '.') }} đ
        </strong>
    </div>
</div>

@if ($booking->status === 'cancelled'
    && $booking->refund_status === 'pending_refund')
    <div class="finance-notice">
        <strong>Lưu ý về xác nhận hoàn tiền</strong>

        <p style="margin-top:8px;">
            Đây là chức năng xác nhận hoàn tiền mô phỏng.
            Hệ thống chưa thực hiện chuyển tiền qua ngân hàng
            hoặc ví điện tử. Chỉ xác nhận khi yêu cầu đã được
            xử lý theo quy trình của nhóm.
        </p>
    </div>
@endif

<div class="finance-actions">
    <a href="{{ route('admin.refunds.index') }}"
       class="btn btn-secondary">
        ← Quay lại danh sách
    </a>

    @if ($booking->status === 'cancelled'
        && $booking->refund_status === 'pending_refund')
        <form
            method="POST"
            action="{{ route('admin.refunds.complete', $booking) }}"
            onsubmit="return confirm('Xác nhận đánh dấu yêu cầu này là đã hoàn tiền?');"
        >
            @csrf

            <button type="submit" class="btn btn-gold">
                Xác nhận đã hoàn tiền
            </button>
        </form>
    @endif
</div>
@endsection

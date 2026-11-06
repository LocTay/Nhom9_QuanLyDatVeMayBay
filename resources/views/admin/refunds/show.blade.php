@extends('admin.airports.layout')

@section('title', 'Chi tiết hoàn tiền')

@section('content')
@php
    $code = $booking->pnr_code ?: $booking->booking_code;

    $refundLabels = [
        'pending_refund' => 'Chờ hoàn tiền',
        'completed' => 'Đã hoàn tiền',
        'no_refund' => 'Không hoàn tiền',
    ];
@endphp

<style>
    .refund-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .refund-detail-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .refund-detail-card h2 {
        font-size: 18px;
        margin: 0 0 18px;
        color: #0f172a;
    }

    .refund-detail-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .refund-detail-row span {
        color: #64748b;
    }

    .refund-detail-row strong {
        text-align: right;
        overflow-wrap: anywhere;
    }

    .refund-detail-amount {
        font-size: 26px;
        font-weight: 700;
        color: #0f766e;
    }

    .refund-detail-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .refund-detail-notice {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 14px;
        color: #92400e;
        margin-bottom: 20px;
    }

    .refund-detail-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 14px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .refund-detail-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="page-header">
    <div>
        <h1>Chi tiết yêu cầu hoàn tiền</h1>
        <p>Mã đặt vé: {{ $code }}</p>
    </div>
</div>

@if (session('success'))
    <div class="refund-detail-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="refund-detail-error">
        {{ session('error') }}
    </div>
@endif

<div class="refund-detail-grid">
    <div class="refund-detail-card">
        <h2>Thông tin khách hàng</h2>

        <div class="refund-detail-row">
            <span>Họ tên</span>
            <strong>{{ $booking->passenger_name }}</strong>
        </div>

        <div class="refund-detail-row">
            <span>Email</span>
            <strong>{{ $booking->passenger_email }}</strong>
        </div>

        <div class="refund-detail-row">
            <span>Số điện thoại</span>
            <strong>{{ $booking->passenger_phone ?: 'Chưa có' }}</strong>
        </div>

        <div class="refund-detail-row">
            <span>Số hành khách</span>
            <strong>{{ $booking->passenger_count ?? $booking->number_of_seats ?? 1 }}</strong>
        </div>
    </div>

    <div class="refund-detail-card">
        <h2>Thông tin chuyến bay</h2>

        <div class="refund-detail-row">
            <span>Mã chuyến bay</span>
            <strong>{{ $booking->flight?->flight_number ?? 'Không xác định' }}</strong>
        </div>

        <div class="refund-detail-row">
            <span>Điểm đi</span>
            <strong>{{ $booking->flight?->departureAirport?->city ?? 'Không xác định' }}</strong>
        </div>

        <div class="refund-detail-row">
            <span>Điểm đến</span>
            <strong>{{ $booking->flight?->arrivalAirport?->city ?? 'Không xác định' }}</strong>
        </div>

        <div class="refund-detail-row">
            <span>Ngày khởi hành</span>
            <strong>
                {{ $booking->flight?->departure_time
                    ? \Carbon\Carbon::parse($booking->flight->departure_time)->format('d/m/Y H:i')
                    : 'Không xác định' }}
            </strong>
        </div>
    </div>
</div>

<div class="refund-detail-card">
    <h2>Thông tin yêu cầu hoàn tiền</h2>

    <div class="refund-detail-row">
        <span>Mã đặt vé</span>
        <strong>{{ $code }}</strong>
    </div>

    <div class="refund-detail-row">
        <span>Trạng thái vé</span>
        <strong>{{ $booking->status }}</strong>
    </div>

    <div class="refund-detail-row">
        <span>Trạng thái hoàn tiền</span>
        <strong>
            {{ $refundLabels[$booking->refund_status] ?? $booking->refund_status }}
        </strong>
    </div>

    <div class="refund-detail-row">
        <span>Lý do hủy</span>
        <strong>{{ $booking->cancellation_reason ?: 'Không có' }}</strong>
    </div>

    <div class="refund-detail-row">
        <span>Thời gian cập nhật</span>
        <strong>{{ $booking->updated_at?->format('d/m/Y H:i') }}</strong>
    </div>

    <div class="refund-detail-row">
        <span>Giá trị đơn đặt vé</span>
        <strong class="refund-detail-amount">
            {{ number_format($booking->total_price, 0, ',', '.') }} đ
        </strong>
    </div>
</div>

@if ($booking->status === 'cancelled' && $booking->refund_status === 'pending_refund')
    <div class="refund-detail-notice">
        Đây là chức năng xác nhận hoàn tiền mô phỏng.
        Hệ thống chưa thực hiện chuyển tiền qua ngân hàng hoặc ví điện tử.
        Chỉ xác nhận khi yêu cầu đã được xử lý theo quy trình của nhóm.
    </div>
@endif

<div class="refund-detail-actions">
    <a href="{{ route('admin.refunds.index') }}"
       class="btn btn-secondary">
        Quay lại danh sách
    </a>

    @if ($booking->status === 'cancelled' && $booking->refund_status === 'pending_refund')
        <form method="POST"
              action="{{ route('admin.refunds.complete', $booking) }}"
              onsubmit="return confirm('Xác nhận đánh dấu yêu cầu này là đã hoàn tiền?');">
            @csrf

            <button type="submit" class="btn btn-primary">
                Xác nhận đã hoàn tiền
            </button>
        </form>
    @endif
</div>
@endsection

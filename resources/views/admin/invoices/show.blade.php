@extends('admin.airports.layout')

@section('title', 'Chi tiết hóa đơn')
@section('topbar-title', 'Chi tiết hóa đơn')

@section('content')
@php
$code = $booking->pnr_code ?: $booking->booking_code;

$statusLabels = [
'pending' => 'Chờ thanh toán',
'pending_approval' => 'Chờ Admin duyệt',
'paid' => 'Đã thanh toán',
'failed' => 'Thanh toán thất bại',
'cancelled' => 'Đã hủy',
'refund_pending' => 'Chờ hoàn tiền',
];

$classLabels = [
'pho_thong' => 'Phổ thông',
'pho_thong_dac_biet' => 'Phổ thông đặc biệt',
'thuong_gia' => 'Thương gia',
];

$refundLabels = [
'pending_refund' => 'Đang chờ hoàn tiền',
'no_refund' => 'Không hoàn tiền',
'completed' => 'Đã hoàn tiền',
];

$passengers = $booking->passengers_data ?? [];
@endphp

<style>
    .approval-warning {
        border: 1px solid #f59e0b;
        background: #fffbeb;
    }

    .approval-warning h2 {
        color: #92400e;
    }

    .approval-warning p {
        color: #78350f;
        line-height: 1.7;
        font-size: 12px;
    }

    .approval-deadline {
        color: #b91c1c;
        font-weight: 800;
    }

    .approval-danger-button {
        padding: 11px 17px;
        border: 1px solid #dc2626;
        border-radius: 8px;
        background: #dc2626;
        color: white;
        font-weight: 700;
        cursor: pointer;
    }

    .approval-danger-button:hover {
        background: #b91c1c;
    }

    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }

        body,
        .main,
        .content {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .sidebar,
        .topbar,
        .finance-actions,
        .approval-warning {
            display: none !important;
        }

        .finance-detail-card {
            break-inside: avoid;
            box-shadow: none !important;
            border: 1px solid #ddd;
        }

        .finance-detail-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / HÓA ĐƠN / CHI TIẾT</small>
    <h1>Chi tiết hóa đơn {{ $code }}</h1>
    <p>Thông tin đặt vé, hành khách và thanh toán.</p>
</div>

<div class="finance-detail-card">
    <h2>Thông tin hóa đơn</h2>

    <div class="finance-detail-grid">
        <div>
            <div class="finance-detail-row">
                <span>Mã đặt vé</span>
                <strong>{{ $code }}</strong>
            </div>
            <div class="finance-detail-row">
                <span>Mã đơn trong hệ thống</span>
                <strong>#{{ $booking->id }}</strong>
            </div>
            <div class="finance-detail-row">
                <span>Ngày tạo đơn</span>
                <strong>
                    {{ $booking->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}
                </strong>
            </div>
        </div>

        <div>
            <div class="finance-detail-row">
                <span>Trạng thái</span>
                <strong>
                    <span class="finance-badge finance-badge-{{ $booking->status }}">
                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                    </span>
                </strong>
            </div>
            <div class="finance-detail-row">
                <span>Thời gian thanh toán</span>
                <strong>
                    {{ $booking->payment_time
                        ? \Carbon\Carbon::parse($booking->payment_time)->format('d/m/Y H:i')
                        : 'Chưa thanh toán' }}
                </strong>
            </div>
            <div class="finance-detail-row">
                <span>Phương thức thanh toán</span>
                <strong>{{ $booking->payment_method ?: 'Chưa có' }}</strong>
            </div>
        </div>
    </div>
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
                {{ $booking->passenger_count ?? $booking->number_of_seats }}
            </strong>
        </div>
    </div>

    <div class="finance-detail-card">
        <h2>Thông tin chuyến bay</h2>

        <div class="finance-detail-row">
            <span>Số hiệu chuyến bay</span>
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
                    : 'Chưa có' }}
            </strong>
        </div>
        <div class="finance-detail-row">
            <span>Loại vé</span>
            <strong>
                {{ $booking->trip_type === 'round_trip' ? 'Khứ hồi' : 'Một chiều' }}
            </strong>
        </div>
        <div class="finance-detail-row">
            <span>Hạng vé</span>
            <strong>
                {{ $classLabels[$booking->flight_class] ?? $booking->flight_class ?? 'Chưa có' }}
            </strong>
        </div>
    </div>
</div>

<div class="finance-detail-card">
    <h2>Danh sách hành khách</h2>

    <div class="finance-table-wrap">
        <table class="finance-table finance-detail-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ tên</th>
                    <th>Giới tính</th>
                    <th>Ghế</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($passengers as $index => $passenger)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $passenger['name'] ?? 'Chưa có' }}</td>
                    <td>{{ $passenger['gender'] ?? 'Chưa có' }}</td>
                    <td>{{ $passenger['seat'] ?? 'Chưa chọn' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="finance-empty">
                        Đơn đặt vé cũ chưa lưu danh sách hành khách chi tiết.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="finance-detail-card">
    <h2>Thông tin thanh toán</h2>

    <div class="finance-detail-row">
        <span>Tổng tiền đặt vé</span>
        <strong class="finance-total">
            {{ number_format($booking->total_price, 0, ',', '.') }} đ
        </strong>
    </div>

    <div class="finance-detail-row">
        <span>Trạng thái thanh toán</span>
        <strong>
            {{ $statusLabels[$booking->status] ?? $booking->status }}
        </strong>
    </div>

    @if ($booking->refund_status)
    <div class="finance-detail-row">
        <span>Trạng thái hoàn tiền</span>
        <strong>
            {{ $refundLabels[$booking->refund_status] ?? $booking->refund_status }}
        </strong>
    </div>
    @endif

    @if ($booking->cancellation_reason)
    <div class="finance-detail-row">
        <span>Lý do hủy</span>
        <strong>{{ $booking->cancellation_reason }}</strong>
    </div>
    @endif
</div>

{{-- Xử lý yêu cầu duyệt thanh toán --}}
@if($booking->status === 'pending_approval')
<div class="finance-detail-card approval-warning">
    <h2>Yêu cầu duyệt thanh toán</h2>

    <p>
        Khách hàng đã xác nhận chuyển khoản.
        Admin cần kiểm tra giao dịch và xử lý đơn
        trong vòng 2 phút kể từ thời điểm khách xác nhận.
    </p>

    @if($booking->approval_expires_at)
    <p style="margin-top:12px;">
        Hạn xử lý:
        <strong class="approval-deadline">
            {{ $booking->approval_expires_at->format('d/m/Y H:i:s') }}
        </strong>
    </p>
    @endif

    <div class="finance-actions">
        <form
            action="{{ route('admin.ticket.approve', $booking->booking_code) }}"
            method="POST"
            onsubmit="return confirm('Bạn xác nhận đã kiểm tra giao dịch và duyệt thanh toán cho đơn này?')">
            @csrf

            <button type="submit" class="btn btn-primary">
                Duyệt thanh toán
            </button>
        </form>

        <form
            action="{{ route('admin.ticket.cancel-pending', $booking->booking_code) }}"
            method="POST"
            onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn chờ duyệt này? Số ghế sẽ được hoàn lại.')">
            @csrf

            <button type="submit" class="approval-danger-button">
                Hủy đơn
            </button>
        </form>
    </div>
</div>
@endif

<div class="finance-actions">
    <a href="{{ route('admin.invoices.index') }}"
        class="btn btn-secondary">
        ← Quay lại danh sách
    </a>

    <button type="button"
        class="btn btn-gold"
        onclick="window.print()">
        In hóa đơn
    </button>
</div>
@endsection
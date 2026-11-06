@extends('admin.airports.layout')

@section('title', 'Chi tiết hóa đơn')

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
    .invoice-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .invoice-detail-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .invoice-detail-card h2 {
        font-size: 18px;
        margin: 0 0 18px;
    }

    .invoice-detail-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 11px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .invoice-detail-row span:first-child {
        color: #64748b;
    }

    .invoice-detail-row strong {
        text-align: right;
        overflow-wrap: anywhere;
    }

    .invoice-detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-detail-table th,
    .invoice-detail-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .invoice-detail-total {
        font-size: 26px;
        font-weight: 700;
        color: #0f766e;
    }

    .invoice-detail-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 24px;
    }

    .approval-warning {
        border: 1px solid #f59e0b;
        background: #fffbeb;
    }

    .approval-warning h2 {
        color: #92400e;
    }

    .approval-warning p {
        color: #78350f;
        line-height: 1.6;
    }

    .approval-deadline {
        color: #b91c1c;
        font-weight: 700;
    }

    .btn-danger {
        display: inline-block;
        padding: 10px 18px;
        border: 1px solid #dc2626;
        border-radius: 6px;
        background: #dc2626;
        color: #ffffff;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-danger:hover {
        background: #b91c1c;
        border-color: #b91c1c;
    }

    @media print {
        .sidebar,
        .topbar,
        .invoice-detail-actions {
            display: none !important;
        }

        .invoice-detail-card {
            break-inside: avoid;
            box-shadow: none;
        }
    }
</style>

<div class="page-header">
    <div>
        <h1>Chi tiết hóa đơn</h1>
        <p>Thông tin đặt vé và thanh toán của khách hàng.</p>
    </div>
</div>

{{-- Thông tin chung --}}
<div class="invoice-detail-card">
    <h2>Thông tin hóa đơn</h2>

    <div class="invoice-detail-grid">
        <div>
            <div class="invoice-detail-row">
                <span>Mã đặt vé</span>
                <strong>{{ $code }}</strong>
            </div>

            <div class="invoice-detail-row">
                <span>Mã đơn trong hệ thống</span>
                <strong>#{{ $booking->id }}</strong>
            </div>

            <div class="invoice-detail-row">
                <span>Ngày tạo đơn</span>
                <strong>
                    {{ $booking->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}
                </strong>
            </div>
        </div>

        <div>
            <div class="invoice-detail-row">
                <span>Trạng thái</span>
                <strong>
                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                </strong>
            </div>

            <div class="invoice-detail-row">
                <span>Thời gian thanh toán</span>
                <strong>
                    {{ $booking->payment_time
                        ? \Carbon\Carbon::parse($booking->payment_time)->format('d/m/Y H:i')
                        : 'Chưa thanh toán' }}
                </strong>
            </div>

            <div class="invoice-detail-row">
                <span>Phương thức thanh toán</span>
                <strong>{{ $booking->payment_method ?: 'Chưa có' }}</strong>
            </div>

            @if($booking->status === 'pending_approval')
                <div class="invoice-detail-row">
                    <span>Khách xác nhận chuyển khoản</span>
                    <strong>
                        {{ $booking->payment_confirmed_at
                            ? $booking->payment_confirmed_at->format('d/m/Y H:i:s')
                            : 'Không có dữ liệu' }}
                    </strong>
                </div>

                <div class="invoice-detail-row">
                    <span>Hạn Admin duyệt</span>
                    <strong class="approval-deadline">
                        {{ $booking->approval_expires_at
                            ? $booking->approval_expires_at->format('d/m/Y H:i:s')
                            : 'Không có dữ liệu' }}
                    </strong>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Khách hàng và chuyến bay --}}
<div class="invoice-detail-grid">
    <div class="invoice-detail-card">
        <h2>Thông tin khách hàng</h2>

        <div class="invoice-detail-row">
            <span>Họ tên</span>
            <strong>{{ $booking->passenger_name }}</strong>
        </div>

        <div class="invoice-detail-row">
            <span>Email</span>
            <strong>{{ $booking->passenger_email }}</strong>
        </div>

        <div class="invoice-detail-row">
            <span>Số điện thoại</span>
            <strong>{{ $booking->passenger_phone }}</strong>
        </div>

        <div class="invoice-detail-row">
            <span>Số hành khách</span>
            <strong>
                {{ $booking->passenger_count ?? $booking->number_of_seats }}
            </strong>
        </div>
    </div>

    <div class="invoice-detail-card">
        <h2>Thông tin chuyến bay</h2>

        <div class="invoice-detail-row">
            <span>Số hiệu chuyến bay</span>
            <strong>
                {{ $booking->flight?->flight_number ?? 'Không xác định' }}
            </strong>
        </div>

        <div class="invoice-detail-row">
            <span>Điểm đi</span>
            <strong>
                {{ $booking->flight?->departureAirport?->city ?? 'Không xác định' }}
            </strong>
        </div>

        <div class="invoice-detail-row">
            <span>Điểm đến</span>
            <strong>
                {{ $booking->flight?->arrivalAirport?->city ?? 'Không xác định' }}
            </strong>
        </div>

        <div class="invoice-detail-row">
            <span>Khởi hành</span>
            <strong>
                {{ $booking->flight?->departure_time
                    ? \Carbon\Carbon::parse($booking->flight->departure_time)->format('d/m/Y H:i')
                    : 'Chưa có' }}
            </strong>
        </div>

        <div class="invoice-detail-row">
            <span>Loại vé</span>
            <strong>
                {{ $booking->trip_type === 'round_trip'
                    ? 'Khứ hồi'
                    : 'Một chiều' }}
            </strong>
        </div>

        <div class="invoice-detail-row">
            <span>Hạng vé</span>
            <strong>
                {{ $classLabels[$booking->flight_class] ?? $booking->flight_class ?? 'Chưa có' }}
            </strong>
        </div>
    </div>
</div>

{{-- Danh sách hành khách --}}
<div class="invoice-detail-card">
    <h2>Danh sách hành khách</h2>

    <div style="overflow-x:auto;">
        <table class="invoice-detail-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ tên</th>
                    <th>Giới tính</th>
                    <th>Ghế</th>
                </tr>
            </thead>
            <tbody>
                @forelse($passengers as $index => $passenger)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $passenger['name'] ?? 'Chưa có' }}</td>
                        <td>{{ $passenger['gender'] ?? 'Chưa có' }}</td>
                        <td>{{ $passenger['seat'] ?? 'Chưa chọn' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:#64748b;">
                            Đơn đặt vé cũ chưa lưu danh sách hành khách chi tiết.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Thanh toán --}}
<div class="invoice-detail-card">
    <h2>Thông tin thanh toán</h2>

    <div class="invoice-detail-row">
        <span>Tổng tiền đặt vé</span>
        <strong class="invoice-detail-total">
            {{ number_format($booking->total_price, 0, ',', '.') }} đ
        </strong>
    </div>

    <div class="invoice-detail-row">
        <span>Trạng thái thanh toán</span>
        <strong>
            {{ $statusLabels[$booking->status] ?? $booking->status }}
        </strong>
    </div>

    @if($booking->refund_status)
        <div class="invoice-detail-row">
            <span>Trạng thái hoàn tiền</span>
            <strong>
                {{ $refundLabels[$booking->refund_status] ?? $booking->refund_status }}
            </strong>
        </div>
    @endif

    @if($booking->cancellation_reason)
        <div class="invoice-detail-row">
            <span>Lý do hủy</span>
            <strong>{{ $booking->cancellation_reason }}</strong>
        </div>
    @endif
</div>

{{-- Thao tác duyệt thanh toán --}}
@if($booking->status === 'pending_approval')
    <div class="invoice-detail-card approval-warning">
        <h2>Yêu cầu duyệt thanh toán</h2>

        <p>
            Khách hàng đã xác nhận chuyển khoản. Admin cần kiểm tra giao dịch
            và xử lý đơn trong vòng 2 phút kể từ thời điểm khách xác nhận.
        </p>

        @if($booking->approval_expires_at)
            <p>
                Hạn xử lý:
                <strong class="approval-deadline">
                    {{ $booking->approval_expires_at->format('d/m/Y H:i:s') }}
                </strong>
            </p>
        @endif

        <div class="invoice-detail-actions">
            <form
                action="{{ route('admin.ticket.approve', $booking->booking_code) }}"
                method="POST"
                onsubmit="return confirm('Bạn xác nhận đã kiểm tra giao dịch và duyệt thanh toán cho đơn này?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    Duyệt thanh toán
                </button>
            </form>

            <form
                action="{{ route('admin.ticket.cancel-pending', $booking->booking_code) }}"
                method="POST"
                onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn chờ duyệt này? Số ghế sẽ được hoàn lại.')"
            >
                @csrf

                <button type="submit" class="btn-danger">
                    Hủy đơn
                </button>
            </form>
        </div>
    </div>
@endif

{{-- Nút điều hướng --}}
<div class="invoice-detail-actions">
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
        Quay lại danh sách
    </a>

    <button type="button" class="btn btn-primary" onclick="window.print()">
        In hóa đơn
    </button>
</div>

@endsection

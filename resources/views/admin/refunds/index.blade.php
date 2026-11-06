@extends('admin.airports.layout')

@section('title', 'Quản lý hoàn tiền')

@section('content')
@php
    $statusLabels = [
        'pending_refund' => 'Chờ hoàn tiền',
        'completed' => 'Đã hoàn tiền',
        'no_refund' => 'Không hoàn tiền',
    ];
@endphp

<style>
    .refund-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .refund-stat {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 22px;
    }

    .refund-stat-label {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .refund-stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
    }

    .refund-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .refund-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .refund-filters input,
    .refund-filters select {
        padding: 11px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
    }

    .refund-filters input {
        flex: 1;
        min-width: 220px;
    }

    .refund-table-wrapper {
        overflow-x: auto;
    }

    .refund-table {
        width: 100%;
        border-collapse: collapse;
    }

    .refund-table th,
    .refund-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    .refund-table th {
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        background: #f8fafc;
    }

    .refund-status {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .refund-status-pending_refund {
        background: #fef3c7;
        color: #92400e;
    }

    .refund-status-completed {
        background: #dcfce7;
        color: #166534;
    }

    .refund-status-no_refund {
        background: #f1f5f9;
        color: #475569;
    }

    .refund-action {
        color: #0f766e;
        font-weight: 600;
        text-decoration: none;
    }

    .refund-action:hover {
        text-decoration: underline;
    }

    .refund-empty {
        text-align: center;
        padding: 36px;
        color: #64748b;
    }

    .refund-pagination {
        margin-top: 22px;
    }
</style>

<div class="page-header">
    <div>
        <h1>Quản lý hoàn tiền</h1>
        <p>Theo dõi và xử lý các yêu cầu hoàn tiền của khách hàng.</p>
    </div>
</div>

<div class="refund-stats">
    <div class="refund-stat">
        <div class="refund-stat-label">Yêu cầu đang chờ</div>
        <div class="refund-stat-value">
            {{ number_format($pendingCount) }}
        </div>
    </div>

    <div class="refund-stat">
        <div class="refund-stat-label">Yêu cầu đã hoàn tất</div>
        <div class="refund-stat-value">
            {{ number_format($completedCount) }}
        </div>
    </div>

    <div class="refund-stat">
        <div class="refund-stat-label">Tổng tiền chờ hoàn</div>
        <div class="refund-stat-value">
            {{ number_format($pendingAmount, 0, ',', '.') }} đ
        </div>
    </div>
</div>

<div class="refund-card">
    <form method="GET"
          action="{{ route('admin.refunds.index') }}"
          class="refund-filters">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Tìm mã vé, tên hoặc email khách hàng"
        >

        <select name="refund_status">
            <option value="">Tất cả trạng thái</option>

            <option value="pending_refund"
                @selected(request('refund_status') === 'pending_refund')>
                Chờ hoàn tiền
            </option>

            <option value="completed"
                @selected(request('refund_status') === 'completed')>
                Đã hoàn tiền
            </option>

            <option value="no_refund"
                @selected(request('refund_status') === 'no_refund')>
                Không hoàn tiền
            </option>
        </select>

        <button type="submit" class="btn btn-primary">
            Tìm kiếm
        </button>

        <a href="{{ route('admin.refunds.index') }}"
           class="btn btn-secondary">
            Đặt lại
        </a>
    </form>

    <div class="refund-table-wrapper">
        <table class="refund-table">
            <thead>
                <tr>
                    <th>Mã đặt vé</th>
                    <th>Khách hàng</th>
                    <th>Chuyến bay</th>
                    <th>Số tiền</th>
                    <th>Trạng thái hoàn tiền</th>
                    <th>Ngày cập nhật</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($refunds as $booking)
                    <tr>
                        <td>
                            <strong>
                                {{ $booking->pnr_code ?: $booking->booking_code }}
                            </strong>
                        </td>

                        <td>
                            <strong>{{ $booking->passenger_name }}</strong>

                            <div style="color:#64748b;font-size:13px;">
                                {{ $booking->passenger_email }}
                            </div>
                        </td>

                        <td>
                            {{ $booking->flight?->flight_number ?? 'Không xác định' }}
                        </td>

                        <td>
                            <strong>
                                {{ number_format($booking->total_price, 0, ',', '.') }} đ
                            </strong>
                        </td>

                        <td>
                            <span class="refund-status refund-status-{{ $booking->refund_status }}">
                                {{ $statusLabels[$booking->refund_status] ?? $booking->refund_status }}
                            </span>
                        </td>

                        <td>
                            {{ $booking->updated_at?->format('d/m/Y H:i') ?? 'Chưa có' }}
                        </td>

                        <td>
                            <a class="refund-action"
                               href="{{ route('admin.refunds.show', $booking) }}">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="refund-empty">
                            Chưa có yêu cầu hoàn tiền phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="refund-pagination">
        {{ $refunds->links() }}
    </div>
</div>
@endsection

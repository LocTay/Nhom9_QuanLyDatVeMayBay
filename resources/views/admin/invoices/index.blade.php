@extends('admin.airports.layout')

@section('title', 'Quản lý hóa đơn')

<style>
    .invoice-status {
        font-weight: 600;
        color: #64748b;
    }

    .invoice-status-pending {
        color: #d97706;
    }

    .invoice-status-paid {
        color: #16a34a;
    }

    .invoice-status-failed {
        color: #dc2626;
    }

    .invoice-status-cancelled {
        color: #64748b;
    }

    .invoice-status-refund_pending {
        color: #9333ea;
    }
</style>

@section('content')
<div class="page-header">
    <div>
        <h1>Quản lý hóa đơn</h1>
        <p>Theo dõi đơn đặt vé và trạng thái thanh toán của khách hàng.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

{{-- Thống kê tổng quan --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    <div class="card" style="padding:20px;">
        <div style="color:#64748b;">Tổng đơn đặt vé</div>
        <h2>{{ number_format($totalBookings) }}</h2>
    </div>

    <div class="card" style="padding:20px;">
        <div style="color:#64748b;">Đã thanh toán</div>
        <h2>{{ number_format($paidBookings) }}</h2>
    </div>

    <div class="card" style="padding:20px;">
        <div style="color:#64748b;">Chờ thanh toán</div>
        <h2>{{ number_format($pendingBookings) }}</h2>
    </div>

    <div class="card" style="padding:20px;">
        <div style="color:#64748b;">Doanh thu đã thanh toán</div>
        <h2>{{ number_format($totalRevenue, 0, ',', '.') }} đ</h2>
    </div>
</div>

{{-- Tìm kiếm và lọc --}}
<div class="card" style="padding:20px;margin-bottom:24px;">
    <form method="GET" action="{{ route('admin.invoices.index') }}"
        style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;">

        <div style="flex:2;min-width:220px;">
            <label for="search">Tìm kiếm hóa đơn</label>
            <input
                id="search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Mã vé, tên hoặc email khách hàng"
                style="width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:8px;">
        </div>

        <div style="flex:1;min-width:180px;">
            <label for="status">Trạng thái</label>
            <select
                id="status"
                name="status"
                style="width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:8px;">
                <option value="">Tất cả trạng thái</option>
                @foreach([
                'pending' => 'Chờ thanh toán',
                'paid' => 'Đã thanh toán',
                'failed' => 'Thanh toán thất bại',
                'cancelled' => 'Đã hủy',
                'refund_pending' => 'Chờ hoàn tiền',
                ] as $value => $label)
                <option value="{{ $value }}"
                    {{ request('status') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
            Đặt lại
        </a>
    </form>
</div>

{{-- Danh sách hóa đơn --}}
<div class="card" style="padding:20px;">
    <h2 style="margin-bottom:20px;">Danh sách hóa đơn</h2>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:2px solid #e2e8f0;">
                    <th style="padding:12px;">Mã đặt vé</th>
                    <th style="padding:12px;">Khách hàng</th>
                    <th style="padding:12px;">Chuyến bay</th>
                    <th style="padding:12px;">Số khách</th>
                    <th style="padding:12px;">Tổng tiền</th>
                    <th style="padding:12px;">Trạng thái</th>
                    <th style="padding:12px;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse($bookings as $booking)
                @php
                $statusLabels = [
                'pending' => 'Chờ thanh toán',
                'paid' => 'Đã thanh toán',
                'failed' => 'Thất bại',
                'cancelled' => 'Đã hủy',
                'refund_pending' => 'Chờ hoàn tiền',
                ];

                $statusColors = [
                'pending' => '#d97706',
                'paid' => '#16a34a',
                'failed' => '#dc2626',
                'cancelled' => '#64748b',
                'refund_pending' => '#9333ea',
                ];
                @endphp

                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:12px;font-weight:600;">
                        {{ $booking->pnr_code ?: $booking->booking_code }}
                    </td>

                    <td style="padding:12px;">
                        <strong>{{ $booking->passenger_name }}</strong>
                        <div style="font-size:12px;color:#64748b;">
                            {{ $booking->passenger_email }}
                        </div>
                    </td>

                    <td style="padding:12px;">
                        {{ $booking->flight?->flight_number ?? 'Không xác định' }}
                    </td>

                    <td style="padding:12px;">
                        {{ $booking->passenger_count ?? $booking->number_of_seats }}
                    </td>

                    <td style="padding:12px;">
                        {{ number_format($booking->total_price, 0, ',', '.') }} đ
                    </td>

                    <td style="padding:12px;">
                        <span class="invoice-status invoice-status-{{ $booking->status }}">
                            {{ $statusLabels[$booking->status] ?? $booking->status }}
                        </span>
                    </td>

                    <td style="padding:12px;">
                        <a href="{{ route('admin.invoices.show', $booking->id) }}"
                            class="btn btn-primary">
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7"
                        style="padding:30px;text-align:center;color:#64748b;">
                        Không tìm thấy hóa đơn phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
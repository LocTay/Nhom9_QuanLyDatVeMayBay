@extends('admin.airports.layout')

@section('title', 'Quản lý khách hàng')
@section('topbar-title', 'Quản lý khách hàng')

@section('content')
<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / KHÁCH HÀNG</small>
    <h1>Quản lý khách hàng</h1>
    <p>Theo dõi tài khoản khách hàng và lịch sử đặt vé trên SkyBooking.</p>
</div>

<div class="finance-stats customer-stats">

    <div class="finance-stat">
        <div class="finance-stat-label">Tổng khách hàng</div>
        <div class="finance-stat-value">
            {{ number_format($totalCustomers) }}
        </div>
        <div class="finance-stat-note">Tài khoản có vai trò Customer</div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Khách hàng mới trong tháng</div>
        <div class="finance-stat-value">
            {{ number_format($newCustomers) }}
        </div>
        <div class="finance-stat-note">Đăng ký trong tháng hiện tại</div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Đã xác minh email</div>
        <div class="finance-stat-value">
            {{ number_format($verifiedCustomers) }}
        </div>
        <div class="finance-stat-note">Theo trường email_verified_at</div>
    </div>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Tìm kiếm khách hàng</h2>
            <p>Tìm theo họ tên, email hoặc số điện thoại.</p>
        </div>
    </div>

    <form action="{{ route('admin.customers.index') }}"
        method="GET"
        class="finance-filters customer-filters">

        <div class="finance-field">
            <label for="customer-search">Thông tin khách hàng</label>
            <input id="customer-search"
                name="search"
                type="text"
                value="{{ request('search') }}"
                placeholder="Nhập tên, email hoặc số điện thoại">
        </div>

        <div class="finance-filter-actions">
            <button type="submit" class="btn btn-primary">
                Tìm kiếm
            </button>

            <a href="{{ route('admin.customers.index') }}"
                class="btn btn-secondary">
                Đặt lại
            </a>
        </div>
    </form>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Danh sách khách hàng</h2>
            <p>
                Tìm thấy {{ number_format($customers->total()) }}
                khách hàng phù hợp.
            </p>
        </div>
    </div>

    <div class="finance-table-wrap">
        <table class="finance-table">
            <thead>
                <tr>
                    <th>Mã tài khoản</th>
                    <th>Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Số đơn đặt vé</th>
                    <th>Ngày đăng ký</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <span class="finance-code">
                            #{{ $customer->id }}
                        </span>
                    </td>

                    <td>
                        <span class="finance-person">
                            {{ $customer->name }}
                        </span>

                        <span class="finance-subtext">
                            {{ $customer->email }}
                        </span>
                    </td>

                    <td>
                        {{ $customer->phone ?: 'Chưa cập nhật' }}
                    </td>

                    <td>
                        <strong>{{ $customer->bookings_count }}</strong>
                    </td>

                    <td>
                        {{ $customer->created_at?->format('d/m/Y') ?? 'Chưa có' }}
                    </td>

                    <td>
                        <a href="{{ route('admin.customers.show', $customer) }}"
                            class="btn btn-primary">
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="finance-empty">
                        Không tìm thấy khách hàng phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="finance-pagination">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
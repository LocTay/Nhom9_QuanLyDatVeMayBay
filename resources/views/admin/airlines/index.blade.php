@extends('admin.airports.layout')

@section('title', 'Quản lý hãng hàng không')
@section('topbar-title', 'Quản lý hãng hàng không')

@section('content')
<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / HÃNG HÀNG KHÔNG</small>
    <h1>Quản lý hãng hàng không</h1>
    <p>Quản lý thông tin và trạng thái hoạt động của các hãng hàng không.</p>
</div>

<div class="finance-stats airline-stats">
    <div class="finance-stat">
        <div class="finance-stat-label">Tổng số hãng</div>
        <div class="finance-stat-value">{{ number_format($totalAirlines) }}</div>
        <div class="finance-stat-note">Hãng hàng không trong hệ thống</div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Đang hoạt động</div>
        <div class="finance-stat-value">{{ number_format($activeAirlines) }}</div>
        <div class="finance-stat-note">Hãng đang được khai thác</div>
    </div>

    <div class="finance-stat">
        <div class="finance-stat-label">Ngừng hoạt động</div>
        <div class="finance-stat-value">{{ number_format($inactiveAirlines) }}</div>
        <div class="finance-stat-note">Hãng tạm ngừng khai thác</div>
    </div>
</div>

<div class="finance-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Danh sách hãng hàng không</h2>
            <p>Hiển thị {{ $airlines->total() }} hãng phù hợp.</p>
        </div>

        <a href="{{ route('admin.airlines.create') }}" class="btn btn-primary">
            + Thêm hãng hàng không
        </a>
    </div>

    <form method="GET"
        action="{{ route('admin.airlines.index') }}"
        class="finance-filters airline-filters">

        <div class="finance-field">
            <label for="search">Tìm kiếm</label>
            <input type="text"
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Mã hãng, tên hãng hoặc quốc gia">
        </div>

        <div class="finance-filter-actions">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <a href="{{ route('admin.airlines.index') }}"
                class="btn btn-secondary">Đặt lại</a>
        </div>
    </form>

    <div class="finance-table-wrap" style="margin-top:22px;">
        <table class="finance-table">
            <thead>
                <tr>
                    <th>Mã hãng</th>
                    <th>Tên hãng hàng không</th>
                    <th>Quốc gia</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($airlines as $airline)
                <tr>
                    <td>
                        <span class="finance-code">{{ $airline->code }}</span>
                    </td>
                    <td>
                        <span class="finance-person">{{ $airline->name }}</span>
                    </td>
                    <td>{{ $airline->country ?: 'Chưa cập nhật' }}</td>
                    <td>
                        <span class="finance-badge {{ $airline->is_active ? 'finance-badge-paid' : 'finance-badge-cancelled' }}">
                            {{ $airline->is_active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </td>
                    <td>{{ $airline->created_at?->format('d/m/Y') }}</td>
                    <td>
                        <div class="finance-actions" style="margin:0;">
                            <a href="{{ route('admin.airlines.edit', $airline) }}"
                                class="btn btn-primary">
                                Chỉnh sửa
                            </a>

                            <form action="{{ route('admin.airlines.destroy', $airline) }}"
                                method="POST"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa hãng hàng không này?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="btn btn-danger"
                                    @disabled($airline->flights_count > 0)>
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="finance-empty">
                        Chưa có hãng hàng không phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($airlines->hasPages())
    <div class="finance-pagination">
        {{ $airlines->links() }}
    </div>
    @endif
</div>
@endsection
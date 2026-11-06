@extends('admin.airports.layout')

@section('title', 'Quản lý sân bay')
@section('topbar-title', 'Quản lý sân bay')

@section('content')

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / SÂN BAY</small>
    <h1>Quản lý sân bay</h1>
    <p>
        Theo dõi, thêm mới và cập nhật thông tin
        các sân bay trong hệ thống SkyBooking.
    </p>
</div>

<div class="airport-summary">
    <div class="airport-summary-card">
        <div class="summary-label">
            Tổng số sân bay
        </div>

        <div class="summary-value">
            {{ number_format($airports->total()) }}
        </div>

        <div class="summary-note">
            Sân bay đang có trong hệ thống
        </div>
    </div>

    <div class="airport-summary-card">
        <div class="summary-label">
            Quản lý dữ liệu
        </div>

        <div class="summary-value" style="font-size:20px;">
            SkyBooking
        </div>

        <div class="summary-note">
            Quản lý thông tin sân bay và địa điểm
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h2>Danh sách sân bay</h2>
            <p style="margin-top:8px;color:var(--muted);font-size:11px;">
                Thông tin các sân bay được quản lý trên hệ thống.
            </p>
        </div>

        <a href="{{ route('admin.airports.create') }}"
           class="btn btn-gold">
            + Thêm sân bay
        </a>
    </div>

    <div class="table-wrap">
        <table class="airport-table">
            <thead>
                <tr>
                    <th>Mã sân bay</th>
                    <th>Tên sân bay</th>
                    <th>Thành phố</th>
                    <th>Quốc gia</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($airports as $airport)
                    <tr>
                        <td>
                            <span class="airport-code-badge">
                                {{ $airport->code }}
                            </span>
                        </td>

                        <td>
                            <span class="airport-name">
                                {{ $airport->name }}
                            </span>
                        </td>

                        <td>{{ $airport->city }}</td>

                        <td>{{ $airport->country }}</td>

                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.airports.edit', $airport) }}"
                                   class="btn btn-primary">
                                    Sửa
                                </a>

                                <form
                                    action="{{ route('admin.airports.destroy', $airport) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa sân bay này?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="airport-empty">
                            Chưa có sân bay nào trong hệ thống.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($airports->hasPages())
        <div class="pagination">
            {{ $airports->links() }}
        </div>
    @endif
</div>

@endsection

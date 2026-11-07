@extends('admin.airports.layout')

@section('title', 'Quản lý chuyến bay')
@section('topbar-title', 'Quản lý chuyến bay')

@section('content')

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / CHUYẾN BAY</small>
    <h1>Quản lý chuyến bay</h1>
    <p>
        Theo dõi lịch trình, thông tin chặng bay,
        giá vé, số ghế còn trống và trạng thái chuyến bay.
    </p>
</div>

<div class="flight-summary">
    <div class="flight-summary-card">
        <div class="summary-label">Tổng chuyến bay</div>
        <div class="summary-value">
            {{ number_format($flights->total()) }}
        </div>
        <div class="summary-note">
            Số chuyến bay trong hệ thống
        </div>
    </div>

    <div class="flight-summary-card">
        <div class="summary-label">Đang hiển thị</div>
        <div class="summary-value">
            {{ number_format($flights->count()) }}
        </div>
        <div class="summary-note">
            Chuyến bay trên trang hiện tại
        </div>
    </div>

    <div class="flight-summary-card">
        <div class="summary-label">Tổng số trang</div>
        <div class="summary-value">
            {{ number_format($flights->lastPage()) }}
        </div>
        <div class="summary-note">
            Trang {{ $flights->currentPage() }}
            / {{ $flights->lastPage() }}
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h2>Danh sách chuyến bay</h2>
            <p style="margin-top:8px; color:var(--muted); font-size:11px;">
                Quản lý các chuyến bay của SkyBooking.
            </p>
        </div>

        <a href="{{ route('admin.flights.create') }}"
            class="btn btn-gold">
            + Thêm chuyến bay
        </a>
    </div>

    <div class="table-wrap">
        <table class="flight-table">
            <thead>
                <tr>
                    <th>Mã chuyến bay</th>
                    <th>Hãng hàng không</th>
                    <th>Sân bay đi</th>
                    <th>Sân bay đến</th>
                    <th>Khởi hành</th>
                    <th>Đến nơi</th>
                    <th>Giá vé</th>
                    <th>Ghế trống</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($flights as $flight)
                <tr>
                    <td>
                        <span class="flight-code-badge">
                            {{ $flight->flight_number }}
                        </span>
                    </td>

                    <td>
                        @if($flight->airline)
                        <span class="finance-person">
                            {{ $flight->airline->name }}
                        </span>

                        <span class="finance-subtext">
                            {{ $flight->airline->code }}
                        </span>
                        @else
                        <span class="text-muted">
                            Chưa gán hãng
                        </span>
                        @endif
                    </td>

                    <td>
                        <div class="flight-route">
                            {{ $flight->departureAirport?->city ?? 'N/A' }}
                            <small>
                                {{ $flight->departureAirport?->code ?? 'N/A' }}
                            </small>
                        </div>
                    </td>

                    <td>
                        <div class="flight-route">
                            {{ $flight->arrivalAirport?->city ?? 'N/A' }}
                            <small>
                                {{ $flight->arrivalAirport?->code ?? 'N/A' }}
                            </small>
                        </div>
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($flight->departure_time)->format('d/m/Y') }}
                        <div style="color:var(--muted);margin-top:5px;">
                            {{ \Carbon\Carbon::parse($flight->departure_time)->format('H:i') }}
                        </div>
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($flight->arrival_time)->format('d/m/Y') }}
                        <div style="color:var(--muted);margin-top:5px;">
                            {{ \Carbon\Carbon::parse($flight->arrival_time)->format('H:i') }}
                        </div>
                    </td>

                    <td>
                        <span class="flight-price">
                            {{ number_format($flight->price, 0, ',', '.') }} đ
                        </span>
                    </td>

                    <td>
                        <span class="flight-seats {{ $flight->available_seats == 0 ? 'empty' : '' }}">
                            {{ $flight->available_seats }}
                        </span>
                    </td>

                    <td>
                        @php
                        $statuses = [
                        'scheduled' => ['Đã lên lịch', 'scheduled'],
                        'delayed' => ['Hoãn chuyến', 'delayed'],
                        'cancelled' => ['Đã hủy', 'cancelled'],
                        'completed' => ['Hoàn thành', 'completed'],
                        ];

                        $status = $statuses[$flight->status]
                        ?? [$flight->status, 'default'];
                        @endphp

                        <span class="flight-status flight-status-{{ $status[1] }}">
                            {{ $status[0] }}
                        </span>
                    </td>

                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.flights.edit', $flight) }}"
                                class="btn btn-primary">
                                Sửa
                            </a>

                            <form
                                action="{{ route('admin.flights.destroy', $flight) }}"
                                method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa chuyến bay này?')">
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
                    <td colspan="10" class="flight-empty">
                        Chưa có chuyến bay nào trong hệ thống.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($flights->hasPages())
    <div class="pagination">
        {{ $flights->links() }}
    </div>
    @endif
</div>

@endsection
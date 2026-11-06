@extends('admin.airports.layout')

@section('title', 'Quản lý chuyến bay')
@section('topbar-title', 'Quản lý chuyến bay')

@section('content')
    <div class="page-heading">
        <small>Hệ thống quản trị</small>
        <h1>Quản lý chuyến bay</h1>
        <p>Theo dõi lịch trình, giá vé, số ghế còn trống và trạng thái chuyến bay.</p>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Danh sách chuyến bay</h2>
            <a href="{{ route('admin.flights.create') }}" class="btn btn-gold">
                + Thêm chuyến bay
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã chuyến bay</th>
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
                            <td class="airport-code">
                                {{ $flight->flight_number }}
                            </td>

                            <td>
                                {{ $flight->departureAirport?->city ?? 'N/A' }}
                                ({{ $flight->departureAirport?->code ?? 'N/A' }})
                            </td>

                            <td>
                                {{ $flight->arrivalAirport?->city ?? 'N/A' }}
                                ({{ $flight->arrivalAirport?->code ?? 'N/A' }})
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($flight->departure_time)->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($flight->arrival_time)->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ number_format($flight->price, 0, ',', '.') }} đ
                            </td>

                            <td>{{ $flight->available_seats }}</td>

                            <td>
                                @switch($flight->status)
                                    @case('scheduled')
                                        Đã lên lịch
                                        @break
                                    @case('delayed')
                                        Hoãn chuyến
                                        @break
                                    @case('cancelled')
                                        Đã hủy
                                        @break
                                    @case('completed')
                                        Hoàn thành
                                        @break
                                    @default
                                        {{ $flight->status }}
                                @endswitch
                            </td>

                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.flights.edit', $flight) }}"
                                       class="btn btn-primary">
                                        Sửa
                                    </a>

                                    <form action="{{ route('admin.flights.destroy', $flight) }}"
                                          method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa chuyến bay này?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:#77869a;">
                                Chưa có chuyến bay nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $flights->links() }}
        </div>
    </div>
@endsection

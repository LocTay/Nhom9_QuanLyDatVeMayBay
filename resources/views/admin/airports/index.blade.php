@extends('admin.airports.layout')

@section('title', 'Quản lý sân bay')
@section('topbar-title', 'Quản lý sân bay')

@section('content')
    <div class="page-heading">
        <small>Hệ thống quản trị</small>
        <h1>Quản lý sân bay</h1>
        <p>Quản lý thông tin các sân bay trong hệ thống SkyBooking.</p>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Danh sách sân bay</h2>

            <a href="{{ route('admin.airports.create') }}" class="btn btn-gold">
                + Thêm sân bay
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="table-wrap">
            <table>
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
                            <td class="airport-code">{{ $airport->code }}</td>
                            <td>{{ $airport->name }}</td>
                            <td>{{ $airport->city }}</td>
                            <td>{{ $airport->country }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.airports.edit', $airport) }}"
                                       class="btn btn-primary">
                                        Sửa
                                    </a>

                                    <form action="{{ route('admin.airports.destroy', $airport) }}"
                                          method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa sân bay này?')">
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
                            <td colspan="5" style="text-align:center; color:#77869a;">
                                Chưa có sân bay nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $airports->links() }}
        </div>
    </div>
@endsection
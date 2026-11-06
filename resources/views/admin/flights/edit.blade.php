@extends('admin.airports.layout')

@section('title', 'Chỉnh sửa chuyến bay')
@section('topbar-title', 'Chỉnh sửa chuyến bay')

@section('content')
<div class="page-heading">
    <small>Quản lý dữ liệu</small>
    <h1>Chỉnh sửa chuyến bay {{ $flight->flight_number }}</h1>
    <p>Cập nhật thông tin và trạng thái của chuyến bay.</p>
</div>

<div class="panel">
    <div class="panel-header">
        <h2>Thông tin chuyến bay</h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left:20px">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.flights.update', $flight) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="flight_number">Mã chuyến bay</label>
            <input id="flight_number" name="flight_number"
                   maxlength="20"
                   value="{{ old('flight_number', $flight->flight_number) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="departure_airport_id">Sân bay đi</label>
            <select id="departure_airport_id"
                    name="departure_airport_id" required>
                <option value="">-- Chọn sân bay đi --</option>
                @foreach ($airports as $airport)
                    <option value="{{ $airport->id }}"
                        @selected(old('departure_airport_id', $flight->departure_airport_id) == $airport->id)>
                        {{ $airport->city }} ({{ $airport->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="arrival_airport_id">Sân bay đến</label>
            <select id="arrival_airport_id"
                    name="arrival_airport_id" required>
                <option value="">-- Chọn sân bay đến --</option>
                @foreach ($airports as $airport)
                    <option value="{{ $airport->id }}"
                        @selected(old('arrival_airport_id', $flight->arrival_airport_id) == $airport->id)>
                        {{ $airport->city }} ({{ $airport->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="departure_time">Thời gian khởi hành</label>
            <input id="departure_time" type="datetime-local"
                   name="departure_time"
                   value="{{ old('departure_time', \Carbon\Carbon::parse($flight->departure_time)->format('Y-m-d\TH:i')) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="arrival_time">Thời gian đến</label>
            <input id="arrival_time" type="datetime-local"
                   name="arrival_time"
                   value="{{ old('arrival_time', \Carbon\Carbon::parse($flight->arrival_time)->format('Y-m-d\TH:i')) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="price">Giá vé (VNĐ)</label>
            <input id="price" type="number" name="price"
                   min="0" step="1000"
                   value="{{ old('price', $flight->price) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="available_seats">Số ghế trống</label>
            <input id="available_seats" type="number"
                   name="available_seats" min="0"
                   value="{{ old('available_seats', $flight->available_seats) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="status">Trạng thái</label>
            <select id="status" name="status" required>
                @foreach ([
                    'scheduled' => 'Đã lên lịch',
                    'delayed' => 'Hoãn chuyến',
                    'cancelled' => 'Đã hủy',
                    'completed' => 'Hoàn thành'
                ] as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('status', $flight->status) === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">
                Cập nhật chuyến bay
            </button>

            <a href="{{ route('admin.flights.index') }}"
               class="btn btn-secondary">
                Quay lại
            </a>
        </div>
    </form>
</div>
@endsection

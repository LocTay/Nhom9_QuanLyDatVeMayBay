@extends('admin.airports.layout')

@section('title', 'Chỉnh sửa chuyến bay')
@section('topbar-title', 'Chỉnh sửa chuyến bay')

@section('content')

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / CHỈNH SỬA CHUYẾN BAY</small>
    <h1>Chỉnh sửa chuyến bay {{ $flight->flight_number }}</h1>
    <p>Cập nhật lịch trình và thông tin chuyến bay.</p>
</div>

<div class="panel flight-form">
    <div class="panel-header">
        <div>
            <h2>Thông tin chuyến bay</h2>
            <p style="margin-top:8px;color:var(--muted);font-size:11px;">
                Mã hiện tại:
                <strong>{{ $flight->flight_number }}</strong>
            </p>
        </div>

        <a href="{{ route('admin.flights.index') }}"
            class="btn btn-secondary">
            ← Danh sách chuyến bay
        </a>
    </div>

    <div class="flight-form-note">
        Bạn đang chỉnh sửa chuyến bay
        <strong>{{ $flight->flight_number }}</strong>.
        Một số thay đổi như giá vé hoặc xóa chuyến bay
        có thể bị hạn chế nếu chuyến bay đã có đặt vé.
    </div>

    @if ($errors->any())
    <div class="alert alert-error">
        <strong>Vui lòng kiểm tra thông tin:</strong>
        <ul style="margin:8px 0 0 20px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
        action="{{ route('admin.flights.update', $flight) }}">
        @csrf
        @method('PUT')

        <div class="flight-form-section">
            1. Thông tin chặng bay
        </div>

        <div class="form-group">
            <label>Hãng hàng không</label>
            <div>
                {{ $flight->airline?->name ?? 'Chưa gán hãng' }}
            </div>
        </div>

        <div class="flight-form-grid">
            <div class="form-group">
                <label for="flight_number">Mã chuyến bay *</label>
                <input
                    id="flight_number"
                    name="flight_number"
                    maxlength="20"
                    value="{{ old('flight_number', $flight->flight_number) }}"
                    required>
                @error('flight_number')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Trạng thái *</label>
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
                @error('status')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="departure_airport_id">Sân bay đi *</label>
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
                @error('departure_airport_id')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="arrival_airport_id">Sân bay đến *</label>
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
                @error('arrival_airport_id')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="flight-form-section">
            2. Lịch trình chuyến bay
        </div>

        <div class="flight-form-grid">
            <div class="form-group">
                <label for="departure_time">Thời gian khởi hành *</label>
                <input
                    id="departure_time"
                    type="datetime-local"
                    name="departure_time"
                    value="{{ old('departure_time', \Carbon\Carbon::parse($flight->departure_time)->format('Y-m-d\TH:i')) }}"
                    required>
                @error('departure_time')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="arrival_time">Thời gian đến *</label>
                <input
                    id="arrival_time"
                    type="datetime-local"
                    name="arrival_time"
                    value="{{ old('arrival_time', \Carbon\Carbon::parse($flight->arrival_time)->format('Y-m-d\TH:i')) }}"
                    required>
                @error('arrival_time')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="flight-form-section">
            3. Giá vé và số ghế
        </div>

        <div class="flight-form-grid">
            <div class="form-group">
                <label for="price">Giá vé (VNĐ) *</label>
                <input
                    id="price"
                    type="number"
                    name="price"
                    min="0"
                    step="1000"
                    value="{{ old('price', $flight->price) }}"
                    required>
                @error('price')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="available_seats">Số ghế trống *</label>
                <input
                    id="available_seats"
                    type="number"
                    name="available_seats"
                    min="0"
                    value="{{ old('available_seats', $flight->available_seats) }}"
                    required>
                @error('available_seats')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">
                Lưu thay đổi
            </button>

            <a href="{{ route('admin.flights.index') }}"
                class="btn btn-secondary">
                Hủy và quay lại
            </a>
        </div>
    </form>
</div>

@endsection
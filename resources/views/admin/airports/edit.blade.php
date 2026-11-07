@extends('admin.airports.layout')

@section('title', 'Chỉnh sửa sân bay')
@section('topbar-title', 'Chỉnh sửa sân bay')

@section('content')

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / CHỈNH SỬA SÂN BAY</small>

    <h1>
        Chỉnh sửa sân bay
        {{ $airport->code }}
    </h1>

    <p>
        Cập nhật thông tin sân bay trong hệ thống SkyBooking.
    </p>
</div>

<div class="panel airport-form">
    <div class="panel-header">
        <div>
            <h2>Thông tin sân bay</h2>

            <p style="margin-top:8px;color:var(--muted);font-size:11px;">
                Mã hiện tại:
                <strong>{{ $airport->code }}</strong>
            </p>
        </div>

        <a href="{{ route('admin.airports.index') }}"
           class="btn btn-secondary">
            ← Danh sách sân bay
        </a>
    </div>

    <div class="airport-form-note">
        Bạn đang chỉnh sửa thông tin sân bay
        <strong>{{ $airport->name }}</strong>.
        Các thay đổi sẽ được lưu sau khi nhấn nút cập nhật.
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

    <form action="{{ route('admin.airports.update', $airport) }}"
          method="POST">
        @csrf
        @method('PUT')

        <div class="airport-form-grid">
            <div class="form-group">
                <label for="code">Mã sân bay *</label>

                <input
                    id="code"
                    type="text"
                    name="code"
                    maxlength="3"
                    value="{{ old('code', $airport->code) }}"
                    required
                >

                @error('code')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Tên sân bay *</label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $airport->name) }}"
                    required
                >

                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="city">Thành phố *</label>

                <input
                    id="city"
                    type="text"
                    name="city"
                    value="{{ old('city', $airport->city) }}"
                    required
                >

                @error('city')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="country">Quốc gia *</label>

                <input
                    id="country"
                    type="text"
                    name="country"
                    value="{{ old('country', $airport->country) }}"
                    required
                >

                @error('country')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">
                Lưu thay đổi
            </button>

            <a href="{{ route('admin.airports.index') }}"
               class="btn btn-secondary">
                Hủy và quay lại
            </a>
        </div>
    </form>
</div>

@endsection

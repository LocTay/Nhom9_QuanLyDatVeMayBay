@extends('admin.airports.layout')

@section('title', 'Chỉnh sửa sân bay')
@section('topbar-title', 'Chỉnh sửa sân bay')

@section('content')
    <div class="page-heading">
        <small>Quản lý dữ liệu</small>
        <h1>Chỉnh sửa sân bay {{ $airport->code }}</h1>
        <p>Cập nhật thông tin sân bay trong hệ thống.</p>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Thông tin sân bay</h2>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Vui lòng kiểm tra lại thông tin:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.airports.update', $airport) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code">Mã sân bay</label>
                <input id="code" type="text" name="code" maxlength="3"
                       value="{{ old('code', $airport->code) }}" required>
                @error('code')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Tên sân bay</label>
                <input id="name" type="text" name="name"
                       value="{{ old('name', $airport->name) }}" required>
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="city">Thành phố</label>
                <input id="city" type="text" name="city"
                       value="{{ old('city', $airport->city) }}" required>
                @error('city')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="country">Quốc gia</label>
                <input id="country" type="text" name="country"
                       value="{{ old('country', $airport->country) }}" required>
                @error('country')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.airports.index') }}" class="btn btn-secondary">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
@endsection
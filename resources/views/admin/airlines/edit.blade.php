@extends('admin.airports.layout')

@section('title', 'Chỉnh sửa hãng hàng không')
@section('topbar-title', 'Chỉnh sửa hãng hàng không')

@section('content')
<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / HÃNG HÀNG KHÔNG / CHỈNH SỬA</small>
    <h1>Chỉnh sửa hãng hàng không</h1>
    <p>Cập nhật thông tin hãng {{ $airline->name }}.</p>
</div>

<div class="finance-panel airline-form-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Thông tin hãng hàng không</h2>
            <p>Mã hãng: {{ $airline->code }}</p>
        </div>
    </div>

    <form action="{{ route('admin.airlines.update', $airline) }}"
          method="POST">
        @csrf
        @method('PUT')

        <div class="airline-form-grid">
            <div class="finance-field">
                <label for="code">Mã hãng *</label>
                <input type="text"
                       id="code"
                       name="code"
                       maxlength="3"
                       value="{{ old('code', $airline->code) }}"
                       required>
                @error('code')
                    <div class="airline-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="finance-field">
                <label for="name">Tên hãng hàng không *</label>
                <input type="text"
                       id="name"
                       name="name"
                       maxlength="150"
                       value="{{ old('name', $airline->name) }}"
                       required>
                @error('name')
                    <div class="airline-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="finance-field">
                <label for="country">Quốc gia</label>
                <input type="text"
                       id="country"
                       name="country"
                       maxlength="100"
                       value="{{ old('country', $airline->country) }}">
                @error('country')
                    <div class="airline-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="finance-field">
                <label for="is_active">Trạng thái *</label>
                <select id="is_active" name="is_active" required>
                    <option value="1"
                        @selected(old('is_active', (int) $airline->is_active) == '1')>
                        Đang hoạt động
                    </option>
                    <option value="0"
                        @selected(old('is_active', (int) $airline->is_active) == '0')>
                        Ngừng hoạt động
                    </option>
                </select>
                @error('is_active')
                    <div class="airline-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="finance-actions">
            <button type="submit" class="btn btn-primary">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.airlines.index') }}"
               class="btn btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </form>
</div>
@endsection

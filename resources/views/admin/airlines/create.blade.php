@extends('admin.airports.layout')

@section('title', 'Thêm hãng hàng không')
@section('topbar-title', 'Thêm hãng hàng không')

@section('content')
<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / HÃNG HÀNG KHÔNG / THÊM MỚI</small>
    <h1>Thêm hãng hàng không</h1>
    <p>Nhập thông tin hãng hàng không để bổ sung vào hệ thống.</p>
</div>

<div class="finance-panel airline-form-panel">
    <div class="finance-panel-header">
        <div>
            <h2>Thông tin hãng hàng không</h2>
            <p>Các trường có dấu * là bắt buộc.</p>
        </div>
    </div>

    <form action="{{ route('admin.airlines.store') }}" method="POST">
        @csrf

        <div class="airline-form-grid">
            <div class="finance-field">
                <label for="code">Mã hãng *</label>
                <input type="text"
                       id="code"
                       name="code"
                       maxlength="3"
                       value="{{ old('code') }}"
                       placeholder="Ví dụ: VN, VJ"
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
                       value="{{ old('name') }}"
                       placeholder="Nhập tên hãng"
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
                       value="{{ old('country') }}"
                       placeholder="Ví dụ: Việt Nam">
                @error('country')
                    <div class="airline-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="finance-field">
                <label for="is_active">Trạng thái *</label>
                <select id="is_active" name="is_active" required>
                    <option value="1" @selected(old('is_active', '1') == '1')>
                        Đang hoạt động
                    </option>
                    <option value="0" @selected(old('is_active') == '0')>
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
                Lưu hãng hàng không
            </button>
            <a href="{{ route('admin.airlines.index') }}"
               class="btn btn-secondary">
                Hủy và quay lại
            </a>
        </div>
    </form>
</div>
@endsection

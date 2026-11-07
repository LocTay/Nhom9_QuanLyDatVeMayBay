@extends('admin.airports.layout')

@section('title', 'Thêm sân bay')
@section('topbar-title', 'Thêm sân bay mới')

@section('content')

<div class="page-heading">
    <small>QUẢN TRỊ HỆ THỐNG / THÊM SÂN BAY</small>
    <h1>Thêm sân bay mới</h1>
    <p>
        Cập nhật danh sách sân bay phục vụ
        quản lý lịch trình và chuyến bay.
    </p>
</div>

<div class="panel airport-form">
    <div class="panel-header">
        <h2>Thông tin sân bay</h2>

        <a href="{{ route('admin.airports.index') }}"
           class="btn btn-secondary">
            ← Danh sách sân bay
        </a>
    </div>

    <div class="airport-form-note">
        Vui lòng nhập đầy đủ thông tin sân bay.
        Mã sân bay gồm 3 ký tự và không được trùng
        với mã đã tồn tại trên hệ thống.
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

    <form action="{{ route('admin.airports.store') }}"
          method="POST">
        @csrf

        <div class="airport-form-grid">
            <div class="form-group">
                <label for="code">Mã sân bay *</label>

                <input
                    id="code"
                    type="text"
                    name="code"
                    maxlength="3"
                    value="{{ old('code') }}"
                    placeholder="Ví dụ: SGN"
                    autocomplete="off"
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
                    value="{{ old('name') }}"
                    placeholder="Nhập tên sân bay"
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
                    value="{{ old('city') }}"
                    placeholder="Nhập thành phố"
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
                    value="{{ old('country', 'Việt Nam') }}"
                    placeholder="Nhập quốc gia"
                    required
                >

                @error('country')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">
                + Lưu sân bay
            </button>

            <a href="{{ route('admin.airports.index') }}"
               class="btn btn-secondary">
                Hủy và quay lại
            </a>
        </div>
    </form>
</div>

@endsection


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa hồ sơ - SkyBooking</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #243247;
        }

        .container {
            max-width: 650px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 22px #152b4610;
        }

        h1 {
            color: #0756a5;
            margin-top: 0;
        }

        .description {
            color: #718096;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 18px 0 8px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd5e0;
            border-radius: 8px;
            font-size: 15px;
            background: white;
            color: #243247;
        }

        input:focus,
        select:focus {
            outline: 2px solid #a6d1ff;
            border-color: #0756a5;
        }

        .error {
            color: #bd3030;
            font-size: 14px;
            margin-top: 6px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 26px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
        }

        .primary {
            background: #0756a5;
            color: white;
        }

        .secondary {
            background: #eaf2fc;
            color: #0756a5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Chỉnh sửa thông tin cá nhân</h1>

            <p class="description">
                Cập nhật họ tên, email và thông tin cá nhân
                sử dụng cho tài khoản SkyBooking.
            </p>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <label for="name">Họ và tên</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    maxlength="255"
                    required
                >
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="email">Địa chỉ email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    maxlength="255"
                    required
                >
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="phone">Số điện thoại</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    maxlength="20"
                    placeholder="Nhập số điện thoại"
                >
                @error('phone')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="date_of_birth">Ngày sinh</label>
                <input
                    type="date"
                    id="date_of_birth"
                    name="date_of_birth"
                    value="{{ old('date_of_birth', $user->date_of_birth ? \Illuminate\Support\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}"
                    max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                >
                @error('date_of_birth')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="gender">Giới tính</label>
                <select id="gender" name="gender">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam"
                        {{ old('gender', $user->gender) == 'Nam' ? 'selected' : '' }}>
                        Nam
                    </option>
                    <option value="Nữ"
                        {{ old('gender', $user->gender) == 'Nữ' ? 'selected' : '' }}>
                        Nữ
                    </option>
                    <option value="Khác"
                        {{ old('gender', $user->gender) == 'Khác' ? 'selected' : '' }}>
                        Khác
                    </option>
                </select>
                @error('gender')
                    <div class="error">{{ $message }}</div>
                @enderror

                <div class="actions">
                    <button type="submit" class="btn primary">
                        Lưu thay đổi
                    </button>

                    <a href="{{ route('profile.index') }}"
                       class="btn secondary">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - SkyBooking</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f5ff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 420px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #174ea6;
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin: 18px 0 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #174ea6;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #103c80;
        }

        .error {
            color: #d62828;
            margin-top: 8px;
            font-size: 14px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #174ea6;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đặt lại mật khẩu</h2>

        <p>Nhập mật khẩu mới cho tài khoản của bạn.</p>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="error">{{ $error }}</div>
            @endforeach
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <input
                type="hidden"
                name="token"
                value="{{ $token }}"
            >

            <label for="email">Địa chỉ email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $email) }}"
                required
            >

            <label for="password">Mật khẩu mới</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                placeholder="Ít nhất 8 ký tự"
                required
            >

            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                minlength="8"
                placeholder="Nhập lại mật khẩu mới"
                required
            >

            <button type="submit">Cập nhật mật khẩu</button>
        </form>

        <a href="{{ route('login') }}" class="back">
            Quay lại đăng nhập
        </a>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - SkyBooking</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Be Vietnam Pro', Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(214, 173, 85, 0.18), transparent 35%),
                linear-gradient(135deg, #061d3a, #082b55 55%, #0756a5);
        }

        .container {
            width: 100%;
            max-width: 460px;
            padding: 38px 34px;
            background: #fff;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.22);
        }

        .logo {
            margin-bottom: 25px;
            color: #082b55;
            font-size: 25px;
            font-weight: 800;
            text-align: center;
            letter-spacing: -0.8px;
        }

        .logo span {
            color: #d6ad55;
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff7e6;
            color: #b58a36;
            font-size: 29px;
        }

        h2 {
            margin: 0 0 12px;
            color: #082b55;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
        }

        .description {
            margin: 0 0 25px;
            color: #718096;
            font-size: 14px;
            line-height: 1.7;
            text-align: center;
        }

        label {
            display: block;
            margin: 18px 0 9px;
            color: #263b55;
            font-size: 14px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #dce3ec;
            border-radius: 10px;
            outline: none;
            background: #fbfcfe;
            color: #23344d;
            font-family: inherit;
            font-size: 14px;
            transition: 0.2s;
        }

        input:focus {
            border-color: #0756a5;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(7, 86, 165, 0.1);
        }

        input[readonly] {
            background: #f1f4f8;
            color: #718096;
            cursor: not-allowed;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #082b55, #0756a5);
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(8, 43, 85, 0.22);
        }

        .error {
            margin-bottom: 8px;
            padding: 10px 12px;
            border: 1px solid #f3c4c4;
            border-radius: 8px;
            background: #fff1f1;
            color: #c62828;
            font-size: 13px;
            line-height: 1.5;
        }

        .back {
            display: block;
            margin-top: 24px;
            color: #0756a5;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
        }

        .back:hover {
            color: #d6ad55;
        }

        .footer {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #edf0f5;
            color: #9aa5b4;
            font-size: 11px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 22px;
            }

            h2 {
                font-size: 21px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Sky<span>Booking</span></div>

        <div class="icon">🔐</div>

        <h2>Đặt lại mật khẩu</h2>

        <p class="description">
            Hãy tạo mật khẩu mới để tiếp tục sử dụng tài khoản SkyBooking.
            Mật khẩu nên có ít nhất 8 ký tự.
        </p>

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
                autocomplete="email"
                required
            >

            <label for="password">Mật khẩu mới</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
                placeholder="Ít nhất 8 ký tự"
                required
            >

            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                minlength="8"
                autocomplete="new-password"
                placeholder="Nhập lại mật khẩu mới"
                required
            >

            <button type="submit">
                Cập nhật mật khẩu
            </button>
        </form>

        <a href="{{ route('login') }}" class="back">
            ← Quay lại đăng nhập
        </a>

        <div class="footer">
            © {{ date('Y') }} SkyBooking. All rights reserved.
        </div>
    </div>
</body>
</html>
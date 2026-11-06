
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - SkyBooking</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Be Vietnam Pro', Arial, sans-serif;
            background: linear-gradient(135deg, #082b55, #0756a5);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 25px 15px;
        }

        .container {
            background: white;
            width: 440px;
            max-width: 100%;
            padding: 35px 38px;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.2);
        }

        .brand {
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #082b55;
            margin-bottom: 8px;
        }

        .brand span {
            color: #d6ad55;
        }

        .subtitle {
            text-align: center;
            color: #7b8798;
            font-size: 13px;
            margin: 0 0 25px;
        }

        h2 {
            text-align: center;
            color: #082b55;
            font-size: 22px;
            margin: 0 0 23px;
        }

        label {
            display: block;
            margin: 16px 0 8px;
            color: #23344d;
            font-size: 13px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #dce3ec;
            border-radius: 8px;
            outline: none;
            font-family: inherit;
            font-size: 14px;
            transition: .2s;
        }

        input:focus {
            border-color: #0756a5;
            box-shadow: 0 0 0 3px rgba(7, 86, 165, .1);
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #082b55;
            color: white;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
        }

        button:hover {
            background: #d6ad55;
            color: #082b55;
        }

        .error {
            color: #b42318;
            background: #fff0ef;
            border: 1px solid #f5ceca;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .error p {
            margin: 5px 0;
        }

        .link {
            text-align: center;
            margin-top: 22px;
            color: #7b8798;
            font-size: 13px;
        }

        .link a {
            color: #0756a5;
            font-weight: 700;
            text-decoration: none;
        }

        .link a:hover {
            color: #d6ad55;
            text-decoration: underline;
        }

        .back-home {
            text-align: center;
            margin-top: 23px;
            padding-top: 17px;
            border-top: 1px solid #edf0f4;
            font-size: 12px;
        }

        .back-home a {
            color: #7b8798;
            text-decoration: none;
        }

        .back-home a:hover {
            color: #0756a5;
        }

        @media (max-width: 480px) {
            .container {
                padding: 28px 22px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="brand">SKY<span>BOOKING</span></div>

    <p class="subtitle">Tạo tài khoản và bắt đầu hành trình của bạn</p>

    <h2>Đăng ký tài khoản</h2>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <label for="name">Họ và tên</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            placeholder="Nhập họ và tên"
            autocomplete="name"
            required
        >

        <label for="email">Địa chỉ Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Nhập địa chỉ email"
            autocomplete="email"
            required
        >

        <label for="password">Mật khẩu</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Tạo mật khẩu (ít nhất 8 ký tự)"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <label for="password_confirmation">Xác nhận mật khẩu</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            placeholder="Nhập lại mật khẩu"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <button type="submit">Đăng ký ngay</button>
    </form>

    <div class="link">
        Đã có tài khoản?
        <a href="{{ route('login') }}">Đăng nhập</a>
    </div>

    <div class="back-home">
        <a href="/">← Quay lại trang chủ</a>
    </div>

</div>

</body>
</html>

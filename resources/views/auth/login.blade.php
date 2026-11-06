
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SkyBooking</title>

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
            padding: 20px;
        }

        .container {
            background: white;
            width: 420px;
            max-width: 100%;
            padding: 38px;
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
            margin-bottom: 28px;
        }

        h2 {
            text-align: center;
            color: #082b55;
            font-size: 23px;
            margin: 0 0 25px;
        }

        label {
            display: block;
            margin: 17px 0 8px;
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
            background: #082b55;
            color: white;
            border: none;
            border-radius: 8px;
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

        .success {
            color: #16764b;
            background: #eaf8f0;
            border: 1px solid #c6ead5;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .link {
            text-align: center;
            margin-top: 19px;
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
            margin-top: 25px;
            padding-top: 18px;
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

    <p class="subtitle">Đồng hành cùng bạn trên mọi hành trình</p>

    <h2>Đăng nhập</h2>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf

        <label for="email">Địa chỉ Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Nhập email của bạn"
            autocomplete="email"
            required
        >

        <label for="password">Mật khẩu</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Nhập mật khẩu"
            autocomplete="current-password"
            required
        >

        <button type="submit">Đăng nhập</button>
    </form>

    <div class="link">
        <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
    </div>

    <div class="link">
        Chưa có tài khoản?
        <a href="{{ route('register') }}">Đăng ký ngay</a>
    </div>

    <div class="back-home">
        <a href="/">← Quay lại trang chủ</a>
    </div>

</div>

</body>
</html>

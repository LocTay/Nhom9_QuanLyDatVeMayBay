
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f5ff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            width: 400px;
            max-width: 92%;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px #00000015;
        }

        h2 {
            text-align: center;
            color: #0756a8;
        }

        label {
            display: block;
            margin: 15px 0 6px;
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            background: #0756a8;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .error {
            color: #d00;
        }

        .link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đăng nhập</h2>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <label>Email</label>
            <input type="email" name="email"
                   value="{{ old('email') }}" required>

            <label>Mật khẩu</label>
            <input type="password" name="password" required>

            <button type="submit">Đăng nhập</button>
        </form>

        
<div class="link">
    <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
</div>

<div class="link">
    Chưa có tài khoản?
    <a href="{{ route('register') }}">Đăng ký ngay</a>
</div>

    </div>
</body>
</html>

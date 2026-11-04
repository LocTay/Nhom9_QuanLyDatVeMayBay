
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>

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
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 14px 0 6px;
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
            border: none;
            border-radius: 6px;
            background: #0756a8;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #064582;
        }

        .error {
            color: #d00;
            font-size: 14px;
            margin-top: 5px;
        }

        .success {
            color: #087a31;
            margin-bottom: 12px;
        }

        .link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
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

            <label>Họ và tên</label>
            <input type="text" name="name"
                   value="{{ old('name') }}" required>

            <label>Email</label>
            <input type="email" name="email"
                   value="{{ old('email') }}" required>

            <label>Mật khẩu</label>
            <input type="password" name="password"
                   minlength="8" required>

            <label>Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation"
                   minlength="8" required>

            <button type="submit">Đăng ký</button>
        </form>

        <div class="link">
            Đã có tài khoản?
            <a href="{{ route('login') }}">Đăng nhập</a>
        </div>
    </div>
</body>
</html>

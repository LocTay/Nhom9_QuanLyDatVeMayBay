<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản của tôi - SkyBooking</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #243247;
        }

        header {
            background: #0756a5;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
        }

        header a {
            color: white;
            text-decoration: none;
            margin-left: 18px;
        }

        .container {
            max-width: 1000px;
            margin: 35px auto;
            padding: 0 18px;
        }

        .welcome {
            margin-bottom: 24px;
        }

        .welcome h1 {
            color: #0756a5;
            margin-bottom: 8px;
        }

        .profile-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 5px 22px #152b4610;
            margin-bottom: 22px;
        }

        .user-header {
            display: flex;
            align-items: center;
            gap: 18px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e5eaf1;
        }

        .avatar {
            width: 78px;
            height: 78px;
            background: #e0efff;
            color: #0756a5;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .user-header h2 {
            margin-bottom: 8px;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-block;
            color: #18794e;
            background: #e5f7ed;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            margin-top: 10px;
        }

        .section-title {
            font-size: 19px;
            margin: 24px 0 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .info-item span {
            display: block;
            color: #718096;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .info-item strong {
            overflow-wrap: anywhere;
        }

        .not-updated {
            color: #8a94a6;
            font-weight: normal;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #0756a5;
            color: white;
        }

        .btn-light {
            background: #eaf2fc;
            color: #0756a5;
        }

        .btn-logout {
            background: #fff0f0;
            color: #bd3030;
        }

        .success {
            background: #e5f7ed;
            color: #18794e;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        footer {
            text-align: center;
            color: #718096;
            padding: 25px;
        }

        @media (max-width: 600px) {
            header {
                padding: 16px 5%;
                align-items: flex-start;
                flex-direction: column;
            }

            header a {
                margin-left: 0;
                margin-right: 15px;
                display: inline-block;
                margin-top: 8px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .profile-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">✈ SkyBooking</div>

        <nav>
            <a href="{{ auth()->check() && auth()->user()->role === 'admin'
    ? route('admin.dashboard')
    : url('/') }}">
                Trang chủ
            </a>
            <a href="{{ route('profile.index') }}">Tài khoản của tôi</a>
        </nav>
    </header>

    <main class="container">
        <div class="welcome">
            <h1>Tài khoản của tôi</h1>
            <p>Quản lý thông tin cá nhân và tài khoản SkyBooking của bạn.</p>
        </div>

        @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
        @endif

        <section class="profile-card">
            <div class="user-header">
                <div class="avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }}</p>
                    <span class="status">● Tài khoản đang hoạt động</span>
                </div>
            </div>

            <h3 class="section-title">Thông tin cá nhân</h3>

            <div class="info-grid">
                <div class="info-item">
                    <span>Họ và tên</span>
                    <strong>{{ $user->name }}</strong>
                </div>

                <div class="info-item">
                    <span>Địa chỉ email</span>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div class="info-item">
                    <span>Số điện thoại</span>
                    <strong>
                        @if ($user->phone)
                        {{ $user->phone }}
                        @else
                        <span class="not-updated">Chưa cập nhật</span>
                        @endif
                    </strong>
                </div>

                <div class="info-item">
                    <span>Ngày sinh</span>
                    <strong>
                        @if ($user->date_of_birth)
                        {{ \Illuminate\Support\Carbon::parse($user->date_of_birth)->format('d/m/Y') }}
                        @else
                        <span class="not-updated">Chưa cập nhật</span>
                        @endif
                    </strong>
                </div>

                <div class="info-item">
                    <span>Giới tính</span>
                    <strong>
                        @if ($user->gender)
                        {{ $user->gender }}
                        @else
                        <span class="not-updated">Chưa cập nhật</span>
                        @endif
                    </strong>
                </div>

                <div class="info-item">
                    <span>Mã tài khoản</span>
                    <strong>SB{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </div>

                <div class="info-item">
                    <span>Ngày tạo tài khoản</span>
                    <strong>
                        {{ $user->created_at
                            ? $user->created_at->format('d/m/Y H:i')
                            : 'Chưa có thông tin' }}
                    </strong>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('profile.edit') }}" class="btn btn-light">
                    ✏️ Chỉnh sửa thông tin
                </a>

                <a href="{{ auth()->check() && auth()->user()->role === 'admin'
    ? route('admin.dashboard')
    : url('/') }}" class="back">
                    ← Quay lại trang chủ
                </a>

                <a href="{{ route('password.request') }}" class="btn btn-light">
                    Đổi mật khẩu / Quên mật khẩu
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-logout">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        © {{ date('Y') }} SkyBooking - Đồng hành cùng mọi chuyến bay.
    </footer>
</body>

</html>
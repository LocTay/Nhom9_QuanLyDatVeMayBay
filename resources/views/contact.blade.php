<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - SkyBooking</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Be Vietnam Pro', Arial, sans-serif;
            background: #f3f6fa;
            color: #23344d;
        }

        a {
            text-decoration: none;
        }

        .navbar {
            min-height: 76px;
            padding: 0 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #082b55;
            box-shadow: 0 4px 15px rgba(0,0,0,.12);
        }

        .logo {
            color: #fff;
            font-size: 25px;
            font-weight: 800;
        }

        .logo span {
            color: #d6ad55;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-links a {
            color: #e5edf7;
            font-size: 13px;
            font-weight: 600;
            transition: .2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #e6bf68;
        }

        .nav-links .home-btn {
            padding: 10px 17px;
            border: 1px solid #d6ad55;
            border-radius: 8px;
            color: #f2d58f;
        }

        .hero {
            padding: 75px 20px 85px;
            text-align: center;
            color: #fff;
            background:
                radial-gradient(circle at 15% 20%, rgba(214,173,85,.18), transparent 30%),
                linear-gradient(135deg, #061d3a, #082b55 60%, #0756a5);
        }

        .hero-label {
            margin-bottom: 15px;
            color: #e6bf68;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
        }

        .hero h1 {
            margin-bottom: 16px;
            font-size: 38px;
            font-weight: 800;
        }

        .hero p {
            max-width: 650px;
            margin: auto;
            color: #dce7f5;
            font-size: 14px;
            line-height: 1.8;
        }

        .main {
            width: 90%;
            max-width: 1150px;
            margin: -35px auto 70px;
            position: relative;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: .85fr 1.4fr;
            gap: 25px;
            align-items: stretch;
        }

        .info-card,
        .form-card {
            padding: 35px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 12px 35px rgba(8,43,85,.08);
        }

        .info-card {
            color: #fff;
            background: linear-gradient(150deg, #082b55, #0756a5);
        }

        .info-card h2,
        .form-card h2 {
            margin-bottom: 12px;
            font-size: 22px;
            font-weight: 800;
        }

        .info-intro {
            margin-bottom: 32px;
            color: #dce7f5;
            font-size: 13px;
            line-height: 1.8;
        }

        .info-item {
            display: flex;
            gap: 15px;
            margin-bottom: 27px;
            align-items: flex-start;
        }

        .info-icon {
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: rgba(255,255,255,.13);
            color: #f2d58f;
            font-size: 19px;
        }

        .info-item h3 {
            margin: 2px 0 7px;
            color: #f2d58f;
            font-size: 13px;
        }

        .info-item p {
            color: #e1eaf5;
            font-size: 12px;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .info-note {
            margin-top: 35px;
            padding: 17px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 11px;
            color: #e1eaf5;
            font-size: 12px;
            line-height: 1.8;
        }

        .form-card h2 {
            color: #082b55;
        }

        .form-intro {
            margin-bottom: 25px;
            color: #8190a3;
            font-size: 13px;
            line-height: 1.7;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            margin-bottom: 19px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            color: #263b55;
            font-size: 13px;
            font-weight: 700;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #dce3ec;
            border-radius: 9px;
            outline: none;
            background: #fbfcfe;
            color: #23344d;
            font-family: inherit;
            font-size: 13px;
            transition: .2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #0756a5;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(7,86,165,.09);
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #082b55, #0756a5);
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(8,43,85,.2);
        }

        .alert {
            margin-bottom: 20px;
            padding: 13px 15px;
            border-radius: 9px;
            font-size: 13px;
            line-height: 1.7;
        }

        .alert-success {
            border: 1px solid #b7e4c7;
            background: #edfff3;
            color: #16803c;
        }

        .alert-error {
            border: 1px solid #f3c4c4;
            background: #fff1f1;
            color: #c62828;
        }

        .footer {
            padding: 30px 20px;
            background: #061d3a;
            color: #aebdd0;
            text-align: center;
            font-size: 12px;
            line-height: 1.9;
        }

        .footer strong {
            color: #f2d58f;
            font-size: 17px;
        }

        @media (max-width: 800px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 18px 5%;
                flex-wrap: wrap;
                gap: 15px;
            }

            .nav-links {
                gap: 15px;
                flex-wrap: wrap;
            }

            .hero h1 {
                font-size: 29px;
            }
        }

        @media (max-width: 520px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .info-card,
            .form-card {
                padding: 25px 20px;
            }

            .nav-links {
                gap: 12px;
            }

            .nav-links a {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

    <!-- THANH DIEU HUONG -->
    <header class="navbar">
        <a href="{{ url('/') }}" class="logo">
            ✈ Sky<span>Booking</span>
        </a>

        <nav class="nav-links">
            <a href="{{ url('/') }}">Trang chủ</a>
            <a href="{{ route('flights.search') }}">Tìm chuyến bay</a>
            <a href="{{ route('ticket.lookup.form') }}">Tra cứu vé</a>
            <a href="{{ route('contact') }}" class="active">Liên hệ</a>
            <a href="{{ route('login') }}" class="home-btn">Đăng nhập</a>
        </nav>
    </header>

    <!-- BANNER -->
    <section class="hero">
        <div class="hero-label">SKYBOOKING SUPPORT</div>
        <h1>Liên hệ với chúng tôi</h1>
        <p>
            SkyBooking luôn sẵn sàng lắng nghe ý kiến và hỗ trợ bạn.
            Hãy gửi câu hỏi hoặc góp ý để chúng tôi phục vụ bạn tốt hơn.
        </p>
    </section>

    <!-- NOI DUNG -->
    <main class="main">
        <div class="contact-grid">

            <!-- THONG TIN LIEN HE -->
            <section class="info-card">
                <h2>Thông tin hỗ trợ</h2>

                <p class="info-intro">
                    Bạn cần hỗ trợ về tìm kiếm chuyến bay, đặt vé hoặc tài khoản?
                    Hãy gửi thông tin qua biểu mẫu bên cạnh.
                </p>

                <div class="info-item">
                    <div class="info-icon">✉</div>
                    <div>
                        <h3>Email hỗ trợ</h3>
                        <p>Gửi yêu cầu qua biểu mẫu liên hệ.</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">✈</div>
                    <div>
                        <h3>Dịch vụ</h3>
                        <p>Tìm kiếm chuyến bay và quản lý đặt vé trực tuyến.</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">◷</div>
                    <div>
                        <h3>Hỗ trợ</h3>
                        <p>Chúng tôi sẽ tiếp nhận và phản hồi yêu cầu của bạn qua email.</p>
                    </div>
                </div>

                <div class="info-note">
                    ✦ SkyBooking – Hành trình của bạn, khởi đầu từ đây.
                </div>
            </section>

            <!-- FORM LIEN HE -->
            <section class="form-card">
                <h2>Gửi lời nhắn cho chúng tôi</h2>

                <p class="form-intro">
                    Vui lòng điền đầy đủ thông tin bên dưới.
                    Các trường có dấu * là bắt buộc.
                </p>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <strong>Vui lòng kiểm tra lại:</strong>
                        <ul style="padding-left: 20px; margin-top: 7px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Họ và tên *</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Nhập họ và tên"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="example@gmail.com"
                                maxlength="255"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="Nhập số điện thoại"
                                maxlength="20"
                            >
                        </div>

                        <div class="form-group">
                            <label for="subject">Chủ đề *</label>
                            <select id="subject" name="subject" required>
                                <option value="">-- Chọn chủ đề --</option>
                                <option value="Hỗ trợ đặt vé" {{ old('subject') == 'Hỗ trợ đặt vé' ? 'selected' : '' }}>Hỗ trợ đặt vé</option>
                                <option value="Tra cứu vé" {{ old('subject') == 'Tra cứu vé' ? 'selected' : '' }}>Tra cứu vé</option>
                                <option value="Tài khoản" {{ old('subject') == 'Tài khoản' ? 'selected' : '' }}>Tài khoản</option>
                                <option value="Góp ý dịch vụ" {{ old('subject') == 'Góp ý dịch vụ' ? 'selected' : '' }}>Góp ý dịch vụ</option>
                                <option value="Khác" {{ old('subject') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Nội dung liên hệ *</label>
                        <textarea
                            id="message"
                            name="message"
                            maxlength="5000"
                            placeholder="Nhập nội dung bạn muốn gửi đến SkyBooking..."
                            required
                        >{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        ✉ Gửi thông tin liên hệ
                    </button>
                </form>
            </section>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <strong>✈ SkyBooking</strong>
        <div>Hệ thống quản lý và đặt vé máy bay trực tuyến.</div>
        <div>© {{ date('Y') }} SkyBooking. Dự án học tập quản lý đặt vé máy bay.</div>
        <div><a href="{{ url('/') }}" style="color:#f2d58f;">Quay lại trang chủ</a></div>
    </footer>

</body>
</html>

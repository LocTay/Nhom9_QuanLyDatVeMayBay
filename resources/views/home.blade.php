<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SkyBooking | Đặt vé máy bay trực tuyến</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #062b55;
            --navy-light: #0b4278;
            --blue: #1267a5;
            --gold: #d7ae58;
            --gold-light: #f5e5bd;
            --bg: #f4f7fb;
            --text: #1c2f46;
            --muted: #77869a;
            --border: #e4eaf2;
            --white: #ffffff;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 25px;
        }

        body {
            font-family: 'Be Vietnam Pro', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select {
            font: inherit;
        }

        button,
        a,
        input,
        select {
            -webkit-tap-highlight-color: transparent;
        }

        /* THANH ĐIỀU HƯỚNG BÊN TRÁI */

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #06264b, #073a6b 75%, #052544);
            color: white;
            padding: 30px 18px 20px;
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow-y: auto;
            box-shadow: 8px 0 30px rgba(6, 43, 85, 0.08);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 28px;
            border-bottom: 1px solid rgba(255, 255, 255, .13);
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: var(--gold);
            color: var(--navy);
            font-size: 24px;
            box-shadow: 0 5px 18px rgba(215, 174, 88, .2);
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.8px;
        }

        .brand-name span {
            color: var(--gold);
        }

        .brand-caption {
            margin-top: 4px;
            font-size: 10px;
            color: #b8cce0;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .nav-caption {
            margin: 28px 12px 12px;
            color: #8da9c4;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .side-nav {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .side-link {
            min-height: 47px;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            color: #d3e0ed;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            transition: background .2s, color .2s, transform .2s;
        }

        .side-link:hover {
            color: white;
            background: rgba(255, 255, 255, .09);
            transform: translateX(2px);
        }

        .side-link.active {
            color: white;
            background: linear-gradient(100deg, rgba(215, 174, 88, .24), rgba(215, 174, 88, .08));
            box-shadow: inset 3px 0 var(--gold);
        }

        .side-icon {
            width: 22px;
            text-align: center;
            font-size: 18px;
        }

        .sidebar-spacer {
            flex: 1;
            min-height: 25px;
        }

        .sidebar-promo {
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .06);
            border-radius: 14px;
            padding: 17px 15px;
            margin-top: 25px;
        }

        .sidebar-promo .promo-icon {
            color: var(--gold);
            font-size: 24px;
            margin-bottom: 10px;
        }

        .sidebar-promo h3 {
            font-size: 13px;
            margin-bottom: 8px;
        }

        .sidebar-promo p {
            font-size: 11px;
            line-height: 1.8;
            color: #c1d2e3;
        }

        .sidebar-bottom {
            margin-top: 20px;
            padding: 15px 10px 0;
            border-top: 1px solid rgba(255, 255, 255, .12);
            color: #9fb6cc;
            font-size: 10px;
            line-height: 1.9;
        }

        /* KHU VỰC NỘI DUNG */

        .main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            min-width: 0;
        }

        .topbar {
            min-height: 82px;
            padding: 17px 4.5%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 1px solid var(--border);
        }

        .topbar-eyebrow {
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 5px;
        }

        .topbar-title {
            font-size: 17px;
            color: var(--navy);
            font-weight: 700;
        }

        .account-area {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .welcome {
            font-size: 12px;
            color: #566a80;
            margin-right: 6px;
        }

        .top-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            min-height: 39px;
            padding: 10px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--navy);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .top-button:hover {
            border-color: var(--gold);
            background: #fffaf0;
        }

        .top-button.primary {
            color: white;
            background: var(--navy);
            border-color: var(--navy);
        }

        .top-button.primary:hover {
            background: var(--navy-light);
        }

        .logout-form {
            display: inline-flex;
            margin: 0;
        }

        /* BANNER */

        .hero {
            position: relative;
            overflow: hidden;
            margin: 28px 4.5% 0;
            min-height: 285px;
            border-radius: 20px;
            padding: 43px 43px;
            display: flex;
            align-items: center;
            color: white;
            background:
                radial-gradient(ellipse at 90% 15%, rgba(83, 155, 205, .4), transparent 35%),
                radial-gradient(ellipse at 75% 100%, rgba(215, 174, 88, .18), transparent 35%),
                linear-gradient(115deg, #05264a 0%, #084779 58%, #126b9f 100%);
            box-shadow: 0 14px 35px rgba(6, 43, 85, .12);
        }

        .hero::before {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            right: 2%;
            top: -135px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 50%;
        }

        .hero::after {
            content: "";
            position: absolute;
            width: 245px;
            height: 245px;
            right: 8%;
            top: -87px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 650px;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #f2dba5;
            border: 1px solid rgba(245, 229, 189, .3);
            background: rgba(255, 255, 255, .06);
            border-radius: 30px;
            padding: 8px 13px;
            margin-bottom: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(27px, 3vw, 39px);
            line-height: 1.35;
            letter-spacing: -1.2px;
            max-width: 600px;
            margin-bottom: 15px;
        }

        .hero h1 span {
            color: var(--gold);
        }

        .hero p {
            color: #d5e5f3;
            line-height: 1.9;
            font-size: 13px;
            max-width: 530px;
        }

        .hero-note {
            margin-top: 22px;
            display: flex;
            align-items: center;
            gap: 9px;
            color: #e9f1f8;
            font-size: 11px;
        }

        .hero-note span {
            color: var(--gold);
            font-size: 17px;
        }

        /* FORM TÌM CHUYẾN BAY */

        .booking {
            position: relative;
            z-index: 3;
            margin: 28px 4.5% 0;
            padding: 30px;
            border-radius: 17px;
            background: white;
            border: 1px solid #eaf0f6;
            box-shadow: 0 12px 38px rgba(16, 45, 78, .06);
        }

        .section-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 24px;
        }

        .section-kicker {
            color: #a37b2e;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.7px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .section-heading h2 {
            color: var(--navy);
            font-size: 22px;
            letter-spacing: -.5px;
        }

        .section-heading p {
            margin-top: 8px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.7;
        }

        .flight-tag {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 7px;
            padding: 9px 12px;
            color: #6e5422;
            background: #fcf5e6;
            font-size: 10px;
            font-weight: 700;
        }

        .trip-type {
            display: flex;
<<<<<<< HEAD
            gap: 24px;
            margin-bottom: 25px;
        }

        .trip-type label {
            cursor: pointer;
=======
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .trip-option {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 12px 16px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: .2s;
        }

        .trip-option:has(input:checked) {
            border-color: var(--navy);
            color: var(--navy);
            background: #f0f6fc;
        }

        .trip-option input {
            accent-color: var(--navy);
>>>>>>> 6ce392736ef507a26f5ead9550d5984dea71a065
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 21px;
        }

        .field {
            min-width: 0;
        }

        .field label {
            display: block;
            color: #40536a;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 9px;
        }

        .field-control {
            width: 100%;
            min-height: 48px;
            border: 1px solid #dce4ee;
            border-radius: 9px;
            background: #fff;
            color: var(--text);
            padding: 12px 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .field-control:focus {
            border-color: #3982b9;
            box-shadow: 0 0 0 3px rgba(18, 103, 165, .09);
        }

        .field-control:disabled {
            background: #f1f4f8;
            color: #96a1af;
            cursor: not-allowed;
        }

        .field-hint {
            margin-top: 7px;
            color: #93a0af;
            font-size: 10px;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            border-top: 1px solid #edf1f6;
            margin-top: 25px;
            padding-top: 22px;
        }

        .form-assurance {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.7;
        }

        .search-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            min-width: 210px;
            min-height: 49px;
            padding: 13px 22px;
            border: 0;
            border-radius: 9px;
            color: var(--navy);
            background: linear-gradient(135deg, #f2d38b, #d7ae58);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(215, 174, 88, .2);
            transition: transform .2s, box-shadow .2s;
        }

        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 21px rgba(215, 174, 88, .3);
        }

        .search-button span {
            font-size: 19px;
        }

        /* DỊCH VỤ */

        .services {
            margin: 42px 4.5% 0;
        }

        .services-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .services-header h2 {
            font-size: 21px;
            color: var(--navy);
        }

        .services-header p {
            color: var(--muted);
            font-size: 11px;
            margin-top: 7px;
            line-height: 1.7;
        }

        .services-link {
            color: var(--blue);
            font-weight: 700;
            font-size: 11px;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .service-card {
            min-width: 0;
            background: white;
            border: 1px solid #e9eef5;
            border-radius: 14px;
            padding: 23px 21px;
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }

        .service-card:hover {
            transform: translateY(-4px);
            border-color: #e2c47e;
            box-shadow: 0 12px 28px rgba(16, 45, 78, .07);
        }

        .service-icon {
            display: grid;
            place-items: center;
            width: 47px;
            height: 47px;
            border-radius: 13px;
            background: #edf4fb;
            color: var(--navy);
            font-size: 23px;
            margin-bottom: 18px;
        }

        .service-card:nth-child(2) .service-icon {
            background: #fbf4e5;
        }

        .service-card:nth-child(3) .service-icon {
            background: #e9f5f1;
        }

        .service-card h3 {
            color: var(--navy);
            font-size: 13px;
            margin-bottom: 10px;
        }

        .service-card p {
            color: var(--muted);
            font-size: 11px;
            line-height: 1.9;
            min-height: 42px;
        }

        .service-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 17px;
            color: var(--blue);
            font-size: 11px;
            font-weight: 700;
        }

        .service-action:hover {
            color: #a37b2e;
        }

        /* CHÂN TRANG */

        .footer {
            margin-top: 48px;
            background: #061f3c;
            color: #cfdae7;
            padding: 29px 4.5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 10px;
            line-height: 1.8;
        }

        .footer-brand {
            color: white;
            font-weight: 800;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .footer-brand span {
            color: var(--gold);
        }

        .footer-note {
            color: #9db2c8;
            text-align: right;
        }

        /* THÔNG BÁO */

        .notice {
            margin: 20px 4.5% 0;
            padding: 13px 16px;
            border-radius: 9px;
            font-size: 12px;
            line-height: 1.7;
        }

        .notice.success {
            color: #176442;
            background: #e9f8f0;
            border: 1px solid #bde8d0;
        }

        .notice.error {
            color: #a12c2c;
            background: #fff0f0;
            border: 1px solid #f5cccc;
        }

        /* RESPONSIVE */

        @media (max-width: 1100px) {
            :root {
                --sidebar-width: 215px;
            }

            .sidebar {
                padding-left: 13px;
                padding-right: 13px;
            }

            .hero {
                padding: 36px 30px;
            }

            .booking {
                padding: 24px;
            }

            .service-grid {
                gap: 12px;
            }

            .service-card {
                padding: 19px 16px;
            }
        }

        @media (max-width: 760px) {
            :root {
                --sidebar-width: 0px;
            }

            .sidebar {
                position: relative;
                inset: auto;
                width: 100%;
                padding: 13px 16px;
                overflow: visible;
            }

            .brand {
                padding: 0 4px 13px;
            }

            .brand-icon {
                width: 38px;
                height: 38px;
                font-size: 21px;
            }

            .brand-name {
                font-size: 19px;
            }

            .nav-caption,
            .sidebar-promo,
            .sidebar-spacer,
            .sidebar-bottom {
                display: none;
            }

            .side-nav {
                flex-direction: row;
                overflow-x: auto;
                gap: 7px;
                padding-top: 12px;
                scrollbar-width: thin;
            }

            .side-link {
                flex: 0 0 auto;
                min-height: 40px;
                padding: 9px 11px;
                gap: 7px;
                font-size: 11px;
                white-space: nowrap;
            }

            .side-link.active {
                box-shadow: inset 0 -2px var(--gold);
            }

            .side-icon {
                width: auto;
                font-size: 15px;
            }

            .topbar {
                padding: 16px 5%;
                min-height: 72px;
            }

            .topbar-title {
                font-size: 14px;
            }

            .topbar-eyebrow {
                font-size: 10px;
            }

            .welcome {
                display: none;
            }

            .top-button {
                min-height: 35px;
                padding: 8px 10px;
                font-size: 10px;
            }

            .hero {
                margin: 19px 4% 0;
                padding: 30px 23px;
                min-height: 260px;
                border-radius: 15px;
            }

            .hero h1 {
                font-size: 28px;
            }

            .hero p {
                font-size: 12px;
            }

            .booking {
                margin: 20px 4% 0;
                padding: 22px 18px;
            }

            .section-heading h2 {
                font-size: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 17px;
            }

            .form-footer {
                align-items: stretch;
            }

            .search-button {
                width: 100%;
            }

            .services {
                margin: 32px 4% 0;
            }

            .service-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .service-card {
                padding: 19px;
            }

            .service-card p {
                min-height: 0;
            }

            .service-icon {
                margin-bottom: 13px;
            }

            .footer {
                margin-top: 35px;
                padding: 24px 5%;
            }

            .footer-note {
                text-align: left;
            }
        }

        @media (max-width: 400px) {
            .topbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
            }

            .account-area {
                justify-content: flex-start;
            }

            .hero h1 {
                font-size: 25px;
            }

            .booking {
                padding: 18px 14px;
            }
        }
    </style>
</head>

<body>

    <!-- THANH ĐIỀU HƯỚNG DỌC -->
    <aside class="sidebar">
        <a href="/" class="brand">
            <div class="brand-icon">✈</div>
            <div>
                <div class="brand-name">Sky<span>Booking</span></div>
                <div class="brand-caption">Your journey begins</div>
            </div>
        </a>

        <div class="nav-caption">Điều hướng chính</div>

        <nav class="side-nav">
            <a href="/" class="side-link active">
                <span class="side-icon">⌂</span>
                <span>Trang chủ</span>
            </a>

            <a href="#datve" class="side-link">
                <span class="side-icon">✈</span>
                <span>Tìm chuyến bay</span>
            </a>

            <a href="{{ route('ticket.lookup.form') }}" class="side-link">
                <span class="side-icon">▤</span>
                <span>Tra cứu vé</span>
            </a>

            <a href="#tienich" class="side-link">
                <span class="side-icon">✧</span>
                <span>Dịch vụ</span>
            </a>

            @auth
            <a href="{{ route('profile.index') }}" class="side-link">
                <span class="side-icon">♙</span>
                <span>Tài khoản của tôi</span>
            </a>
            @endauth

            @if (auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.airports.index') }}" class="side-link">
                <span class="side-icon">✧</span>
                <span>Quản lý sân bay</span>
            </a>
            @endif

            <a href="#footer" class="side-link">
                <span class="side-icon">☏</span>
                <span>Liên hệ</span>
            </a>
        </nav>

        <div class="sidebar-spacer"></div>

        <div class="sidebar-promo">
            <div class="promo-icon">✦</div>
            <h3>Khám phá hành trình mới</h3>
            <p>Lựa chọn chuyến bay phù hợp và chuẩn bị cho trải nghiệm đáng nhớ tiếp theo của bạn.</p>
        </div>

        <div class="sidebar-bottom">
            SKYBOOKING<br>
            Hệ thống đặt vé máy bay trực tuyến
        </div>
    </aside>

    <!-- NỘI DUNG CHÍNH -->
    <main class="main">

        <!-- THANH TRÊN -->
        <header class="topbar">
            <div>
                <div class="topbar-eyebrow">CHÀO MỪNG BẠN ĐẾN VỚI SKYBOOKING</div>
                <div class="topbar-title">Hành trình của bạn, khởi đầu từ đây</div>
            </div>

            <div class="account-area">
                @guest
                <a href="{{ route('login') }}" class="top-button">
                    Đăng nhập
                </a>

                <a href="{{ route('register') }}" class="top-button primary">
                    Đăng ký tài khoản
                </a>
                @else
                <span class="welcome">
                    Xin chào, <strong>{{ Auth::user()->name }}</strong>
                </span>

                <a href="{{ route('profile.index') }}" class="top-button">
                    ♙ Tài khoản
                </a>

                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="top-button primary">
                        Đăng xuất
                    </button>
                </form>
                @endguest
            </div>
        </header>

        @if (session('success'))
        <div class="notice success">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="notice error">
            @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <!-- BANNER CHÀO MỪNG -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-label">
                    <span>✦</span>
                    <span>TRẢI NGHIỆM BAY CỦA BẠN</span>
                </div>

                <h1>
                    Chạm đến những chân trời
                    <span>mới.</span>
                </h1>

                <p>
                    Tìm kiếm hành trình lý tưởng, lựa chọn chuyến bay phù hợp
                    và bắt đầu khám phá thế giới cùng SkyBooking.
                </p>

                <div class="hero-note">
                    <span>✧</span>
                    Đơn giản trong từng thao tác · Chủ động trong mỗi hành trình
                </div>
            </div>
        </section>

        <!-- FORM TÌM CHUYẾN BAY -->
        <section class="booking" id="datve">
            <div class="section-heading">
                <div>
                    <div class="section-kicker">FLIGHT SEARCH</div>
                    <h2>Tìm chuyến bay của bạn</h2>
                    <p>Điền thông tin hành trình để tìm chuyến bay phù hợp.</p>
                </div>

                <div class="flight-tag">
                    <span>✈</span>
                    ĐẶT VÉ TRỰC TUYẾN
                </div>
            </div>

            <form id="formTimChuyenBay" action="{{ route('flights.search') }}" method="GET">
                <div class="trip-type">
                    <label class="trip-option">
                        <input type="radio" name="loai_chuyen" value="mot_chieu" checked>
                        Một chiều
                    </label>

                    <label class="trip-option">
                        <input type="radio" name="loai_chuyen" value="khu_hoi">
                        Khứ hồi
                    </label>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="diem_di">ĐIỂM KHỞI HÀNH</label>
                        <select class="field-control" id="diem_di" name="diem_di" required>
                            <option value="">Chọn thành phố khởi hành</option>
                            @foreach ($sanBays as $sanBay)
                            <option value="{{ $sanBay->code }}">
                                {{ $sanBay->city }} ({{ $sanBay->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="diem_den">ĐIỂM ĐẾN</label>
                        <select class="field-control" id="diem_den" name="diem_den" required>
                            <option value="">Chọn thành phố bạn muốn đến</option>
                            @foreach ($sanBays as $sanBay)
                            <option value="{{ $sanBay->code }}">
                                {{ $sanBay->city }} ({{ $sanBay->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="ngay_di">NGÀY KHỞI HÀNH</label>
                        <input
                            class="field-control"
                            type="date"
                            id="ngay_di"
                            name="ngay_di"
                            required>
                    </div>

                    <div class="field" id="ngay_ve_field">
                        <label for="ngay_ve">NGÀY TRỞ VỀ</label>
                        <input
                            class="field-control"
                            type="date"
                            id="ngay_ve"
                            name="ngay_ve"
                            disabled>
                        <div class="field-hint">Chỉ áp dụng cho hành trình khứ hồi.</div>
                    </div>

                    <div class="field">
                        <label for="hanh_khach">SỐ HÀNH KHÁCH</label>
                        <select class="field-control" id="hanh_khach" name="hanh_khach">
                            <option value="1">1 hành khách</option>
                            <option value="2">2 hành khách</option>
                            <option value="3">3 hành khách</option>
                            <option value="4">4 hành khách</option>
                            <option value="5">5 hành khách</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="hang_ve">HẠNG VÉ</label>
                        <select class="field-control" id="hang_ve" name="hang_ve">
                            <option value="pho_thong">Phổ thông</option>
                            <option value="pho_thong_dac_biet">Phổ thông đặc biệt</option>
                            <option value="thuong_gia">Thương gia</option>
                        </select>
                    </div>
                </div>

                <div class="form-footer">
                    <div class="form-assurance">
                        <span>🔒</span>
                        <span>Thông tin hành trình được gửi qua hệ thống<br>đặt vé SkyBooking.</span>
                    </div>

                    <button type="submit" class="search-button">
                        Tìm chuyến bay
                        <span>→</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- CÁC DỊCH VỤ -->
        <section class="services" id="tienich">
            <div class="services-header">
                <div>
                    <h2>Dịch vụ dành cho bạn</h2>
                    <p>Mọi tiện ích cần thiết cho hành trình trong một nơi.</p>
                </div>

                <a href="{{ route('ticket.lookup.form') }}" class="services-link">
                    Khám phá dịch vụ →
                </a>
            </div>

            <div class="service-grid">
                <article class="service-card">
                    <div class="service-icon">✈</div>
                    <h3>Tìm kiếm chuyến bay</h3>
                    <p>Lựa chọn điểm đi, điểm đến và ngày bay theo kế hoạch của bạn.</p>
                    <a href="#datve" class="service-action">
                        Tìm chuyến bay <span>→</span>
                    </a>
                </article>

                <article class="service-card">
                    <div class="service-icon">▤</div>
                    <h3>Tra cứu và quản lý vé</h3>
                    <p>Tra cứu thông tin đặt chỗ và xem chi tiết vé theo mã đặt vé.</p>
                    <a href="{{ route('ticket.lookup.form') }}" class="service-action">
                        Tra cứu vé <span>→</span>
                    </a>
                </article>

                <article class="service-card">
                    <div class="service-icon">♙</div>
                    <h3>Quản lý tài khoản</h3>
                    <p>Cập nhật thông tin cá nhân và quản lý thông tin tài khoản.</p>

                    @auth
                    <a href="{{ route('profile.index') }}" class="service-action">
                        Xem tài khoản <span>→</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="service-action">
                        Đăng nhập <span>→</span>
                    </a>
                    @endauth
                </article>
            </div>
        </section>

        <!-- CHÂN TRANG -->
        <footer class="footer" id="footer">
            <div>
                <div class="footer-brand">✈ Sky<span>Booking</span></div>
                <div>Hệ thống quản lý và đặt vé máy bay trực tuyến.</div>
            </div>

            <div class="footer-note">
                © 2026 SkyBooking<br>
                Dự án học tập quản lý đặt vé máy bay
            </div>
        </footer>
    </main>

    <script>
        const formTimChuyenBay = document.getElementById('formTimChuyenBay');
        const loaiChuyenRadios = document.querySelectorAll(
            'input[name="loai_chuyen"]'
        );

        const diemDi = document.getElementById('diem_di');
        const diemDen = document.getElementById('diem_den');
        const ngayDi = document.getElementById('ngay_di');
        const ngayVe = document.getElementById('ngay_ve');
        const ngayVeField = document.getElementById('ngay_ve_field');

        function layNgayLocal() {
            const homNay = new Date();
            const nam = homNay.getFullYear();
            const thang = String(homNay.getMonth() + 1).padStart(2, '0');
            const ngay = String(homNay.getDate()).padStart(2, '0');

            return `${nam}-${thang}-${ngay}`;
        }

        function capNhatNgayVe() {
            const luaChon = document.querySelector(
                'input[name="loai_chuyen"]:checked'
            );

            const khuHoi = luaChon && luaChon.value === 'khu_hoi';

            ngayVe.disabled = !khuHoi;
            ngayVe.required = khuHoi;
            ngayVeField.style.opacity = khuHoi ? '1' : '0.6';

            if (!khuHoi) {
                ngayVe.value = '';
            }

            ngayVe.min = ngayDi.value || layNgayLocal();
        }

        ngayDi.min = layNgayLocal();

        ngayDi.addEventListener('change', function() {
            ngayVe.min = ngayDi.value || layNgayLocal();

            if (ngayVe.value && ngayVe.value < ngayDi.value) {
                ngayVe.value = '';
            }
        });

        loaiChuyenRadios.forEach(function(radio) {
            radio.addEventListener('change', capNhatNgayVe);
        });

        capNhatNgayVe();

        formTimChuyenBay.addEventListener('submit', function(e) {
            if (diemDi.value === diemDen.value) {
                e.preventDefault();
                alert('Điểm khởi hành và điểm đến không được giống nhau.');
                return;
            }

            if (ngayDi.value < layNgayLocal()) {
                e.preventDefault();
                alert('Ngày khởi hành không được ở trong quá khứ.');
                return;
            }

            if (ngayVe.required && ngayVe.value < ngayDi.value) {
                e.preventDefault();
                alert('Ngày trở về phải bằng hoặc sau ngày khởi hành.');
            }
        });
    </script>
</body>

</html>
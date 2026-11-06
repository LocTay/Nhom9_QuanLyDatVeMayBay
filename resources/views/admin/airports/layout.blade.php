<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SkyBooking</title>

    <style>
        :root {
            --navy: #06264b;
            --navy-light: #073a6b;
            --gold: #e8bd62;
            --bg: #f4f7fb;
            --text: #1c2f46;
            --muted: #77869a;
            --border: #e4eaf2;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 250px;
            padding: 30px 18px 20px;
            background: linear-gradient(180deg, #06264b, #073a6b 75%, #052544);
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .brand {
            color: white;
            text-decoration: none;
            font-size: 21px;
            font-weight: bold;
            padding: 0 8px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, .14);
        }

        .brand span {
            color: var(--gold);
        }

        .nav-caption {
            margin: 25px 8px 12px;
            color: #9fb6cc;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .side-link {
            display: block;
            padding: 13px 12px;
            margin-bottom: 5px;
            color: #dce8f5;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
        }

        .side-link:hover,
        .side-link.active {
            background: rgba(255, 255, 255, .10);
            color: white;
            border-left: 3px solid var(--gold);
        }

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            min-height: 82px;
            padding: 17px 4%;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .topbar small {
            color: var(--muted);
            font-size: 11px;
        }

        .topbar strong {
            display: block;
            margin-top: 5px;
            color: var(--navy);
            font-size: 14px;
        }

        .topbar a {
            color: var(--navy);
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .content {
            padding: 35px 4%;
        }

        .page-heading {
            margin-bottom: 22px;
        }

        .page-heading small {
            color: #b78128;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .page-heading h1 {
            margin-top: 8px;
            font-size: 25px;
            color: var(--navy);
        }

        .page-heading p {
            margin-top: 8px;
            color: var(--muted);
            font-size: 13px;
        }

        .panel {
            padding: 25px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(6, 38, 75, .05);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .panel-header h2 {
            color: var(--navy);
            font-size: 17px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: 0;
            border-radius: 7px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--navy);
            color: white;
        }

        .btn-primary:hover {
            background: var(--navy-light);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-secondary {
            background: #e9eef5;
            color: var(--navy);
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            white-space: nowrap;
        }

        th {
            background: #f4f7fb;
            color: var(--navy);
            font-size: 11px;
            text-transform: uppercase;
        }

        tbody tr:hover {
            background: #fafbfd;
        }

        .airport-code {
            color: var(--navy);
            font-weight: bold;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .actions form {
            display: inline;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 18px;
            border-radius: 7px;
            font-size: 13px;
        }

        .alert-success {
            color: #176b42;
            background: #e8f6ee;
        }

        .alert-error {
            color: #a42632;
            background: #fdecee;
        }

        .form-group {
            margin-bottom: 19px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--navy);
            font-size: 12px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid var(--border);
            border-radius: 7px;
            outline: none;
            font-size: 13px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #4d83b8;
            box-shadow: 0 0 0 3px rgba(77, 131, 184, .1);
        }

        .field-error {
            margin-top: 6px;
            color: #dc3545;
            font-size: 12px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .pagination {
            margin-top: 20px;
        }

        @media (max-width: 760px) {
            .sidebar {
                position: relative;
                width: 100%;
                padding: 15px;
            }

            .nav-caption {
                margin-top: 15px;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 20px 15px;
            }

            .panel {
                padding: 16px;
            }

            .panel-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            Sky<span>Booking</span>
        </a>

        <div class="nav-caption">Điều hướng chính</div>

        <a href="{{ route('admin.dashboard') }}"
            class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            ⌂ &nbsp; Trang chủ Admin
        </a>

        <a href="{{ route('flights.search') }}" class="side-link">✈ &nbsp; Tìm chuyến bay</a>
        <a href="{{ route('ticket.lookup.form') }}" class="side-link">▤ &nbsp; Tra cứu vé</a>
        <a href="{{ route('profile.index') }}" class="side-link">♙ &nbsp; Tài khoản của tôi</a>
        <a href="{{ route('admin.airports.index') }}"
            class="side-link {{ request()->routeIs('admin.airports.*') ? 'active' : '' }}">
            ✧ &nbsp; Quản lý sân bay
        </a>

        <a href="{{ route('admin.flights.index') }}"
            class="side-link {{ request()->routeIs('admin.flights.*') ? 'active' : '' }}">
            ✈ &nbsp; Quản lý chuyến bay
        </a>

        <a href="{{ route('admin.invoices.index') }}"
            class="side-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
            ▤ &nbsp; Quản lý hóa đơn
        </a>

        <a href="{{ route('admin.refunds.index') }}"
            class="side-link {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}">
            ↺ &nbsp; Quản lý hoàn tiền
        </a>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <small>QUẢN TRỊ HỆ THỐNG SKYBOOKING</small>
                <strong>@yield('topbar-title', View::yieldContent('title', 'Quản trị hệ thống'))</strong>
            </div>

            <a href="{{ route('admin.dashboard') }}">← Về trang chủ Admin</a>
        </header>

        <section class="content">
            @yield('content')
        </section>
    </main>
</body>

</html>
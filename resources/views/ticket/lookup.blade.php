
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra Cứu Vé Máy Bay</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #071b3a, #123e73, #1683c7);
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar-custom {
            padding: 18px 0;
            background: rgba(5, 22, 48, 0.65);
            backdrop-filter: blur(12px);
        }

        .navbar-brand {
            color: white !important;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .navbar-brand i {
            color: #55c7ff;
            margin-right: 10px;
        }

        .nav-text {
            color: #dcecff;
            font-size: 14px;
        }

        /* Main */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 15px;
            position: relative;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .circle-one {
            width: 350px;
            height: 350px;
            top: -100px;
            right: -100px;
        }

        .circle-two {
            width: 250px;
            height: 250px;
            bottom: -100px;
            left: -50px;
        }

        .lookup-card {
            width: 100%;
            max-width: 550px;
            background: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0,0,0,0.25);
            position: relative;
            z-index: 1;
            animation: fadeIn 0.7s ease;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #0879c9, #0750a0);
            color: white;
            padding: 35px 25px;
            text-align: center;
        }

        .icon-plane {
            width: 75px;
            height: 75px;
            margin: 0 auto 15px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .card-header-custom h4 {
            font-weight: 800;
            font-size: 23px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .card-header-custom p {
            font-size: 14px;
            margin: 0;
            color: #d9edff;
        }

        .card-body-custom {
            padding: 35px;
        }

        .form-label {
            font-weight: 700;
            color: #243b53;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom i {
            position: absolute;
            top: 50%;
            left: 17px;
            transform: translateY(-50%);
            color: #1683c7;
            font-size: 18px;
            z-index: 2;
        }

        .form-control-custom {
            height: 58px;
            border: 2px solid #e5edf5;
            border-radius: 12px !important;
            padding-left: 48px;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .form-control-custom:focus {
            border-color: #1683c7;
            box-shadow: 0 0 0 4px rgba(22,131,199,0.12);
        }

        .form-control-custom::placeholder {
            color: #aab7c4;
            font-weight: 400;
            letter-spacing: 0;
        }

        .btn-lookup {
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #0879c9, #0750a0);
            color: white;
            font-size: 17px;
            font-weight: 700;
            margin-top: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 7px 18px rgba(7,104,183,0.25);
        }

        .btn-lookup:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(7,104,183,0.35);
            color: white;
            background: linear-gradient(135deg, #0565ad, #063e82);
        }

        .btn-lookup i {
            margin-right: 9px;
        }

        .security-note {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #8795a5;
        }

        .security-note i {
            color: #20a66a;
            margin-right: 5px;
        }

        .alert {
            border-radius: 10px;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px 10px;
            color: #d5e8fa;
            font-size: 13px;
            background: rgba(5,22,48,0.35);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .card-body-custom {
                padding: 25px 20px;
            }

            .card-header-custom {
                padding: 30px 15px;
            }

            .card-header-custom h4 {
                font-size: 20px;
            }

            .navbar-brand {
                font-size: 19px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-plane-departure"></i> SKY TRAVEL
            </a>

            <span class="nav-text">
                <i class="fas fa-headset me-2"></i>
                Hỗ trợ khách hàng
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">

        <div class="circle circle-one"></div>
        <div class="circle circle-two"></div>

        <div class="lookup-card">

            <!-- Header -->
            <div class="card-header-custom">

                <div class="icon-plane">
                    <i class="fas fa-plane"></i>
                </div>

                <h4>TRA CỨU VÉ MÁY BAY</h4>

                <p>Kiểm tra thông tin chuyến bay của bạn nhanh chóng</p>

            </div>

            <!-- Body -->
            <div class="card-body-custom">

                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('ticket.lookup') }}" method="POST">
                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            MÃ ĐẶT CHỖ (BOOKING CODE)
                        </label>

                        <div class="input-group-custom">
                            <i class="fas fa-ticket-alt"></i>

                            <input
                                type="text"
                                name="booking_code"
                                class="form-control form-control-custom text-uppercase"
                                placeholder="Nhập mã đặt chỗ, ví dụ: VN101"
                                value="{{ old('booking_code') }}"
                                maxlength="20"
                                required
                            >
                        </div>

                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Vui lòng nhập chính xác mã đặt chỗ của bạn.
                        </small>

                    </div>

                    <button type="submit" class="btn btn-lookup">
                        <i class="fas fa-search"></i>
                        TRA CỨU VÉ NGAY
                    </button>

                </form>

                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    Thông tin tra cứu được bảo mật an toàn
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="footer">
        © {{ date('Y') }} SKY TRAVEL - Hệ thống quản lý và tra cứu vé máy bay
        <br>
        <span class="mt-2 d-inline-block">
            <i class="fas fa-plane me-1"></i>
            Chúc bạn có một chuyến bay tốt đẹp!
        </span>
    </footer>

</body>
</html>

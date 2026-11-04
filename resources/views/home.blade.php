<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyBooking - Đặt vé máy bay</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f3f6fb;
            color: #172b4d;
        }

        header {
            background: #ffffff;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 12px #0000000d;
        }

        .logo {
            color: #0756a5;
            font-size: 26px;
            font-weight: bold;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        nav a {
            color: #344563;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        nav a:hover {
            color: #0877d1;
        }

        .login {
            background: #0756a5;
            color: white;
            padding: 11px 18px;
            border-radius: 6px;
        }

        .hero {
            background: linear-gradient(120deg, #073b75, #087dc1);
            color: white;
            padding: 65px 8% 110px;
        }

        .hero h1 {
            font-size: 38px;
            margin-bottom: 14px;
        }

        .hero p {
            font-size: 16px;
            line-height: 1.7;
            color: #e1efff;
        }

        .booking {
            max-width: 1080px;
            margin: -55px auto 40px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px #172b4d14;
        }

        .booking h2 {
            margin-bottom: 22px;
            font-size: 23px;
        }

        .trip-type {
            display: flex;
            gap: 24px;
            margin-bottom: 25px;
        }

        .trip-type label {
            cursor: pointer;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .field label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 9px;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 13px;
            border: 1px solid #d7dfeb;
            border-radius: 6px;
            outline: none;
            background: white;
            color: #172b4d;
        }

        .field input:focus,
        .field select:focus {
            border-color: #0877d1;
        }

        .search-button {
            width: 100%;
            border: none;
            border-radius: 6px;
            background: #f5a623;
            color: #172b4d;
            padding: 15px;
            margin-top: 24px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .search-button:hover {
            background: #ffc04d;
        }

        .benefits {
            max-width: 1080px;
            margin: 0 auto 55px;
            padding: 0 15px;
        }

        .benefits h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .benefit-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .benefit {
            background: white;
            padding: 25px 20px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.2s;
        }

        .benefit:hover {
            transform: translateY(-3px);
        }

        .benefit .icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .benefit h3 {
            margin-bottom: 10px;
            font-size: 17px;
        }

        .benefit p {
            color: #667085;
            font-size: 14px;
            line-height: 1.6;
        }

        footer {
            background: #082b52;
            color: #dbeafe;
            padding: 25px;
            text-align: center;
            font-size: 13px;
        }

        @media (max-width: 700px) {
            header {
                padding: 18px 5%;
                flex-wrap: wrap;
                gap: 15px;
            }

            nav {
                gap: 12px;
                flex-wrap: wrap;
            }

            .hero {
                padding: 45px 5% 85px;
            }

            .hero h1 {
                font-size: 29px;
            }

            .booking {
                margin: -45px 15px 35px;
                padding: 22px 18px;
            }

            .form-grid,
            .benefit-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">✈ SkyBooking</div>

        
<nav>
    <a href="/">Trang chủ</a>
    <a href="#datve">Tìm chuyến bay</a>
    <a href="#tienich">Tiện ích</a>
    <a href="{{ route('ticket.lookup.form') }}">Tra cứu vé</a>
    <a href="#footer">Liên hệ</a>

    @guest
        <a href="{{ route('login') }}" class="login">Đăng nhập</a>
        <a href="{{ route('register') }}">Đăng ký</a>
    @else
        <span>Xin chào, {{ Auth::user()->name }}</span>
        <a href="{{ route('profile.index') }}">Tài khoản của tôi</a>
        <form action="{{ route('logout') }}" method="POST"
              style="display: inline;">
            @csrf
            <button type="submit" class="login"
                    style="border: none; cursor: pointer;">
                Đăng xuất
            </button>
        </form>
    @endguest
    
</nav>

        <nav>
            <a href="/">Trang chủ</a>
            <a href="#datve">Tìm chuyến bay</a>
            <!-- CHUYỂN TRỰC TIẾP TỚI TRANG LOOKUP -->
            <a href="{{ route('ticket.lookup.form') }}">Tra cứu vé</a>
            <a href="#tienich">Tiện ích</a>
            <a href="#footer">Liên hệ</a>
            <a href="#" class="login">Đăng nhập</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Chuyến đi của bạn bắt đầu từ đây</h1>
        <p>
            Tìm kiếm chuyến bay phù hợp, lựa chọn hành trình
            và chuẩn bị cho chuyến đi tiếp theo của bạn.
        </p>
    </section>

    <!-- KHỐI TÌM CHUYẾN BAY (Thành viên 1) -->
    <section class="booking" id="datve">
        <h2>Tìm chuyến bay</h2>

        <form id="formTimChuyenBay" action="{{ route('flights.search') }}" method="GET">
            <div class="trip-type">
                <label>
                    <input type="radio" name="loai_chuyen" value="mot_chieu" checked>
                    Một chiều
                </label>

                <label>
                    <input type="radio" name="loai_chuyen" value="khu_hoi">
                    Khứ hồi
                </label>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="diem_di">Điểm khởi hành</label>
                    <select id="diem_di" name="diem_di" required>
                        <option value="">-- Chọn điểm đi --</option>
                        @foreach ($sanBays as $sanBay)
                            <option value="{{ $sanBay->code }}">
                                {{ $sanBay->city }} ({{ $sanBay->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="diem_den">Điểm đến</label>
                    <select id="diem_den" name="diem_den" required>
                        <option value="">-- Chọn điểm đến --</option>
                        @foreach ($sanBays as $sanBay)
                            <option value="{{ $sanBay->code }}">
                                {{ $sanBay->city }} ({{ $sanBay->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="ngay_di">Ngày khởi hành</label>
                    <input type="date" id="ngay_di" name="ngay_di" required>
                </div>

                <div class="field" id="ngay_ve_field">
                    <label for="ngay_ve">Ngày về (chỉ dành cho khứ hồi)</label>
                    <input type="date" id="ngay_ve" name="ngay_ve" disabled>
                </div>

                <div class="field">
                    <label for="hanh_khach">Số hành khách</label>
                    <select id="hanh_khach" name="hanh_khach">
                        <option value="1">1 hành khách</option>
                        <option value="2">2 hành khách</option>
                        <option value="3">3 hành khách</option>
                        <option value="4">4 hành khách</option>
                        <option value="5">5 hành khách</option>
                    </select>
                </div>

                <div class="field">
                    <label for="hang_ve">Hạng vé</label>
                    <select id="hang_ve" name="hang_ve">
                        <option value="pho_thong">Phổ thông</option>
                        <option value="pho_thong_dac_biet">Phổ thông đặc biệt</option>
                        <option value="thuong_gia">Thương gia</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="search-button">
                Tìm chuyến bay →
            </button>
        </form>
    </section>

    <!-- KHỐI TIỆN ÍCH DỊCH VỤ -->
    <section class="benefits" id="tienich">
        <h2>Dịch vụ của SkyBooking</h2>

        <div class="benefit-grid">
            <a href="#datve" class="benefit">
                <div class="icon">✈️</div>
                <h3>Tìm chuyến bay</h3>
                <p>Lựa chọn hành trình phù hợp với kế hoạch của bạn.</p>
            </a>

            <!-- ĐIỀU HƯỚNG THẲNG TỚI TRANG LOOKUP (TICKET LOOKUP FORM) -->
            <a href="{{ route('ticket.lookup.form') }}" class="benefit">
                <div class="icon">🎫</div>
                <h3>Quản lý đặt vé</h3>
                <p>Tra cứu thông tin đặt chỗ, kiểm tra vé điện tử dễ dàng.</p>
            </a>

            <!-- ĐIỀU HƯỚNG THẲNG TỚI TRANG LOOKUP -->
            <a href="{{ route('ticket.lookup.form') }}" class="benefit">
                <div class="icon">💳</div>
                <h3>Thanh toán tiện lợi</h3>
                <p>Mô phỏng quy trình thanh toán nhanh chóng và an toàn.</p>
            </a>
        </div>
    </section>

    <footer id="footer">
        <p>© 2026 SkyBooking - Dự án học tập quản lý đặt vé máy bay</p>
    </footer>

    <script>
        const loaiChuyen = document.querySelectorAll(
            'input[name="loai_chuyen"]'
        );

        const ngayVe = document.getElementById('ngay_ve');
        const ngayDi = document.getElementById('ngay_di');
        const ngayVeField = document.getElementById('ngay_ve_field');

        function capNhatNgayVe() {
            const khuHoi = document.querySelector(
                'input[name="loai_chuyen"]:checked'
            ).value === 'khu_hoi';

            ngayVe.disabled = !khuHoi;
            ngayVe.required = khuHoi;
            ngayVeField.style.opacity = khuHoi ? '1' : '0.55';

            if (!khuHoi) {
                ngayVe.value = '';
            }

            if (ngayDi.value) {
                ngayVe.min = ngayDi.value;
            }
        }

        const homNay = new Date();
        const nam = homNay.getFullYear();
        const thang = String(homNay.getMonth() + 1).padStart(2, '0');
        const ngay = String(homNay.getDate()).padStart(2, '0');
        ngayDi.min = `${nam}-${thang}-${ngay}`;

        ngayDi.addEventListener('change', function () {
            ngayVe.min = ngayDi.value;

            if (ngayVe.value && ngayVe.value < ngayDi.value) {
                ngayVe.value = '';
            }
        });

        loaiChuyen.forEach(function (radio) {
            radio.addEventListener('change', capNhatNgayVe);
        });

        capNhatNgayVe();


document.querySelector('#formTimChuyenBay').addEventListener('submit', function (e) {

            const diemDi = document.getElementById('diem_di').value;
            const diemDen = document.getElementById('diem_den').value;

            if (diemDi === diemDen) {
                e.preventDefault();
                alert('Điểm đi và điểm đến không được giống nhau!');
                return;
            }

            if (ngayVe.required && ngayVe.value < ngayDi.value) {
                e.preventDefault();
                alert('Ngày về phải bằng hoặc sau ngày khởi hành!');
            }
        });
    </script>
</body>
</html>
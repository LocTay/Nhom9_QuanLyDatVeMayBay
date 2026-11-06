<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán vé - {{ $booking->booking_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f2f6fb; font-family: "Segoe UI", Arial, sans-serif; color: #243b53; }
        .site-header { background: linear-gradient(120deg, #071b3a, #0750a0); padding: 17px 0; }
        .brand { color: #fff; text-decoration: none; font-size: 22px; font-weight: 800; }
        .payment-card { background: #fff; border-radius: 16px; box-shadow: 0 8px 28px rgba(30,60,90,.08); overflow: hidden; margin: 30px auto; max-width: 750px; }
        .payment-header { background: linear-gradient(120deg, #0879c9, #0750a0); color: #fff; padding: 20px 25px; }
        .qr-box { background: #fff; border: 2px dashed #006838; border-radius: 12px; padding: 12px; display: inline-block; max-width: 280px; }
        .info-label { color: #718096; font-size: 13px; font-weight: 600; }
        .info-val { color: #123e73; font-weight: 700; font-size: 15px; }
    </style>
</head>
<body>

<header class="site-header">
    <div class="container">
        <a class="brand" href="{{ route('ticket.lookup.form') }}">
            <i class="fas fa-plane-departure"></i> SKY TRAVEL
        </a>
    </div>
</header>

<div class="container">
    <div class="payment-card">
        <div class="payment-header text-center">
            <h4 class="mb-1 fw-bold"><i class="fas fa-qrcode me-2"></i> THANH TOÁN CHUYỂN KHOẢN VIETQR</h4>
            <p class="mb-0 small opacity-75">Quét mã QR bằng ứng dụng Ngân hàng để tự động điền số tiền và nội dung</p>
        </div>

        <div class="p-4">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-5 text-center mb-4 mb-md-0">
                    <div class="qr-box shadow-sm">
                        <!-- Tạo mã VietQR động theo tài khoản Vietcombank 1033275661 tự động khớp tiền và nội dung -->
                        <img src="https://img.vietqr.io/image/VCB-1033275661-compact2.png?amount={{ (int)$booking->total_price }}&addInfo=SKYBOOKING%20{{ $booking->booking_code }}&accountName=NGUYEN%20TU%20PHAT%20TRIEN" 
                             alt="Mã VietQR Vietcombank" 
                             class="img-fluid rounded">

                        <!-- Nếu muốn dùng trực tiếp ảnh tĩnh bạn tải lên, hãy lưu ảnh vào public/images/qr.png và dùng dòng dưới: -->
                        <!-- <img src="{{ asset('images/qr.png') }}" alt="Mã QR Vietcombank" class="img-fluid rounded"> -->
                    </div>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-camera me-1"></i> Quét mã bằng App Vietcombank / Mobile Banking
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="mb-3">
                        <span class="info-label">Ngân hàng thụ hưởng:</span>
                        <div class="info-val text-success">
                            <i class="fas fa-university me-1"></i> Vietcombank (Ngoại thương Việt Nam)
                        </div>
                        <small class="text-muted">PGD Sông Hinh</small>
                    </div>

                    <div class="mb-3">
                        <span class="info-label">Số tài khoản:</span>
                        <div class="info-val text-primary fs-4">1033275661</div>
                    </div>

                    <div class="mb-3">
                        <span class="info-label">Chủ tài khoản:</span>
                        <div class="info-val">NGUYEN TU PHAT TRIEN</div>
                    </div>

                    <div class="mb-3">
                        <span class="info-label">Số tiền thanh toán:</span>
                        <div class="info-val text-danger fs-4">{{ number_format($booking->total_price) }} VNĐ</div>
                    </div>

                    <div class="mb-3">
                        <span class="info-label">Nội dung chuyển khoản:</span>
                        <div class="info-val text-dark bg-warning-subtle p-2 rounded d-inline-block border">
                            SKYBOOKING {{ $booking->booking_code }}
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('ticket.detail', $booking->booking_code) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại chi tiết vé
                </a>

                <form action="{{ route('ticket.payment.submit', $booking->booking_code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Xác nhận chuyển khoản
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
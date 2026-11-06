<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt vé chuyến bay - SkyBooking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 0;
        }
        .container {
            max-width: 750px;
        }
        h2 {
            color: #ffffff;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            text-align: center;
            margin-bottom: 25px !important;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            background: #ffffff;
            padding: 35px !important;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
        }
        .passenger-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 15px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #0052d4, #4364f7);
            border: none;
            padding: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            border-radius: 10px;
            width: 100%;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #003fa3, #3152d1);
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h2><i class="bi bi-ticket-detailed me-2"></i>Thông tin đặt vé & Hành khách</h2>
    <div class="card shadow-sm">
        <form action="{{ route('booking.store') }}" method="POST">
            @csrf
            
            @php
                $rawTrip = $tripType ?? request('trip_type', request('loai_chuyen', 'one_way'));
                $rawClass = $flightClass ?? request('flight_class', request('hang_ve', 'pho_thong'));
                
                $classMap = [
                    'pho_thong' => 'Phổ thông',
                    'pho_thong_dac_biet' => 'Phổ thông đặc biệt',
                    'thuong_gia' => 'Thương gia',
                    'hang_nhat' => 'Hạng nhất',
                ];
                $displayClass = $classMap[$rawClass] ?? ucwords(str_replace(['_', '-'], ' ', $rawClass));
                $isRoundTrip = ($rawTrip == 'round_trip' || $rawTrip == 'khu_hoi');
// Lấy số lượng hành khách từ URL hoặc request, mặc định là 1
                $defaultCount = old('passenger_count', request('passenger_count', 1));
            @endphp

            <input type="hidden" name="flight_id" value="{{ $flight->id }}">
            <input type="hidden" name="trip_type" value="{{ request('trip_type', request('loai_chuyen', 'one_way')) }}">
            <input type="hidden" name="flight_class" value="{{ request('flight_class', request('hang_ve', 'pho_thong')) }}">
            <input type="hidden" name="ngay_ve" value="{{ $ngayVe ?? request('ngay_ve') }}">            <div class="alert alert-info py-2 mb-4" style="font-size: 14px;">
                <i class="bi bi-info-circle me-1"></i> Hành trình: <strong>{{ $isRoundTrip ? 'Khứ hồi' : 'Một chiều' }}</strong> | Hạng vé: <strong>{{ $displayClass }}</strong>
            </div>

            <!-- Chọn số lượng hành khách -->
            <div class="mb-4">
                <label class="form-label">Số lượng hành khách</label>
                <input type="number" id="passenger_count" name="passenger_count" class="form-control form-control-lg" value="{{ $defaultCount }}" min="1" max="10" required>
            </div>

            <!-- Thông tin người đại diện đặt vé -->
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person-lines-fill me-2"></i>Người đại diện đặt vé</h5>
            <div class="passenger-box mb-4">
                <div class="row">
                    <!-- Họ và tên người đại diện -->
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Họ và tên người đại diện</label>
                        <input type="text" name="passenger_name" id="rep_name" class="form-control" value="{{ old('passenger_name') }}" placeholder="VD: Nguyễn Văn A" required>
                    </div>
                    
                    <!-- Giới tính người đại diện -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Giới tính</label>
                        <select name="passenger_gender" class="form-select" required>
                            <option value="Nam" {{ old('passenger_gender') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('passenger_gender') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold">Email nhận vé</label>
                        <input type="email" name="passenger_email" class="form-control" value="{{ old('passenger_email') }}" placeholder="example@gmail.com" required>
                    </div>
                    <div class="col-md-6">
<label class="form-label fw-bold">Số điện thoại liên hệ</label>
                        <input type="text" name="passenger_phone" class="form-control @error('passenger_phone') is-invalid @enderror" value="{{ old('passenger_phone') }}" placeholder="VD: 037xxxxxxx" maxlength="11" required>
                        @error('passenger_phone')
                            <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Khung danh sách tên các hành khách đi cùng (Chỉ hiện khi số lượng > 1) -->
            <div id="additional_passengers_section" style="display: none;">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-people-fill me-2"></i>Danh sách hành khách đi cùng</h5>
                <div id="passengers_list_container"></div>
            </div>

            <!-- Input ẩn chứa tên hành khách số 1 (để luôn đồng bộ với người đại diện phía trên khi chỉ đặt 1 vé) -->
            <div id="hidden_passenger_1"></div>
            <!-- Khung sơ đồ chọn ghế trực quan (Lọc theo hạng vé) -->
            <div class="card p-3 mb-4 border shadow-sm bg-light">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Sơ đồ chọn ghế ngồi</h5>
                
                <!-- Chú thích trạng thái ghế -->
                <div class="d-flex gap-3 mb-3 text-muted" style="font-size: 14px;">
                    <div><span class="badge bg-white border text-dark">&nbsp;&nbsp;&nbsp;</span> Trống</div>
                    <div><span class="badge bg-success">&nbsp;&nbsp;&nbsp;</span> Đang chọn</div>
                    <div><span class="badge bg-secondary">&nbsp;&nbsp;&nbsp;</span> Đã có người đặt</div>
                </div>

                <div id="seat-map-container">
                    <!-- CHỈ HIỆN KHOANG THƯƠNG GIA NẾU CHỌN HẠNG THƯƠNG GIA -->
                    @if($rawClass === 'thuong_gia')
                        <label class="form-label fw-bold text-secondary">Khoang Thương gia (Ghế A1 - A10)</label>
                        <div class="row row-cols-5 g-2 mb-3">
                            @for($i = 1; $i <= 10; $i++)
                                @php 
                                    $seatCode = 'A' . $i; 
                                    $isBooked = in_array($seatCode, $bookedSeats ?? []); 
                                @endphp
                                <div class="col">
                                    <button type="button" class="btn w-100 seat-btn {{ $isBooked ? 'btn-secondary disabled' : 'btn-outline-primary' }}" 
                                            data-seat="{{ $seatCode }}" {{ $isBooked ? 'disabled' : '' }}>
                                        {{ $seatCode }}
                                    </button>
</div>
                            @endfor
                        </div>
                    @endif

                    <!-- CHỈ HIỆN KHOANG PHỔ THÔNG ĐẶC BIỆT NẾU CHỌN -->
                    @if($rawClass === 'pho_thong_dac_biet')
                        <label class="form-label fw-bold text-secondary">Khoang Phổ thông đặc biệt (Ghế B1 - B10)</label>
                        <div class="row row-cols-5 g-2 mb-3">
                            @for($i = 1; $i <= 10; $i++)
                                @php 
                                    $seatCode = 'B' . $i; 
                                    $isBooked = in_array($seatCode, $bookedSeats ?? []); 
                                @endphp
                                <div class="col">
                                    <button type="button" class="btn w-100 seat-btn {{ $isBooked ? 'btn-secondary disabled' : 'btn-outline-warning' }}" 
                                            data-seat="{{ $seatCode }}" {{ $isBooked ? 'disabled' : '' }}>
                                        {{ $seatCode }}
                                    </button>
                                </div>
                            @endfor
                        </div>
                    @endif

                    <!-- CHỈ HIỆN KHOANG PHỔ THÔNG NẾU CHỌN PHỔ THÔNG (Linh động theo số ghế tồn thực tế) -->
                    @if($rawClass === 'pho_thong' || $rawClass == '')
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-secondary mb-0">Khoang Phổ thông (Ghế C1 - C{{ $maxSeats['C'] }})</label>
                            <span class="badge bg-success">Còn lại: {{ $remainingSeats['C'] }} / {{ $maxSeats['C'] }} ghế</span>
                        </div>
                        <div class="row row-cols-5 g-2" style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                            @for($i = 1; $i <= $maxSeats['C']; $i++)
                                @php 
                                    $seatCode = 'C' . $i; 
                                    $isBooked = in_array($seatCode, $bookedSeats ?? []); 
                                @endphp
                                <div class="col">
                                    <button type="button" class="btn w-100 seat-btn {{ $isBooked ? 'btn-secondary disabled' : 'btn-outline-success' }}" 
                                            data-seat="{{ $seatCode }}" {{ $isBooked ? 'disabled' : '' }}>
                                        {{ $seatCode }}
                                    </button>
                                </div>
                            @endfor
                        </div>
                    @endif
                </div>

                <!-- Các input ẩn chứa mã ghế được chọn -->
                <div id="selected-seats-inputs"></div>
            </div>
                <!-- === KHUNG CHỌN MÃ ƯU ĐÃI (TOGGLE VOUCHER) === -->
            <div class="card p-3 mb-4 border shadow-sm bg-light">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-tag-fill me-2"></i>Chọn mã ưu đãi / khuyến mãi</h5>
                
                @if(isset($availableVouchers) && count($availableVouchers) > 0)
                    <div id="vouchers-list">
                        @foreach($availableVouchers as $voucher)
                            @php
                                $isEligible = ($subTotal ?? 0) >= $voucher['min_spend'];
                            @endphp <!-- Sửa lại thành @endphp ở đây -->

                            <div class="voucher-card border p-3 mb-2 rounded {{ $isEligible ? 'bg-white shadow-sm' : 'bg-light text-muted opacity-75' }}"
                                data-code="{{ $voucher['code'] }}"
                                data-min-spend="{{ $voucher['min_spend'] }}"
                                data-percent="{{ $voucher['discount_percent'] }}"
                                style="cursor: pointer; transition: all 0.2s;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1 {{ $isEligible ? 'text-dark' : 'text-muted' }}">
                                            <i class="bi bi-gift text-danger me-1"></i> {{ $voucher['title'] }}
                                        </h6>
                                        <small class="text-muted">Áp dụng cho đơn hàng từ {{ number_format($voucher['min_spend']) }} VNĐ trở lên</small>
                                    </div>
                                    
                                    <div>
                                        @if($isEligible)
                                            <span class="badge bg-secondary voucher-badge px-3 py-2">Chưa chọn</span>
                                        @else
                                            <span class="badge bg-secondary voucher-badge px-3 py-2">Chưa đủ điều kiện</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-0">Không có mã ưu đãi nào khả dụng cho đơn hàng này.</p>
                @endif

                <!-- Input ẩn để lưu mã voucher gửi về Controller khi submit form -->
                <input type="hidden" name="selected_voucher" id="selected_voucher_input" value="">

                <!-- BẢNG TỔNG KẾT CHI TIẾT TIỀN -->
                <div class="mt-3 p-3 bg-white rounded border">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tạm tính:</span>
                        <strong id="subtotal-display">{{ number_format($subTotal ?? 0) }} VNĐ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-success">
                        <span>Giảm giá ưu đãi:</span>
                        <strong>-<span id="discount-display">0</span> VNĐ</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between text-danger fs-5 fw-bold">
                        <span>Tổng thanh toán:</span>
                        <span id="final-price-display">{{ number_format($subTotal ?? 0) }} VNĐ</span>
                    </div>
                </div>
            </div>
            <!-- === HẾT KHUNG CHỌN MÃ ƯU ĐÃI === -->

            <!-- NÚT ĐIỀU HƯỚNG -->
            <div class="d-flex gap-3 mt-3">
                <a href="{{ url()->previous() }}" class="btn btn-secondary w-50 py-2">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary w-50 py-2">Xác nhận đặt vé</button>
            </div>
        </form>
    </div>
</div>

<script>
    let activeCode = null;
    const baseSubtotalVal = {{ $subTotal ?? 0 }};

    function toggleVoucher(code, percent, element) {
        // Chỉ cho phép click nếu thẻ đang đủ điều kiện (có class bg-white)
        if (!element.classList.contains('bg-white')) return;

        const badge = element.querySelector('.voucher-badge');
        const input = document.getElementById('selected_voucher_input');

        if (activeCode === code) {
            activeCode = null;
            input.value = '';
            element.classList.remove('border-primary', 'bg-primary-subtle');
            badge.className = 'badge bg-secondary voucher-badge px-3 py-2';
            badge.innerText = 'Chưa chọn';
            updatePrice(0);
        } else {
            document.querySelectorAll('.voucher-card').forEach(c => {
                c.classList.remove('border-primary', 'bg-primary-subtle');
                let b = c.querySelector('.voucher-badge');
                if(b && b.innerText.includes('Đang áp dụng')) {
                    b.className = 'badge bg-secondary voucher-badge px-3 py-2';
                    b.innerText = 'Chưa chọn';
                }
            });

            activeCode = code;
            input.value = code;
            element.classList.add('border-primary', 'bg-primary-subtle');
            badge.className = 'badge bg-success voucher-badge px-3 py-2';
            badge.innerText = 'Đang áp dụng ✓';
            
            updatePrice(percent);
        }
    }

    // Hàm cập nhật giá tiền và kiểm tra lại điều kiện voucher theo tổng tiền mới
    function updatePrice(forcedPercent = null) {
        const countInput = document.getElementById('passenger_count');
        const passengerCount = parseInt(countInput.value) || 1;
        
        const currentSubtotal = baseSubtotalVal * passengerCount;
        
        let currentPercent = 0;
        
        // Kiểm tra và cập nhật trạng thái sáng/mờ của từng voucher dựa trên tổng tiền mới
        document.querySelectorAll('.voucher-card').forEach(card => {
            const minSpend = parseInt(card.getAttribute('data-min-spend')) || 0;
            const percent = parseInt(card.getAttribute('data-percent')) || 0;
            const badge = card.querySelector('div:last-child span');

            if (currentSubtotal >= minSpend) {
                // Đủ điều kiện: Sáng lên và cho phép click
                card.classList.remove('bg-light', 'text-muted', 'opacity-75');
                card.classList.add('bg-white', 'shadow-sm');
                
                if (card.getAttribute('data-code') === activeCode) {
                    badge.className = 'badge bg-success voucher-badge px-3 py-2';
                    badge.innerText = 'Đang áp dụng ✓';
                    currentPercent = percent;
                } else if (badge.innerText === 'Chưa đủ điều kiện') {
                    badge.className = 'badge bg-secondary voucher-badge px-3 py-2';
                    badge.innerText = 'Chưa chọn';
                }
            } else {
                // Chưa đủ điều kiện: Mờ đi và khóa lại
                card.classList.remove('bg-white', 'shadow-sm', 'border-primary', 'bg-primary-subtle');
                card.classList.add('bg-light', 'text-muted', 'opacity-75');
                badge.className = 'badge bg-secondary';
                badge.innerText = 'Chưa đủ điều kiện';

                // Nếu voucher đang chọn mà nay bị tụt giá không đủ điều kiện nữa thì hủy chọn
                if (card.getAttribute('data-code') === activeCode) {
                    activeCode = null;
                    document.getElementById('selected_voucher_input').value = '';
                }
            }
        });

        // Nếu truyền trực tiếp phần trăm từ sự kiện click
        if (forcedPercent !== null) {
            currentPercent = forcedPercent;
        } else if (activeCode) {
            const activeCard = document.querySelector(`.voucher-card[data-code="${activeCode}"]`);
            if (activeCard) {
                currentPercent = parseInt(activeCard.getAttribute('data-percent')) || 0;
            } else {
                currentPercent = 0;
            }
        }

        let discountAmount = currentSubtotal * (currentPercent / 100);
        let finalPrice = currentSubtotal - discountAmount;
        
        document.getElementById('subtotal-display').innerText = currentSubtotal.toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('discount-display').innerText = discountAmount.toLocaleString('vi-VN');
        document.getElementById('final-price-display').innerText = finalPrice.toLocaleString('vi-VN') + ' VNĐ';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const countInput = document.getElementById('passenger_count');
        const container = document.getElementById('passengers_list_container');
        const section = document.getElementById('additional_passengers_section');
        const hiddenDiv = document.getElementById('hidden_passenger_1');
        const repNameInput = document.getElementById('rep_name');
        const repGenderSelect = document.querySelector('select[name="passenger_gender"]');

        const seatButtons = document.querySelectorAll('.seat-btn:not(.disabled)');
        const seatsInputsContainer = document.getElementById('selected-seats-inputs');
        let selectedSeats = [];

        // Gắn sự kiện click cho các thẻ voucher động
        document.querySelectorAll('.voucher-card').forEach(card => {
            card.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                const percent = parseInt(this.getAttribute('data-percent')) || 0;
                toggleVoucher(code, percent, this);
            });
        });

        function updatePassengerFields() {
            const count = parseInt(countInput.value) || 1;
            container.innerHTML = '';
            hiddenDiv.innerHTML = '';

            // Gọi updatePrice để tự động quét lại điều kiện voucher khi số lượng khách thay đổi
            updatePrice();

            if (selectedSeats.length > count) {
                selectedSeats = selectedSeats.slice(0, count);
                rehighlightSeats();
            }
            updateHiddenSeatInputs();

            if (count > 1) {
                section.style.display = 'block';
                for (let i = 2; i <= count; i++) {
                    const box = document.createElement('div');
                    box.className = 'passenger-box mb-3 p-3 border rounded';
                    box.innerHTML = `
                        <h6 class="fw-bold text-secondary mb-2">Hành khách ${i}</h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label mb-1">Họ và tên hành khách ${i}</label>
                                <input type="text" name="passengers[${i-1}][name]" class="form-control" placeholder="Nhập họ tên hành khách ${i}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">Giới tính</label>
                                <select name="passengers[${i-1}][gender]" class="form-select" required>
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                        </div>
                    `;
                    container.appendChild(box);
                }
            } else {
                section.style.display = 'none';
            }

            const inputName1 = document.createElement('input');
            inputName1.type = 'hidden';
            inputName1.name = 'passengers[0][name]';
            inputName1.value = repNameInput.value;
            hiddenDiv.appendChild(inputName1);

            const inputGender1 = document.createElement('input');
            inputGender1.type = 'hidden';
            inputGender1.name = 'passengers[0][gender]';
            inputGender1.value = repGenderSelect.value;
            hiddenDiv.appendChild(inputGender1);
        }

        seatButtons.forEach(button => {
            button.addEventListener('click', function () {
                const seatCode = this.getAttribute('data-seat');
                const maxPassengers = parseInt(countInput.value) || 1;

                if (selectedSeats.includes(seatCode)) {
                    selectedSeats = selectedSeats.filter(s => s !== seatCode);
                    this.classList.remove('active', 'btn-success');
                    this.classList.add('btn-outline-primary');
                } else {
                    if (selectedSeats.length >= maxPassengers) {
                        alert(`Bạn chỉ được chọn tối đa ${maxPassengers} ghế tương ứng với số lượng hành khách!`);
                        return;
                    }
                    selectedSeats.push(seatCode);
                    this.classList.remove('btn-outline-primary', 'btn-outline-success');
                    this.classList.add('active', 'btn-success');
                }

                updateHiddenSeatInputs();
            });
        });

        function updateHiddenSeatInputs() {
            if (!seatsInputsContainer) return;
            seatsInputsContainer.innerHTML = '';
            selectedSeats.forEach((seat, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `passengers[${index}][seat]`;
                input.value = seat;
                seatsInputsContainer.appendChild(input);
            });
        }

        function rehighlightSeats() {
            seatButtons.forEach(btn => {
                const seatCode = btn.getAttribute('data-seat');
                if (!btn.classList.contains('disabled')) {
                    if (selectedSeats.includes(seatCode)) {
                        btn.classList.remove('btn-outline-primary');
                        btn.classList.add('active', 'btn-success');
                    } else {
                        btn.classList.remove('active', 'btn-success');
                        btn.classList.add('btn-outline-primary');
                    }
                }
            });
        }

        repNameInput.addEventListener('input', function() {
            const hiddenInputName1 = document.querySelector('input[name="passengers[0][name]"]');
            if (hiddenInputName1) {
                hiddenInputName1.value = this.value;
            }
        });

        repGenderSelect.addEventListener('change', function() {
            const hiddenInputGender1 = document.querySelector('input[name="passengers[0][gender]"]');
            if (hiddenInputGender1) {
                hiddenInputGender1.value = this.value;
            }
        });

        countInput.addEventListener('change', updatePassengerFields);
        countInput.addEventListener('keyup', updatePassengerFields);
        updatePassengerFields();
    });
</script>
</body>
</html>

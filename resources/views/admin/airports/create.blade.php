```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sân bay</title>
</head>
<body>
    <h2>Thêm sân bay mới</h2>

    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.airports.store') }}" method="POST">
        @csrf

        <p>
            <label>Mã sân bay:</label><br>
            <input type="text" name="code" maxlength="3"
                   value="{{ old('code') }}" required>
        </p>

        <p>
            <label>Tên sân bay:</label><br>
            <input type="text" name="name"
                   value="{{ old('name') }}" required>
        </p>

        <p>
            <label>Thành phố:</label><br>
            <input type="text" name="city"
                   value="{{ old('city') }}" required>
        </p>

        <p>
            <label>Quốc gia:</label><br>
            <input type="text" name="country"
                   value="{{ old('country', 'Việt Nam') }}" required>
        </p>

        <button type="submit">Lưu sân bay</button>
        <a href="{{ route('admin.airports.index') }}">Quay lại</a>
    </form>
</body>
</html>
```
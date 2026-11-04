```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa sân bay</title>
</head>
<body>
    <h2>Chỉnh sửa sân bay: {{ $airport->code }}</h2>

    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.airports.update', $airport) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label>Mã sân bay:</label><br>
            <input type="text" name="code" maxlength="3"
                   value="{{ old('code', $airport->code) }}" required>
        </p>

        <p>
            <label>Tên sân bay:</label><br>
            <input type="text" name="name"
                   value="{{ old('name', $airport->name) }}" required>
        </p>

        <p>
            <label>Thành phố:</label><br>
            <input type="text" name="city"
                   value="{{ old('city', $airport->city) }}" required>
        </p>

        <p>
            <label>Quốc gia:</label><br>
            <input type="text" name="country"
                   value="{{ old('country', $airport->country) }}" required>
        </p>

        <button type="submit">Cập nhật</button>
        <a href="{{ route('admin.airports.index') }}">Quay lại</a>
    </form>
</body>
</html>
```
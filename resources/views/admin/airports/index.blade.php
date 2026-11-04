```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sân bay</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f6fa;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
        }

        h2 { margin-top: 0; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th { background: #f0f0f0; }

        a, button {
            padding: 7px 12px;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .add { background: #198754; color: white; }
        .edit { background: #0d6efd; color: white; }
        .delete { background: #dc3545; color: white; }

        .success { color: green; }
        .error { color: red; }

        form { display: inline; }
    </style>
</head>
<body>
<div class="container">
    <h2>Quản lý sân bay</h2>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <a href="{{ route('admin.airports.create') }}" class="add">
        + Thêm sân bay
    </a>

    <table>
        <thead>
            <tr>
                <th>Mã</th>
                <th>Tên sân bay</th>
                <th>Thành phố</th>
                <th>Quốc gia</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($airports as $airport)
                <tr>
                    <td>{{ $airport->code }}</td>
                    <td>{{ $airport->name }}</td>
                    <td>{{ $airport->city }}</td>
                    <td>{{ $airport->country }}</td>
                    <td>
                        <a href="{{ route('admin.airports.edit', $airport) }}"
                           class="edit">Sửa</a>

                        <form action="{{ route('admin.airports.destroy', $airport) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn xóa sân bay này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Chưa có sân bay nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $airports->links() }}
</div>
</body>
</html>
```
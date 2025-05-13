<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách danh mục</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        thead tr {
            background-color: #3498db;
            color: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #e3f2fd;
        }

        .text-center {
            text-align: center;
        }

        .status-visible {
            color: #27ae60;
            font-weight: bold;
        }

        .status-hidden {
            color: #e74c3c;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #7f8c8d;
        }

        .summary-info {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Danh sách danh mục</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td class="text-center">{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->description ?? '-' }}</td>
                <td class="text-center {{ $category->status ? 'status-visible' : 'status-hidden' }}">
                    {{ $category->status ? 'Hiển thị' : 'Ẩn' }}
                </td>
                <td class="text-center">{{ \Carbon\Carbon::parse($category->created_at)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="summary-info">
            <p>Ngày xuất: {{ date('d/m/Y H:i:s') }}</p>
            <p>Tổng số danh mục: {{ $categories->count() }}</p>
        </div>
        <p class="text-center">© {{ date('Y') }} DevFoods - Báo cáo được tạo tự động từ hệ thống</p>
    </div>
</body>
</html>

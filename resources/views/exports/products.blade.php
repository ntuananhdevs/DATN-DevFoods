<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách sản phẩm</title>
    <style>
        /* Reset và font cơ bản */
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        
        /* Tiêu đề chính */
        h1 { 
            text-align: center; 
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        /* Bảng dữ liệu */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Tiêu đề bảng */
        thead tr {
            background-color: #3498db;
            color: white;
        }
        
        /* Các ô trong bảng */
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        
        th { 
            background-color: #3498db; 
            font-weight: bold;
        }
        
        /* Màu nền xen kẽ cho các hàng */
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        tbody tr:hover {
            background-color: #e3f2fd;
        }
        
        /* Căn chỉnh văn bản */
        .text-center { 
            text-align: center; 
        }
        
        .text-right { 
            text-align: right; 
        }
        
        /* Định dạng giá */
        .price {
            font-weight: bold;
            color: #e74c3c;
        }
        
        /* Trạng thái */
        .status-instock {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-outofstock {
            color: #e74c3c;
            font-weight: bold;
        }
        
        /* Phần footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #7f8c8d;
        }
        
        /* Thông tin tổng kết */
        .summary-info {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Danh sách sản phẩm</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td class="text-center">{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                <td class="text-right price">{{ number_format($product->base_price, 0, ',', '.') }} đ</td>
                <td class="text-center">{{ $product->stock }}</td>
                <td class="text-center {{ $product->stock > 0 ? 'status-instock' : 'status-outofstock' }}">
                    {{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                </td>
                <td class="text-center">{{ $product->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <div class="summary-info">
            <p>Ngày xuất: {{ date('d/m/Y H:i:s') }}</p>
            <p>Tổng số sản phẩm: {{ $products->count() }}</p>
        </div>
        <p class="text-center">© {{ date('Y') }} DevFoods - Báo cáo được tạo tự động từ hệ thống</p>
    </div>
</body>
</html>
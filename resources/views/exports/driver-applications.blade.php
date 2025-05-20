<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách đơn đăng ký tài xế</title>
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
        
        /* Trạng thái */
        .status-pending {
            color: #f39c12;
            font-weight: bold;
        }
        
        .status-approved {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-rejected {
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
    <h1>Danh sách đơn đăng ký tài xế</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và tên</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Biển số xe</th>
                <th>Loại phương tiện</th>
                <th>Trạng thái</th>
                <th>Ngày nộp đơn</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $application)
            <tr>
                <td class="text-center">{{ $application->id }}</td>
                <td>{{ $application->full_name }}</td>
                <td>{{ $application->phone_number }}</td>
                <td>{{ $application->email }}</td>
                <td>{{ $application->license_plate }}</td>
                <td>{{ $application->vehicle_type }}</td>
                <td class="text-center 
                    @if($application->status == 'pending') status-pending 
                    @elseif($application->status == 'approved') status-approved 
                    @elseif($application->status == 'rejected') status-rejected 
                    @endif">
                    @if($application->status == 'pending') Đang chờ 
                    @elseif($application->status == 'approved') Đã duyệt 
                    @elseif($application->status == 'rejected') Đã từ chối 
                    @else {{ $application->status }} 
                    @endif
                </td>
                <td class="text-center">{{ $application->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <div class="summary-info">
            <p>Ngày xuất: {{ date('d/m/Y H:i:s') }}</p>
            <p>Tổng số đơn: {{ $applications->count() }}</p>
        </div>
        <p class="text-center">© {{ date('Y') }} DevFoods - Báo cáo được tạo tự động từ hệ thống</p>
    </div>
</body>
</html> 
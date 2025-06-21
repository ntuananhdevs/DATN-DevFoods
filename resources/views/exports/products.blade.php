<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách sản phẩm theo chi nhánh</title>
    <style>
        /* Reset và font cơ bản */
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
            background-color: #fff;
        }
        
        /* Tiêu đề chính */
        h1 { 
            text-align: center; 
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid #3498db;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Tiêu đề chi nhánh */
        h2 {
            color: #2980b9;
            font-size: 16px;
            margin: 25px 0 15px 0;
            padding: 8px 15px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Bảng dữ liệu */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* Tiêu đề bảng */
        thead tr {
            background: linear-gradient(135deg, #4472C4, #365899);
            color: white;
        }
        
        /* Các ô trong bảng */
        th, td { 
            border: 1px solid #e0e0e0; 
            padding: 8px; 
            text-align: left; 
        }
        
        th { 
            background: linear-gradient(135deg, #4472C4, #365899); 
            font-weight: bold;
            color: white;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Màu nền xen kẽ cho các hàng */
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        
        tbody tr {
            transition: background-color 0.2s ease;
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
            background-color: #ffeaa7;
            padding: 3px 6px;
            border-radius: 3px;
        }
        
        /* Trạng thái */
        .status-instock {
            color: #ffffff;
            background-color: #27ae60;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        .status-outofstock {
            color: #ffffff;
            background-color: #e74c3c;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        .status-unassigned {
            color: #ffffff;
            background-color: #f39c12;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        /* Phần footer */
        .footer {
            margin-top: 25px;
            padding: 15px;
            border-top: 2px solid #3498db;
            font-size: 10px;
            color: #7f8c8d;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        /* Thông tin tổng kết */
        .summary-info {
            text-align: right;
            margin-bottom: 10px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        /* Branch summary */
        .branch-summary {
            background-color: #ecf0f1;
            padding: 8px;
            margin: 10px 0;
            border-left: 4px solid #3498db;
            font-size: 10px;
        }
        
        /* Page break */
        .page-break {
            page-break-before: always;
        }
        
        /* SKU styling */
        .sku {
            font-family: 'Courier New', monospace;
            background-color: #ecf0f1;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DANH SÁCH SẢN PHẨM</h1>
        @if(isset($selectedBranch) && $selectedBranch)
            <h2 style="color: #2563eb; margin: 10px 0;">Chi nhánh: {{ $selectedBranch->name }}</h2>
        @endif
        <div class="company-info">
            <strong>DevFoods Restaurant</strong><br>
            Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM<br>
            Điện thoại: (028) 1234 5678 | Email: info@devfoods.com
        </div>
    </div>
    
    @php
        // Khởi tạo mặc định để tránh lỗi null
        $products = $products ?? [];
        
        if(isset($selectedBranch) && $selectedBranch) {
            // Nếu chọn chi nhánh cụ thể, hiển thị dạng bảng đơn giản
            $totalProducts = count($products);
            $totalValue = collect($products)->sum(function($product) {
                return (float) str_replace([' VNĐ', '.', ','], ['', '', ''], $product['base_price']);
            });
        } else {
            // Nhóm sản phẩm theo chi nhánh như trước
            $totalProducts = 0;
            $branchData = [];
            
            // Nhóm dữ liệu theo chi nhánh
            foreach($products as $productData) {
                $branchName = $productData['branch_name'] ?? 'Chưa phân bổ chi nhánh';
                if (!isset($branchData[$branchName])) {
                    $branchData[$branchName] = [];
                }
                $branchData[$branchName][] = $productData;
                $totalProducts++;
            }
            $totalValue = collect($products)->sum(function($product) {
                return (float) str_replace([' VNĐ', '.', ','], ['', '', ''], $product['base_price']);
            });
        }
    @endphp
    
    @if(isset($selectedBranch) && $selectedBranch)
        <!-- Hiển thị cho chi nhánh được chọn -->
        <div class="branch-section">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $index => $productData)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $productData['sku'] }}</td>
                            <td>{{ $productData['name'] }}</td>
                            <td>{{ $productData['category'] }}</td>
                            <td>{{ $productData['base_price'] }}</td>
                            <td>{{ $productData['stock_quantity'] }}</td>
                            <td>
                                <span class="status {{ strtolower(str_replace(' ', '-', $productData['status'])) }}">
                                    {{ $productData['status'] }}
                                </span>
                            </td>
                            <td>{{ $productData['created_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Hiển thị sản phẩm theo từng chi nhánh -->
        @foreach($branchData as $branchName => $branchProducts)
            @if(!$loop->first)
                <div class="page-break"></div>
            @endif
            
            <h2>{{ $branchName }}</h2>
            
            <div class="branch-summary">
                <strong>Tổng số sản phẩm:</strong> {{ count($branchProducts ?? []) }} sản phẩm
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branchProducts as $index => $productData)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center sku">{{ $productData['sku'] }}</td>
                        <td>{{ $productData['name'] }}</td>
                        <td class="text-center">{{ $productData['category'] }}</td>
                        <td class="text-right price">{{ $productData['base_price'] }}</td>
                        <td class="text-center">{{ $productData['stock_quantity'] }}</td>
                        <td class="text-center">
                            @if($productData['status'] == 'Còn hàng')
                                <span class="status-instock">{{ $productData['status'] }}</span>
                            @elseif($productData['status'] == 'Hết hàng')
                                <span class="status-outofstock">{{ $productData['status'] }}</span>
                            @else
                                <span class="status-unassigned">{{ $productData['status'] }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $productData['created_at'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
    
    <div class="footer">
        <div class="summary-info">
            <p><strong>Ngày xuất:</strong> {{ date('d/m/Y H:i:s') }}</p>
            @if(isset($selectedBranch) && $selectedBranch)
                <p><strong>Chi nhánh:</strong> {{ $selectedBranch->name }}</p>
            @else
                <p><strong>Tổng số chi nhánh:</strong> {{ count($branchData ?? []) }}</p>
            @endif
            <p><strong>Tổng số dòng dữ liệu:</strong> {{ $totalProducts ?? 0 }}</p>
        </div>
        <p class="text-center">© {{ date('Y') }} DevFoods - Báo cáo được tạo tự động từ hệ thống</p>
    </div>
</body>
</html>
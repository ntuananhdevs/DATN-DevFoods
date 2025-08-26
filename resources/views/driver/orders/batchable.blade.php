@extends('layouts.driver.masterLayout')

@section('title', 'Ghép đơn hàng')

@push('styles')
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --success-color: #10b981;
    --success-hover: #059669;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --purple-color: #8b5cf6;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --border-radius: 12px;
    --border-radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    color: var(--gray-900);
    line-height: 1.6;
    font-weight: 400;
}

.modern-card {
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: var(--transition);
    overflow: hidden;
}

.modern-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.modern-card-header {
    padding: 24px;
    border-bottom: 1px solid var(--gray-100);
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.modern-card-body {
    padding: 24px;
}

.modern-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    background: white;
    color: var(--gray-700);
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}

.modern-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.modern-btn:hover::before {
    left: 100%;
}

.modern-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    text-decoration: none;
}

.modern-btn:active {
    transform: translateY(0);
}

.modern-btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    border-color: var(--primary-color);
}

.modern-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.modern-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: var(--shadow-sm);
}

.modern-badge-primary {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.current-order-card {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 2px solid #93c5fd;
    border-radius: var(--border-radius-lg);
    padding: 24px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}

.current-order-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--purple-color));
}

.modern-table {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.modern-table thead {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.modern-table th {
    padding: 16px;
    font-weight: 600;
    color: var(--gray-700);
    border-bottom: 1px solid var(--gray-200);
    font-size: 14px;
}

.modern-table td {
    padding: 16px;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}

.modern-table tbody tr {
    transition: var(--transition);
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
}

.modern-checkbox {
    width: 18px;
    height: 18px;
    border: 2px solid var(--gray-300);
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
}

.modern-checkbox:checked {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.selection-summary {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #bbf7d0;
    border-radius: var(--border-radius);
    padding: 20px;
    margin-top: 24px;
}
</style>
@endpush

@section('content')
<div class="container-fluid" style="background: var(--gray-50); min-height: 100vh; padding: 20px;">
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2" style="font-weight: 700; color: var(--gray-900);">
                            <i class="fas fa-link" style="color: var(--primary-color);"></i> Ghép đơn hàng
                        </h4>
                        <p class="mb-0" style="color: var(--gray-600); font-size: 15px;">
                            Chọn các đơn hàng phù hợp để ghép với đơn hàng hiện tại
                        </p>
                    </div>
                    <a href="{{ route('driver.orders.show', $currentOrder->id) }}" class="modern-btn">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại
                    </a>
                </div>
                
                <div class="modern-card-body">
                    <!-- Đơn hàng hiện tại -->
                    <div class="current-order-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--purple-color) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px;">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <h5 style="font-weight: 600; color: var(--gray-900); margin: 0;">
                                    Đơn hàng hiện tại
                                </h5>
                                <p style="color: var(--gray-600); margin: 0; font-size: 14px;">
                                    Đơn hàng gốc để ghép với các đơn khác
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <i class="fas fa-receipt me-2" style="color: var(--gray-500); width: 16px;"></i>
                                    <strong style="color: var(--gray-700);">Mã đơn:</strong> {{ $currentOrder->order_code }}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-user me-2" style="color: var(--gray-500); width: 16px;"></i>
                                    <strong style="color: var(--gray-700);">Khách hàng:</strong> {{ $currentOrder->customer_name }}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-phone me-2" style="color: var(--gray-500); width: 16px;"></i>
                                    <strong style="color: var(--gray-700);">Điện thoại:</strong> {{ $currentOrder->customer_phone }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2" style="color: var(--gray-500); width: 16px;"></i>
                                    <strong style="color: var(--gray-700);">Địa chỉ:</strong> {{ $currentOrder->display_full_delivery_address }}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-money-bill-wave me-2" style="color: var(--gray-500); width: 16px;"></i>
                                    <strong style="color: var(--gray-700);">Tổng tiền:</strong> 
                                    <span style="color: var(--success-color); font-weight: 700;">{{ number_format($currentOrder->total_amount) }}đ</span>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-info-circle me-2" style="color: var(--gray-500); width: 16px;"></i>
                                    <strong style="color: var(--gray-700);">Trạng thái:</strong> 
                                    <span class="modern-badge modern-badge-primary">
                                        {{ $currentOrder->status_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($batchableOrders->count() > 0)
                        <form action="{{ route('driver.orders.batch.create') }}" method="POST" id="batchForm">
                            @csrf
                            <input type="hidden" name="current_order_id" value="{{ $currentOrder->id }}">
                            
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div>
                                    <h5 style="font-weight: 600; color: var(--gray-900); margin: 0;">
                                        Chọn đơn hàng để ghép ({{ $batchableOrders->count() }} đơn có thể ghép)
                                    </h5>
                                    <p style="color: var(--gray-600); margin: 0; font-size: 14px;">
                                        Chọn các đơn hàng phù hợp để tạo lô hàng
                                    </p>
                                </div>
                            </div>
                            
                            <div class="modern-table">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">
                                                <input type="checkbox" id="selectAll" class="modern-checkbox">
                                            </th>
                                            <th>Mã đơn</th>
                                            <th>Khách hàng</th>
                                            <th>Địa chỉ</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Khoảng cách</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($batchableOrders as $order)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}" class="modern-checkbox order-checkbox">
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: var(--primary-color);">{{ $order->order_code }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div style="font-weight: 500; color: var(--gray-900);">{{ $order->customer_name }}</div>
                                                    <div style="font-size: 12px; color: var(--gray-500);">{{ $order->customer_phone }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="color: var(--gray-700);" title="{{ $order->display_full_delivery_address }}">
                                                    {{ Str::limit($order->display_full_delivery_address, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: var(--success-color);">
                                                    {{ number_format($order->total_amount) }}đ
                                                </span>
                                            </td>
                                            <td>
                                                <span class="modern-badge" style="background: {{ $order->status_color ?? '#e5e7eb' }}; color: {{ $order->status_text_color ?? '#374151' }}; border: 1px solid {{ $order->status_color ?? '#d1d5db' }};">
                                                    {{ $order->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(isset($order->distance))
                                                    @php
                                                        $distanceColor = '#10b981'; // Green for close distances
                                                        $distanceBg = '#d1fae5';
                                                        $distanceBorder = '#10b981';
                                                        
                                                        if ($order->distance > 5) {
                                                            $distanceColor = '#f59e0b'; // Orange for medium distances
                                                            $distanceBg = '#fef3c7';
                                                            $distanceBorder = '#f59e0b';
                                                        }
                                                        if ($order->distance > 6) {
                                                            $distanceColor = '#ef4444'; // Red for far distances
                                                            $distanceBg = '#fee2e2';
                                                            $distanceBorder = '#ef4444';
                                                        }
                                                    @endphp
                                                    <span class="modern-badge" style="background: {{ $distanceBg }}; color: {{ $distanceColor }}; border: 1px solid {{ $distanceBorder }};">
                                                        {{ number_format($order->distance, 1) }} km
                                                    </span>
                                                @else
                                                    <span style="color: var(--gray-400); font-style: italic;">Không có tọa độ</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="selection-summary" id="selectionSummary" style="display: none;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div>
                                        <h6 style="font-weight: 600; color: var(--gray-900); margin: 0;">
                                            Thông tin ghép đơn
                                        </h6>
                                        <p style="color: var(--gray-600); margin: 0; font-size: 14px;">
                                            Tóm tắt các đơn hàng đã chọn
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-shopping-cart" style="color: var(--primary-color);"></i>
                                            <strong style="color: var(--gray-700);">Số đơn đã chọn:</strong> 
                                            <span id="selectedCount" style="color: var(--primary-color); font-weight: 700;">0</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-money-bill-wave" style="color: var(--success-color);"></i>
                                            <strong style="color: var(--gray-700);">Tổng giá trị:</strong> 
                                            <span id="totalValue" style="color: var(--success-color); font-weight: 700;">0đ</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-route" style="color: var(--warning-color);"></i>
                                            <strong style="color: var(--gray-700);">Tổng khoảng cách:</strong> 
                                            <span id="totalDistance" style="color: var(--warning-color); font-weight: 700;">0 km</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end">
                                <button type="submit" class="modern-btn modern-btn-primary" id="batchButton" disabled>
                                    <i class="fas fa-link me-2"></i> Tạo lô hàng
                                </button>
                            </div>
                        </form>
                    @else
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #fbbf24; border-radius: var(--border-radius); padding: 24px; text-align: center;">
                            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin: 0 auto 16px;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h6 style="font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">
                                Không có đơn hàng phù hợp để ghép
                            </h6>
                            <p style="color: var(--gray-600); margin: 0;">
                                Hiện tại không có đơn hàng nào trong bán kính 7km phù hợp để ghép với đơn hàng hiện tại.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all functionality
    $('#selectAll').change(function() {
        $('.order-checkbox').prop('checked', this.checked);
        updateSelectionSummary();
    });
    
    // Individual checkbox change
    $('.order-checkbox').change(function() {
        updateSelectionSummary();
        
        // Update select all checkbox
        var totalCheckboxes = $('.order-checkbox').length;
        var checkedCheckboxes = $('.order-checkbox:checked').length;
        
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });
    
    function updateSelectionSummary() {
        var checkedBoxes = $('.order-checkbox:checked');
        var count = checkedBoxes.length;
        
        if (count > 0) {
            $('#selectionSummary').show();
            $('#selectedCount').text(count);
            $('#batchButton').prop('disabled', false);
            
            // Calculate total value and distance
            var totalValue = 0;
            var totalDistance = 0;
            
            checkedBoxes.each(function() {
                var row = $(this).closest('tr');
                var valueText = row.find('td:nth-child(5)').text().replace(/[^\d]/g, '');
                var distanceText = row.find('td:nth-child(7)').text();
                
                totalValue += parseInt(valueText) || 0;
                
                var distanceMatch = distanceText.match(/(\d+\.?\d*)/);
                if (distanceMatch) {
                    totalDistance += parseFloat(distanceMatch[1]);
                }
            });
            
            $('#totalValue').text(new Intl.NumberFormat('vi-VN').format(totalValue) + 'đ');
            $('#totalDistance').text(totalDistance.toFixed(1) + ' km');
        } else {
            $('#selectionSummary').hide();
            $('#batchButton').prop('disabled', true);
        }
    }
    
    // Form submission with loading state
    $('#batchForm').on('submit', function() {
        var button = $('#batchButton');
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin me-2"></i> Đang tạo lô hàng...');
    });
    
    // Add smooth animations
    $('.modern-checkbox').on('change', function() {
        $(this).closest('tr').toggleClass('table-active', this.checked);
    });
    
    // Hover effects for table rows
    $('.modern-table tbody tr').hover(
        function() {
            $(this).addClass('table-hover-effect');
        },
        function() {
            $(this).removeClass('table-hover-effect');
        }
    );
});
</script>

<style>
.table-active {
    background-color: var(--gray-50) !important;
    border-left: 3px solid var(--primary-color);
}

.table-hover-effect {
    background-color: var(--gray-50) !important;
    transform: scale(1.01);
    transition: var(--transition);
}

.modern-checkbox:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3e%3cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3e%3c/svg%3e");
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
}

.modern-checkbox:indeterminate {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3e%3cpath fill-rule='evenodd' d='M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.selection-summary {
    animation: fadeInUp 0.3s ease-out;
}
</style>
@endpush
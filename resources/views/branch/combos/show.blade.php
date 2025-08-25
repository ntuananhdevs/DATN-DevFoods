@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chi tiết combo - ' . $combo->name)

@section('content')
<style>
    .page-container {
        background: #f9fafb;
        min-height: 100vh;
        padding: 1.5rem 0;
    }
    
    .combo-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .combo-image {
        border-radius: 0.5rem;
        width: 100%;
        height: 320px;
        object-fit: cover;
        background: #f3f4f6;
    }
    
    .combo-image-section {
        margin-bottom: 1.5rem;
    }
    
    .combo-details {
        padding: 1rem;
    }
    
    .combo-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .combo-price {
        font-size: 2.25rem;
        font-weight: 700;
        color: #dc2626;
        margin-bottom: 1rem;
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
    }
    
    .detail-label {
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        color: #111827;
        font-weight: 400;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-active {
        background: #dcfce7;
        color: #166534;
    }
    
    .status-warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-danger {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .combo-products-section {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .combo-products-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        transition: background-color 0.2s;
    }
    
    .product-item:hover {
        background: #f3f4f6;
    }
    
    .product-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .product-image {
        width: 48px;
        height: 48px;
        border-radius: 0.375rem;
        object-fit: cover;
        background: #e5e7eb;
    }
    
    .product-name {
        font-weight: 500;
        color: #111827;
    }
    
    .product-quantity {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .product-price {
        font-weight: 600;
        color: #111827;
    }
    
    .table-combo {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .table-combo th {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
        padding: 0.75rem;
        font-size: 0.875rem;
    }
    
    .table-combo td {
        padding: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    
    .table-combo tbody tr:last-child td {
        border-bottom: none;
    }
    
    .product-image-container {
        width: 60px;
        height: 60px;
        margin: 0 auto;
    }
    
    .product-item-image {
        width: 60px;
        height: 60px;
        border-radius: 0.375rem;
        object-fit: cover;
        background: #f3f4f6;
    }
    
    .price-summary-card {
        background: #f9fafb;
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        margin-top: 1rem;
    }
    
    .savings-highlight {
        font-weight: 700;
        color: #059669 !important;
    }
    
    .badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }
    
    @media (max-width: 768px) {
        .combo-title {
            font-size: 1.5rem;
        }
        
        .combo-price {
            font-size: 1.875rem;
        }
        
        .detail-grid {
            grid-template-columns: 1fr;
        }
        
        .combo-details {
            padding: 0.5rem;
        }
        
        .table-combo {
            font-size: 0.875rem;
        }
        
        .product-image-container,
        .product-item-image {
            width: 48px;
            height: 48px;
        }
    }
    
    .page-header {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
    }
    
    .page-title {
        color: #111827;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
    }
    
    .btn-back {
        background: #3b82f6;
        border: 1px solid #3b82f6;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        color: white;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-back:hover {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
        text-decoration: none;
    }
    
    .table-combo {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    
    .table-combo thead {
        background: white;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .table-combo thead th {
        background: white;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
        padding: 0.75rem;
        font-size: 0.875rem;
        text-align: center;
    }
    
    .table-combo thead th:nth-child(2) {
        text-align: left;
    }
    
    .table-combo tbody td {
        padding: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        text-align: center;
    }
    
    .table-combo tbody td:nth-child(2) {
        text-align: left;
        font-weight: 600;
        color: #374151;
    }
    
    .table-combo tbody tr:last-child td {
        border-bottom: none;
    }
    
    .page-header {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
    }
    
    .page-title {
        color: #111827;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
    }
    
    .btn-back {
        background: #3b82f6;
        border: 1px solid #3b82f6;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        color: white;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-back:hover {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
        text-decoration: none;
    }
    
    .price-summary-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        margin-top: 1rem;
    }
    
    .product-item-image {
        width: 60px;
        height: 60px;
        border-radius: 0.375rem;
        object-fit: cover;
        background: #f3f4f6;
    }
    
    .product-image-container {
        width: 60px;
        height: 60px;
        margin: 0 auto;
    }
    
    .combo-products-section {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .combo-products-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    .savings-highlight {
        font-weight: 700;
        color: #059669 !important;
    }
    
    .container-fluid {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .text-center {
        text-align: center;
    }
    
    .py-5 {
        padding: 3rem 0;
    }
    
    .fa-3x {
        font-size: 3rem;
    }
    
    .text-muted {
        color: #6b7280;
    }
    
    .mb-3 {
        margin-bottom: 0.75rem;
    }
    
    .mr-2 {
        margin-right: 0.5rem;
    }
    
    .m-0 {
        margin: 0;
    }
    
    .mb-4 {
        margin-bottom: 1rem;
    }
    
    .mt-3 {
        margin-top: 0.75rem;
    }
    
    .mb-2 {
        margin-bottom: 0.5rem;
    }
    
    .mr-1 {
        margin-right: 0.25rem;
    }
    
    .d-flex {
        display: flex;
    }
    
    .justify-content-between {
        justify-content: space-between;
    }
    
    .align-items-center {
        align-items: center;
    }
    
    .font-weight-bold {
        font-weight: 700;
    }
    
    .text-success {
        color: #ea580c;
        font-weight: 600;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .d-sm-flex {
        display: flex;
    }
    
    .col-lg-4 {
        width: 33.333333%;
    }
    
    .col-lg-8 {
        width: 66.666667%;
    }
    
    .col-md-6 {
        width: 50%;
    }
    
    .offset-md-6 {
        margin-left: 50%;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -0.75rem;
    }
    
    .row > * {
        padding: 0 0.75rem;
    }
    
    @media (max-width: 768px) {
        .col-lg-4, .col-lg-8, .col-md-6 {
            width: 100%;
        }
        
        .offset-md-6 {
            margin-left: 0;
        }
        
        .d-sm-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .page-header {
            text-align: center;
        }
        
        .container-fluid {
            padding: 0 1rem;
        }
    }
</style>

<div class="page-container">
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-sm-flex align-items-center justify-content-between">
        <h1 class="page-title">Chi tiết combo</h1>
        <a href="{{ route('branch.combos') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <!-- Combo Image -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="combo-image-section">
                @if($combo->image)
                    <img src="{{ Storage::disk('s3')->url($combo->image) }}" alt="{{ $combo->name }}" class="combo-image">
                @else
                    <div class="combo-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Combo Details -->
        <div class="col-lg-8 col-md-6">
            <div class="combo-details">
                <h1 class="combo-title">{{ $combo->name }}</h1>
                <div class="combo-price">{{ number_format($combo->price, 0, ',', '.') }}đ</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Mã combo:</span>
                        <span class="detail-value">{{ $combo->sku }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Danh mục:</span>
                        <span class="detail-value">Combo</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Trạng thái:</span>
                        <span class="detail-value">
                            @if($combo->status == 'selling' && $combo->active)
                                <span class="status-badge status-active">Đang bán</span>
                            @elseif($combo->status == 'coming_soon')
                                <span class="status-badge status-warning">Sắp ra mắt</span>
                            @elseif($combo->status == 'discontinued' || !$combo->active)
                                <span class="status-badge status-danger">Ngừng bán</span>
                            @else
                                <span class="status-badge status-warning">Không hoạt động</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tồn kho:</span>
                        <span class="detail-value">
                            @php
                                $comboStock = $combo->comboBranchStocks ? $combo->comboBranchStocks->first() : null;
                            @endphp
                            @if($comboStock && $comboStock->quantity > 0)
                                {{ $comboStock->quantity }} combo
                            @elseif($comboStock && $comboStock->quantity == 0)
                                Hết hàng
                            @else
                                Chưa có thông tin
                            @endif
                        </span>
                    </div>
                </div>
                
                @if($combo->description)
                <div class="combo-description">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">Mô tả:</h3>
                    <p style="color: #6b7280; line-height: 1.5;">{{ $combo->description }}</p>
                </div>
                @endif
                
                <div class="combo-products-section">
                    <h3 class="combo-products-title">Sản phẩm trong combo</h3>
                    @if($combo->comboItems && $combo->comboItems->count() > 0)
                        @php
                            $totalOriginalPrice = 0;
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-combo">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th style="width: 100px;">Số lượng</th>
                                        <th style="width: 120px;">Đơn giá</th>
                                        <th style="width: 120px;">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($combo->comboItems as $comboItem)
                                        @php
                                            $productVariant = $comboItem->productVariant;
                                            $productPrice = $productVariant->price ?? 0;
                                            $subtotal = $productPrice * $comboItem->quantity;
                                            $totalOriginalPrice += $subtotal;
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <div class="product-image-container">
                                                    @if($productVariant && $productVariant->product && $productVariant->product->image)
                                                        <img src="{{ Storage::disk('s3')->url($productVariant->product->image) }}" 
                                                             alt="{{ $productVariant->product->name }}" 
                                                             class="product-item-image">
                                                    @else
                                                        <div class="product-item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $productVariant->product->name ?? 'Sản phẩm không tồn tại' }}</strong>
                                                @if($productVariant && $productVariant->sku)
                                                    <br><small class="text-muted">SKU: {{ $productVariant->sku }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $comboItem->quantity }}</span>
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($productPrice, 0, ',', '.') }}đ
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có sản phẩm nào trong combo này.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
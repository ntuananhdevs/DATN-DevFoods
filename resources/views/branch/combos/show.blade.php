@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chi tiết combo - ' . $combo->name)

@section('content')
<style>
    .page-container {
        background: linear-gradient(135deg, #fff5f0 0%, #fef7f0 100%);
        min-height: 100vh;
        padding: 1.5rem 0;
    }
    
    .combo-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(251, 146, 60, 0.1), 0 2px 4px -1px rgba(251, 146, 60, 0.06);
        border: 1px solid #fed7aa;
        margin-bottom: 1.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .combo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px -3px rgba(251, 146, 60, 0.15), 0 4px 6px -2px rgba(251, 146, 60, 0.1);
    }
    
    .card-header-combo {
        background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        border-bottom: none;
        position: relative;
        overflow: hidden;
    }
    
    .card-header-combo::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .card-header-combo:hover::before {
        left: 100%;
    }
    
    .combo-image {
        border-radius: 0.5rem;
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        transition: all 0.3s ease;
        border: 2px solid #fed7aa;
        box-shadow: 0 4px 6px -1px rgba(251, 146, 60, 0.1);
    }
    
    .combo-image:hover {
        transform: scale(1.05);
        border-color: #fb923c;
        box-shadow: 0 8px 15px -3px rgba(251, 146, 60, 0.2);
    }
    
    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 500;
        color: #374151;
        min-width: 140px;
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
    
    .info-value {
        flex: 1;
        color: #111827;
        font-weight: 400;
    }
    
    .price-highlight {
        font-size: 1.25rem;
        font-weight: 700;
        color: #ea580c;
        text-shadow: 0 1px 2px rgba(234, 88, 12, 0.1);
    }
    
    .badge-custom {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .badge-success {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
    
    .badge-info {
        background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(251, 146, 60, 0.2);
    }
    
    .badge-secondary {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(156, 163, 175, 0.2);
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
    }
    
    .badge-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
    }
    
    .table-combo {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .table-combo thead {
        background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
        border-bottom: 1px solid #fb923c;
    }
    
    .table-combo thead th {
        border: none;
        padding: 0.75rem 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-size: 0.75rem;
        color: #9a3412;
        text-align: center;
    }
    
    .table-combo thead th:nth-child(2) {
        text-align: left;
    }
    
    .table-combo tbody td {
        padding: 1rem;
        border-top: 1px solid #f3f4f6;
        vertical-align: middle;
        text-align: center;
    }
    
    .table-combo tbody td:nth-child(2) {
        text-align: left;
    }
    
    .table-combo tbody tr:hover {
        background-color: #fff7ed;
        transform: scale(1.01);
        transition: all 0.2s ease;
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
        background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        border: 1px solid #fb923c;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        color: white;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(251, 146, 60, 0.2);
    }
    
    .btn-back:hover {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border-color: #f97316;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(251, 146, 60, 0.3);
    }
    
    .price-summary-card {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        border: 1px solid #fed7aa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 1rem;
        box-shadow: 0 4px 6px -1px rgba(251, 146, 60, 0.1);
    }
    
    .product-item-image {
        border-radius: 0.375rem;
        width: 50px;
        height: 50px;
        object-fit: cover;
        box-shadow: 0 2px 4px rgba(251, 146, 60, 0.15);
        border: 1px solid #fed7aa;
        transition: all 0.2s ease;
    }
    
    .product-item-image:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(251, 146, 60, 0.25);
        border-color: #fb923c;
    }
    
    .savings-highlight {
        color: #ea580c;
        font-weight: 700;
        font-size: 1.125rem;
        text-shadow: 0 1px 2px rgba(234, 88, 12, 0.1);
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
        <a href="{{ route('branch.combos') }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <!-- Combo Image -->
        <div class="col-lg-4">
            <div class="card combo-card mb-4">
                <div class="card-header card-header-combo">
                    <h6 class="m-0"><i class="fas fa-images mr-2"></i>Hình ảnh combo</h6>
                </div>
                <div class="card-body text-center">
                    @if($combo->image)
                        <img src="{{ Storage::disk('s3')->url($combo->image) }}" 
                             class="img-fluid combo-image" 
                             alt="{{ $combo->name }}"
                             style="max-height: 350px; object-fit: cover;">
                    @else
                        <div class="py-5">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có hình ảnh</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Combo Information -->
        <div class="col-lg-8">
            <div class="card combo-card mb-4">
                <div class="card-header card-header-combo">
                    <h6 class="m-0"><i class="fas fa-info-circle mr-2"></i>Thông tin combo</h6>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-tag mr-2"></i>Tên combo:</span>
                        <span class="info-value">{{ $combo->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-barcode mr-2"></i>Mã combo:</span>
                        <span class="info-value"><code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $combo->sku }}</code></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-money-bill-wave mr-2"></i>Giá bán:</span>
                        <span class="info-value price-highlight">{{ number_format($combo->price, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-sort-numeric-up mr-2"></i>Số lượng:</span>
                        <span class="info-value">
                            @php
                                $comboStock = $combo->comboBranchStocks ? $combo->comboBranchStocks->first() : null;
                            @endphp
                            @if($comboStock && $comboStock->quantity > 0)
                                <span class="badge badge-success badge-custom">
                                    <i class="fas fa-box mr-1"></i>{{ $comboStock->quantity }} combo
                                </span>
                            @elseif($comboStock && $comboStock->quantity == 0)
                                <span class="badge badge-danger badge-custom">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Hết hàng
                                </span>
                            @else
                                <span class="badge badge-warning badge-custom">
                                    <i class="fas fa-question-circle mr-1"></i>Chưa có thông tin
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-toggle-on mr-2"></i>Trạng thái:</span>
                        <span class="info-value">
                            @if($combo->status == 'selling' && $combo->active)
                                <span class="badge badge-success badge-custom"><i class="fas fa-check-circle mr-1"></i>Đang bán</span>
                            @elseif($combo->status == 'coming_soon')
                                <span class="badge badge-info badge-custom"><i class="fas fa-clock mr-1"></i>Sắp ra mắt</span>
                            @elseif($combo->status == 'discontinued' || !$combo->active)
                                <span class="badge badge-danger badge-custom"><i class="fas fa-times-circle mr-1"></i>Ngừng bán</span>
                            @else
                                <span class="badge badge-secondary badge-custom"><i class="fas fa-pause-circle mr-1"></i>Không hoạt động</span>
                            @endif
                        </span>
                    </div>

                    @if($combo->description)
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-align-left mr-2"></i>Mô tả:</span>
                            <span class="info-value" style="line-height: 1.6;">{{ $combo->description }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Combo Items -->
    @if($combo->comboItems->count() > 0)
        <div class="card combo-card mb-4">
            <div class="card-header card-header-combo">
                <h6 class="m-0"><i class="fas fa-list-ul mr-2"></i>Sản phẩm trong combo</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-combo">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá gốc</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($combo->comboItems as $item)
                                <tr>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            @if($item->productVariant && $item->productVariant->product && $item->productVariant->product->images->count() > 0)
                                                <img src="{{ Storage::disk('s3')->url($item->productVariant->product->images->first()->img) }}" 
                                                     alt="{{ $item->productVariant->product->name }}"
                                                     class="product-item-image">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center rounded" 
                                                     style="width: 50px; height: 50px; background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%); border: 1px solid #fb923c;">
                                                    <i class="fas fa-image" style="color: #9a3412;"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->productVariant && $item->productVariant->product)
                                            {{ $item->productVariant->product->name }}
                                        @else
                                            <span class="text-muted">Sản phẩm không tồn tại</span>
                                        @endif
                                    </td>
                                    <td class="text-center font-weight-bold">{{ $item->quantity }}</td>
                                    <td class="text-center font-weight-bold" style="color: #ea580c;">
                                        @if($item->productVariant)
                                            {{ number_format($item->productVariant->price, 0, ',', '.') }}đ
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Price Summary -->
                <div class="row mt-3">
                    <div class="col-md-6 offset-md-6">
                        <div class="card price-summary-card">
                            <div class="card-body">
                                @php
                                    $totalOriginalPrice = 0;
                                    if($combo->comboItems) {
                                        foreach($combo->comboItems as $item) {
                                            if($item->productVariant) {
                                                $totalOriginalPrice += $item->productVariant->price * $item->quantity;
                                            }
                                        }
                                    }
                                    $savings = $totalOriginalPrice - $combo->price;
                                @endphp
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tổng giá gốc:</span>
                                    <span>{{ number_format($totalOriginalPrice, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Giá combo:</span>
                                    <span class="text-success font-weight-bold">{{ number_format($combo->price, 0, ',', '.') }}đ</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="font-weight-bold"><i class="fas fa-piggy-bank mr-2"></i>Tiết kiệm:</span>
                                    <span class="savings-highlight">{{ number_format($savings, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
@endsection
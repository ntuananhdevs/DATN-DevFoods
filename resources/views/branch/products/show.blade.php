@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
<style>
    .product-detail-container {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 30px 0;
    }
    
    .product-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        background: white;
        margin-bottom: 20px;
    }
    
    .card-header-custom {
        background-color: #495057;
        color: white;
        border-bottom: 1px solid #dee2e6;
        padding: 15px 20px;
        font-weight: 600;
        border-radius: 8px 8px 0 0;
    }
    
    .product-image {
        border-radius: 6px;
        width: 100%;
        height: 350px;
        object-fit: cover;
    }
    
    .info-row {
        padding: 12px 0;
        border-bottom: 1px solid #f1f3f4;
        display: flex;
        align-items: center;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 140px;
        display: flex;
        align-items: center;
    }
    
    .info-value {
        flex: 1;
        color: #212529;
    }
    
    .price-highlight {
        font-size: 1.5em;
        font-weight: 700;
        color: #28a745;
    }
    
    .badge-custom {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.875em;
        font-weight: 500;
    }
    
    .table-custom {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .table-custom thead {
        background-color: #6c757d;
        color: white;
    }
    
    .table-custom thead th {
        border: none;
        padding: 12px 15px;
        font-weight: 600;
    }
    
    .table-custom tbody td {
        padding: 12px 15px;
        border-top: 1px solid #dee2e6;
        vertical-align: middle;
    }
    
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .topping-item {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .topping-item:hover {
        border-color: #6c757d;
        background-color: #f8f9fa;
    }
    
    .page-header {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px 25px;
        margin-bottom: 25px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .page-title {
        color: #212529;
        font-weight: 700;
        margin: 0;
        font-size: 1.75rem;
    }
    
    .btn-back {
        background-color: #6c757d;
        border: 1px solid #6c757d;
        border-radius: 6px;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        font-weight: 500;
    }
    
    .btn-back:hover {
        background-color: #5a6268;
        border-color: #545b62;
        color: white;
        text-decoration: none;
    }
    
    .carousel-item img {
        border-radius: 6px;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 8%;
    }
    
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(108, 117, 125, 0.8);
        border-radius: 4px;
        width: 40px;
        height: 40px;
    }
    
    .product-section {
        margin-bottom: 25px;
    }
    
    .section-spacing {
        margin-top: 25px;
    }
</style>
<div class="product-detail-container">
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title">Chi tiết sản phẩm</h1>
            <a href="{{ route('branch.products') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-5">
            <div class="card product-card mb-4">
                <div class="card-header card-header-custom">
                    <h6 class="m-0"><i class="fas fa-images mr-2"></i>Hình ảnh sản phẩm</h6>
                </div>
                <div class="card-body">
                    @if($product->images->count() > 0)
                        <div id="productCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($product->images as $index => $image)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ Storage::disk('s3')->url($image->img) }}" 
                                             class="d-block w-100 product-image" 
                                             alt="{{ $product->name }}"
                                             style="height: 350px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if($product->images->count() > 1)
                                <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có hình ảnh</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="col-lg-7">
            <div class="card product-card mb-4">
                <div class="card-header card-header-custom">
                    <h6 class="m-0"><i class="fas fa-info-circle mr-2"></i>Thông tin sản phẩm</h6>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-tag mr-2"></i>Tên sản phẩm:</span>
                        <span class="info-value">{{ $product->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-barcode mr-2"></i>Mã sản phẩm:</span>
                        <span class="info-value"><code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $product->sku }}</code></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-list mr-2"></i>Danh mục:</span>
                        <span class="info-value">
                            <span class="badge badge-info badge-custom">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-money-bill-wave mr-2"></i>Giá bán:</span>
                        <span class="info-value">
                            <span class="price-highlight">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-toggle-on mr-2"></i>Trạng thái:</span>
                        <span class="info-value">
                            @if($product->status == 'selling')
                                <span class="badge badge-success">Đang bán</span>
                            @elseif($product->status == 'out_of_stock')
                                <span class="badge badge-warning">Hết hàng</span>
                            @else
                                <span class="badge badge-secondary">Ngừng bán</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-boxes mr-2"></i>Tồn kho:</span>
                        <span class="info-value">
                            @php
                                $stock = $product->branchStocks ? $product->branchStocks->first() : null;
                            @endphp
                            @if($stock)
                                <span class="badge {{ $stock->quantity > 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $stock->quantity }} sản phẩm
                                </span>
                            @else
                                <span class="badge badge-secondary">Chưa có thông tin</span>
                            @endif
                        </span>
                    </div>
                    @if($product->description)
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-align-left mr-2"></i>Mô tả:</span>
                            <span class="info-value" style="line-height: 1.6;">{{ $product->description }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Variants -->
            @if($product->variants->count() > 0)
                <div class="card product-card mb-4">
                    <div class="card-header card-header-custom">
                        <h6 class="m-0"><i class="fas fa-cogs mr-2"></i>Biến thể sản phẩm</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-custom">
                                <thead>
                                    <tr>
                                        <th>Tên biến thể</th>
                                        <th>Thuộc tính</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr>
                                            <td>{{ $variant->name }}</td>
                                            <td>
                                                @foreach($variant->attributes as $attribute)
                                                    <span class="badge badge-secondary mr-1">{{ $attribute->name }}: {{ $attribute->value }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ number_format($variant->price, 0, ',', '.') }}đ</td>
                                            <td>
                                                @if($variant->status == 'active')
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-secondary">Không hoạt động</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Product Toppings -->
            @if($product->toppings->count() > 0)
                <div class="card product-card mb-4">
                    <div class="card-header card-header-custom">
                        <h6 class="m-0"><i class="fas fa-plus-circle mr-2"></i>Topping có thể thêm</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($product->toppings as $topping)
                                <div class="col-md-6 mb-2">
                                    <div class="topping-item d-flex justify-content-between align-items-center p-3">
                                        <span style="font-weight: 500;"><i class="fas fa-cookie-bite mr-2"></i>{{ $topping->name }}</span>
                                        <span class="text-success font-weight-bold">+{{ number_format($topping->price, 0, ',', '.') }}đ</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
@extends('branch.layouts.app')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết sản phẩm</h1>
        <a href="{{ route('branch.products') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh sản phẩm</h6>
                </div>
                <div class="card-body">
                    @if($product->images->count() > 0)
                        <div id="productCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($product->images as $index => $image)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                                             class="d-block w-100" 
                                             alt="{{ $product->name }}"
                                             style="height: 300px; object-fit: cover;">
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Tên sản phẩm:</strong></div>
                        <div class="col-sm-9">{{ $product->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Mã sản phẩm:</strong></div>
                        <div class="col-sm-9">{{ $product->sku }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Danh mục:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge badge-info">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Giá bán:</strong></div>
                        <div class="col-sm-9">
                            <span class="text-success font-weight-bold">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Trạng thái:</strong></div>
                        <div class="col-sm-9">
                            @if($product->status == 'selling')
                                <span class="badge badge-success">Đang bán</span>
                            @elseif($product->status == 'out_of_stock')
                                <span class="badge badge-warning">Hết hàng</span>
                            @else
                                <span class="badge badge-secondary">Ngừng bán</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Tồn kho:</strong></div>
                        <div class="col-sm-9">
                            @php
                                $stock = $product->branchStocks->first();
                            @endphp
                            @if($stock)
                                <span class="badge {{ $stock->quantity > 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $stock->quantity }} sản phẩm
                                </span>
                            @else
                                <span class="badge badge-secondary">Chưa có thông tin</span>
                            @endif
                        </div>
                    </div>
                    @if($product->description)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Mô tả:</strong></div>
                            <div class="col-sm-9">{{ $product->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Variants -->
            @if($product->variants->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Biến thể sản phẩm</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Topping có thể thêm</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($product->toppings as $topping)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                        <span>{{ $topping->name }}</span>
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

    <!-- Product Reviews -->
    @if($product->reviews->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Đánh giá từ khách hàng ({{ $product->reviews->count() }} đánh giá)</h6>
            </div>
            <div class="card-body">
                @foreach($product->reviews->take(5) as $review)
                    <div class="media mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="media-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="mt-0">{{ $review->customer->name ?? 'Khách hàng' }}</h6>
                                <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <span class="ml-2">{{ $review->rating }}/5</span>
                            </div>
                            @if($review->comment)
                                <p class="mb-0">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($product->reviews->count() > 5)
                    <div class="text-center">
                        <small class="text-muted">Và {{ $product->reviews->count() - 5 }} đánh giá khác...</small>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
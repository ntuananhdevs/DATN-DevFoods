@extends('branch.layouts.app')

@section('title', 'Chi tiết combo - ' . $combo->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết combo</h1>
        <a href="{{ route('branch.combos') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <!-- Combo Image -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh combo</h6>
                </div>
                <div class="card-body text-center">
                    @if($combo->image)
                        <img src="{{ asset('storage/' . $combo->image) }}" 
                             class="img-fluid rounded" 
                             alt="{{ $combo->name }}"
                             style="max-height: 300px; object-fit: cover;">
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin combo</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Tên combo:</strong></div>
                        <div class="col-sm-9">{{ $combo->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Mã combo:</strong></div>
                        <div class="col-sm-9">{{ $combo->sku }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Giá bán:</strong></div>
                        <div class="col-sm-9">
                            <span class="text-success font-weight-bold">{{ number_format($combo->price, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Số lượng:</strong></div>
                        <div class="col-sm-9">{{ $combo->quantity }} phần</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Trạng thái:</strong></div>
                        <div class="col-sm-9">
                            @if($combo->status == 'active')
                                <span class="badge badge-success">Hoạt động</span>
                            @else
                                <span class="badge badge-secondary">Không hoạt động</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Tồn kho:</strong></div>
                        <div class="col-sm-9">
                            @php
                                $stock = $combo->branchStocks->first();
                            @endphp
                            @if($stock)
                                <span class="badge {{ $stock->quantity > 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $stock->quantity }} combo
                                </span>
                            @else
                                <span class="badge badge-secondary">Chưa có thông tin</span>
                            @endif
                        </div>
                    </div>
                    @if($combo->description)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Mô tả:</strong></div>
                            <div class="col-sm-9">{{ $combo->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Combo Items -->
    @if($combo->comboItems->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sản phẩm trong combo</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Số lượng</th>
                                <th>Giá gốc</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($combo->comboItems as $item)
                                <tr>
                                    <td>
                                        @if($item->productVariant && $item->productVariant->product && $item->productVariant->product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $item->productVariant->product->images->first()->image_url) }}" 
                                                 alt="{{ $item->productVariant->product->name }}"
                                                 style="width: 50px; height: 50px; object-fit: cover;" 
                                                 class="rounded">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->productVariant && $item->productVariant->product)
                                            {{ $item->productVariant->product->name }}
                                        @else
                                            <span class="text-muted">Sản phẩm không tồn tại</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->productVariant)
                                            <div>
                                                <strong>{{ $item->productVariant->name }}</strong>
                                                @if($item->productVariant->attributes->count() > 0)
                                                    <br>
                                                    @foreach($item->productVariant->attributes as $attribute)
                                                        <small class="badge badge-secondary mr-1">{{ $attribute->name }}: {{ $attribute->value }}</small>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Biến thể không tồn tại</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
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
                        <div class="card bg-light">
                            <div class="card-body">
                                @php
                                    $totalOriginalPrice = 0;
                                    foreach($combo->items as $item) {
                                        if($item->productVariant) {
                                            $totalOriginalPrice += $item->productVariant->price * $item->quantity;
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
                                    <span class="font-weight-bold">Tiết kiệm:</span>
                                    <span class="text-danger font-weight-bold">{{ number_format($savings, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Combo Reviews -->
    @if($combo->reviews->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Đánh giá từ khách hàng ({{ $combo->reviews->count() }} đánh giá)</h6>
            </div>
            <div class="card-body">
                @foreach($combo->reviews->take(5) as $review)
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
                @if($combo->reviews->count() > 5)
                    <div class="text-center">
                        <small class="text-muted">Và {{ $combo->reviews->count() - 5 }} đánh giá khác...</small>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
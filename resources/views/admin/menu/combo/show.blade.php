@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi Tiết Combo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi Tiết Combo: {{ $combo->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.combos.edit', $combo) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.combos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Thông tin cơ bản -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Tên Combo:</strong></label>
                                        <p class="form-control-static">{{ $combo->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Danh mục:</strong></label>
                                        <p class="form-control-static">
                                            @if($combo->category)
                                                <span class="badge badge-info">{{ $combo->category->name }}</span>
                                            @else
                                                <span class="text-muted">Chưa phân loại</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Giá bán:</strong></label>
                                        <p class="form-control-static text-success">
                                            <strong>{{ number_format($combo->price, 0, ',', '.') }} VNĐ</strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Giá gốc:</strong></label>
                                        <p class="form-control-static">
                                            @if($combo->original_price)
                                                <span class="text-muted">{{ number_format($combo->original_price, 0, ',', '.') }} VNĐ</span>
                                                @if($combo->original_price > $combo->price)
                                                    <span class="badge badge-danger ml-2">
                                                        -{{ round((($combo->original_price - $combo->price) / $combo->original_price) * 100) }}%
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Mô tả ngắn:</strong></label>
                                        <p class="form-control-static">
                                            {{ $combo->short_description ?: 'Không có mô tả ngắn' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Mô tả chi tiết:</strong></label>
                                        <div class="form-control-static">
                                            {!! nl2br(e($combo->description ?: 'Không có mô tả chi tiết')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Trạng thái:</strong></label>
                                        <p class="form-control-static">
                                            @if($combo->active)
                                                <span class="badge badge-success">Kích hoạt</span>
                                            @else
                                                <span class="badge badge-secondary">Không kích hoạt</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Nổi bật:</strong></label>
                                        <p class="form-control-static">
                                            @if($combo->featured)
                                                <span class="badge badge-warning">Nổi bật</span>
                                            @else
                                                <span class="badge badge-light">Thường</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Ngày tạo:</strong></label>
                                        <p class="form-control-static">{{ $combo->created_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Cập nhật lần cuối:</strong></label>
                                        <p class="form-control-static">{{ $combo->updated_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hình ảnh -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Hình ảnh:</strong></label>
                                <div class="text-center">
                                    @if($combo->image)
                                        <img src="{{ asset('storage/' . $combo->image) }}" 
                                             alt="{{ $combo->name }}" 
                                             class="img-fluid img-thumbnail" 
                                             style="max-width: 300px; max-height: 300px;">
                                    @else
                                        <div class="no-image-placeholder">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">Không có hình ảnh</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sản phẩm trong combo -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sản phẩm trong combo ({{ $combo->products->count() }} sản phẩm)</h3>
                </div>
                <div class="card-body">
                    @if($combo->products->count() > 0)
                        <div class="row">
                            @foreach($combo->products as $product)
                                <div class="col-md-4 mb-3">
                                    <div class="card product-item">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                                             alt="{{ $product->name }}" 
                                                             class="img-fluid rounded" 
                                                             style="max-height: 80px; object-fit: cover;">
                                                    @else
                                                        <div class="no-image-small">
                                                            <i class="fas fa-image fa-2x text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="card-title mb-1">
                                                        <a href="{{ route('admin.products.show', $product->id) }}" class="text-decoration-none">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h6>
                                                    <p class="card-text text-success mb-1">
                                                        <strong>{{ number_format($product->price, 0, ',', '.') }} VNĐ</strong>
                                                    </p>
                                                    <p class="card-text">
                                                        <small class="text-muted">{{ $product->category->name ?? 'Chưa phân loại' }}</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Tổng giá trị -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Tổng giá trị sản phẩm:</strong> 
                                            {{ number_format($combo->products->sum('price'), 0, ',', '.') }} VNĐ
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Giá combo:</strong> 
                                            {{ number_format($combo->price, 0, ',', '.') }} VNĐ
                                            @php
                                                $totalProductPrice = $combo->products->sum('price');
                                                $savings = $totalProductPrice - $combo->price;
                                            @endphp
                                            @if($savings > 0)
                                                <span class="badge badge-success ml-2">
                                                    Tiết kiệm: {{ number_format($savings, 0, ',', '.') }} VNĐ
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Combo này chưa có sản phẩm nào</p>
                            <a href="{{ route('admin.combos.edit', $combo) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm sản phẩm
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thống kê bán hàng</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tổng đơn hàng</span>
                                    <span class="info-box-number">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doanh thu</span>
                                    <span class="info-box-number">0 VNĐ</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tháng này</span>
                                    <span class="info-box-number">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-star"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Đánh giá TB</span>
                                    <span class="info-box-number">0/5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.combos.edit', $combo) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.combos.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-list"></i> Danh sách
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash"></i> Xóa Combo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa combo "{{ $combo->name }}"? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.combos.destroy', $combo) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-control-static {
    padding-top: 7px;
    padding-bottom: 7px;
    margin-bottom: 0;
    min-height: 34px;
}

.no-image-placeholder {
    padding: 50px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}

.no-image-small {
    padding: 20px;
    background-color: #f8f9fa;
    border: 1px dashed #dee2e6;
    border-radius: 4px;
    text-align: center;
}

.product-item {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.product-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.25);
}

.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}
</style>
@endpush
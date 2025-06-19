@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi Tiết Topping')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi Tiết Topping: {{ $topping->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.toppings.edit', $topping) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.toppings.index') }}" class="btn btn-secondary btn-sm">
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
                                        <label><strong>Tên Topping:</strong></label>
                                        <p class="form-control-static">{{ $topping->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Giá:</strong></label>
                                        <p class="form-control-static text-success">
                                            <strong>{{ number_format($topping->price, 0, ',', '.') }} VNĐ</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Mô tả:</strong></label>
                                        <p class="form-control-static">
                                            {{ $topping->description ?: 'Không có mô tả' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Trạng thái:</strong></label>
                                        <p class="form-control-static">
                                            @if($topping->active)
                                                <span class="badge badge-success">Kích hoạt</span>
                                            @else
                                                <span class="badge badge-secondary">Không kích hoạt</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Ngày tạo:</strong></label>
                                        <p class="form-control-static">{{ $topping->created_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Cập nhật lần cuối:</strong></label>
                                        <p class="form-control-static">{{ $topping->updated_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hình ảnh -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Hình ảnh:</strong></label>
                                <div class="text-center">
                                    @if($topping->image)
                                        <img src="{{ asset('storage/' . $topping->image) }}" 
                                             alt="{{ $topping->name }}" 
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

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.toppings.edit', $topping) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.toppings.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-list"></i> Danh sách
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash"></i> Xóa Topping
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê sử dụng topping -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thống kê sử dụng</h3>
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
                Bạn có chắc chắn muốn xóa topping "{{ $topping->name }}"? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.toppings.destroy', $topping) }}" method="POST" class="d-inline">
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
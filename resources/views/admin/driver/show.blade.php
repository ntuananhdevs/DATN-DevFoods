@extends('layouts/admin/contentLayoutMaster')
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Chi tiết tài xế</h3>
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
                <div class="card-body">
                    <!-- Thông tin cơ bản -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            @if($driver->profile_image)
                                <img src="{{ asset($driver->profile_image) }}" class="img-thumbnail mb-2" style="max-width: 150px;" alt="Ảnh đại diện">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($driver->name) }}&background=eee&color=555" class="img-thumbnail mb-2" style="max-width: 150px;" alt="Ảnh đại diện">
                            @endif
                            <div>
                                <span class="badge badge-{{ $driver->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $driver->status === 'active' ? 'Đang hoạt động' : 'Không hoạt động' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h4>{{ $driver->name }}</h4>
                            <p class="mb-1"><strong>Email:</strong> {{ $driver->email }}</p>
                            <p class="mb-1"><strong>Số điện thoại:</strong> {{ $driver->phone_number }}</p>
                            <p class="mb-1"><strong>Ngày tham gia:</strong> {{ $driver->created_at->format('d/m/Y') }}</p>
                            <p class="mb-1">
                                <strong>Đánh giá:</strong>
                                <i class="fas fa-star text-warning"></i>
                                {{ number_format($driver->rating, 1) }}
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Thông tin phương tiện -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Thông tin phương tiện</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 200px;">Loại xe</th>
                                        <td>{{ $driver->vehicle_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Màu xe</th>
                                        <td>{{ $driver->vehicle_color }}</td>
                                    </tr>
                                    <tr>
                                        <th>Số GPLX</th>
                                        <td>{{ $driver->license_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Giấy đăng ký xe</th>
                                        <td>
                                            @if($driver->vehicle_registration)
                                                <img src="{{ asset($driver->vehicle_registration) }}" class="img-thumbnail" style="max-width: 200px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Thống kê hoạt động -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Thống kê hoạt động</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Số chuyến đã hoàn thành</h6>
                                            <h3 class="mb-0">{{ $driver->completed_trips ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Số lần hủy chuyến</h6>
                                            <h3 class="mb-0">{{ $driver->cancellation_count ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Điểm tin cậy</h6>
                                            <h3 class="mb-0">{{ $driver->reliability_score ?? 100 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Số lần vi phạm</h6>
                                            <h3 class="mb-0">{{ $driver->penalty_count ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vị trí hiện tại -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Vị trí hiện tại</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-map-marker-alt"></i>
                                @if($driver->current_latitude && $driver->current_longitude)
                                    Vĩ độ: {{ $driver->current_latitude }}, Kinh độ: {{ $driver->current_longitude }}
                                @else
                                    Chưa cập nhật vị trí
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Nút thao tác -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.drivers.edit', $driver->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger" onclick="dtmodalConfirmDelete({
                                    itemName: '{{ $driver->name }}',
                                    onConfirm: () => this.closest('form').submit()
                                })">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

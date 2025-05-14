@extends('layouts/admin/contentLayoutMaster')
@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="alert-message">
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-message">
            {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Chi tiết đơn ứng tuyển tài xế</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 text-center">
                            @if($application->profile_image)
                                <img src="{{ asset($application->profile_image) }}" class="img-thumbnail mb-2" style="max-width: 150px;" alt="Ảnh đại diện">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($application->full_name) }}&background=eee&color=555" class="img-thumbnail mb-2" style="max-width: 150px;" alt="Ảnh đại diện">
                            @endif
                            <div><span class="badge badge-{{ $application->status == 'approved' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($application->status) }}</span></div>
                        </div>
                        <div class="col-md-9">
                            <h4>{{ $application->full_name }}</h4>
                            <p class="mb-1"><strong>Email:</strong> {{ $application->email }}</p>
                            <p class="mb-1"><strong>Số điện thoại:</strong> {{ $application->phone_number }}</p>
                            <p class="mb-1"><strong>Ngày sinh:</strong> {{ \Carbon\Carbon::parse($application->date_of_birth)->format('d/m/Y') }}</p>
                            <p class="mb-1"><strong>Giới tính:</strong> {{ $application->gender == 'male' ? 'Nam' : ($application->gender == 'female' ? 'Nữ' : 'Khác') }}</p>
                            <p class="mb-1"><strong>Địa chỉ:</strong> {{ $application->address }}, {{ $application->district }}, {{ $application->city }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Thông tin giấy tờ tuỳ thân</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Số CMND/CCCD:</strong> {{ $application->id_card_number }}</li>
                                <li class="list-group-item"><strong>Ngày cấp:</strong> {{ \Carbon\Carbon::parse($application->id_card_issue_date)->format('d/m/Y') }}</li>
                                <li class="list-group-item"><strong>Nơi cấp:</strong> {{ $application->id_card_issue_place }}</li>
                                <li class="list-group-item"><strong>Ảnh mặt trước:</strong><br>
                                    @if($application->id_card_front_image)
                                        <img src="{{ asset($application->id_card_front_image) }}" class="img-thumbnail mt-1" style="max-width: 120px;">
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </li>
                                <li class="list-group-item"><strong>Ảnh mặt sau:</strong><br>
                                    @if($application->id_card_back_image)
                                        <img src="{{ asset($application->id_card_back_image) }}" class="img-thumbnail mt-1" style="max-width: 120px;">
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin phương tiện</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Loại xe:</strong> {{ $application->vehicle_type }}</li>
                                <li class="list-group-item"><strong>Model xe:</strong> {{ $application->vehicle_model }}</li>
                                <li class="list-group-item"><strong>Màu xe:</strong> {{ $application->vehicle_color }}</li>
                                <li class="list-group-item"><strong>Biển số xe:</strong> {{ $application->license_plate }}</li>
                                <li class="list-group-item"><strong>Số GPLX:</strong> {{ $application->driver_license_number }}</li>
                                <li class="list-group-item"><strong>Ảnh đăng ký xe:</strong><br>
                                    @if($application->vehicle_registration_image)
                                        <img src="{{ asset($application->vehicle_registration_image) }}" class="img-thumbnail mt-1" style="max-width: 120px;">
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </li>
                                <li class="list-group-item"><strong>Ảnh GPLX:</strong><br>
                                    @if($application->driver_license_image)
                                        <img src="{{ asset($application->driver_license_image) }}" class="img-thumbnail mt-1" style="max-width: 120px;">
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Thông tin ngân hàng</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Ngân hàng:</strong> {{ $application->bank_name }}</li>
                                <li class="list-group-item"><strong>Số tài khoản:</strong> {{ $application->bank_account_number }}</li>
                                <li class="list-group-item"><strong>Tên chủ tài khoản:</strong> {{ $application->bank_account_name }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Liên hệ khẩn cấp</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Họ tên:</strong> {{ $application->emergency_contact_name }}</li>
                                <li class="list-group-item"><strong>Số điện thoại:</strong> {{ $application->emergency_contact_phone }}</li>
                                <li class="list-group-item"><strong>Mối quan hệ:</strong> {{ $application->emergency_contact_relationship }}</li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Ghi chú & Trạng thái</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Ghi chú admin:</strong> {{ $application->admin_notes ?? 'Không có' }}</li>
                                <li class="list-group-item"><strong>Ngày nộp đơn:</strong> {{ $application->created_at->format('d/m/Y H:i') }}</li>
                                <li class="list-group-item"><strong>Ngày cập nhật:</strong> {{ $application->updated_at->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>

                    @if($application->status === 'pending')
                        <div class="mt-4 text-center">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#approveModal">
                                <i class="fas fa-check"></i> Chấp nhận
                            </button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times"></i> Từ chối
                            </button>
                        </div>

                        <!-- Modal phê duyệt -->
                        <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('admin.drivers.applications.approve', $application) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="approveModalLabel">Phê duyệt đơn ứng tuyển</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="admin_notes">Ghi chú admin</label>
                                                <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3">Đơn được phê duyệt bởi quản trị viên</textarea>
                                            </div>
                                            <!-- Hidden fields to pass application data -->
                                            <input type="hidden" name="id_card_number" value="{{ $application->id_card_number }}">
                                            <input type="hidden" name="id_card_front_image" value="{{ $application->id_card_front_image }}">
                                            <input type="hidden" name="id_card_back_image" value="{{ $application->id_card_back_image }}">
                                            <input type="hidden" name="driver_license_number" value="{{ $application->driver_license_number }}">
                                            <input type="hidden" name="driver_license_front_image" value="{{ $application->driver_license_front_image }}">
                                            <input type="hidden" name="driver_license_back_image" value="{{ $application->driver_license_back_image }}">
                                            <input type="hidden" name="vehicle_type" value="{{ $application->vehicle_type }}">
                                            <input type="hidden" name="vehicle_registration_image" value="{{ $application->vehicle_registration_image }}">
                                            <input type="hidden" name="vehicle_color" value="{{ $application->vehicle_color }}">
                                            <input type="hidden" name="full_name" value="{{ $application->full_name }}">
                                            <input type="hidden" name="phone_number" value="{{ $application->phone_number }}">
                                            <input type="hidden" name="email" value="{{ $application->email }}">
                                            <input type="hidden" name="address" value="{{ $application->address }}">
                                            <input type="hidden" name="district" value="{{ $application->district }}">
                                            <input type="hidden" name="city" value="{{ $application->city }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-success">Phê duyệt đơn</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal từ chối -->
                        <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('admin.drivers.applications.reject', $application) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel">Từ chối đơn ứng tuyển</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="admin_notes">Ghi chú admin</label>
                                                <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-danger">Từ chối đơn</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
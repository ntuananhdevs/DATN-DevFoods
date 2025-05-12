@extends('layouts/admin/contentLayoutMaster')
@section('content')
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
                                <li class="list-group-item"><strong>Ghi chú admin:</strong> {{ $application->admin_notes ?? 'Không có ghi chú' }}</li>
                                <li class="list-group-item"><strong>Ngày nộp đơn:</strong> {{ $application->created_at->format('d/m/Y H:i') }}</li>
                                <li class="list-group-item"><strong>Ngày cập nhật:</strong> {{ $application->updated_at->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>

                    @if($application->status === 'pending')
                        <div class="mt-4 text-center">
                            <form action="{{ route('admin.drivers.applications.approve', $application) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-success" onclick="dtmodalConfirmDelete({
                                    itemName: '{{ $application->full_name }}',
                                    title: 'Xác nhận phê duyệt',
                                    onConfirm: () => this.closest('form').submit()
                                })">
                                    <i class="fas fa-check"></i> Chấp nhận
                                </button>
                            </form>
                            <form action="{{ route('admin.drivers.applications.reject', $application) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-danger" onclick="dtmodalConfirmDelete({
                                    itemName: '{{ $application->full_name }}',
                                    title: 'Xác nhận từ chối',
                                    message: 'Bạn có chắc chắn muốn từ chối đơn ứng tuyển của {{ $application->full_name }}?',
                                    confirmText: 'Từ chối',
                                    onConfirm: () => this.closest('form').submit()
                                })">Từ chối đơn</button>
                            </form>
                        </div>


                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
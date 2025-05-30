@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi tiết đơn đăng ký tài xế')

@section('content')
<div class="data-table-wrapper">
    <!-- Header -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-user-check"></i>
            </div>
            <h1 class="data-table-title">Chi tiết đơn đăng ký tài xế #{{ $application->id }}</h1>
        </div>
        <div class="data-table-header-actions">
            <a href="{{ route('admin.drivers.applications.index') }}" class="data-table-btn data-table-btn-outline">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Status Update Form -->
    @if($application->status === 'pending')
    <div class="data-table-card mb-4">
        <div class="data-table-header">
            <h2 class="data-table-card-title">Cập nhật trạng thái</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.drivers.applications.update-status', $application) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Trạng thái:</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="approved">Duyệt</option>
                                <option value="rejected">Từ chối</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="notes">Ghi chú:</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Ghi chú về quyết định..."></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
            </form>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin cá nhân</h2>
                </div>
                <div class="p-4">
                    <div class="text-center mb-4">
                        @if($imageUrls['profile_image'])
                            <img src="{{ $imageUrls['profile_image'] }}" alt="Ảnh chân dung" class="profile-image mb-3" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff;">
                        @else
                            <div class="profile-placeholder mb-3" style="width: 120px; height: 120px; border-radius: 50%; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 3px solid #dee2e6;">
                                <i class="fas fa-user fa-3x text-muted"></i>
                            </div>
                        @endif
                        <h4>{{ $application->full_name }}</h4>
                        <span class="data-table-status 
                            @if($application->status === 'approved') data-table-status-success
                            @elseif($application->status === 'rejected') data-table-status-failed
                            @else data-table-status-warning @endif">
                            @if($application->status === 'approved') 
                                <i class="fas fa-check"></i> Đã duyệt
                            @elseif($application->status === 'rejected') 
                                <i class="fas fa-times"></i> Đã từ chối
                            @else 
                                <i class="fas fa-clock"></i> Chờ xử lý
                            @endif
                        </span>
                    </div>

                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $application->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Số điện thoại:</strong></td>
                            <td>{{ $application->phone_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ngày sinh:</strong></td>
                            <td>{{ Carbon\Carbon::parse($application->date_of_birth)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Giới tính:</strong></td>
                            <td>
                                @if($application->gender === 'male') Nam
                                @elseif($application->gender === 'female') Nữ
                                @else Khác @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Địa chỉ:</strong></td>
                            <td>{{ $application->address }}</td>
                        </tr>
                        <tr>
                            <td><strong>Thành phố:</strong></td>
                            <td>{{ $application->city }}</td>
                        </tr>
                        <tr>
                            <td><strong>Quận/Huyện:</strong></td>
                            <td>{{ $application->district }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thông tin CMND/CCCD -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin CMND/CCCD</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Số CMND/CCCD:</strong></td>
                            <td>{{ $application->id_card_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ngày cấp:</strong></td>
                            <td>{{ Carbon\Carbon::parse($application->id_card_issue_date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nơi cấp:</strong></td>
                            <td>{{ $application->id_card_issue_place }}</td>
                        </tr>
                    </table>

                    <div class="row mt-3">
                        <div class="col-6">
                            <h6>Mặt trước CMND/CCCD:</h6>
                            @if($imageUrls['id_card_front_image'])
                                <img src="{{ $imageUrls['id_card_front_image'] }}" alt="CMND/CCCD mặt trước" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['id_card_front_image'] }}" data-image-title="CMND/CCCD mặt trước">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <h6>Mặt sau CMND/CCCD:</h6>
                            @if($imageUrls['id_card_back_image'])
                                <img src="{{ $imageUrls['id_card_back_image'] }}" alt="CMND/CCCD mặt sau" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['id_card_back_image'] }}" data-image-title="CMND/CCCD mặt sau">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Thông tin phương tiện -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin phương tiện</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Loại phương tiện:</strong></td>
                            <td>
                                @if($application->vehicle_type === 'motorcycle') Xe máy
                                @elseif($application->vehicle_type === 'car') Ô tô
                                @else Xe đạp @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dòng xe:</strong></td>
                            <td>{{ $application->vehicle_model }}</td>
                        </tr>
                        <tr>
                            <td><strong>Màu xe:</strong></td>
                            <td>{{ $application->vehicle_color }}</td>
                        </tr>
                        <tr>
                            <td><strong>Biển số xe:</strong></td>
                            <td><strong class="text-primary">{{ $application->license_plate }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Số GPLX:</strong></td>
                            <td>{{ $application->driver_license_number }}</td>
                        </tr>
                    </table>

                    <div class="row mt-3">
                        <div class="col-6">
                            <h6>Giấy phép lái xe:</h6>
                            @if($imageUrls['driver_license_image'])
                                <img src="{{ $imageUrls['driver_license_image'] }}" alt="Giấy phép lái xe" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['driver_license_image'] }}" data-image-title="Giấy phép lái xe">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <h6>Đăng ký xe:</h6>
                            @if($imageUrls['vehicle_registration_image'])
                                <img src="{{ $imageUrls['vehicle_registration_image'] }}" alt="Đăng ký xe" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['vehicle_registration_image'] }}" data-image-title="Đăng ký xe">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin ngân hàng và liên hệ khẩn cấp -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin ngân hàng</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Ngân hàng:</strong></td>
                            <td>{{ $application->bank_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Số tài khoản:</strong></td>
                            <td>{{ $application->bank_account_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tên chủ tài khoản:</strong></td>
                            <td>{{ $application->bank_account_name }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="data-table-card mt-3">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Liên hệ khẩn cấp</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tên:</strong></td>
                            <td>{{ $application->emergency_contact_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Số điện thoại:</strong></td>
                            <td>{{ $application->emergency_contact_phone }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mối quan hệ:</strong></td>
                            <td>{{ $application->emergency_contact_relationship }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Thông tin admin notes nếu có -->
            @if($application->admin_notes)
            <div class="data-table-card mt-3">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Ghi chú của Admin</h2>
                </div>
                <div class="p-4">
                    <p class="mb-0">{{ $application->admin_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Timeline -->
    <div class="data-table-card mt-4">
        <div class="data-table-header">
            <h2 class="data-table-card-title">Lịch sử xử lý</h2>
        </div>
        <div class="p-4">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6>Nộp đơn đăng ký</h6>
                        <p class="text-muted">{{ $application->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                
                @if($application->reviewed_at)
                <div class="timeline-item">
                    <div class="timeline-marker timeline-marker-success"></div>
                    <div class="timeline-content">
                        <h6>
                            @if($application->status === 'approved') Đã duyệt đơn
                            @else Đã từ chối đơn @endif
                        </h6>
                        <p class="text-muted">{{ Carbon\Carbon::parse($application->reviewed_at)->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Xem ảnh</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<style>
.document-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.document-image:hover {
    transform: scale(1.05);
}

.document-placeholder {
    width: 100%;
    height: 120px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.document-placeholder i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -2.25rem;
    top: 0.25rem;
    width: 1rem;
    height: 1rem;
    background-color: #007bff;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-marker-success {
    background-color: #28a745;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image modal
    $('#imageModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var imageSrc = button.data('image-src');
        var imageTitle = button.data('image-title');
        
        var modal = $(this);
        modal.find('.modal-title').text(imageTitle);
        modal.find('#modalImage').attr('src', imageSrc);
    });
});
</script>
@endpush
@endsection

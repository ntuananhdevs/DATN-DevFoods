@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh sửa thông tin tài xế')
@section('description', 'Cập nhật thông tin tài xế')

@section('content')
<style>
    .edit-form-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    
    .edit-form-header {
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #3b82f6;
    }
    
    .edit-form-header h5 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .required {
        color: #dc2626;
        margin-left: 0.25rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    
    .form-control.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    
    .invalid-feedback {
        display: block;
        color: #dc2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    
    .form-check input[type="checkbox"] {
        width: 1rem;
        height: 1rem;
        border-radius: 0.25rem;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .alert {
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
    }
    
    .alert-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        border-color: #fecaca;
    }
    
    .form-text {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #15803d;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .status-locked {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .file-upload-area:hover {
        border-color: #3b82f6;
        background-color: #f8fafc;
    }
    
    .file-upload-area.dragover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    
    .current-image {
        max-width: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }
    
    .form-section {
        border-top: 1px solid #e5e7eb;
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .form-section:first-child {
        border-top: none;
        padding-top: 0;
        margin-top: 0;
    }
    
    .back-btn {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .back-btn:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
</style>

<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Chỉnh sửa thông tin tài xế</h1>
            <p class="text-muted">Cập nhật thông tin tài xế #{{ $driver->id }} - {{ $driver->full_name }}</p>
        </div>
        <a href="{{ route('admin.drivers.show', $driver) }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Current Status Indicator -->
    <div class="mb-4">
        <span class="status-indicator {{ $driver->status === 'active' ? 'status-active' : ($driver->status === 'locked' ? 'status-locked' : 'status-inactive') }}">
            <i class="fas fa-{{ $driver->status === 'active' ? 'check-circle' : ($driver->status === 'locked' ? 'lock' : 'times-circle') }}"></i>
            Trạng thái hiện tại: {{ $driver->status === 'active' ? 'Đang hoạt động' : ($driver->status === 'locked' ? 'Bị khóa' : 'Không hoạt động') }}
        </span>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Có lỗi xảy ra:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.drivers.update', $driver) }}" method="POST" id="editDriverForm">
        @csrf
        @method('PUT')

        <!-- Personal Information -->
        <div class="edit-form-card">
            <div class="edit-form-header">
                <h5><i class="fas fa-user"></i>Thông tin cá nhân</h5>
                <p class="text-muted mb-0">Cập nhật thông tin cơ bản của tài xế</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Họ và tên <span class="required">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                               name="full_name" value="{{ old('full_name', $driver->full_name) }}" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', $driver->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                               name="phone_number" value="{{ old('phone_number', $driver->phone_number) }}" required>
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                               name="address" value="{{ old('address', $driver->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- License Information -->
        <div class="edit-form-card">
            <div class="edit-form-header">
                <h5><i class="fas fa-id-card"></i>Thông tin giấy phép lái xe</h5>
                <p class="text-muted mb-0">Cập nhật thông tin giấy phép lái xe</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Số giấy phép lái xe <span class="required">*</span></label>
                        <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                               name="license_number" value="{{ old('license_number', $driver->license_number) }}" required>
                        @error('license_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Hạng bằng lái</label>
                        <select class="form-control @error('license_class') is-invalid @enderror" name="license_class">
                            <option value="">Chọn hạng bằng lái</option>
                            <option value="A1" {{ old('license_class', $driver->license_class) === 'A1' ? 'selected' : '' }}>A1</option>
                            <option value="A2" {{ old('license_class', $driver->license_class) === 'A2' ? 'selected' : '' }}>A2</option>
                            <option value="A3" {{ old('license_class', $driver->license_class) === 'A3' ? 'selected' : '' }}>A3</option>
                            <option value="A4" {{ old('license_class', $driver->license_class) === 'A4' ? 'selected' : '' }}>A4</option>
                            <option value="B1" {{ old('license_class', $driver->license_class) === 'B1' ? 'selected' : '' }}>B1</option>
                            <option value="B2" {{ old('license_class', $driver->license_class) === 'B2' ? 'selected' : '' }}>B2</option>
                            <option value="C" {{ old('license_class', $driver->license_class) === 'C' ? 'selected' : '' }}>C</option>
                        </select>
                        @error('license_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Ngày hết hạn</label>
                        <input type="date" class="form-control @error('license_expiry') is-invalid @enderror" 
                               name="license_expiry" value="{{ old('license_expiry', $driver->license_expiry ? \Carbon\Carbon::parse($driver->license_expiry)->format('Y-m-d') : '') }}"
                               min="{{ now()->addDays(1)->format('Y-m-d') }}">
                        @error('license_expiry')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Biển số xe</label>
                        <input type="text" class="form-control @error('license_plate') is-invalid @enderror" 
                               name="license_plate" value="{{ old('license_plate', $driver->license_plate) }}"
                               placeholder="VD: 29A-12345">
                        @error('license_plate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Information -->
        <div class="edit-form-card">
            <div class="edit-form-header">
                <h5><i class="fas fa-car"></i>Thông tin phương tiện</h5>
                <p class="text-muted mb-0">Cập nhật thông tin phương tiện</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Loại xe <span class="required">*</span></label>
                        <select class="form-control @error('vehicle_type') is-invalid @enderror" name="vehicle_type" required>
                            <option value="">Chọn loại xe</option>
                            <option value="motorbike" {{ old('vehicle_type', $driver->vehicle_type) === 'motorbike' ? 'selected' : '' }}>Xe máy</option>
                            <option value="car" {{ old('vehicle_type', $driver->vehicle_type) === 'car' ? 'selected' : '' }}>Ô tô</option>
                            <option value="bicycle" {{ old('vehicle_type', $driver->vehicle_type) === 'bicycle' ? 'selected' : '' }}>Xe đạp</option>
                            <option value="truck" {{ old('vehicle_type', $driver->vehicle_type) === 'truck' ? 'selected' : '' }}>Xe tải</option>
                        </select>
                        @error('vehicle_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Màu xe <span class="required">*</span></label>
                        <input type="text" class="form-control @error('vehicle_color') is-invalid @enderror" 
                               name="vehicle_color" value="{{ old('vehicle_color', $driver->vehicle_color) }}" required
                               placeholder="VD: Đỏ, Xanh, Trắng...">
                        @error('vehicle_color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Số đăng ký xe</label>
                        <input type="text" class="form-control @error('vehicle_registration') is-invalid @enderror" 
                               name="vehicle_registration" value="{{ old('vehicle_registration', $driver->vehicle_registration) }}">
                        @error('vehicle_registration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Status & Settings -->
        <div class="edit-form-card">
            <div class="edit-form-header">
                <h5><i class="fas fa-cog"></i>Trạng thái tài khoản & Cài đặt</h5>
                <p class="text-muted mb-0">Quản lý trạng thái và cài đặt tài khoản</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Trạng thái tài khoản <span class="required">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                            <option value="active" {{ old('status', $driver->status) === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="inactive" {{ old('status', $driver->status) === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                            <option value="locked" {{ old('status', $driver->status) === 'locked' ? 'selected' : '' }}>Bị khóa</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Điểm tin cậy</label>
                        <input type="number" class="form-control @error('reliability_score') is-invalid @enderror" 
                               name="reliability_score" value="{{ old('reliability_score', $driver->reliability_score) }}"
                               min="0" max="100">
                        @error('reliability_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Số dư tài khoản (VNĐ)</label>
                        <input type="number" class="form-control @error('balance') is-invalid @enderror" 
                               name="balance" value="{{ old('balance', $driver->balance) }}" min="0">
                        @error('balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Đánh giá (1-5)</label>
                        <input type="number" class="form-control @error('rating') is-invalid @enderror" 
                               name="rating" value="{{ old('rating', $driver->rating) }}" 
                               min="1" max="5" step="0.1">
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Số lần hủy đơn</label>
                        <input type="number" class="form-control @error('cancellation_count') is-invalid @enderror" 
                               name="cancellation_count" value="{{ old('cancellation_count', $driver->cancellation_count) }}" 
                               min="0" readonly>
                        <small class="form-text">Chỉ xem, không thể chỉnh sửa</small>
                        @error('cancellation_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Số lần vi phạm</label>
                        <input type="number" class="form-control @error('penalty_count') is-invalid @enderror" 
                               name="penalty_count" value="{{ old('penalty_count', $driver->penalty_count) }}" 
                               min="0" readonly>
                        <small class="form-text">Chỉ xem, không thể chỉnh sửa</small>
                        @error('penalty_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_available" id="is_available" 
                               value="1" {{ old('is_available', $driver->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_available">
                            Sẵn sàng nhận đơn hàng
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="auto_deposit_earnings" id="auto_deposit_earnings" 
                               value="1" {{ old('auto_deposit_earnings', $driver->auto_deposit_earnings) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_deposit_earnings">
                            Tự động nạp tiền thu nhập vào tài khoản
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Change -->
        <div class="edit-form-card">
            <div class="edit-form-header">
                <h5><i class="fas fa-key"></i>Thay đổi mật khẩu</h5>
                <p class="text-muted mb-0">Để trống nếu không muốn thay đổi mật khẩu</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Tối thiểu 6 ký tự</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                        <small class="form-text">Nhập lại mật khẩu mới để xác nhận</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Notes -->
        <div class="edit-form-card">
            <div class="edit-form-header">
                <h5><i class="fas fa-sticky-note"></i>Ghi chú nội bộ</h5>
                <p class="text-muted mb-0">Ghi chú chỉ dành cho admin, tài xế không thể xem</p>
            </div>

            <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                          name="admin_notes" rows="4" 
                          placeholder="Nhập ghi chú nội bộ về tài xế này...">{{ old('admin_notes', $driver->admin_notes) }}</textarea>
                @error('admin_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Hủy bỏ
            </a>
            
            <div>
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editDriverForm');
    const saveBtn = document.getElementById('saveBtn');
    
    form.addEventListener('submit', function(e) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

<!-- Driver Documents Section -->
<div class="edit-form-card">
    <div class="edit-form-header">
        <h5><i class="fas fa-file-alt"></i>Giấy tờ tuỳ thân & Giấy phép</h5>
        <p class="text-muted mb-0">Xem và cập nhật giấy tờ của tài xế</p>
    </div>
    <div class="row">
        @if($driver->documents)
            @foreach($driver->documents as $doc)
            <div class="col-md-4 mb-4">
                <div class="form-group text-center">
                    <label class="form-label">{{ $doc->type_label }}</label>
                    <!-- Các trường khác -->
                    <div>
                        @if($doc->url)
                            <img src="{{ $doc->url }}" alt="{{ $doc->type_label }}" class="current-image mb-2" style="max-width:180px;cursor:pointer" onclick="showImageModal('{{ $doc->url }}')">
                        @else
                            <span class="text-muted">Chưa có ảnh</span>
                        @endif
                    </div>
                    <input type="file" name="documents[{{ $doc->id }}]" class="form-control mt-2">
                    <small class="form-text">Chọn ảnh mới để cập nhật</small>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
<!-- Modal xem ảnh giấy tờ -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img id="modalImage" src="" alt="Document Image" style="width:100%;border-radius:8px;">
      </div>
      <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
function showImageModal(url) {
    document.getElementById('modalImage').src = url;
    var modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}
</script>
@endsection
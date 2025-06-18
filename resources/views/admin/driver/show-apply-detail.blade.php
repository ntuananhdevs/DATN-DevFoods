@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi tiết đơn ứng tuyển')
@section('description', 'Xem chi tiết thông tin đơn ứng tuyển tài xế')

@section('content')
<style>
    /* Custom styling */
    .detail-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .detail-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        text-decoration: none;
    }

    .detail-content {
        padding: 2rem;
    }

    .profile-section {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .profile-image {
        flex-shrink: 0;
        text-align: center;
    }

    .profile-image img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #f3f4f6;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .status-badge {
        margin-top: 1rem;
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-badge.approved {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .profile-info {
        flex: 1;
    }

    .profile-info h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
    }

    .info-item .icon {
        width: 1.25rem;
        color: #6b7280;
        text-align: center;
    }

    .info-item .label {
        font-weight: 600;
        color: #374151;
        min-width: 100px;
    }

    .info-item .value {
        color: #111827;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .info-section {
        background: #f9fafb;
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid #f3f4f6;
    }

    .info-section h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-section h3 i {
        color: #6366f1;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-list .label {
        font-weight: 500;
        color: #374151;
    }

    .info-list .value {
        color: #111827;
        font-weight: 600;
    }

    .document-image {
        margin-top: 0.5rem;
    }

    .document-image img {
        max-width: 120px;
        border-radius: 0.375rem;
        border: 2px solid #e5e7eb;
        transition: transform 0.2s ease;
    }

    .document-image img:hover {
        transform: scale(1.05);
        cursor: pointer;
    }

    .action-section {
        background: #f8fafc;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        margin-top: 2rem;
        border: 1px solid #e2e8f0;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-modern {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .btn-modern.success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-modern.success:hover {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .btn-modern.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-modern.danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    }

    .alert-modern {
        border-radius: 0.75rem;
        border: none;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-modern.success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .alert-modern.error {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }

    /* Modal form styling */
    .modal-form-group {
        margin-bottom: 1.5rem;
    }

    .modal-form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .modal-form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        resize: vertical;
        transition: border-color 0.2s ease;
        font-family: inherit;
        line-height: 1.5;
    }

    .modal-form-textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .modal-form-textarea.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .modal-character-count {
        font-size: 0.75rem;
        color: #6b7280;
        text-align: right;
        margin-top: 0.25rem;
    }

    .modal-error-message {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .modal-info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

    .modal-info-box strong {
        font-weight: 600;
    }

    .modal-info-box ul {
        margin: 0.5rem 0 0 1.5rem;
        padding: 0;
    }

    .modal-warning-box {
        background: #fffbeb;
        border: 1px solid #fed7aa;
        color: #92400e;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

    .modal-warning-box strong {
        font-weight: 600;
    }

    .modal-warning-box ul {
        margin: 0.5rem 0 0 1.5rem;
        padding: 0;
    }

    @media (max-width: 768px) {
        .profile-section {
            flex-direction: column;
            text-align: center;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .detail-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

@if(session('success'))
    <div class="alert-modern success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert-modern error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="fade-in">
    <div class="detail-card">
        <div class="detail-header">
            <h1>Chi tiết đơn ứng tuyển tài xế</h1>
            <a href="{{ route('admin.drivers.applications.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="detail-content">
            <!-- Profile Section -->
            <div class="profile-section">
                <div class="profile-image">
                    @if($application->profile_image)
                        <img src="{{ Storage::disk('driver_documents')->url($application->profile_image) }}" alt="Ảnh đại diện">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($application->full_name) }}&background=eee&color=555&size=150" alt="Ảnh đại diện">
                    @endif
                    <div class="status-badge {{ $application->status }}">
                        @if($application->status == 'approved')
                            <i class="fas fa-check-circle"></i> Đã duyệt
                        @elseif($application->status == 'rejected')
                            <i class="fas fa-times-circle"></i> Đã từ chối
                        @else
                            <i class="fas fa-clock"></i> Chờ xử lý
                        @endif
                    </div>
                </div>
                <div class="profile-info">
                    <h2>{{ $application->full_name }}</h2>
                    <div class="info-item">
                        <i class="fas fa-envelope icon"></i>
                        <span class="label">Email:</span>
                        <span class="value">{{ $application->email }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone icon"></i>
                        <span class="label">Điện thoại:</span>
                        <span class="value">{{ $application->phone_number }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-birthday-cake icon"></i>
                        <span class="label">Ngày sinh:</span>
                        <span class="value">{{ \Carbon\Carbon::parse($application->date_of_birth)->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-venus-mars icon"></i>
                        <span class="label">Giới tính:</span>
                        <span class="value">{{ $application->gender == 'male' ? 'Nam' : ($application->gender == 'female' ? 'Nữ' : 'Khác') }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt icon"></i>
                        <span class="label">Địa chỉ:</span>
                        <span class="value">{{ $application->address }}</span>
                    </div>
                </div>
            </div>

            <!-- Information Grid -->
            <div class="info-grid">
                <!-- ID Card Information -->
                <div class="info-section">
                    <h3><i class="fas fa-id-card"></i> Thông tin CMND/CCCD</h3>
                    <ul class="info-list">
                        <li>
                            <span class="label">Số CMND/CCCD:</span>
                            <span class="value">{{ $application->id_card_number }}</span>
                        </li>
                        <li>
                            <span class="label">Ngày cấp:</span>
                            <span class="value">{{ \Carbon\Carbon::parse($application->id_card_issue_date)->format('d/m/Y') }}</span>
                        </li>
                        <li>
                            <span class="label">Nơi cấp:</span>
                            <span class="value">{{ $application->id_card_issue_place }}</span>
                        </li>
                        <li>
                            <span class="label">Ảnh mặt trước:</span>
                            <div class="document-image">
                                @if($application->id_card_front_image)
                                    <img src="{{ Storage::disk('driver_documents')->url($application->id_card_front_image) }}" alt="CMND mặt trước">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </li>
                        <li>
                            <span class="label">Ảnh mặt sau:</span>
                            <div class="document-image">
                                @if($application->id_card_back_image)
                                    <img src="{{ Storage::disk('driver_documents')->url($application->id_card_back_image) }}" alt="CMND mặt sau">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Vehicle Information -->
                <div class="info-section">
                    <h3><i class="fas fa-car"></i> Thông tin phương tiện</h3>
                    <ul class="info-list">
                        <li>
                            <span class="label">Loại xe:</span>
                            <span class="value">{{ $application->vehicle_type }}</span>
                        </li>
                        <li>
                            <span class="label">Model xe:</span>
                            <span class="value">{{ $application->vehicle_model }}</span>
                        </li>
                        <li>
                            <span class="label">Màu xe:</span>
                            <span class="value">{{ $application->vehicle_color }}</span>
                        </li>
                        <li>
                            <span class="label">Biển số xe:</span>
                            <span class="value">{{ $application->license_plate }}</span>
                        </li>
                        <li>
                            <span class="label">Số GPLX:</span>
                            <span class="value">{{ $application->driver_license_number }}</span>
                        </li>
                        <li>
                            <span class="label">Ảnh đăng ký xe:</span>
                            <div class="document-image">
                                @if($application->vehicle_registration_image)
                                    <img src="{{ Storage::disk('driver_documents')->url($application->vehicle_registration_image) }}" alt="Đăng ký xe">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </li>
                        <li>
                            <span class="label">Ảnh GPLX:</span>
                            <div class="document-image">
                                @if($application->driver_license_image)
                                    <img src="{{ Storage::disk('driver_documents')->url($application->driver_license_image) }}" alt="GPLX">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Bank Information -->
                <div class="info-section">
                    <h3><i class="fas fa-university"></i> Thông tin ngân hàng</h3>
                    <ul class="info-list">
                        <li>
                            <span class="label">Ngân hàng:</span>
                            <span class="value">{{ $application->bank_name }}</span>
                        </li>
                        <li>
                            <span class="label">Số tài khoản:</span>
                            <span class="value">{{ $application->bank_account_number }}</span>
                        </li>
                        <li>
                            <span class="label">Tên chủ tài khoản:</span>
                            <span class="value">{{ $application->bank_account_name }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Emergency Contact -->
                <div class="info-section">
                    <h3><i class="fas fa-phone-alt"></i> Liên hệ khẩn cấp</h3>
                    <ul class="info-list">
                        <li>
                            <span class="label">Họ tên:</span>
                            <span class="value">{{ $application->emergency_contact_name }}</span>
                        </li>
                        <li>
                            <span class="label">Số điện thoại:</span>
                            <span class="value">{{ $application->emergency_contact_phone }}</span>
                        </li>
                        <li>
                            <span class="label">Mối quan hệ:</span>
                            <span class="value">{{ $application->emergency_contact_relationship }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Status & Notes -->
            <div class="info-section">
                <h3><i class="fas fa-clipboard-list"></i> Ghi chú & Trạng thái</h3>
                <ul class="info-list">
                    <li>
                        <span class="label">Ghi chú admin:</span>
                        <span class="value">{{ $application->admin_notes ?? 'Không có' }}</span>
                    </li>
                    <li>
                        <span class="label">Ngày nộp đơn:</span>
                        <span class="value">{{ $application->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                    <li>
                        <span class="label">Ngày cập nhật:</span>
                        <span class="value">{{ $application->updated_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </div>

            @if($application->status === 'pending')
                <div class="action-section">
                    <h3 style="margin-bottom: 1rem; color: #374151;">Thao tác xử lý đơn</h3>
                    <div class="action-buttons">
                        <button type="button" class="btn-modern success" onclick="showApproveModal()">
                            <i class="fas fa-check"></i> Chấp nhận đơn
                        </button>
                        <button type="button" class="btn-modern danger" onclick="showRejectModal()">
                            <i class="fas fa-times"></i> Từ chối đơn
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden Forms -->
<form id="approveForm" action="{{ route('admin.drivers.applications.approve', $application) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="admin_notes" id="approveNotes">
    <input type="hidden" name="license_number" value="{{ $application->driver_license_number }}">
    <input type="hidden" name="vehicle_type" value="{{ $application->vehicle_type }}">
    <input type="hidden" name="vehicle_model" value="{{ $application->vehicle_model }}">                                             
    <input type="hidden" name="vehicle_color" value="{{ $application->vehicle_color }}">
    <input type="hidden" name="vehicle_registration" value="{{ $application->vehicle_registration_image }}">
    <input type="hidden" name="email" value="{{ $application->email }}">
    <input type="hidden" name="phone_number" value="{{ $application->phone_number }}">
    <input type="hidden" name="full_name" value="{{ $application->full_name }}">
</form>

<form id="rejectForm" action="{{ route('admin.drivers.applications.reject', $application) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="admin_notes" id="rejectNotes">
</form>

<script>
// Global variables
let approveTextarea, rejectTextarea;
let approveCharCount, rejectCharCount;

function showApproveModal() {
    const modalId = dtmodalCreateModal({
        type: "success",
        title: "Phê duyệt đơn ứng tuyển",
        subtitle: `Chấp nhận ${@json($application->full_name)} trở thành tài xế`,
        message: `
            <div class="modal-form-group">
                <label class="modal-form-label" for="approve_admin_notes">Ghi chú phê duyệt</label>
                <textarea 
                    id="approve_admin_notes" 
                    class="modal-form-textarea" 
                    rows="4" 
                    placeholder="Nhập ghi chú về quyết định phê duyệt..."
                    maxlength="500">Đơn được phê duyệt bởi quản trị viên. Ứng viên đáp ứng đầy đủ các yêu cầu và tiêu chuẩn để trở thành tài xế của hệ thống.</textarea>
                <div class="modal-character-count">
                    <span id="approve-char-count">0</span>/500 ký tự
                </div>
            </div>
            
            <div class="modal-info-box">
                <i class="fas fa-info-circle"></i>
                <strong>Lưu ý:</strong> Sau khi phê duyệt, hệ thống sẽ tự động:
                <ul>
                    <li>Tạo tài khoản tài xế mới</li>
                    <li>Gửi email thông báo và thông tin đăng nhập</li>
                    <li>Cập nhật trạng thái đơn thành "Đã duyệt"</li>
                </ul>
            </div>
        `,
        confirmText: "Phê duyệt đơn",
        cancelText: "Hủy bỏ",
        onConfirm: function() {
            const notes = document.getElementById('approve_admin_notes').value;
            document.getElementById('approveNotes').value = notes;
            document.getElementById('approveForm').submit();
        }
    });

    // Setup character counting
    setTimeout(() => {
        approveTextarea = document.getElementById('approve_admin_notes');
        approveCharCount = document.getElementById('approve-char-count');
        
        if (approveTextarea && approveCharCount) {
            approveTextarea.addEventListener('input', function() {
                const length = this.value.length;
                approveCharCount.textContent = length;
                
                if (length > 450) {
                    approveCharCount.style.color = '#ef4444';
                } else if (length > 400) {
                    approveCharCount.style.color = '#f59e0b';
                } else {
                    approveCharCount.style.color = '#6b7280';
                }
            });
            
            // Initial count
            approveCharCount.textContent = approveTextarea.value.length;
            approveTextarea.focus();
        }
    }, 100);
}

function showRejectModal() {
    const modalId = dtmodalCreateModal({
        type: "warning",
        title: "Từ chối đơn ứng tuyển",
        subtitle: `Từ chối đơn của ${@json($application->full_name)}`,
        message: `
            <div class="modal-form-group">
                <label class="modal-form-label" for="reject_admin_notes">
                    Lý do từ chối <span style="color: #ef4444;">*</span>
                </label>
                <textarea 
                    id="reject_admin_notes" 
                    class="modal-form-textarea" 
                    rows="5" 
                    required 
                    placeholder="Vui lòng nhập lý do cụ thể từ chối đơn ứng tuyển..."
                    maxlength="500"></textarea>
                <div id="reject-error" class="modal-error-message" style="display: none;"></div>
                <div class="modal-character-count">
                    <span id="reject-char-count">0</span>/500 ký tự
                </div>
            </div>

            <div class="modal-warning-box">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Lưu ý:</strong> Sau khi từ chối:
                <ul>
                    <li>Ứng viên sẽ nhận được email thông báo từ chối kèm lý do</li>
                    <li>Trạng thái đơn sẽ được cập nhật thành "Đã từ chối"</li>
                    <li>Hành động này không thể hoàn tác</li>
                </ul>
            </div>
        `,
        confirmText: "Từ chối đơn",
        cancelText: "Quay lại",
        onConfirm: function() {
            const textarea = document.getElementById('reject_admin_notes');
            const notes = textarea.value.trim();
            
            if (notes.length < 10) {
                const errorDiv = document.getElementById('reject-error');
                errorDiv.textContent = 'Lý do từ chối phải có ít nhất 10 ký tự.';
                errorDiv.style.display = 'block';
                textarea.classList.add('error');
                textarea.focus();
                return false; // Prevent modal from closing
            }
            
            document.getElementById('rejectNotes').value = notes;
            document.getElementById('rejectForm').submit();
            return true;
        }
    });

    // Setup character counting and validation
    setTimeout(() => {
        rejectTextarea = document.getElementById('reject_admin_notes');
        rejectCharCount = document.getElementById('reject-char-count');
        const rejectError = document.getElementById('reject-error');
        
        if (rejectTextarea && rejectCharCount) {
            rejectTextarea.addEventListener('input', function() {
                const length = this.value.length;
                const trimmedLength = this.value.trim().length;
                
                rejectCharCount.textContent = length;
                
                // Color coding for character count
                if (length > 450) {
                    rejectCharCount.style.color = '#ef4444';
                } else if (length > 400) {
                    rejectCharCount.style.color = '#f59e0b';
                } else {
                    rejectCharCount.style.color = '#6b7280';
                }
                
                // Validation
                if (trimmedLength < 10) {
                    this.classList.add('error');
                    rejectError.textContent = 'Lý do từ chối phải có ít nhất 10 ký tự.';
                    rejectError.style.display = 'block';
                } else {
                    this.classList.remove('error');
                    rejectError.style.display = 'none';
                }
            });
            
            rejectTextarea.focus();
        }
    }, 100);
}
</script>
<!-- Modal xem ảnh -->
<div id="imageModal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;">
  <span id="closeImageModal" style="position:absolute;top:30px;right:40px;font-size:40px;color:#fff;cursor:pointer;font-weight:bold;z-index:10001;">&times;</span>
  <img id="modalImage" src="" alt="Xem ảnh" style="max-width:90vw;max-height:90vh;box-shadow:0 0 20px #000;border-radius:8px;z-index:10000;">
</div>
<script>
    document.querySelectorAll('.document-image img, .profile-image img').forEach(function(img) {
    img.style.cursor = 'pointer';
    img.addEventListener('click', function(e) {
      var modal = document.getElementById('imageModal');
      var modalImg = document.getElementById('modalImage');
      modalImg.src = img.src;
      modal.style.display = 'flex';
    });
  });
  document.getElementById('closeImageModal').onclick = function() {
    document.getElementById('imageModal').style.display = 'none';
    document.getElementById('modalImage').src = '';
  };
  document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
      this.style.display = 'none';
      document.getElementById('modalImage').src = '';
    }
  });
</script>
@endsection
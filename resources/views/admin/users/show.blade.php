@extends('layouts.admin.contentLayoutMaster')

@section('title', 'User Details')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
{{-- Page css files --}}
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
<style>
    :root {
        --primary: #7367f0;
        --secondary: #82868b;
        --success: #28c76f;
        --info: #00cfe8;
        --warning: #ff9f43;
        --danger: #ea5455;
        --light: #f8f8f8;
        --dark: #4b4b4b;
        --primary-light: #e4e2ff;
        --border-radius: 0.428rem;
        --card-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
        --transition: all 0.35s ease-in-out;
    }

    /* User Profile Card */
    .user-profile-card {
        background-color: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: var(--transition);
    }

    .user-profile-card:hover {
        box-shadow: 0 8px 32px 0 rgba(34, 41, 47, 0.18);
    }

    .user-profile-header {
        background-color: var(--primary);
        background-image: linear-gradient(45deg, var(--primary), #9c8fff);
        color: #fff;
        padding: 2rem;
    }

    .user-profile-avatar-wrapper {
        margin-top: -4rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: center;
    }

    .user-profile-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 4px 12px rgba(34, 41, 47, 0.2);
        object-fit: cover;
        background-color: #fff;
    }

    .user-profile-info {
        padding: 1.5rem;
        text-align: center;
    }

    .user-profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: var(--dark);
    }

    .user-profile-username {
        font-size: 1rem;
        color: var(--secondary);
        margin-bottom: 1rem;
    }

    .user-profile-roles {
        margin-bottom: 1.5rem;
    }

    .user-profile-stats {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
        gap: 1.5rem;
    }

    .user-profile-stat {
        text-align: center;
    }

    .user-profile-stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }

    .user-profile-stat-label {
        font-size: 0.85rem;
        color: var(--secondary);
    }

    /* Details Section */
    .user-details-section {
        background-color: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }

    .user-details-section:hover {
        box-shadow: 0 8px 32px 0 rgba(34, 41, 47, 0.1);
    }

    .user-details-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #ebe9f1;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .user-details-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .user-details-body {
        padding: 1.5rem;
    }

    .user-info-row {
        display: flex;
        border-bottom: 1px solid #ebe9f1;
        padding: 1rem 0;
    }

    .user-info-row:last-child {
        border-bottom: none;
    }

    .user-info-label {
        font-weight: 600;
        color: var(--secondary);
        width: 180px;
        display: flex;
        align-items: center;
    }

    .user-info-label i {
        margin-right: 0.75rem;
        font-size: 1.1rem;
        width: 24px;
        color: var(--primary);
    }

    .user-info-value {
        flex: 1;
        color: var(--dark);
        display: flex;
        align-items: center;
    }

    /* Status Badge */
    .badge-custom {
        padding: 0.4rem 0.75rem;
        border-radius: 50rem;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .badge-active {
        background-color: rgba(40, 199, 111, 0.15);
        color: var(--success);
    }

    .badge-inactive {
        background-color: rgba(234, 84, 85, 0.15);
        color: var(--danger);
    }

    .badge-role {
        background-color: rgba(115, 103, 240, 0.15);
        color: var(--primary);
    }

    /* Action Buttons */
    .user-action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn-custom {
        padding: 0.6rem 1.2rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: var(--transition);
        border: none;
    }

    .btn-primary-custom {
        background-color: var(--primary);
        color: #fff;
    }

    .btn-primary-custom:hover {
        background-color: #5e50ee;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.4);
    }

    .btn-info-custom {
        background-color: var(--info);
        color: #fff;
    }

    .btn-info-custom:hover {
        background-color: #00bdda;
        box-shadow: 0 4px 12px rgba(0, 207, 232, 0.4);
    }

    .btn-secondary-custom {
        background-color: var(--secondary);
        color: #fff;
    }

    .btn-secondary-custom:hover {
        background-color: #737981;
        box-shadow: 0 4px 12px rgba(130, 134, 139, 0.4);
    }

   /* Thêm vào phần style */
.btn-success {
    background-color: #28c76f !important;
    border-color: #28c76f !important;
}

.btn-danger {
    background-color: #ea5455 !important;
    border-color: #ea5455 !important;
}

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .user-info-label {
            width: 140px;
        }
    }

    @media (max-width: 767.98px) {
        .user-profile-stat {
            padding: 0 0.5rem;
        }

        .user-info-row {
            flex-direction: column;
        }

        .user-info-label {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection

@section('content')

<div class="content-body">
    <div class="row match-height">
        <!-- User Profile Card -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="user-profile-card mb-2">
                <div class="user-profile-header"></div>
                <div class="user-profile-avatar-wrapper">
                    @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="User Avatar" class="user-profile-avatar">
                    @else
                    <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="user-profile-avatar">
                    @endif
                </div>
                <div class="user-profile-info">
                    <h3 class="user-profile-name">{{ $user->full_name }}</h3>
                    <p class="user-profile-username">{{ '@' . $user->user_name }}</p>

                    <div class="user-profile-roles">
                        <span class="badge badge-role">{{ $user->role->name ?? 'N/A' }}</span>
                    </div>

                    <div class="user-profile-stats">
                        <div class="user-profile-stat">
                            <div class="user-profile-stat-value">${{ number_format($user->balance, 2) }}</div>
                            <div class="user-profile-stat-label">Balance</div>
                        </div>
                        <div class="user-profile-stat">
                            <div class="user-profile-stat-value">
                                <button type="button"
                                    class="btn btn-sm {{ $user->active ? 'btn-success' : 'btn-danger' }} 
                                        btn-hover-state btn-focus-effect"
                                    style="min-width: 110px; transition: all 0.3s ease;"
                                    onclick="toggleUserStatus(this, {{ $user->id }}, '{{ $user->full_name }}', {{ $user->active ? 'true' : 'false' }})">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas {{ $user->active ? 'fa-check' : 'fa-times' }} mr-2"></i>
                                        {{ $user->active ? 'Hoạt động' : 'Vô hiệu hóa' }}
                                    </div>
                                </button>
                            </div>
                            <div class="user-profile-stat-label">Trạng thái</div>
                        </div>

                        <a href="{{ route('admin.users.index') }}" class="btn btn-custom btn-secondary-custom">
                            <i data-feather="list" class="font-small-4"></i>
                            <span>Quay lại</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details Section -->
        <div class="col-lg-8 col-md-6 col-12">
            <div class="user-details-section mb-2">
                <div class="user-details-header">
                    <h4 class="user-details-title">
                        <i data-feather="user" class="font-medium-2 mr-1"></i>
                        Personal Information
                    </h4>
                </div>
                <div class="user-details-body">
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-user-line"></i>
                            <span>Username</span>
                        </div>
                        <div class="user-info-value">{{ $user->user_name }}</div>
                    </div>
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-profile-line"></i>
                            <span>Full Name</span>
                        </div>
                        <div class="user-info-value">{{ $user->full_name }}</div>
                    </div>
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-mail-line"></i>
                            <span>Email</span>
                        </div>
                        <div class="user-info-value">{{ $user->email }}</div>
                    </div>
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-phone-line"></i>
                            <span>Phone</span>
                        </div>
                        <div class="user-info-value">{{ $user->phone ?: 'Not provided' }}</div>
                    </div>
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-time-line"></i>
                            <span>Created At</span>
                        </div>
                        <div class="user-info-value">
                            {{ $user->created_at ? $user->created_at->format('F d, Y - h:i A') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="user-details-section">
                <div class="user-details-header">
                    <h4 class="user-details-title">
                        <i data-feather="shield" class="font-medium-2 mr-1"></i>
                        Account Settings
                    </h4>
                </div>
                <div class="user-details-body">
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-user-settings-line"></i>
                            <span>Role</span>
                        </div>
                        <div class="user-info-value">
                        @foreach($user->roles as $role)
                            <span class="badge badge-role">{{ $role->name }}</span>
                        @endforeach
                    </div>
                    </div>
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-wallet-3-line"></i>
                            <span>Balance</span>
                        </div>
                        <div class="user-info-value">
                            <span class="font-weight-bold">${{ number_format($user->balance, 2) }}</span>
                        </div>
                    </div>
                    <div class="user-info-row">
                        <div class="user-info-label">
                            <i class="ri-shield-check-line"></i>
                            <span>Status</span>
                        </div>
                        <div class="user-info-value">
                            <span class="badge badge-custom {{ $user->active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $user->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('page-script')
<script>
    function toggleUserStatus(button, userId, userName, currentStatus) {
        // Configuration object for messages
        const messages = {
            confirmTitle: 'Xác nhận thay đổi trạng thái',
            confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của người dùng này?',
            confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của người dùng.',
            successMessage: 'Đã thay đổi trạng thái người dùng thành công',
            errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái người dùng'
        };

        // Sử dụng modal thay vì confirm
        dtmodalCreateModal({
            type: 'warning',
            title: messages.confirmTitle,
            subtitle: messages.confirmSubtitle,
            message: `Bạn đang thay đổi trạng thái của: <strong>"${userName}"</strong><br>${messages.confirmMessage}`,
            confirmText: 'Xác nhận thay đổi',
            cancelText: 'Hủy bỏ',
            onConfirm: function() {
                // Send AJAX request to toggle status
                $.ajax({
                    url: `{{ url('admin/users') }}/${userId}/toggle-status`,
                    type: 'PATCH',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'PATCH'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI
                            const newStatus = !currentStatus;
                            const statusButton = $(button);

                            statusButton
                                .removeClass(currentStatus ? 'data-table-status-success' : 'data-table-status-failed')
                                .addClass(newStatus ? 'data-table-status-success' : 'data-table-status-failed');

                            statusButton.html(
                                newStatus ?
                                '<i class="fas fa-check"></i> Hoạt động' :
                                '<i class="fas fa-times"></i> Vô hiệu hóa'
                            );

                            // Update onclick handler with new status
                            statusButton.attr('onclick', `toggleUserStatus(this, ${userId}, '${userName}', ${newStatus})`);

                            // Show success toast message instead of alert
                            dtmodalShowToast('success', {
                                title: 'Thành công',
                                message: messages.successMessage
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = messages.errorMessage;

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMessage = 'Không tìm thấy người dùng';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Bạn không có quyền thực hiện thao tác này';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Dữ liệu không hợp lệ';
                        }

                        // Show error toast message instead of alert
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: errorMessage
                        });
                    }
                });
            }
        });
    }
    function fetchUserData() {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Cập nhật thông tin chính
                    $('.user-profile-name').text(response.data.full_name);
                    $('.user-profile-username').text('@' + response.data.user_name);
                    $('.user-profile-stat-value').first().text('$' + response.data.balance.toFixed(2));
                    
                    // Cập nhật trạng thái
                    const statusButton = $('.btn-status-toggle');
                    statusButton
                        .toggleClass('btn-success btn-danger', response.data.active)
                        .find('i')
                        .toggleClass('fa-check fa-times', response.data.active);
                    statusButton.find('div').last().text(response.data.active ? 'Hoạt động' : 'Vô hiệu hóa');
                    
                    // Cập nhật thông tin chi tiết
                    $('#user-email').text(response.data.email);
                    $('#user-phone').text(response.data.phone || 'Not provided');
                    $('#user-created-at').text(response.data.created_at);
                }
            },
            error: function(xhr) {
                console.error('Lỗi khi tải dữ liệu:', xhr.responseText);
            }
        });
    }

    // Khởi chạy lần đầu và lặp lại mỗi giây
    fetchUserData();
    setInterval(fetchUserData, 1);
    
</script>
@endsection
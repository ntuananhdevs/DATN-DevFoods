@extends('layouts.admin.contentLayoutMaster')



@section('content')
<div class="assign-manager-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="header-text">
                    <h1>Phân công quản lý chi nhánh</h1>
                    <p>Chọn người quản lý cho chi nhánh {{ $branch->name }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card">
        <div class="card-body">
            <div class="assign-manager-content">
                <!-- Branch Information -->
                <div class="branch-summary">
                    <div class="branch-summary-header">
                        <div class="branch-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h2>Thông tin chi nhánh</h2>
                    </div>
                    <div class="branch-summary-body">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-store-alt"></i>
                                <span>Tên chi nhánh</span>
                            </div>
                            <div class="info-value">{{ $branch->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Địa chỉ</span>
                            </div>
                            <div class="info-value">{{ $branch->address }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user-shield"></i>
                                <span>Người quản lý hiện tại</span>
                            </div>
                            <div class="info-value">
                                @if($branch->manager)
                                    <div class="current-manager">
                                        <div class="manager-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="manager-info">
                                            <div class="manager-name">{{ $branch->manager->full_name }}</div>
                                            <div class="manager-email">{{ $branch->manager->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="no-manager">
                                        <i class="fas fa-user-slash"></i>
                                        <span>Chưa có người quản lý</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manager Selection Form -->
                <div class="manager-selection">
                    <div class="manager-selection-header">
                        <div class="selection-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h2>Chọn người quản lý mới</h2>
                    </div>
                    <div class="manager-selection-body">
                        <form action="{{ route('admin.branches.update-manager', $branch->id) }}" method="POST" id="assignManagerForm">
                            @csrf
                            <div class="form-group">
                                <label for="manager_user_id">Người quản lý</label>
                                <div class="select-wrapper">
                                    <select id="manager_user_id" name="manager_user_id" class="form-select @error('manager_user_id') is-invalid @enderror">
                                        <option value="">-- Chọn người quản lý --</option>
                                        @foreach($availableManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('manager_user_id', $branch->manager_user_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name }} ({{ $manager->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="select-icon">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                @error('manager_user_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                                
                                @if(count($availableManagers) == 0)
                                    <div class="alert-message">
                                        <div class="alert-icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="alert-content">
                                            Không có quản lý nào khả dụng. Tất cả quản lý đã được phân công cho các chi nhánh khác.
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary btn-lg" {{ count($availableManagers) == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i>
                                    <span>Lưu thay đổi</span>
                                </button>
                                
                             
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal" id="confirmModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Xác nhận gỡ bỏ</h3>
                <button type="button" class="modal-close" id="closeConfirmModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn gỡ bỏ người quản lý hiện tại khỏi chi nhánh này?</p>
                <p class="text-danger">Chi nhánh sẽ không có người quản lý cho đến khi bạn phân công người mới.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelRemoveBtn">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveBtn">
                    <i class="fas fa-user-minus"></i>
                    <span>Gỡ bỏ</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Variables */
:root {
    --primary: #4361ee;
    --primary-light: #4895ef;
    --primary-dark: #3f37c9;
    --secondary: #4cc9f0;
    --success: #4ade80;
    --danger: #f43f5e;
    --warning: #f59e0b;
    --info: #3b82f6;
    --light: #f9fafb;
    --dark: #1f2937;
    --gray: #6b7280;
    --gray-light: #e5e7eb;
    --gray-dark: #4b5563;
    --white: #ffffff;
    --black: #000000;
    
    --border-radius: 12px;
    --border-radius-sm: 8px;
    --border-radius-lg: 16px;
    --border-radius-xl: 24px;
    --border-radius-full: 9999px;
    
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    
    --transition: all 0.3s ease;
    --transition-fast: all 0.15s ease;
    --transition-slow: all 0.5s ease;
}

/* Base Styles */
.assign-manager-container {
    font-family: 'Inter', 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--dark);
    max-width: 1280px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
    margin: 0;
    font-weight: 600;
    line-height: 1.2;
}

h1 {
    font-size: 1.5rem;
}

h2 {
    font-size: 1.25rem;
}

h3 {
    font-size: 1.125rem;
}

p {
    margin: 0;
    line-height: 1.5;
}

/* Page Header */
.page-header {
    margin-bottom: 1.5rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 3rem;
    height: 3rem;
    background-color: rgba(67, 97, 238, 0.1);
    color: var(--primary);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.header-text p {
    color: var(--gray);
    margin-top: 0.25rem;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition-fast);
    border: none;
    font-size: 0.875rem;
    gap: 0.5rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.btn-primary {
    background-color: var(--primary);
    color: var(--white);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    color: var(--white);
}

.btn-primary:disabled {
    background-color: var(--gray-light);
    color: var(--gray);
    cursor: not-allowed;
}

.btn-outline {
    background-color: transparent;
    color: var(--gray-dark);
    border: 1px solid var(--gray-light);
}

.btn-outline:hover {
    background-color: var(--gray-light);
    color: var(--dark);
}

.btn-danger {
    background-color: var(--danger);
    color: var(--white);
}

.btn-danger:hover {
    background-color: #e11d48;
    color: var(--white);
}

/* Card */
.card {
    background-color: var(--white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: var(--transition);
    margin-bottom: 1.5rem;
}

.card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.card-body {
    padding: 0;
}

/* Assign Manager Content */
.assign-manager-content {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 992px) {
    .assign-manager-content {
        grid-template-columns: 1fr 1fr;
    }
}

/* Branch Summary */
.branch-summary {
    padding: 2rem;
    border-bottom: 1px solid var(--gray-light);
}

@media (min-width: 992px) {
    .branch-summary {
        border-bottom: none;
        border-right: 1px solid var(--gray-light);
    }
}

.branch-summary-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.branch-icon {
    width: 3rem;
    height: 3rem;
    background-color: rgba(67, 97, 238, 0.1);
    color: var(--primary);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.branch-summary-body {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray);
    font-size: 0.875rem;
}

.info-value {
    font-weight: 500;
    font-size: 1rem;
    padding-left: 1.75rem;
}

.current-manager {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background-color: rgba(67, 97, 238, 0.05);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.current-manager:hover {
    background-color: rgba(67, 97, 238, 0.1);
}

.manager-avatar {
    width: 3rem;
    height: 3rem;
    background-color: var(--primary);
    color: var(--white);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.manager-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.manager-name {
    font-weight: 600;
}

.manager-email {
    font-size: 0.875rem;
    color: var(--gray);
}

.no-manager {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background-color: rgba(244, 63, 94, 0.05);
    border-radius: var(--border-radius);
    color: var(--danger);
}

/* Manager Selection */
.manager-selection {
    padding: 2rem;
}

.manager-selection-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.selection-icon {
    width: 3rem;
    height: 3rem;
    background-color: rgba(67, 97, 238, 0.1);
    color: var(--primary);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.select-wrapper {
    position: relative;
}

.form-select {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: var(--dark);
    background-color: var(--white);
    background-clip: padding-box;
    border: 1px solid var(--gray-light);
    border-radius: var(--border-radius);
    transition: var(--transition-fast);
    appearance: none;
    padding-right: 2.5rem;
}

.form-select:focus {
    border-color: var(--primary);
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
}

.form-select.is-invalid {
    border-color: var(--danger);
}

.select-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    pointer-events: none;
}

.error-message {
    margin-top: 0.5rem;
    color: var(--danger);
    font-size: 0.875rem;
}

.alert-message {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background-color: rgba(245, 158, 11, 0.1);
    border-radius: var(--border-radius);
    margin-top: 1rem;
}

.alert-icon {
    color: var(--warning);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.alert-content {
    font-size: 0.875rem;
    color: var(--dark);
}

.form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 2rem;
}

/* Modal */
.modal {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal.show {
    display: flex;
}

.modal-backdrop {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-dialog {
    position: relative;
    width: 100%;
    max-width: 32rem;
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
}

.modal-dialog.modal-sm {
    max-width: 24rem;
}

.modal-content {
    background-color: var(--white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-light);
}

.modal-close {
    width: 2rem;
    height: 2rem;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: transparent;
    color: var(--gray);
    border: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.modal-close:hover {
    background-color: var(--gray-light);
    color: var(--dark);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--gray-light);
}

/* Utilities */
.hidden {
    display: none;
}

.text-danger {
    color: var(--danger);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove manager functionality
    const removeManagerBtn = document.getElementById('removeManagerBtn');
    const removeManagerForm = document.getElementById('remove-manager-form');
    const confirmModal = document.getElementById('confirmModal');
    const closeConfirmModal = document.getElementById('closeConfirmModal');
    const cancelRemoveBtn = document.getElementById('cancelRemoveBtn');
    const confirmRemoveBtn = document.getElementById('confirmRemoveBtn');
    
    // Open confirmation modal
    function openConfirmModal() {
        confirmModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    // Close confirmation modal
    function closeConfirmModal() {
        confirmModal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    if (removeManagerBtn) {
        removeManagerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openConfirmModal();
        });
    }
    
    if (closeConfirmModal) {
        closeConfirmModal.addEventListener('click', closeConfirmModal);
    }
    
    if (cancelRemoveBtn) {
        cancelRemoveBtn.addEventListener('click', closeConfirmModal);
    }
    
    if (confirmRemoveBtn) {
        confirmRemoveBtn.addEventListener('click', function() {
            removeManagerForm.submit();
        });
    }
    
    // Close modal when clicking on backdrop
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-backdrop')) {
            closeConfirmModal();
        }
    });
    
    // Form validation
    const assignManagerForm = document.getElementById('assignManagerForm');
    const managerSelect = document.getElementById('manager_user_id');
    
    if (assignManagerForm) {
        assignManagerForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Reset validation
            managerSelect.classList.remove('is-invalid');
            const errorElement = document.querySelector('.error-message');
            if (errorElement) {
                errorElement.remove();
            }
            
            // Validate manager selection
            if (managerSelect.value === '') {
                isValid = false;
                managerSelect.classList.add('is-invalid');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = 'Vui lòng chọn người quản lý';
                
                managerSelect.parentNode.insertAdjacentElement('afterend', errorDiv);
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Add animation to card
    const card = document.querySelector('.card');
    
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    }
});
</script>
@endsection
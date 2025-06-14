@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="assign-manager-container">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="header-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h1 class="text-xl font-semibold">Phân công quản lý chi nhánh</h1>
                    <p class="text-gray">Chọn người quản lý cho chi nhánh {{ $branch->name }}</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Main Content -->
    <div class="card">
        <div class="card-body">
            <div class="assign-manager-content">
                <!-- Branch Information -->
                <div class="branch-summary">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="branch-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h2 class="text-lg font-semibold">Thông tin chi nhánh</h2>
                    </div>
                    <div class="space-y-5">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-store-alt"></i> Tên chi nhánh</div>
                            <div class="info-value">{{ $branch->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ</div>
                            <div class="info-value">{{ $branch->address }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user-shield"></i> Người quản lý hiện tại</div>
                            <div class="info-value">
                                @if($branch->manager)
                                <div class="current-manager">
                                    <div class="manager-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="manager-name">{{ $branch->manager->full_name }}</div>
                                        <div class="manager-email">{{ $branch->manager->email }}</div>
                                    </div>
                                </div>
                                @else
                                <div class="no-manager">
                                    <i class="fas fa-user-slash"></i> Chưa có người quản lý
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manager Selection Form -->
                <div class="manager-selection">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="selection-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h2 class="text-lg font-semibold">Chọn người quản lý mới</h2>
                    </div>
                    <form action="{{ route('admin.branches.update-manager', $branch->id) }}" method="POST" id="assignManagerForm">
                        @csrf
                        <div class="form-group">
                            <label for="manager_user_id" class="form-label">Người quản lý</label>
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
                                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                <div class="alert-content">Không có quản lý nào khả dụng. Tất cả quản lý đã được phân công.</div>
                            </div>
                            @endif
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg" {{ count($availableManagers) == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
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
                <h3 class="text-base font-semibold">Xác nhận gỡ bỏ</h3>
                <button type="button" class="modal-close" id="closeConfirmModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn gỡ bỏ người quản lý hiện tại?</p>
                <p class="text-danger">Chi nhánh sẽ không có người quản lý cho đến khi bạn phân công người mới.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelRemoveBtn">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveBtn">
                    <i class="fas fa-user-minus"></i> Gỡ bỏ
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #4361ee;
        --primary-hover: #3f37c9;
        --danger-color: #f43f5e;
        --danger-hover: #e11d48;
        --gray-light: #e5e7eb;
        --gray-dark: #6b7280;
        --text-dark: #1f2937;
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --border-radius: 12px;
    }

    .assign-manager-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    h1 {
        font-size: 1.5rem;
        font-weight: 600;
    }

    h2 {
        font-size: 1.25rem;
        font-weight: 600;
    }

    h3 {
        font-size: 1.125rem;
        font-weight: 600;
    }

    p {
        line-height: 1.5;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
        border: none;
        font-size: 0.875rem;
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--primary-hover);
    }

    .btn-primary:disabled {
        background: var(--gray-light);
        color: var(--gray-dark);
        cursor: not-allowed;
    }

    .btn-outline {
        background: transparent;
        color: var(--text-dark);
        border: 1px solid var(--gray-light);
    }

    .btn-outline:hover {
        background: var(--gray-light);
    }

    .btn-danger {
        background: var(--danger-color);
        color: #fff;
    }

    .btn-danger:hover {
        background: var(--danger-hover);
    }

    .mb-6 {
        margin-bottom: 1.5rem;
    }

    .text-gray {
        color: var(--gray-dark);
    }

    .card {
        background: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .card-body {
        padding: 0;
    }

    .assign-manager-content {
        display: grid;
        gap: 2rem;
    }

    @media (min-width: 992px) {
        .assign-manager-content {
            grid-template-columns: 1fr 1fr;
        }
    }

    .header-icon,
    .branch-icon,
    .selection-icon {
        width: 3rem;
        height: 3rem;
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

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

    .space-y-5>*+* {
        margin-top: 1.25rem;
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
        color: var(--gray-dark);
        font-size: 0.875rem;
    }

    .info-value {
        font-weight: 500;
        padding-left: 1.75rem;
    }

    .current-manager {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(67, 97, 238, 0.05);
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
    }

    .current-manager:hover {
        background: rgba(67, 97, 238, 0.1);
    }

    .manager-avatar {
        width: 3rem;
        height: 3rem;
        background: var(--primary-color);
        color: #fff;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .manager-name {
        font-weight: 600;
    }

    .manager-email {
        font-size: 0.875rem;
        color: var(--gray-dark);
    }

    .no-manager {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: rgba(244, 63, 94, 0.05);
        border-radius: var(--border-radius);
        color: var(--danger-color);
    }

    .manager-selection {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .select-wrapper {
        position: relative;
    }

    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        color: var(--text-dark);
        background: #fff;
        border: 1px solid var(--gray-light);
        border-radius: var(--border-radius);
        transition: all 0.15s ease;
        appearance: none;
        padding-right: 2.5rem;
    }

    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    .form-select.is-invalid {
        border-color: var(--danger-color);
    }

    .select-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-dark);
        pointer-events: none;
    }

    .error-message {
        margin-top: 0.5rem;
        color: var(--danger-color);
        font-size: 0.875rem;
    }

    .alert-message {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: rgba(245, 158, 11, 0.1);
        border-radius: var(--border-radius);
        margin-top: 1rem;
    }

    .alert-icon {
        color: #f59e0b;
        font-size: 1.25rem;
    }

    .alert-content {
        font-size: 0.875rem;
        color: var(--text-dark);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

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
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .modal-dialog {
        width: 100%;
        max-width: 24rem;
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
    }

    .modal-content {
        background: #fff;
        border-radius: var(--border-radius);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: var(--gray-dark);
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .modal-close:hover {
        background: var(--gray-light);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid var(--gray-light);
    }

    .text-danger {
        color: var(--danger-color);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const elements = {
            confirmModal: document.getElementById('confirmModal'),
            closeConfirmModal: document.getElementById('closeConfirmModal'),
            cancelRemoveBtn: document.getElementById('cancelRemoveBtn'),
            confirmRemoveBtn: document.getElementById('confirmRemoveBtn'),
            removeManagerBtn: document.getElementById('removeManagerBtn'),
            assignManagerForm: document.getElementById('assignManagerForm'),
            managerSelect: document.getElementById('manager_user_id'),
            card: document.querySelector('.card')
        };

        const toggleModal = (show) => {
            elements.confirmModal.classList.toggle('show', show);
            document.body.style.overflow = show ? 'hidden' : '';
        };

        const handleValidation = (e) => {
            e.preventDefault();
            const {
                managerSelect,
                assignManagerForm
            } = elements;
            managerSelect.classList.remove('is-invalid');
            const errorElement = managerSelect.parentNode.querySelector('.error-message');
            if (errorElement) errorElement.remove();

            if (!managerSelect.value) {
                managerSelect.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = 'Vui lòng chọn người quản lý';
                managerSelect.parentNode.insertAdjacentElement('afterend', errorDiv);
                return;
            }
            assignManagerForm.submit();
        };

        const handleRemoveManager = () => {
            const {
                assignManagerForm,
                managerSelect
            } = elements;
            managerSelect.value = '';
            assignManagerForm.submit();
        };

        const animateCard = () => {
            const {
                card
            } = elements;
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }
        };

        // Event Listeners
        elements.closeConfirmModal?.addEventListener('click', () => toggleModal(false));
        elements.cancelRemoveBtn?.addEventListener('click', () => toggleModal(false));
        elements.removeManagerBtn?.addEventListener('click', () => toggleModal(true));
        elements.confirmRemoveBtn?.addEventListener('click', () => {
            toggleModal(false);
            handleRemoveManager();
        });
        elements.assignManagerForm?.addEventListener('submit', handleValidation);
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) toggleModal(false);
        });

        animateCard();
    });
</script>
@endsection
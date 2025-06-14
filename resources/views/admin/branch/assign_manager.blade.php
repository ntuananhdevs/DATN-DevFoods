@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Phân công quản lý chi nhánh')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/branchs/assign-manager.css') }}">
@endsection

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
@endsection

@section('page-script')
<script src="{{ asset('js/admin/branchs/assign-manager.js') }}"></script>
@endsection
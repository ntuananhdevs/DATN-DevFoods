@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Phân công quản lý chi nhánh')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user-tie text-primary me-2"></i>Phân công quản lý chi nhánh
                    </h5>
                    <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <div class="branch-info bg-light p-4 rounded-3 mb-4">
                                <h6 class="fw-bold mb-3">Thông tin chi nhánh</h6>
                                <p class="mb-2"><strong>Tên chi nhánh:</strong> {{ $branch->name }}</p>
                                <p class="mb-2"><strong>Địa chỉ:</strong> {{ $branch->address }}</p>
                                <p class="mb-0"><strong>Người quản lý hiện tại:</strong> 
                                    @if($branch->manager)
                                        {{ $branch->manager->full_name }}
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </p>
                            </div>

                            <form action="{{ route('admin.branches.update-manager', $branch->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="manager_user_id" class="form-label fw-bold">Chọn người quản lý mới</label>
                                    <select class="form-select @error('manager_user_id') is-invalid @enderror" id="manager_user_id" name="manager_user_id">
                                        <option value="">-- Chọn người quản lý --</option>
                                        @foreach($availableManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('manager_user_id', $branch->manager_user_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name }} ({{ $manager->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manager_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(count($availableManagers) == 0)
                                        <div class="alert alert-warning mt-2">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Không có quản lý nào khả dụng. Tất cả quản lý đã được phân công cho các chi nhánh khác.
                                        </div>
                                    @endif
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" {{ count($availableManagers) == 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-save me-1"></i>Lưu thay đổi
                                    </button>
                                 
                                </div>
                            </form>

                            @if($branch->manager)
                                <form id="remove-manager-form" action="{{ route('admin.branches.remove-manager', $branch->id) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
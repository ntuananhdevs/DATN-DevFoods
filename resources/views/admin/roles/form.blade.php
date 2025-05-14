<link rel="stylesheet" href="{{ asset('css/roles.css') }}">
<form action="{{ $action }}" method="POST" class="role-form-container">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <h2 class="text-center mb-4 role-form-title">
        <i class="fas fa-user-shield text-success me-2"></i>
        {{ $isEdit ? 'Cập nhật Role' : 'Thêm Role' }}
    </h2>

    {{-- Tên Role --}}
    <div class="mb-2">
        <label for="name" class="form-label role-form-label">Tên Role</label>
        <input type="text" id="name" name="name" class="form-control role-form-input"
            placeholder="Nhập tên role" value="{{ old('name', $role->name ?? '') }}">
        @error('name')
            <div class="text-danger mt-2">{{ $message }}</div>
        @enderror
    </div>

    {{-- Quyền --}}
    <div class="mb-4">
        <label class="form-label role-form-label">Quyền</label>
        <div class="role-permissions-container">
            @php
                $allPermissions = [
                    'create' => 'Tạo',
                    'edit' => 'Chỉnh sửa',
                    'view' => 'Xem',
                    'delete' => 'Xóa',
                ];
            @endphp
            @foreach ($allPermissions as $key => $label)
                <div class="role-permission-item">
                    <input type="checkbox" id="permission_{{ $key }}" name="permissions[]"
                        value="{{ $key }}" class="role-permission-checkbox"
                        {{ isset($role) && is_array($role->permissions) && in_array($key, $role->permissions) ? 'checked' : '' }}>
                    <label for="permission_{{ $key }}"
                        class="role-permission-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        @error('permissions')
            <div class="text-danger mt-2">{{ $message }}</div>
        @enderror
    </div>

    {{-- Nút hành động --}}
    <div>
        <button type="submit" class="btn btn-success me-2 role-form-btn">
            <i class="fas fa-{{ $isEdit ? 'save' : 'plus-circle' }} me-2"></i>
            {{ $isEdit ? 'Cập nhật Role' : 'Tạo Role' }}
        </button>
        <a href="{{ route('admin.roles.index') }}"
            class="btn btn-white text-dark border border-secondary role-form-btn">
            <i class="fas fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>
</form>

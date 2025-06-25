<link rel="stylesheet" href="{{ asset('css/roles.css') }}">
<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <h2 class="mb-4 role-form-title">
        <i class="fas fa-user-shield text-success me-2"></i>
        {{ $isEdit ? 'Cập nhật Role' : 'Thêm Role' }}
    </h2>

    {{-- Tên Role --}}
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Role</label>
        <input type="text" id="name" name="name"
            class="form-input block w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/30"
            placeholder="Nhập tên role" value="{{ old('name', $role->name ?? '') }}">
        @error('name')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Quyền --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Quyền</label>
        <div class="flex flex-wrap gap-4">
            @php
                $allPermissions = [
                    'create' => 'Tạo',
                    'edit' => 'Chỉnh sửa',
                    'view' => 'Xem',
                    'delete' => 'Xóa',
                ];
            @endphp
            @foreach ($allPermissions as $key => $label)
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="permission_{{ $key }}" name="permissions[]"
                        value="{{ $key }}" class="form-checkbox text-primary focus:ring-primary"
                        {{ isset($role) && is_array($role->permissions) && in_array($key, $role->permissions) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('permissions')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Nút hành động --}}
    <div class="flex items-center gap-3">
        <button type="submit" class="btn btn-primary flex items-center gap-2">
            <i class="fas fa-{{ $isEdit ? 'save' : 'plus-circle' }}"></i>
            {{ $isEdit ? 'Cập nhật Role' : 'Tạo Role' }}
        </button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</form>

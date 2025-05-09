<form action="{{ $action }}" method="POST">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label" style="font-size:1.2em; font-weight: bold;">Tên Role</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ old('name', $role->name ?? '') }}">
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" style="font-size:1.2em; font-weight: bold;">Quyền</label>
        <div class="form-check mb-2">
            <input type="checkbox" name="permissions[]" value="create" id="permission-create" class="form-check-input"
                {{ isset($role) && is_array($role->permissions) && in_array('create', $role->permissions) ? 'checked' : '' }}>
            <label for="permission-create" class="form-check-label">Tạo</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="permissions[]" value="edit" id="permission-edit" class="form-check-input"
                {{ isset($role) && is_array($role->permissions) && in_array('edit', $role->permissions) ? 'checked' : '' }}>
            <label for="permission-edit" class="form-check-label">Chỉnh sửa</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="permissions[]" value="view" id="permission-view" class="form-check-input"
                {{ isset($role) && is_array($role->permissions) && in_array('view', $role->permissions) ? 'checked' : '' }}>
            <label for="permission-view" class="form-check-label">Xem</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="permissions[]" value="delete" id="permission-delete" class="form-check-input"
                {{ isset($role) && is_array($role->permissions) && in_array('delete', $role->permissions) ? 'checked' : '' }}>
            <label for="permission-delete" class="form-check-label">Xóa</label>
        </div>
        @error('permissions')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex ">
        <button type="submit" class="btn btn-{{ $isEdit ? 'primary' : 'success' }} mr-1">
            {{ $isEdit ? 'Cập nhật Role' : 'Tạo Role' }}
        </button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary ">Quay lại</a>
    </div>
</form>

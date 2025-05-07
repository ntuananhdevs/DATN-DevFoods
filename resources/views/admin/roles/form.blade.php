<form action="{{ $action }}" method="POST">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="form-group mb-3">
        <label for="name">Tên Role</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $role->name ?? '') }}"
            required>
    </div>

    <div class="form-group mb-3">
        <label for="permissions">Quyền (dạng JSON)</label>
        <input type="text" name="permissions" id="permissions" class="form-control"
            value='{{ old('permissions', $role->permissions ?? '["view"]') }}' required>
        <small class="text-muted">VD: ["view", "create", "edit", "delete"]</small>
    </div>

    <button type="submit" class="btn btn-{{ $isEdit ? 'primary' : 'success' }}">
        {{ $isEdit ? 'Cập nhật Role' : 'Tạo Role' }}
    </button>
</form>

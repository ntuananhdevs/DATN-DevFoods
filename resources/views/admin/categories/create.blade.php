@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <h3>Thêm danh mục mới</h3>
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tên danh mục</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" >
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-control">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
            </select>
        </div>

        <div class="d-flex">
        <button type="submit" class="btn btn-success mr-1">Lưu</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
        </div>
    </form>
@endsection

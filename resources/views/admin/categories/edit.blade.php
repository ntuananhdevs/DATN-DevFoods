@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <h3>Cập nhật danh mục</h3>
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Tên danh mục</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" >
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh hiện tại</label><br>
            @if ($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" width="100">
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh mới</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-control">
                <option value="1" {{ $category->status ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ !$category->status ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div class="d-flex">
        <button type="submit" class="btn btn-success mr-1">Cập nhật</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
        </div>
    </form>
@endsection

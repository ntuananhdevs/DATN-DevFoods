@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <h3>Cập nhật banner</h3>
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description', $banner->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Liên kết</label>
            <input type="url" name="link" class="form-control" value="{{ old('link', $banner->link) }}" placeholder="https://example.com">
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh hiện tại</label><br>
            @if ($banner->image_path)
                <img src="{{ asset('storage/' . $banner->image_path) }}" width="200">
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh mới</label>
            <input type="file" name="image_path" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at', $banner->start_at->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày kết thúc</label>
            <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at', $banner->end_at->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $banner->is_active ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ !$banner->is_active ? 'selected' : '' }}>Vô hiệu hóa</option>
            </select>
        </div>

        <div class="d-flex">
            <button type="submit" class="btn btn-success mr-1">Cập nhật</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
@endsection

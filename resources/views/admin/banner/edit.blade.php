@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <h3>Cập nhật banner</h3>
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $banner->title) }}">
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $banner->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Liên kết sản phẩm</label>
            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $banner->link) }}" placeholder="https://example.com/products/123">
            @error('link')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh hiện tại</label><br>
            @if ($banner->image_path)
                <img src="{{ asset('storage/' . $banner->image_path) }}" width="200">
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh mới</label>
            <input type="file" name="image_path" class="form-control @error('image_path') is-invalid @enderror">
            @error('image_path')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="datetime-local" name="start_at" class="form-control @error('start_at') is-invalid @enderror" value="{{ old('start_at', $banner->start_at->format('Y-m-d\TH:i')) }}">
            @error('start_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày kết thúc</label>
            <input type="datetime-local" name="end_at" class="form-control @error('end_at') is-invalid @enderror" value="{{ old('end_at', $banner->end_at->format('Y-m-d\TH:i')) }}">
            @error('end_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $banner->is_active ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ !$banner->is_active ? 'selected' : '' }}>Vô hiệu hóa</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Vị trí hiển thị</label>
            <select name="order" class="form-control">
                <option value="0" {{ $banner->order == 0 ? 'selected' : '' }}>Đầu tiên</option>
                <option value="1" {{ $banner->order == 1 ? 'selected' : '' }}>Giữa</option>
                <option value="2" {{ $banner->order == 2 ? 'selected' : '' }}>Cuối cùng</option>
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

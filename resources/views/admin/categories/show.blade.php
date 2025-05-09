@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <div class="container mt-4">
        <h2>Chi tiết danh mục</h2>
        <div class="card">
            <div class="card-header">{{ $category->name }}</div>
            <div class="card-body">
                @if ($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" class="img-thumbnail mb-3" width="200">
                @endif

                <p><strong>Mô tả:</strong> {{ $category->description ?: 'Không có mô tả' }}</p>
                <p><strong>Trạng thái:</strong> {{ $category->status ? 'Hiển thị' : 'Ẩn' }}</p>
                <p><strong>Ngày tạo:</strong> {{ $category->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning">Sửa</a>
            </div>
        </div>
    </div>
@endsection

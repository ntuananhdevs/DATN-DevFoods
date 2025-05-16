@extends('layouts.admin.contentLayoutMaster')

@section('content')

<div class="container">
    <h2 class="mb-4">Chi tiết danh mục</h2>

    <div class="category-details-container">
        {{-- Bên trái: Ảnh + tên + trạng thái --}}
        <div class="category-card">
            @if ($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="category-avatar">
            @else
                <center>
                    <div class="category-avatar bg-light d-flex align-items-center justify-content-center">
                    <span class="d-flex text-center justify-content-center align-items-center">Không có ảnh</p>
                </div>
                </center>
            @endif

            <h3 class="category-name">{{ $category->name }}</h3>
            <span class="category-status-badge {{ $category->status ? 'category-status-show' : 'category-status-hide' }}">
                {{ $category->status ? 'Hiển thị' : 'Ẩn' }}
            </span>

            <div class="category-actions mt-4">
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning">
                    <i class="fas fa-pen"></i> Sửa
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- Bên phải: Thông tin chi tiết --}}
        <div class="category-info-card">
            <h5 class="mb-3">Thông tin danh mục</h5>
            <table class="table table-borderless category-info-table">
                <tr>
                    <td>Mô tả</td>
                    <td>{{ $category->description ?: 'Không có mô tả' }}</td>
                </tr>
                <tr>
                    <td>Trạng thái</td>
                    <td>{{ $category->status ? 'Hiển thị' : 'Ẩn' }}</td>
                </tr>
                <tr>
                    <td>Ngày tạo</td>
                    <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Lần cập nhật</td>
                    <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

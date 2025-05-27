@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chi tiết danh mục')

@section('content')
<main class="container mx-auto px-4">
  <div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Chi tiết danh mục</h1>
      <div>
        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-primary">Chỉnh sửa</a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-secondary ml-2">Quay lại</a>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Thông tin chi tiết -->
      <div class="space-y-4">
        <div>
          <p class="text-sm text-gray-600">ID</p>
          <p class="text-lg font-medium">{{ $category->id }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-600">Tên danh mục</p>
          <p class="text-lg font-medium">{{ $category->name }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-600">Mô tả</p>
          <p class="text-gray-800">{{ $category->description ?? 'Không có mô tả' }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-600">Trạng thái</p>
          <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $category->status ? 'Hiển thị' : 'Ẩn' }}
          </span>
        </div>

        <div>
          <p class="text-sm text-gray-600">Ngày tạo</p>
          <p class="text-gray-700">{{ $category->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-600">Cập nhật lần cuối</p>
          <p class="text-gray-700">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
        </div>
      </div>

      <!-- Hình ảnh -->
      <div>
        <p class="text-sm text-gray-600 mb-2">Ảnh danh mục</p>
        @php
        $imagePath = $category->image ?? 'categories/default-logo.avif';
        @endphp
        <img src="{{ Storage::disk('s3')->url($imagePath) }}" alt="{{ $category->name }}">
      </div>
    </div>
  </div>
</main>
@endsection
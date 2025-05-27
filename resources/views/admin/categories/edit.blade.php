@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chỉnh sửa danh mục')

@section('content')
<style>
  input[type="text"], textarea, input[type="file"], select {
    padding: 0.625rem 0.75rem;
    height: 2.75rem;
  }
  textarea {
    min-height: 6rem;
  }
  #image-upload-area {
    transition: all 0.2s ease;
    border: 2px dashed #d1d5db;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    border-radius: 0.5rem;
  }
  #image-upload-area:hover {
    background-color: #f9fafb;
    border-color: #9ca3af;
  }
  #main-image-preview img {
    border-radius: 0.375rem;
  }
</style>

<main class="container">
  <h1 class="text-3xl font-extrabold mb-1">Chỉnh sửa Danh Mục</h1>
  <p class="text-gray-500 mb-8">Cập nhật thông tin danh mục</p>

  <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
      <header class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-xl font-semibold text-gray-900">Thông tin cơ bản</h2>
        <p class="text-gray-500 text-sm mt-1">Cập nhật thông tin của danh mục</p>
      </header>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
        <!-- Left Column -->
        <div class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Tên danh mục</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            @error('name')
              <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
            <textarea id="description" name="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">{{ old('description', $category->description) }}</textarea>
            @error('description')
              <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select id="status" name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
              <option value="1" {{ old('status', $category->status) == 1 ? 'selected' : '' }}>Hiển thị</option>
              <option value="0" {{ old('status', $category->status) == 0 ? 'selected' : '' }}>Ẩn</option>
            </select>
          </div>
        </div>

        <!-- Upload ảnh -->
        <div class="md:col-span-1">
          <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh danh mục</label>
          <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
            <div id="image-placeholder" class="w-full h-80 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all relative">
              <div id="main-image-preview" class="absolute inset-0 w-full h-full {{ $category->image ? '' : 'hidden' }}">
                @php
                $imagePath = $category->image ?: 'categories/default-logo.avif';
                @endphp
                <img src="{{ Storage::disk('s3')->url($imagePath) }}" alt="Main image preview" class="w-full h-full object-cover" />
              </div>
              <div id="upload-content" class="flex flex-col items-center justify-center {{ $category->image ? 'hidden' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-gray-400 mb-3" width="48" height="48" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="17 8 12 3 7 8" />
                  <line x1="12" y1="3" x2="12" y2="15" />
                </svg>
                <p class="text-base text-gray-600 mb-2">Kéo thả ảnh chính vào đây</p>
                <p class="text-sm text-gray-500 mb-4">hoặc</p>
                <button type="button" id="select-primary-image-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Chọn ảnh</button>
                <p class="text-xs text-gray-500 mt-3">Hỗ trợ: JPG, PNG, GIF (Tối đa 5MB)</p>
              </div>
              <input type="file" id="primary-image-upload" name="image" accept="image/*" class="hidden" />
              @error('image')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">
            <span class="font-semibold text-blue-600">Lưu ý:</span> Ảnh sẽ được sử dụng làm ảnh đại diện danh mục.
          </p>
        </div>
      </div>
    </section>

    <div class="px-6 pb-6">
      <button type="submit" class="btn btn-primary">Cập nhật</button>
      <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ml-2">Hủy</a>
    </div>
  </form>
</main>

<script>
  document.getElementById('select-primary-image-btn').addEventListener('click', () => {
    document.getElementById('primary-image-upload').click();
  });

  document.getElementById('image-placeholder').addEventListener('click', (e) => {
    if (e.target.id === 'image-placeholder' || e.target.id === 'upload-content') {
      document.getElementById('primary-image-upload').click();
    }
  });

  function previewImage(event) {
    const input = event.target;
    const previewWrapper = document.getElementById('main-image-preview');
    const previewImage = previewWrapper.querySelector('img');
    const uploadContent = document.getElementById('upload-content');

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImage.src = e.target.result;
        previewWrapper.style.display = 'block';
        uploadContent.style.display = 'none';
        previewWrapper.classList.remove('hidden');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  document.getElementById('primary-image-upload').addEventListener('change', previewImage);
  document.getElementById('main-image-preview').addEventListener('click', () => {
    document.getElementById('primary-image-upload').click();
  });
</script>
@endsection
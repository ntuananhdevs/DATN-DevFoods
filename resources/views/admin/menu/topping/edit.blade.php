@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chỉnh sửa Topping - ' . $topping->name)

@section('content')

<main class="container">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold mb-1">Chỉnh sửa Topping</h1>
            <p class="text-gray-500">Cập nhật thông tin topping: {{ $topping->name }}</p>
        </div>
        <a href="{{ route('admin.toppings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <form id="edit-topping-form" class="space-y-8" action="{{ route('admin.toppings.update', $topping->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Thông tin cơ bản</h2>
                    <p class="text-gray-500 text-sm mt-1">Cập nhật thông tin cơ bản của topping</p>
                </div>
            </header>

            <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-5 md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên Topping <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" placeholder="Nhập tên topping"
                                value="{{ old('name', $topping->name) }}"
                                class="mt-1 block w-full px-4 py-3 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror" />
                            @error('name')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Giá <span class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <input type="number" id="price" name="price" min="0" step="1000"
                                    placeholder="0" value="{{ old('price', $topping->price) }}"
                                    class="block w-full px-4 py-3 pr-12 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('price') border-red-500 @enderror" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">VNĐ</span>
                                </div>
                            </div>
                            @error('price')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea id="description" name="description" rows="4"
                            placeholder="Nhập mô tả topping"
                            class="mt-1 block w-full px-4 py-3 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none @error('description') border-red-500 @enderror">{{ old('description', $topping->description) }}</textarea>
                        @error('description')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700">Trạng thái</span>
                        <div class="mt-2 space-y-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="active"
                                    {{ old('status', $topping->active ? 'active' : 'inactive') == 'active' ? 'checked' : '' }}
                                    class="form-radio text-blue-600" />
                                <span>Đang bán</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="inactive"
                                    {{ old('status', $topping->active ? 'active' : 'inactive') == 'inactive' ? 'checked' : '' }}
                                    class="form-radio text-yellow-600" />
                                <span>Tạm ngưng</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="discontinued"
                                    {{ old('status', $topping->active ? 'active' : 'inactive') == 'discontinued' ? 'checked' : '' }}
                                    class="form-radio text-red-600" />
                                <span>Chưa bán nữa</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Chọn trạng thái hiển thị của topping</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh topping</label>
                        @error('image')
                            <div class="text-red-500 text-xs mb-2">{{ $message }}</div>
                        @enderror
                        <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                            <div id="image-placeholder"
                                class="w-full h-80 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all relative">
                                <div id="image-preview" class="absolute inset-0 w-full h-full {{ $topping->image ? '' : 'hidden' }}">
                                    <img id="preview-img" src="{{ $topping->image ? Storage::disk('s3')->url($topping->image) : '' }}" alt="Image preview"
                                        class="w-full h-full object-cover" />
                                </div>
                                <div id="upload-content" class="flex flex-col items-center justify-center {{ $topping->image ? 'hidden' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="stroke-current text-gray-400 mb-3" width="48"
                                        height="48" fill="none" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="17 8 12 3 7 8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                    <p class="text-base text-gray-600 mb-2">Kéo thả ảnh vào đây</p>
                                    <p class="text-sm text-gray-500 mb-4">hoặc</p>
                                    <button type="button" id="select-image-btn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Chọn
                                        ảnh</button>
                                    <p class="text-xs text-gray-500 mt-3">Hỗ trợ: JPG, PNG, GIF (Tối đa 2MB)
                                    </p>
                                </div>
                                <input type="file" id="image-upload" name="image"
                                    accept="image/*" class="hidden" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.toppings.stock', $topping->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-boxes mr-2"></i>Quản lý tồn kho
            </a>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.toppings.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Cập nhật Topping
                </button>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const imageUpload = $('#image-upload');
    const imagePlaceholder = $('#image-placeholder');
    const imagePreview = $('#image-preview');
    const previewImg = $('#preview-img');
    const uploadContent = $('#upload-content');
    const selectImageBtn = $('#select-image-btn');

    // Handle file selection button click
    selectImageBtn.on('click', function(e) {
        e.preventDefault();
        imageUpload.click();
    });

    // Handle file input change
    imageUpload.on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.attr('src', e.target.result);
                imagePreview.removeClass('hidden');
                uploadContent.addClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            // If no file selected, show existing image or upload content
            const existingImage = '{{ $topping->image ? Storage::disk("s3")->url($topping->image) : "" }}';
            if (existingImage) {
                previewImg.attr('src', existingImage);
                imagePreview.removeClass('hidden');
                uploadContent.addClass('hidden');
            } else {
                imagePreview.addClass('hidden');
                uploadContent.removeClass('hidden');
            }
        }
    });

    // Handle drag and drop
    imagePlaceholder.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('border-blue-400 bg-blue-50');
    });

    imagePlaceholder.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('border-blue-400 bg-blue-50');
    });

    imagePlaceholder.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('border-blue-400 bg-blue-50');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                imageUpload[0].files = files;
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.attr('src', e.target.result);
                    imagePreview.removeClass('hidden');
                    uploadContent.addClass('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Handle click on placeholder to select file
    imagePlaceholder.on('click', function(e) {
        if (e.target === this || $(e.target).closest('#upload-content').length || $(e.target).closest('#image-preview').length) {
            imageUpload.click();
        }
    });

    // Format price input
    $('#price').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(parseInt(value));
        }
    });
});
</script>
@endpush
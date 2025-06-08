@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Thêm sản phẩm mới')

@section('content')

@section('page-style-prd-edit')
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
@endsection

<main class="container">
    <h1 class="text-3xl font-extrabold mb-1">Thêm Sản Phẩm Mới</h1>
    <p class="text-gray-500 mb-8">Nhập thông tin chi tiết để tạo sản phẩm mới</p>

    <form id="add-product-form" class="space-y-8" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Thông tin cơ bản</h2>
                    <p class="text-gray-500 text-sm mt-1">Nhập thông tin cơ bản của sản phẩm</p>
                </div>
            </header>

            <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-5 md:col-span-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Tên sản phẩm <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Nhập tên sản phẩm"
                            value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror" />
                        @error('name')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục <span
                                    class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('category_id') border-red-500 @enderror">
                                <option value="">Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">Giá cơ bản <span
                                    class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <input type="number" id="base_price" name="base_price" min="0" step="0.01"
                                    placeholder="0" value="{{ old('base_price') }}"
                                    class="block w-full pl-7 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('base_price') border-red-500 @enderror" />
                            </div>
                            @error('base_price')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-700">Thời gian
                                chuẩn bị (phút) <span
                                class="text-red-500">*</span></label>
                            <input type="number" id="preparation_time" name="preparation_time" min="0"
                                placeholder="Nhập thời gian chuẩn bị" value="{{ old('preparation_time') }}"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('preparation_time') border-red-500 @enderror" />
                            @error('preparation_time')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="ingredients" class="block text-sm font-medium text-gray-700">Nguyên liệu 
                            <span class="text-red-500">*</span></label>
                        <textarea id="ingredients" name="ingredients" rows="3"
                            placeholder="Nhập danh sách nguyên liệu (mỗi nguyên liệu một dòng)"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none @error('ingredients') border-red-500 @enderror">{{ old('ingredients') }}</textarea>
                        @error('ingredients')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700">Mô tả ngắn
                            <span class="text-red-500">*</span></label>
                        </label>
                        <textarea id="short_description" name="short_description" rows="2" placeholder="Nhập mô tả ngắn về sản phẩm" 
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none @error('short_description') border-red-500 @enderror">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
                        <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Tùy chọn</span>
                        <div class="space-y-4 mt-2">
                            <div class="flex gap-4">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_featured" value="1"
                                        {{ old('is_featured') ? 'checked' : '' }}
                                        class="form-checkbox text-blue-600" />
                                    <span>Sản phẩm nổi bật</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái sản phẩm</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="coming_soon"
                                            @if (old('status', 'selling') == 'coming_soon') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Sắp ra mắt</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="selling"
                                            @if (old('status', 'selling') == 'selling') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Đang bán</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="discontinued"
                                            @if (old('status', 'selling') == 'discontinued') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Ngừng bán</span>
                                    </label>
                                </div>
                            </div>

                            <div id="release_at_container" class="hidden">
                                <label for="release_at" class="block text-sm font-medium text-gray-700">Ngày ra mắt</label>
                                <input type="datetime-local" id="release_at" name="release_at"
                                    value="{{ old('release_at') }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh sản phẩm <span
                                class="text-red-500">*</span></label>
                        @error('primary_image')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                        @error('images')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                            <!-- Primary Image -->
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh chính</label>
                                <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                                    <div id="image-placeholder"
                                        class="w-full h-80 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all relative">
                                        <div id="main-image-preview" class="absolute inset-0 w-full h-full hidden">
                                            <img src="" alt="Main image preview"
                                                class="w-full h-full object-cover" />
                                        </div>
                                        <div id="upload-content" class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="stroke-current text-gray-400 mb-3" width="48"
                                                height="48" fill="none" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                <polyline points="17 8 12 3 7 8" />
                                                <line x1="12" y1="3" x2="12" y2="15" />
                                            </svg>
                                            <p class="text-base text-gray-600 mb-2">Kéo thả ảnh chính vào đây</p>
                                            <p class="text-sm text-gray-500 mb-4">hoặc</p>
                                            <button type="button" id="select-primary-image-btn"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Chọn
                                                ảnh chính</button>
                                            <p class="text-xs text-gray-500 mt-3">Hỗ trợ: JPG, PNG, GIF (Tối đa 5MB)
                                            </p>
                                        </div>
                                        <input type="file" id="primary-image-upload" name="primary_image"
                                            accept="image/*" class="hidden" />
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mb-2">
                                    <span class="font-semibold text-blue-600">Lưu ý:</span> Ảnh đầu tiên sẽ được sử
                                    dụng làm ảnh chính của sản phẩm.
                                </p>
                            </div>

                            <!-- Additional Images -->
                            <div class="md:col-span-2">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Ảnh phụ</label>
                                    <button type="button" id="select-additional-images-btn"
                                        class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Thêm ảnh
                                    </button>
                                    <input type="file" id="additional-images-upload" name="images[]"
                                        accept="image/*" multiple class="hidden" />
                                </div>
                                <div id="image-gallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Attributes and Variant Values -->
        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Thuộc tính và Giá trị biến thể</h2>
                <p class="text-gray-500 text-sm mt-1">Thêm các thuộc tính và giá trị biến thể cho sản phẩm</p>
            </header>

            <div class="px-6 py-6">
                <div id="attributes-container">
                    <!-- Default attribute group -->
                    <div class="p-4 border border-gray-200 rounded-md mb-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-md font-semibold text-gray-800">Thuộc tính 1</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="attribute_name_0" class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                                <input type="text" id="attribute_name_0" name="attributes[0][name]" placeholder="VD: Kích thước"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ old('attributes.0.name') }}">
                                @error('attributes.0.name')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-span-2">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Giá trị thuộc tính</h4>
                                <div id="attribute_values_container_0" class="space-y-3">
                                    <!-- Default attribute value -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-dashed border-gray-300 rounded-md">
                                        <div class="md:col-span-2">
                                            <label for="attribute_value_0_0" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                            <input type="text" id="attribute_value_0_0" name="attributes[0][values][0][value]" placeholder="VD: Nhỏ"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="{{ old('attributes.0.values.0.value') }}">
                                            @error('attributes.0.values.0.value')
                                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="attribute_price_0_0" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                            <input type="number" id="attribute_price_0_0" name="attributes[0][values][0][price]" placeholder="0" step="any"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="{{ old('attributes.0.values.0.price') }}">
                                            @error('attributes.0.values.0.price')
                                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="add-attribute-value-btn mt-2 text-sm text-blue-600 hover:text-blue-800" data-index="0">+ Thêm giá trị</button>
                            </div>
                        </div>
                    </div>
                </div>
                @error('attributes')
                    <div class="text-red-500 text-xs mt-2 mb-2">{{ $message }}</div>
                @enderror
                <button type="button" id="add-attribute-btn"
                    class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16"
                        fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Thêm thuộc tính
                </button>
            </div>
        </section>
        <!-- Toppings Section -->
        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Toppings</h2>
                <p class="text-gray-500 text-sm mt-1">Thêm các topping cho sản phẩm</p>
            </header>

            <div class="px-6 py-6">
                <div id="toppings-container">
                    <!-- Topping groups will be added here -->
                </div>
                <button type="button" id="add-topping-btn"
                    class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16"
                        fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Thêm topping
                </button>
            </div>
        </section>
        <!-- Save Buttons -->
        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-4 shadow-sm mt-6">
            <button type="button" id="save-draft-btn"
                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Lưu nháp</button>
            <button type="submit" id="save-product-btn"
                class="rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Tạo sản phẩm</button>
        </div>
    </form>
</main>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image Upload Logic (Primary and Additional)
        const primaryImageUpload = document.getElementById('primary-image-upload');
        const selectPrimaryImageBtn = document.getElementById('select-primary-image-btn');
        const imagePlaceholder = document.getElementById('image-placeholder');
        const mainImagePreviewDiv = document.getElementById('main-image-preview');
        const mainImagePreviewImg = mainImagePreviewDiv.querySelector('img');
        const uploadContent = document.getElementById('upload-content');

        const additionalImagesUpload = document.getElementById('additional-images-upload');
        const selectAdditionalImagesBtn = document.getElementById('select-additional-images-btn');
        const imageGallery = document.getElementById('image-gallery');
        let additionalImageFiles = []; 

        // Function to handle primary image selection
        selectPrimaryImageBtn.addEventListener('click', () => primaryImageUpload.click());

        primaryImageUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainImagePreviewImg.src = e.target.result;
                    mainImagePreviewDiv.classList.remove('hidden');
                    uploadContent.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop for primary image
        imagePlaceholder.addEventListener('dragover', (event) => {
            event.preventDefault();
            imagePlaceholder.classList.add('border-blue-500', 'bg-blue-50');
        });

        imagePlaceholder.addEventListener('dragleave', () => {
            imagePlaceholder.classList.remove('border-blue-500', 'bg-blue-50');
        });

        imagePlaceholder.addEventListener('drop', (event) => {
            event.preventDefault();
            imagePlaceholder.classList.remove('border-blue-500', 'bg-blue-50');
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                primaryImageUpload.files = event.dataTransfer.files; // Assign to file input
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainImagePreviewImg.src = e.target.result;
                    mainImagePreviewDiv.classList.remove('hidden');
                    uploadContent.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Function to handle additional images selection
        selectAdditionalImagesBtn.addEventListener('click', () => additionalImagesUpload.click());

        additionalImagesUpload.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    additionalImageFiles.push(file);
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        addImageToGallery(e.target.result, file);
                    }
                    reader.readAsDataURL(file);
                }
            });
            // Clear the input value to allow selecting the same file again if removed
            additionalImagesUpload.value = '';
        });

        function addImageToGallery(src, file) {
            const imageWrapper = document.createElement('div');
            imageWrapper.classList.add('relative', 'group', 'border', 'border-gray-200', 'rounded-md',
                'overflow-hidden');

            const img = document.createElement('img');
            img.src = src;
            img.alt = 'Additional image';
            img.classList.add('w-full', 'h-32', 'object-cover');

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '×';
            removeBtn.classList.add(
                'absolute', 'top-1', 'right-1', 'bg-red-500', 'text-white',
                'rounded-full', 'w-6', 'h-6', 'flex', 'items-center', 'justify-center',
                'opacity-0', 'group-hover:opacity-100', 'transition-opacity', 'text-lg'
            );
            removeBtn.onclick = () => {
                imageWrapper.remove();
                additionalImageFiles = additionalImageFiles.filter(f => f !== file);
                updateAdditionalImagesInput();
            };

            imageWrapper.appendChild(img);
            imageWrapper.appendChild(removeBtn);
            imageGallery.appendChild(imageWrapper);
            updateAdditionalImagesInput();
        }

        function updateAdditionalImagesInput() {
            const dataTransfer = new DataTransfer();
            additionalImageFiles.forEach(file => dataTransfer.items.add(file));
            additionalImagesUpload.files = dataTransfer.files;
        }

        // Attributes and Variant Values Logic
        const attributesContainer = document.getElementById('attributes-container');
        const addAttributeBtn = document.getElementById('add-attribute-btn');
        let attributeCount = 1; // Start from 1 since we have a default attribute

        function createAttributeGroup(index) {
            const group = document.createElement('div');
            group.classList.add('p-4', 'border', 'border-gray-200', 'rounded-md', 'mb-4', 'bg-gray-50');
            group.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-md font-semibold text-gray-800">Thuộc tính ${index + 1}</h3>
                    <button type="button" class="remove-attribute-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa thuộc tính</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="attribute_name_${index}" class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                        <input type="text" id="attribute_name_${index}" name="attributes[${index}][name]" placeholder="VD: Kích thước"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_name" style="display: none;"></div>
                    </div>
                    <div class="col-span-2">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Giá trị thuộc tính</h4>
                        <div id="attribute_values_container_${index}" class="space-y-3">
                            <!-- Default attribute value -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-dashed border-gray-300 rounded-md">
                                <div class="md:col-span-2">
                                    <label for="attribute_value_${index}_0" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                    <input type="text" id="attribute_value_${index}_0" name="attributes[${index}][values][0][value]" placeholder="VD: Nhỏ"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_0_value" style="display: none;"></div>
                                </div>
                                <div>
                                    <label for="attribute_price_${index}_0" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                    <input type="number" id="attribute_price_${index}_0" name="attributes[${index}][values][0][price]" placeholder="0" step="any"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_0_price" style="display: none;"></div>
                                </div>
                                <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end md:col-start-3">Xóa</button>
                            </div>
                        </div>
                        <button type="button" class="add-attribute-value-btn mt-2 text-sm text-blue-600 hover:text-blue-800" data-index="${index}">+ Thêm giá trị</button>
                    </div>
                </div>
            `;

            // Add event listeners for the new attribute group
            const removeAttributeBtn = group.querySelector('.remove-attribute-btn');
            removeAttributeBtn.addEventListener('click', () => {
                group.remove();
                updateAttributeNumbers();
            });

            // Add event listener for adding attribute values
            const addValueBtn = group.querySelector('.add-attribute-value-btn');
            addValueBtn.addEventListener('click', () => {
                addAttributeValue(index);
            });

            // Add event listeners for removing attribute values
            const removeValueBtns = group.querySelectorAll('.remove-attribute-value-btn');
            removeValueBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const valueContainer = e.target.closest('.grid');
                    valueContainer.remove();
                });
            });

            return group;
        }

        function addAttributeValue(attributeIndex) {
            const valuesContainer = document.getElementById(`attribute_values_container_${attributeIndex}`);
            const existingValues = valuesContainer.querySelectorAll('.grid');
            const valueIndex = existingValues.length;

            const valueDiv = document.createElement('div');
            valueDiv.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-3', 'p-3', 'border', 'border-dashed', 'border-gray-300', 'rounded-md');
            valueDiv.innerHTML = `
                <div class="md:col-span-2">
                    <label for="attribute_value_${attributeIndex}_${valueIndex}" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                    <input type="text" id="attribute_value_${attributeIndex}_${valueIndex}" name="attributes[${attributeIndex}][values][${valueIndex}][value]" placeholder="VD: Nhỏ"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${attributeIndex}_values_${valueIndex}_value" style="display: none;"></div>
                </div>
                <div>
                    <label for="attribute_price_${attributeIndex}_${valueIndex}" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                    <input type="number" id="attribute_price_${attributeIndex}_${valueIndex}" name="attributes[${attributeIndex}][values][${valueIndex}][price]" placeholder="0" step="any"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${attributeIndex}_values_${valueIndex}_price" style="display: none;"></div>
                </div>
                <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end md:col-start-3">Xóa</button>
            `;

            // Add event listener for removing this value
            const removeBtn = valueDiv.querySelector('.remove-attribute-value-btn');
            removeBtn.addEventListener('click', () => {
                valueDiv.remove();
            });

            valuesContainer.appendChild(valueDiv);
        }

        function updateAttributeNumbers() {
            const attributeGroups = attributesContainer.querySelectorAll('.p-4.border.border-gray-200');
            attributeGroups.forEach((group, index) => {
                const title = group.querySelector('h3');
                if (title) {
                    title.textContent = `Thuộc tính ${index + 1}`;
                }
            });
        }

        // Add event listener for the main "Add Attribute" button
        addAttributeBtn.addEventListener('click', () => {
            const attributeGroup = createAttributeGroup(attributeCount);
            attributesContainer.appendChild(attributeGroup);
            attributeCount++;
        });

        // Add event listeners for existing attribute elements
        // Add listeners for existing remove attribute button
        const existingRemoveBtn = document.querySelector('.remove-attribute-btn');
        if (existingRemoveBtn) {
            existingRemoveBtn.addEventListener('click', () => {
                const attributeGroup = existingRemoveBtn.closest('.p-4.border.border-gray-200');
                if (attributeGroup) {
                    attributeGroup.remove();
                    updateAttributeNumbers();
                }
            });
        }

        // Add listeners for existing add value button
        const existingAddValueBtn = document.querySelector('.add-attribute-value-btn');
        if (existingAddValueBtn) {
            existingAddValueBtn.addEventListener('click', () => {
                const attributeIndex = existingAddValueBtn.getAttribute('data-index') || 0;
                addAttributeValue(parseInt(attributeIndex));
            });
        }

        // Add listeners for existing remove value buttons
        const existingRemoveValueBtns = document.querySelectorAll('.remove-attribute-value-btn');
        existingRemoveValueBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const valueContainer = e.target.closest('.grid');
                if (valueContainer) {
                    valueContainer.remove();
                }
            });
        });

        // Toppings Logic
        const toppingsContainer = document.getElementById('toppings-container');
        const addToppingBtn = document.getElementById('add-topping-btn');
        let toppingCount = 0;

        function createToppingGroup(index) {
            const group = document.createElement('div');
            group.classList.add('p-4', 'border', 'border-gray-200', 'rounded-md', 'mb-4', 'bg-gray-50');
            group.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-md font-semibold text-gray-800">Topping ${index + 1}</h3>
                    <button type="button" class="remove-topping-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa topping</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <div>
                        <label for="topping_name_${index}" class="block text-sm font-medium text-gray-700">Tên topping</label>
                        <input type="text" id="topping_name_${index}" name="toppings[${index}][name]" placeholder="VD: Trân châu đen"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_toppings_${index}_name" style="display: none;"></div>
                    </div>
                    <div>
                        <label for="topping_price_${index}" class="block text-sm font-medium text-gray-700">Giá</label>
                        <input type="number" id="topping_price_${index}" name="toppings[${index}][price]" min="0" placeholder="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_toppings_${index}_price" style="display: none;"></div>
                    </div>
                    <div class="flex items-center mt-7">
                        <input type="checkbox" id="topping_available_${index}" name="toppings[${index}][available]" value="1" checked
                            class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <label for="topping_available_${index}" class="ml-2 text-sm text-gray-700">Có sẵn</label>
                    </div>
                    <div>
                        <label for="topping_image_${index}" class="block text-sm font-medium text-gray-700">Ảnh topping</label>
                        <input type="file" id="topping_image_${index}" name="toppings[${index}][image]" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_toppings_${index}_image" style="display: none;"></div>
                        <div id="topping_image_preview_container_${index}" class="mt-2 w-24 h-24 border border-gray-300 rounded-md overflow-hidden flex items-center justify-center bg-gray-100">
                            <img id="topping_image_preview_${index}" src="#" alt="Preview" class="hidden w-full h-full object-cover">
                            <span id="topping_image_placeholder_${index}" class="text-xs text-gray-400">Xem trước</span>
                        </div>
                    </div>
                </div>
            `;
            group.querySelector('.remove-topping-btn').addEventListener('click', () => group.remove());

            const imageInput = group.querySelector(`#topping_image_${index}`);
            const imagePreview = group.querySelector(`#topping_image_preview_${index}`);
            const imagePlaceholder = group.querySelector(`#topping_image_placeholder_${index}`);

            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        imagePlaceholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '#';
                    imagePreview.classList.add('hidden');
                    imagePlaceholder.classList.remove('hidden');
                }
            });

            return group;
        }

        addToppingBtn.addEventListener('click', () => {
            const toppingGroup = createToppingGroup(toppingCount);
            toppingsContainer.appendChild(toppingGroup);
            toppingCount++;
        });

        // Restore old topping data if validation fails
        @if (old('toppings'))
            const oldToppings = @json(old('toppings'));
            if (Array.isArray(oldToppings)) {
                oldToppings.forEach((topping, index) => {
                    const toppingGroup = createToppingGroup(index);
                    toppingsContainer.appendChild(toppingGroup);
                    const nameInput = toppingGroup.querySelector(
                        `input[name="toppings[${index}][name]"]`);
                    const priceInput = toppingGroup.querySelector(
                        `input[name="toppings[${index}][price]"]`);
                    const availableInput = toppingGroup.querySelector(
                        `input[name="toppings[${index}][available]"]`);
                    if (nameInput) nameInput.value = topping.name || '';
                    if (priceInput) priceInput.value = topping.price || '';
                    if (availableInput) availableInput.checked = topping.available === '1' || topping
                        .available === true || topping.available === 1;
                });
                toppingCount = oldToppings.length;
            }
        @endif

        // Restore old attribute data if validation fails
        @if (old('attributes'))
            const oldAttributes = @json(old('attributes'));
            if (Array.isArray(oldAttributes)) {
                oldAttributes.forEach((attribute, index) => {
                    let attributeGroup;
                    
                    // Use existing default attribute group for index 0, create new ones for others
                    if (index === 0) {
                        attributeGroup = attributesContainer.querySelector('.p-4.border.border-gray-200');
                    } else {
                        attributeGroup = createAttributeGroup(index);
                        attributesContainer.appendChild(attributeGroup);
                    }
                    
                    if (attributeGroup) {
                        // Set attribute name
                        const nameInput = attributeGroup.querySelector(`input[name="attributes[${index}][name]"]`);
                        if (nameInput) nameInput.value = attribute.name || '';
                        
                        // Set attribute values
                        if (attribute.values && Array.isArray(attribute.values)) {
                            const valuesContainer = attributeGroup.querySelector(`#attribute_values_container_${index}`);
                            // Clear default value first
                            valuesContainer.innerHTML = '';
                            
                            attribute.values.forEach((valueData, valueIndex) => {
                                const valueDiv = document.createElement('div');
                                valueDiv.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-3', 'p-3', 'border', 'border-dashed', 'border-gray-300', 'rounded-md');
                                valueDiv.innerHTML = `
                                    <div class="md:col-span-2">
                                        <label for="attribute_value_${index}_${valueIndex}" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                        <input type="text" id="attribute_value_${index}_${valueIndex}" name="attributes[${index}][values][${valueIndex}][value]" placeholder="VD: Nhỏ"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="${valueData.value || ''}">
                                        <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_${valueIndex}_value" style="display: none;"></div>
                                    </div>
                                    <div>
                                        <label for="attribute_price_${index}_${valueIndex}" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                        <input type="number" id="attribute_price_${index}_${valueIndex}" name="attributes[${index}][values][${valueIndex}][price]" placeholder="0" step="any"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="${valueData.price || ''}">
                                        <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_${valueIndex}_price" style="display: none;"></div>
                                    </div>
                                    <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end md:col-start-3">Xóa</button>
                                `;
                                
                                // Add event listener for removing this value
                                const removeBtn = valueDiv.querySelector('.remove-attribute-value-btn');
                                removeBtn.addEventListener('click', () => {
                                    valueDiv.remove();
                                });
                                
                                valuesContainer.appendChild(valueDiv);
                            });
                        }
                    }
                });
                attributeCount = Math.max(oldAttributes.length, 1); // Ensure at least 1
            }
        @endif

        // Function to display validation errors for dynamic inputs
        function displayValidationErrors() {
            @if ($errors->any())
                const errors = @json($errors->messages());
                
                // Display errors for attributes
                Object.keys(errors).forEach(key => {
                    if (key.startsWith('attributes.')) {
                        const errorElementId = 'error_' + key.replace(/\./g, '_');
                        const errorElement = document.getElementById(errorElementId);
                        if (errorElement) {
                            errorElement.textContent = errors[key][0];
                            errorElement.style.display = 'block';
                        }
                    }
                    
                    // Display errors for toppings
                    if (key.startsWith('toppings.')) {
                        const errorElementId = 'error_' + key.replace(/\./g, '_');
                        const errorElement = document.getElementById(errorElementId);
                        if (errorElement) {
                            errorElement.textContent = errors[key][0];
                            errorElement.style.display = 'block';
                        }
                    }
                });
            @endif
        }

        // Call displayValidationErrors after restoring old data
        setTimeout(displayValidationErrors, 100);

        // Form Submission Logic
        const addProductForm = document.getElementById('add-product-form');
        addProductForm.addEventListener('submit', function(e) {
            // Convert ingredients textarea to JSON array
            const ingredientsText = document.getElementById('ingredients').value;
            const ingredientsArray = ingredientsText.split('\n').filter(item => item.trim());
            const ingredientsInput = document.createElement('input');
            ingredientsInput.type = 'hidden';
            ingredientsInput.name = 'ingredients_json';
            ingredientsInput.value = JSON.stringify(ingredientsArray);
            addProductForm.appendChild(ingredientsInput);

            // Ensure description is always sent (even if empty)
            const description = document.getElementById('description');
            if (!description.value) description.value = '';
        });

        // Handle status and release date visibility
        const statusInputs = document.querySelectorAll('input[name="status"]');
        const releaseAtContainer = document.getElementById('release_at_container');

        function toggleReleaseDate() {
            const selectedStatus = document.querySelector('input[name="status"]:checked').value;
            releaseAtContainer.classList.toggle('hidden', selectedStatus !== 'coming_soon');
        }

        statusInputs.forEach(input => {
            input.addEventListener('change', toggleReleaseDate);
        });

        // Initial check
        toggleReleaseDate();
    });
</script>
@endsection
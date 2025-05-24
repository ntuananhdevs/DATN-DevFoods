@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<style>
  /* Tăng kích thước cho input */
  input[type="text"],
  input[type="number"],
  input[type="date"],
  select {
    padding: 0.625rem 0.75rem;
    height: 2.75rem;
  }
  
  textarea {
    padding: 0.625rem 0.75rem;
    min-height: 6rem;
  }
  
  /* CSS cho khu vực tải lên hình ảnh */
  #image-placeholder {
    transition: all 0.2s ease;
    border: 2px dashed #d1d5db;
  }
  
  #image-placeholder:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
  }
  
  /* CSS cho gallery hình ảnh */
  #image-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 0.75rem;
    margin-top: 1rem;
  }
  
  .image-item {
    position: relative;
    overflow: hidden;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    padding-bottom: 100%;
  }
  
  .image-item img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .image-remove-btn {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    background-color: rgba(239, 68, 68, 0.9);
    color: white;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.2s ease;
  }
  
  .image-remove-btn:hover {
    background-color: rgba(220, 38, 38, 1);
  }

  /* CSS cho attributes và variants */
  .attribute-group {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
  }

  .variant-value-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 1rem;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .remove-value-btn {
    color: #ef4444;
    cursor: pointer;
  }

  .remove-value-btn:hover {
    color: #dc2626;
  }
</style>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-1">Chỉnh Sửa Sản Phẩm</h1>
    <p class="text-gray-500 mb-8">Cập nhật thông tin sản phẩm</p>

    <form id="edit-product-form" class="space-y-8" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Thông tin cơ bản</h2>
                    <p class="text-gray-500 text-sm mt-1">Cập nhật thông tin cơ bản của sản phẩm</p>
                </div>
            </header>

            <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-5 md:col-span-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Tên sản phẩm <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Nhập tên sản phẩm" value="{{ old('name', $product->name) }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục <span class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">Giá cơ bản <span class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <input type="number" id="base_price" name="base_price" min="0" step="0.01" required placeholder="0" value="{{ old('base_price', $product->base_price) }}" class="block w-full pl-7 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-700">Thời gian chuẩn bị (phút)</label>
                            <input type="number" id="preparation_time" name="preparation_time" min="0" placeholder="Nhập thời gian chuẩn bị" value="{{ old('preparation_time', $product->preparation_time) }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                    </div>

                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700">Mô tả ngắn</label>
                        <textarea id="short_description" name="short_description" rows="2" placeholder="Nhập mô tả ngắn về sản phẩm" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
                        <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div>
                        <label for="ingredients" class="block text-sm font-medium text-gray-700">Nguyên liệu</label>
                        <textarea id="ingredients" name="ingredients" rows="3" placeholder="Nhập danh sách nguyên liệu (mỗi nguyên liệu một dòng)" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('ingredients', is_array($product->ingredients) ? implode("\n", $product->ingredients) : $product->ingredients) }}</textarea>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700">Tùy chọn</span>
                        <div class="flex gap-4 mt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="form-checkbox text-blue-600" />
                                <span>Sản phẩm nổi bật</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="available" value="1" {{ old('available', $product->available) ? 'checked' : '' }} class="form-checkbox text-blue-600" />
                                <span>Đang bán</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }} class="form-checkbox text-blue-600" />
                                <span>Trạng thái hoạt động</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh sản phẩm</label>
                        <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                            <div id="image-placeholder" class="w-full h-80 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-gray-400 mb-3" width="48" height="48" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                <p class="text-base text-gray-600 mb-2">Kéo thả hình ảnh vào đây</p>
                                <p class="text-sm text-gray-500 mb-4">hoặc</p>
                                <button type="button" id="select-image-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Chọn hình ảnh</button>
                                <p class="text-xs text-gray-500 mt-3">Hỗ trợ: JPG, PNG, GIF (Tối đa 5MB)</p>
                                <input type="file" id="file-upload" name="images[]" accept="image/*" multiple class="hidden" />
                            </div>
                        </div>
                        <div id="image-gallery" class="mt-3">
                            @foreach($product->images as $image)
                            <div class="image-item">
                                <img src="{{ asset('storage/' . $image->img) }}" alt="Product image" />
                                <button type="button" class="image-remove-btn" data-image-id="{{ $image->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Attributes and Variant Values -->
        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Thuộc tính và Giá trị biến thể</h2>
                <p class="text-gray-500 text-sm mt-1">Cập nhật các thuộc tính và giá trị biến thể cho sản phẩm</p>
            </header>

            <div class="px-6 py-6">
                <div id="attributes-container">
                    <!-- Attribute groups will be added here -->
                </div>
                <button type="button" id="add-attribute-btn" class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Thêm thuộc tính
                </button>
            </div>
        </section>

        <!-- Branch Stock -->
        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Tồn kho chi nhánh</h2>
                <p class="text-gray-500 text-sm mt-1">Cập nhật số lượng tồn kho tại các chi nhánh</p>
            </header>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($branches as $branch)
                    <div class="border rounded-md p-4">
                        <h3 class="font-medium text-gray-900 mb-2">{{ $branch->name }}</h3>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm text-gray-700">Số lượng tồn kho</label>
                                @php
                                    $stock = $product->variants->first()->branchStocks->where('branch_id', $branch->id)->first();
                                    $quantity = $stock ? $stock->stock_quantity : 0;
                                @endphp
                                <input type="number" name="branch_stocks[{{ $branch->id }}]" min="0" value="{{ old("branch_stocks.{$branch->id}", $quantity) }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Save Buttons -->
        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-4 shadow-sm mt-6">
            <a href="{{ route('admin.products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Hủy</a>
            <button type="submit" id="save-product-btn" class="rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Cập nhật sản phẩm</button>
        </div>
    </form>
</main>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image upload handling
        const imagePlaceholder = document.getElementById('image-placeholder');
        const fileUpload = document.getElementById('file-upload');
        const selectImageBtn = document.getElementById('select-image-btn');
        const imageGallery = document.getElementById('image-gallery');
        let uploadedImages = [];

        // Handle drag and drop
        imagePlaceholder.addEventListener('dragover', (e) => {
            e.preventDefault();
            imagePlaceholder.classList.add('border-blue-500');
        });

        imagePlaceholder.addEventListener('dragleave', () => {
            imagePlaceholder.classList.remove('border-blue-500');
        });

        imagePlaceholder.addEventListener('drop', (e) => {
            e.preventDefault();
            imagePlaceholder.classList.remove('border-blue-500');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        // Handle file selection
        selectImageBtn.addEventListener('click', () => {
            fileUpload.click();
        });

        fileUpload.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        uploadedImages.push({
                            file: file,
                            preview: e.target.result
                        });
                        updateImageGallery();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function updateImageGallery() {
            const existingImages = Array.from(imageGallery.querySelectorAll('.image-item')).map(item => item.outerHTML);
            const newImages = uploadedImages.map((image, index) => `
                <div class="image-item">
                    <img src="${image.preview}" alt="Preview" />
                    <button type="button" class="image-remove-btn" onclick="removeImage(${index})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            `).join('');
            imageGallery.innerHTML = existingImages.join('') + newImages;
        }

        window.removeImage = function(index) {
            uploadedImages.splice(index, 1);
            updateImageGallery();
        };

        // Attributes and Variant Values handling
        const attributesContainer = document.getElementById('attributes-container');
        const addAttributeBtn = document.getElementById('add-attribute-btn');
        let attributeCount = 0;

        function createAttributeGroup(index) {
            const attributeGroup = document.createElement('div');
            attributeGroup.className = 'attribute-group';
            attributeGroup.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                        <input type="text" name="attributes[${index}][name]" required placeholder="Ví dụ: Size, Màu sắc" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.attribute-group').remove()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="variant-values-container">
                    <div class="variant-value-row">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Giá trị</label>
                            <input type="text" name="attributes[${index}][values][0][value]" required placeholder="Ví dụ: S, M, L" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Giá điều chỉnh</label>
                            <input type="number" name="attributes[${index}][values][0][price_adjustment]" step="0.01" value="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.variant-value-row').remove()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" class="mt-2 text-blue-600 hover:text-blue-800" onclick="addVariantValue(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Thêm giá trị
                </button>
            `;
            return attributeGroup;
        }

        addAttributeBtn.addEventListener('click', () => {
            const attributeGroup = createAttributeGroup(attributeCount);
            attributesContainer.appendChild(attributeGroup);
            attributeCount++;
        });

        window.addVariantValue = function(button) {
            const container = button.previousElementSibling;
            const attributeIndex = container.closest('.attribute-group').querySelector('input[name^="attributes["]').name.match(/\[(\d+)\]/)[1];
            const valueCount = container.children.length;
            
            const valueRow = document.createElement('div');
            valueRow.className = 'variant-value-row';
            valueRow.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá trị</label>
                    <input type="text" name="attributes[${attributeIndex}][values][${valueCount}][value]" required placeholder="Ví dụ: S, M, L" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá điều chỉnh</label>
                    <input type="number" name="attributes[${attributeIndex}][values][${valueCount}][price_adjustment]" step="0.01" value="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                </div>
                <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.variant-value-row').remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            container.appendChild(valueRow);
        };

        // Restore old attributes and values if they exist
        @if(old('attributes'))
            const oldAttributes = @json(old('attributes'));
            oldAttributes.forEach((attribute, index) => {
                const attributeGroup = createAttributeGroup(index);
                attributesContainer.appendChild(attributeGroup);
                const nameInput = attributeGroup.querySelector(`input[name="attributes[${index}][name]"]`);
                nameInput.value = attribute.name;

                attribute.values.forEach((value, valueIndex) => {
                    if (valueIndex > 0) {
                        addVariantValue(attributeGroup.querySelector('button'));
                    }
                    const valueInput = attributeGroup.querySelector(`input[name="attributes[${index}][values][${valueIndex}][value]"]`);
                    const priceInput = attributeGroup.querySelector(`input[name="attributes[${index}][values][${valueIndex}][price_adjustment]"]`);
                    valueInput.value = value.value;
                    priceInput.value = value.price_adjustment;
                });
            });
            attributeCount = oldAttributes.length;
        @else
            // Load existing attributes
            @if($product->attributes)
                @foreach($product->attributes as $index => $attribute)
                    const attributeGroup = createAttributeGroup({{ $index }});
                    attributesContainer.appendChild(attributeGroup);
                    const nameInput = attributeGroup.querySelector(`input[name="attributes[{{ $index }}][name]"]`);
                    nameInput.value = "{{ $attribute->name }}";

                    @foreach($attribute->values as $valueIndex => $value)
                        @if($valueIndex > 0)
                            addVariantValue(attributeGroup.querySelector('button'));
                        @endif
                        const valueInput = attributeGroup.querySelector(`input[name="attributes[{{ $index }}][values][{{ $valueIndex }}][value]"]`);
                        const priceInput = attributeGroup.querySelector(`input[name="attributes[{{ $index }}][values][{{ $valueIndex }}][price_adjustment]"]`);
                        valueInput.value = "{{ $value->value }}";
                        priceInput.value = {{ $value->price_adjustment }};
                    @endforeach
                @endforeach
                attributeCount = {{ count($product->attributes) }};
            @else
                // Add default attribute if no attributes exist
                const defaultAttribute = createAttributeGroup(0);
                attributesContainer.appendChild(defaultAttribute);
                attributeCount = 1;
            @endif
        @endif

        // Form submission
        const form = document.getElementById('edit-product-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Convert ingredients textarea to JSON array
            const ingredientsText = document.getElementById('ingredients').value;
            const ingredientsArray = ingredientsText.split('\n').filter(item => item.trim());
            const ingredientsInput = document.createElement('input');
            ingredientsInput.type = 'hidden';
            ingredientsInput.name = 'ingredients_json';
            ingredientsInput.value = JSON.stringify(ingredientsArray);
            form.appendChild(ingredientsInput);

            form.submit();
        });
    });
</script>
@endsection 
@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<style>
  /* Tăng kích thước cho input */
  input[type="text"],
  input[type="number"],
  input[type="date"],
  input[type="datetime-local"],
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
    z-index: 1;
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

<main class="container">
    <h1 class="text-3xl font-extrabold mb-1">Thêm Sản Phẩm Mới</h1>
    <p class="text-gray-500 mb-8">Nhập thông tin chi tiết để tạo sản phẩm mới</p>

    <form id="add-product-form" class="space-y-8" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

      <!-- Basic Information -->
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
              <label for="name" class="block text-sm font-medium text-gray-700">Tên sản phẩm <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Nhập tên sản phẩm" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
            </div>

            <div class="grid grid-cols-2 gap-4">
            <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục <span class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                </select>
              </div>
              <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">Giá cơ bản <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                                <input type="number" id="base_price" name="base_price" min="0" step="0.01" required placeholder="0" value="{{ old('base_price') }}" class="block w-full pl-7 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-700">Thời gian chuẩn bị (phút)</label>
                            <input type="number" id="preparation_time" name="preparation_time" min="0" placeholder="Nhập thời gian chuẩn bị" value="{{ old('preparation_time') }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              </div>
            </div>

            <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700">Mô tả ngắn</label>
                        <textarea id="short_description" name="short_description" rows="2" placeholder="Nhập mô tả ngắn về sản phẩm" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('short_description') }}</textarea>
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
                        <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="ingredients" class="block text-sm font-medium text-gray-700">Nguyên liệu</label>
                        <textarea id="ingredients" name="ingredients" rows="3" placeholder="Nhập danh sách nguyên liệu (mỗi nguyên liệu một dòng)" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('ingredients') }}</textarea>
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700">Tùy chọn</span>
              <div class="space-y-4 mt-2">
                <div class="flex gap-4">
                  <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="form-checkbox text-blue-600" />
                    <span>Sản phẩm nổi bật</span>
                  </label>
                  
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái sản phẩm</label>
                  <div class="flex gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                      <input type="radio" name="status" value="coming_soon" {{ old('status', 'selling') == 'coming_soon' ? 'checked' : '' }} class="form-radio text-blue-600" />
                      <span>Sắp ra mắt</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                      <input type="radio" name="status" value="selling" {{ old('status', 'selling') == 'selling' ? 'checked' : '' }} class="form-radio text-blue-600" />
                      <span>Đang bán</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                      <input type="radio" name="status" value="discontinued" {{ old('status', 'selling') == 'discontinued' ? 'checked' : '' }} class="form-radio text-blue-600" />
                      <span>Ngừng bán</span>
                    </label>
                  </div>
                </div>

                <div>
                  <label for="release_at" class="block text-sm font-medium text-gray-700">Ngày ra mắt</label>
                  <input type="datetime-local" id="release_at" name="release_at" value="{{ old('release_at') }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh sản phẩm <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <!-- Primary Image -->
                <div class="md:col-span-1">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh chính</label>
                  <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                    <div id="image-placeholder" class="w-full h-80 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all relative">
                      <div id="main-image-preview" class="absolute inset-0 w-full h-full hidden">
                        <img src="" alt="Main image preview" class="w-full h-full object-cover" />
                      </div>
                      <div id="upload-content" class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-gray-400 mb-3" width="48" height="48" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                          <polyline points="17 8 12 3 7 8" />
                          <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        <p class="text-base text-gray-600 mb-2">Kéo thả ảnh chính vào đây</p>
                        <p class="text-sm text-gray-500 mb-4">hoặc</p>
                        <button type="button" id="select-primary-image-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Chọn ảnh chính</button>
                        <p class="text-xs text-gray-500 mt-3">Hỗ trợ: JPG, PNG, GIF (Tối đa 5MB)</p>
                      </div>
                      <input type="file" id="primary-image-upload" name="primary_image" accept="image/*" class="hidden" />
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mb-2">
                  <span class="font-semibold text-blue-600">Lưu ý:</span> Ảnh đầu tiên sẽ được sử dụng làm ảnh chính của sản phẩm.
                </p>
                </div>

                <!-- Additional Images -->
                <div class="md:col-span-2">
                  <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Ảnh phụ</label>
                    <button type="button" id="select-additional-images-btn" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                      </svg>
                      Thêm ảnh
                    </button>
                    <input type="file" id="additional-images-upload" name="images[]" accept="image/*" multiple class="hidden" />
                  </div>
                  <div id="image-gallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
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
          <button type="button" id="add-topping-btn" class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Thêm topping
          </button>
        </div>
      </section>
      <!-- Save Buttons -->
      <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-4 shadow-sm mt-6">
        <button type="button" id="save-draft-btn" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Lưu nháp</button>
        <button type="submit" id="save-product-btn" class="rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Tạo sản phẩm</button>
      </div>
    </form>
  </main>

@endsection

@section('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image upload handling
        const imagePlaceholder = document.getElementById('image-placeholder');
        const primaryImageUpload = document.getElementById('primary-image-upload');
        const additionalImagesUpload = document.getElementById('additional-images-upload');
        const selectPrimaryImageBtn = document.getElementById('select-primary-image-btn');
        const selectAdditionalImagesBtn = document.getElementById('select-additional-images-btn');
        const imageGallery = document.getElementById('image-gallery');
        const mainImagePreview = document.getElementById('main-image-preview');
        const uploadContent = document.getElementById('upload-content');
        let uploadedImages = [];

        // Handle primary image
        imagePlaceholder.addEventListener('click', (e) => {
            if (e.target !== selectPrimaryImageBtn) {
                primaryImageUpload.click();
            }
        });

        selectPrimaryImageBtn.addEventListener('click', () => {
            primaryImageUpload.click();
        });

        primaryImageUpload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    mainImagePreview.querySelector('img').src = e.target.result;
                    mainImagePreview.classList.remove('hidden');
                    uploadContent.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle additional images
        selectAdditionalImagesBtn.addEventListener('click', () => {
            additionalImagesUpload.click();
        });

        additionalImagesUpload.addEventListener('change', (e) => {
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
            imageGallery.innerHTML = uploadedImages.map((image, index) => `
                <div class="image-item">
                    <img src="${image.preview}" alt="Preview" class="w-full h-32 object-cover rounded-md" />
                    <button type="button" class="image-remove-btn" onclick="removeImage(${index})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            `).join('');
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
            // Add default attribute if no old attributes exist
            const defaultAttribute = createAttributeGroup(0);
            attributesContainer.appendChild(defaultAttribute);
            attributeCount = 1;
        @endif

        // Toppings handling
        const toppingsContainer = document.getElementById('toppings-container');
        const addToppingBtn = document.getElementById('add-topping-btn');
        let toppingCount = 0;

        function createToppingGroup(index) {
            const toppingGroup = document.createElement('div');
            toppingGroup.className = 'border rounded-md p-4 mb-4';
            toppingGroup.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Tên topping</label>
                        <input type="text" name="toppings[${index}][name]" required placeholder="Ví dụ: Sốt mayonnaise" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    </div>
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Giá (VNĐ)</label>
                        <input type="number" name="toppings[${index}][price]" required min="0" step="1000" placeholder="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.border').remove()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="toppings[${index}][available]" value="1" checked class="form-checkbox text-blue-600" />
                        <span class="text-sm text-gray-700">Đang bán</span>
                    </label>
                </div>
            `;
            return toppingGroup;
        }

        addToppingBtn.addEventListener('click', () => {
            const toppingGroup = createToppingGroup(toppingCount);
            toppingsContainer.appendChild(toppingGroup);
            toppingCount++;
        });

        // Restore old toppings if they exist
        @if(old('toppings'))
            const oldToppings = @json(old('toppings'));
            oldToppings.forEach((topping, index) => {
                const toppingGroup = createToppingGroup(index);
                toppingsContainer.appendChild(toppingGroup);
                const nameInput = toppingGroup.querySelector(`input[name="toppings[${index}][name]"]`);
                const priceInput = toppingGroup.querySelector(`input[name="toppings[${index}][price]"]`);
                const availableInput = toppingGroup.querySelector(`input[name="toppings[${index}][available]"]`);
                
                nameInput.value = topping.name;
                priceInput.value = topping.price;
                availableInput.checked = topping.available;
            });
            toppingCount = oldToppings.length;
        @endif

        // Form submission
        const form = document.getElementById('add-product-form');
        form.addEventListener('submit', function(e) {
            // Không ngăn chặn submit mặc định nữa
            // e.preventDefault();
            // Convert ingredients textarea to JSON array
            const ingredientsText = document.getElementById('ingredients').value;
            const ingredientsArray = ingredientsText.split('\n').filter(item => item.trim());
            const ingredientsInput = document.createElement('input');
            ingredientsInput.type = 'hidden';
            ingredientsInput.name = 'ingredients_json';
            ingredientsInput.value = JSON.stringify(ingredientsArray);
            form.appendChild(ingredientsInput);
            // Đảm bảo description luôn gửi lên (kể cả rỗng)
            const description = document.getElementById('description');
            if (!description.value) description.value = '';
            // Không gọi form.submit() ở đây nữa vì đã để mặc định
        });

        // Handle status and release date visibility
        const statusInputs = document.querySelectorAll('input[name="status"]');
        const releaseAtDiv = document.querySelector('label[for="release_at"]').parentElement;

        function toggleReleaseDate() {
            const selectedStatus = document.querySelector('input[name="status"]:checked').value;
            if (selectedStatus === 'coming_soon') {
                releaseAtDiv.classList.remove('hidden');
                releaseAtDiv.querySelector('#release_at').required = true;
            } else {
                releaseAtDiv.classList.add('hidden');
                releaseAtDiv.querySelector('#release_at').required = false;
            }
        }

        statusInputs.forEach(input => {
            input.addEventListener('change', toggleReleaseDate);
        });

        // Initial check
        toggleReleaseDate();

        // Variant stock management
        const bulkStockInput = document.getElementById('bulk-stock-input');
        const applyAllStockBtn = document.getElementById('apply-all-stock');
        const variantStocksTable = document.getElementById('variant-stocks-table');
        let variants = [];

        // Function to generate variant combinations
        function generateCombinations(attributes) {
            if (attributes.length === 0) return [];
            
            const result = [];
            const firstAttr = attributes[0];
            
            if (attributes.length === 1) {
                return firstAttr.values.map(value => [{
                    name: firstAttr.name,
                    value: value
                }]);
            }
            
            const restCombinations = generateCombinations(attributes.slice(1));
            
            firstAttr.values.forEach(value => {
                restCombinations.forEach(combination => {
                    result.push([{
                        name: firstAttr.name,
                        value: value
                    }, ...combination]);
                });
            });
            
            return result;
        }

        // Function to update the stock table
        function updateStockTable() {
            const attributeGroups = document.querySelectorAll('.attribute-group');
            const attributeValues = Array.from(attributeGroups).map(group => {
                const name = group.querySelector('input[name$="[name]"]').value;
                const values = Array.from(group.querySelectorAll('input[name$="[value]"]')).map(input => input.value);
                return { name, values };
            });

            variants = generateCombinations(attributeValues);
            
            // Generate table rows
            variantStocksTable.innerHTML = variants.map((variant, variantIndex) => {
                const variantName = variant.map(v => `${v.name}: ${v.value}`).join(' - ');
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${variantName}
                        </td>
                        @foreach($branches as $branch)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <input type="number" 
                                   name="variant_stocks[${variantIndex}][{{ $branch->id }}]" 
                                   min="0" 
                                   value="0" 
                                   class="block w-24 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </td>
                        @endforeach
                    </tr>
                `;
            }).join('');
        }

        // Update table when attributes change
        document.querySelectorAll('.attribute-group input').forEach(input => {
            input.addEventListener('change', updateStockTable);
        });

        // Handle apply all stock
        applyAllStockBtn.addEventListener('click', () => {
            const stockValue = parseInt(bulkStockInput.value);
            if (isNaN(stockValue) || stockValue < 0) {
                alert('Vui lòng nhập số lượng tồn kho hợp lệ');
                return;
            }

            // Update all stock inputs
            document.querySelectorAll('#variant-stocks-table input[type="number"]').forEach(input => {
                input.value = stockValue;
            });
        });

        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Enter to apply stock to all
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                applyAllStockBtn.click();
            }
        });

        // Initial table update
        updateStockTable();
    });
  </script>
@endsection

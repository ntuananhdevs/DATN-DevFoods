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

  /* CSS cho preview hình topping */
  .topping-image-preview {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    margin-top: 0.5rem;
    display: block;
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
              <input type="text" id="name" name="name" placeholder="Nhập tên sản phẩm" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              <div class="error-message text-red-500 text-xs mt-1" id="name-error"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
            <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục <span class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                </select>
                <div class="error-message text-red-500 text-xs mt-1" id="category_id-error"></div>
              </div>
              <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">Giá cơ bản <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                                <input type="number" id="base_price" name="base_price" min="0" step="0.01" placeholder="0" value="{{ old('base_price') }}" class="block w-full pl-7 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            </div>
                <div class="error-message text-red-500 text-xs mt-1" id="base_price-error"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-700">Thời gian chuẩn bị (phút)</label>
                            <input type="number" id="preparation_time" name="preparation_time" min="0" placeholder="Nhập thời gian chuẩn bị" value="{{ old('preparation_time') }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            <div class="error-message text-red-500 text-xs mt-1" id="preparation_time-error"></div>
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
                        <div class="error-message text-red-500 text-xs mt-1" id="ingredients-error"></div>
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
                <div class="error-message text-red-500 text-xs mt-2 mb-2" id="attributes-error"></div>
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
        // Validation functions
        const validateField = (fieldId, errorMessage) => {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(`${fieldId}-error`);
            
            if (!field || !errorElement) return true; // Skip if elements don't exist
            
            const isValid = field.value.trim() !== '';
            
            if (!isValid) {
                errorElement.textContent = errorMessage;
                field.classList.add('border-red-500');
            } else {
                errorElement.textContent = '';
                field.classList.remove('border-red-500');
            }
            
            return isValid;
        };
        
        const validateRequired = () => {
            let isValid = true;
            
            // Validate product name
            if (!validateField('name', 'Tên sản phẩm không được bỏ trống')) {
                isValid = false;
            }
            
            // Validate category
            if (!validateField('category_id', 'Vui lòng chọn danh mục')) {
                isValid = false;
            }
            
            // Validate price
            if (!validateField('base_price', 'Giá cơ bản không được bỏ trống')) {
                isValid = false;
            }
            
            // Always validate preparation time - show error if not provided or invalid
            const preparationTime = document.getElementById('preparation_time');
            const preparationTimeError = document.getElementById('preparation_time-error');
            if (!preparationTime || preparationTime.value.trim() === '') {
                preparationTimeError.textContent = 'Thời gian chuẩn bị không được bỏ trống';
                preparationTime.classList.add('border-red-500');
                isValid = false;
            } else if (isNaN(preparationTime.value) || parseInt(preparationTime.value) < 0) {
                preparationTimeError.textContent = 'Thời gian chuẩn bị phải là số dương';
                preparationTime.classList.add('border-red-500');
                isValid = false;
            } else {
                preparationTimeError.textContent = '';
                preparationTime.classList.remove('border-red-500');
            }
            
            // Always validate ingredients - show error if not provided or invalid
            const ingredients = document.getElementById('ingredients');
            const ingredientsError = document.getElementById('ingredients-error');
            if (!ingredients || ingredients.value.trim() === '') {
                ingredientsError.textContent = 'Nguyên liệu không được bỏ trống';
                ingredients.classList.add('border-red-500');
                isValid = false;
            } else {
                const lines = ingredients.value.trim().split('\n');
                if (lines.some(line => line.trim() === '')) {
                    ingredientsError.textContent = 'Mỗi dòng nên chứa một nguyên liệu';
                    ingredients.classList.add('border-red-500');
                    isValid = false;
                } else {
                    ingredientsError.textContent = '';
                    ingredients.classList.remove('border-red-500');
                }
            }
            
            // Validate attributes - at least one attribute with name and value
            const attributeGroups = document.querySelectorAll('.attribute-group');
            const attributesError = document.getElementById('attributes-error');
            
            if (attributeGroups.length === 0) {
                attributesError.textContent = 'Sản phẩm cần có ít nhất một thuộc tính';
                isValid = false;
            } else {
                let hasValidAttribute = false;
                
                for (const group of attributeGroups) {
                    const nameInput = group.querySelector('input[name$="[name]"]');
                    const valueInputs = group.querySelectorAll('input[name$="[value]"]');
                    
                    if (nameInput && nameInput.value.trim() !== '' && 
                        valueInputs.length > 0 && Array.from(valueInputs).some(input => input.value.trim() !== '')) {
                        hasValidAttribute = true;
                        break;
                    }
                }
                
                if (!hasValidAttribute) {
                    attributesError.textContent = 'Mỗi thuộc tính cần có tên và ít nhất một giá trị';
                    isValid = false;
                } else {
                    attributesError.textContent = '';
                }
            }
            
            // Validate primary image
            const primaryImageUpload = document.getElementById('primary-image-upload');
            const mainImagePreview = document.getElementById('main-image-preview');
            
            if ((!primaryImageUpload || !primaryImageUpload.files.length) && 
                (mainImagePreview && mainImagePreview.classList.contains('hidden'))) {
                dtmodalShowToast('warning', {
                    title: 'Chú ý',
                    message: 'Vui lòng tải lên ít nhất một hình ảnh cho sản phẩm'
                });
                isValid = false;
            }
            
            return isValid;
        };

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
                        <input type="text" name="attributes[${index}][name]" placeholder="Ví dụ: Size, Màu sắc" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        <div class="error-message text-red-500 text-xs mt-1" id="attribute-${index}-name-error"></div>
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
                            <input type="text" name="attributes[${index}][values][0][value]" placeholder="Ví dụ: S, M, L" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            <div class="error-message text-red-500 text-xs mt-1" id="attribute-${index}-value-0-error"></div>
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
                    <input type="text" name="attributes[${attributeIndex}][values][${valueCount}][value]" placeholder="Ví dụ: S, M, L" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    <div class="error-message text-red-500 text-xs mt-1" id="attribute-${attributeIndex}-value-${valueCount}-error"></div>
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

        // Thêm hàm xử lý preview ảnh topping
        function handleToppingImagePreview(input) {
            const previewId = input.getAttribute('data-preview-id');
            const previewWrapId = input.getAttribute('data-preview-wrap-id');
            const uploadContentId = input.getAttribute('data-upload-content-id');
            const previewImg = document.getElementById(previewId);
            const previewWrap = document.getElementById(previewWrapId);
            const uploadContent = document.getElementById(uploadContentId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewWrap.classList.remove('hidden');
                    uploadContent.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                previewImg.src = '';
                previewWrap.classList.add('hidden');
                uploadContent.classList.remove('hidden');
            }
        }

        function createToppingGroup(index) {
            const toppingGroup = document.createElement('div');
            toppingGroup.className = 'border rounded-md p-4 mb-4';
            toppingGroup.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Tên topping</label>
                        <input type="text" name="toppings[${index}][name]" placeholder="Ví dụ: Sốt mayonnaise" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        <div class="error-message text-red-500 text-xs mt-1" id="topping-${index}-name-error"></div>
                    </div>
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Giá (VNĐ)</label>
                        <input type="number" name="toppings[${index}][price]" min="0" step="1000" placeholder="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        <div class="error-message text-red-500 text-xs mt-1" id="topping-${index}-price-error"></div>
                    </div>
                    <div class="w-48 mr-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh</label>
                        <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                          <div id="topping-image-placeholder-${index}" class="w-full h-28 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all relative">
                            <div id="topping-image-preview-wrap-${index}" class="absolute inset-0 w-full h-full hidden">
                              <img id="topping-image-preview-${index}" src="" alt="Topping image preview" class="w-full h-full object-cover rounded-md" />
                            </div>
                            <div id="topping-upload-content-${index}" class="flex flex-col items-center justify-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-gray-400 mb-1" width="28" height="28" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" y1="3" x2="12" y2="15" />
                              </svg>
                              <p class="text-xs text-gray-600 mb-1">Chọn ảnh</p>
                              <button type="button" id="select-topping-image-btn-${index}" class="px-2 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-xs">Tải lên</button>
                            </div>
                            <input type="file" id="topping-image-upload-${index}" name="toppings.${index}.image" accept="image/*" class="hidden topping-image-input" data-preview-id="topping-image-preview-${index}" data-preview-wrap-id="topping-image-preview-wrap-${index}" data-upload-content-id="topping-upload-content-${index}" />
                          </div>
                        </div>
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
            
            // Sự kiện click chọn ảnh topping
            setTimeout(() => {
                const placeholder = document.getElementById(`topping-image-placeholder-${index}`);
                const uploadBtn = document.getElementById(`select-topping-image-btn-${index}`);
                const fileInput = document.getElementById(`topping-image-upload-${index}`);
                
                if (placeholder && fileInput) {
                    placeholder.addEventListener('click', (e) => {
                        if (e.target !== uploadBtn) {
                            fileInput.click();
                        }
                    });
                    
                    uploadBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        fileInput.click();
                    });

                    fileInput.addEventListener('change', function() {
                        handleToppingImagePreview(this);
                    });
                }
            }, 10);
            
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
        @else
            // Remove the default topping - let user add toppings as needed
            // const defaultTopping = createToppingGroup(0);
            // toppingsContainer.appendChild(defaultTopping);
            toppingCount = 0;
        @endif

        // Form submission
        const form = document.getElementById('add-product-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default submission to avoid page reload
            
            // Validate the form
            if (!validateRequired()) {
                return false;
            }
            
            // Convert ingredients textarea to JSON array
            const ingredientsText = document.getElementById('ingredients').value;
            const ingredientsArray = ingredientsText.split('\n').filter(item => item.trim());
            const ingredientsInput = document.createElement('input');
            ingredientsInput.type = 'hidden';
            ingredientsInput.name = 'ingredients_json';
            ingredientsInput.value = JSON.stringify(ingredientsArray);
            form.appendChild(ingredientsInput);
            
            // Ensure description is always sent (even if empty)
            const description = document.getElementById('description');
            if (!description.value) description.value = '';
            
            // Send form data via AJAX to avoid page reload
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    dtmodalShowToast('success', {
                        title: 'Thành công',
                        message: 'Sản phẩm đã được tạo thành công!'
                    });
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect || '/admin/products';
                    }, 1500);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`${key.replace(/\./g, '-')}-error`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                const inputElement = document.querySelector(`[name="${key}"]`);
                                if (inputElement) {
                                    inputElement.classList.add('border-red-500');
                                }
                            } else if (key === 'primary_image') {
                                dtmodalShowToast('error', {
                                    title: 'Lỗi',
                                    message: data.errors[key][0]
                                });
                            }
                        });
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: data.message || 'Đã có lỗi xảy ra khi tạo sản phẩm.'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Đã có lỗi xảy ra khi gửi biểu mẫu.'
                });
            });
        });

        // Handle status and release date visibility
        const statusInputs = document.querySelectorAll('input[name="status"]');
        const releaseAtDiv = document.querySelector('label[for="release_at"]').parentElement;

        function toggleReleaseDate() {
            const selectedStatus = document.querySelector('input[name="status"]:checked').value;
            if (selectedStatus === 'coming_soon') {
                releaseAtDiv.classList.remove('hidden');
            } else {
                releaseAtDiv.classList.add('hidden');
            }
        }

        statusInputs.forEach(input => {
            input.addEventListener('change', toggleReleaseDate);
        });

        // Initial check
        toggleReleaseDate();
    });
  </script>
@endsection

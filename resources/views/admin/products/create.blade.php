@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Thêm sản phẩm mới')

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
</style>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold mb-1">Thêm Sản Phẩm Mới</h1>
    <p class="text-gray-500 mb-8">Nhập thông tin chi tiết để tạo sản phẩm mới</p>

    <form id="add-product-form" class="space-y-8">

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
              <input type="text" id="name" name="name" required placeholder="Nhập tên sản phẩm" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
            </div>

            <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Danh mục <span class="text-red-500">*</span></label>
                <select id="category" name="category" required class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="">Chọn danh mục</option>
                  <option value="1">Quần áo</option>
                  <option value="2">Giày dép</option>
                  <option value="3">Phụ kiện</option>
                  <option value="4">Đồ điện tử</option>
                  <option value="5">Đồ gia dụng</option>
                </select>
              </div>
              <div>
                <label for="originalPrice" class="block text-sm font-medium text-gray-700">Giá gốc <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                  <input type="number" id="originalPrice" name="originalPrice" min="0" required placeholder="0" class="block w-full pl-7 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                </div>
              </div>
              
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="sku" class="block text-sm font-medium text-gray-700">Mã SKU <span class="text-red-500">*</span></label>
                <input type="text" id="sku" name="sku" required placeholder="Nhập mã SKU" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              </div>
              <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700">Mã vạch</label>
                <input type="text" id="barcode" name="barcode" placeholder="Nhập mã vạch (nếu có)" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Tồn kho <span class="text-red-500">*</span></label>
                <input type="number" id="stock" name="stock" min="0" required placeholder="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              </div>
              <div>
                <label for="weight" class="block text-sm font-medium text-gray-700">Trọng lượng (gram)</label>
                <input type="number" id="weight" name="weight" min="0" placeholder="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              </div>
            </div>

            <div>
              <label for="shortDescription" class="block text-sm font-medium text-gray-700">Mô tả ngắn</label>
              <textarea id="shortDescription" name="shortDescription" rows="2" placeholder="Nhập mô tả ngắn gọn về sản phẩm" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none"></textarea>
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
              <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none"></textarea>
            </div>

            <div>
              <span class="block text-sm font-medium text-gray-700">Trạng thái</span>
              <div class="flex gap-4 mt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="status" value="active" checked class="form-radio text-blue-600" />
                  <span>Đang bán</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="status" value="draft" class="form-radio text-blue-600" />
                  <span>Bản nháp</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="status" value="inactive" class="form-radio text-blue-600" />
                  <span>Ngừng bán</span>
                </label>
              </div>
            </div>

            <div class="mt-3">
              <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="featured" name="featured" class="form-checkbox text-red-500" />
                <span>Sản phẩm nổi bật</span>
              </label>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh sản phẩm <span class="text-red-500">*</span></label>
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
                  <input type="file" id="file-upload" name="images[]" accept="image/*" class="hidden" />
                </div>
              </div>
              <div id="image-gallery" class="mt-3"></div>
              <div class="flex justify-start mt-2">
                <button type="button" id="add-more-images-btn" class="flex items-center gap-1 text-blue-600 hover:text-blue-800">
                  <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                  </svg>
                  Thêm hình ảnh
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Product Attributes and Variants -->
      <section class="space-y-4">
        <div class="flex space-x-1 bg-gray-100 rounded-md p-1">
          <button type="button" data-tab="attributes" class="tab active flex-1 text-center py-2 rounded-md text-blue-700 bg-white font-semibold">Thuộc tính</button>
          <button type="button" data-tab="variants" class="tab flex-1 text-center py-2 rounded-md text-gray-700 hover:bg-white hover:text-blue-700">Biến thể</button>
        </div>

        <!-- Attributes Tab -->
        <div id="attributes-tab" class="tab-content block">
          <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
              <div>
                <h2 class="text-xl font-semibold text-gray-900">Thuộc tính sản phẩm</h2>
                <p class="text-gray-500 text-sm mt-1">Thêm các thuộc tính và giá trị cho sản phẩm</p>
              </div>
              <button id="add-attribute-btn" type="button" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Thêm thuộc tính
              </button>
            </header>
            <div class="px-6 py-6 space-y-6" id="attributes-container">
              <!-- Attribute rows here, keep original or dynamically add -->
              <!-- ... -->
            </div>
          </section>
        </div>

        <!-- Variants Tab -->
        <div id="variants-tab" class="tab-content hidden">
          <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
              <div>
                <h2 class="text-xl font-semibold text-gray-900">Biến thể sản phẩm</h2>
                <p class="text-gray-500 text-sm mt-1">Quản lý các biến thể dựa trên thuộc tính</p>
              </div>
              <div class="flex gap-2">
                <button id="generate-variants-btn" type="button" class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-gray-700 hover:bg-gray-100">
                  <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M8 3v3a2 2 0 0 1-2 2H3"></path>
                    <path d="M21 8h-3a2 2 0 0 1-2-2V3"></path>
                    <path d="M3 16h3a2 2 0 0 1 2 2v3"></path>
                    <path d="M16 21v-3a2 2 0 0 1 2-2h3"></path>
                  </svg>
                  Tạo tự động
                </button>
                <button id="add-variant-btn" type="button" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                  Thêm biến thể
                </button>
              </div>
            </header>
            <div class="overflow-x-auto">
              <table class="w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 font-medium">
                  <tr>
                    <th class="py-3 px-4 text-left border border-gray-200">SKU</th>
                    <th class="py-3 px-4 text-left border border-gray-200">Thuộc tính</th>
                    <th class="py-3 px-4 text-left border border-gray-200">Giá</th>
                    <th class="py-3 px-4 text-left border border-gray-200">Tồn kho</th>
                    <th class="py-3 px-4 text-right border border-gray-200">Thao tác</th>
                  </tr>
                </thead>
                <tbody id="variants-table" class="text-gray-800">
                  <!-- Variant rows here -->
                  <!-- ... -->
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </section>

      <!-- Save Buttons -->
      <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-4 shadow-sm mt-6">
        <button type="button" id="save-draft-btn" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Lưu nháp</button>
        <button type="submit" id="save-product-btn" class="rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Tạo sản phẩm</button>
      </div>
    </form>
  </main>

  <!-- Variant Modal -->
  <div id="variant-modal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 hidden z-30"></div>
  <div id="variant-modal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-11/12 max-w-md z-40 hidden max-h-[90vh] overflow-y-auto">
    <div class="px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900">Thêm biến thể sản phẩm</h3>
      <p class="text-gray-500 text-sm mt-1">Nhập thông tin và chọn thuộc tính cho biến thể mới</p>
    </div>
    <div class="px-6 py-6 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="variant-sku" class="block text-sm font-medium text-gray-700">Mã SKU <span class="text-red-500">*</span></label>
          <input type="text" id="variant-sku" placeholder="Ví dụ: AT-D-XL" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
        </div>
        <div>
          <label for="variant-price" class="block text-sm font-medium text-gray-700">Giá <span class="text-red-500">*</span></label>
          <div class="relative">
            <input type="number" id="variant-price" min="0" value="199000" class="mt-1 block w-full rounded-md border border-gray-300 pl-7 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
          </div>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="variant-original-price" class="block text-sm font-medium text-gray-700">Giá gốc</label>
          <div class="relative">
            <input type="number" id="variant-original-price" min="0" placeholder="0" class="mt-1 block w-full rounded-md border border-gray-300 pl-7 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
          </div>
        </div>
        <div>
          <label for="variant-stock" class="block text-sm font-medium text-gray-700">Tồn kho <span class="text-red-500">*</span></label>
          <input type="number" id="variant-stock" min="0" value="10" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 font-semibold mb-2">Thuộc tính biến thể</label>
        <div class="border border-gray-300 rounded-md bg-gray-50 p-4 space-y-4">
          <div>
            <label for="variant-color" class="block text-sm font-medium text-gray-700">Màu sắc</label>
            <select id="variant-color" class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
              <option value="">Chọn màu sắc</option>
              <option value="1">Đen</option>
              <option value="2">Trắng</option>
              <option value="3">Xanh</option>
            </select>
          </div>
          <div>
            <label for="variant-size" class="block text-sm font-medium text-gray-700">Kích thước</label>
            <select id="variant-size" class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
              <option value="">Chọn kích thước</option>
              <option value="4">S</option>
              <option value="5">M</option>
              <option value="6">L</option>
              <option value="7">XL</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
      <button type="button" id="cancel-variant-btn" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Hủy</button>
      <button type="button" id="confirm-variant-btn" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Thêm</button>
    </div>
  </div>

  <script>
    // JavaScript remains mostly unchanged from original, just hooks to updated classes and IDs
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    const addAttributeBtn = document.getElementById('add-attribute-btn');
    const attributesContainer = document.getElementById('attributes-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    const generateVariantsBtn = document.getElementById('generate-variants-btn');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const metaTitleInput = document.getElementById('meta-title');
    const metaDescriptionInput = document.getElementById('meta-description');
    const seoPreviewTitle = document.getElementById('seo-preview-title');
    const seoPreviewUrl = document.getElementById('seo-preview-url');
    const seoPreviewDescription = document.getElementById('seo-preview-description');
    const variantModalBackdrop = document.getElementById('variant-modal-backdrop');
    const variantModal = document.getElementById('variant-modal');
    const cancelVariantBtn = document.getElementById('cancel-variant-btn');
    const confirmVariantBtn = document.getElementById('confirm-variant-btn');
    const imageUploader = document.getElementById('image-placeholder');
    const fileUpload = document.getElementById('file-upload');
    const selectImageBtn = document.getElementById('select-image-btn');
    const addMoreImagesBtn = document.getElementById('add-more-images-btn');
    
    // Xử lý upload hình ảnh
    let imageCounter = 0;
    const uploadedImages = new Map();

    // Khi click vào nút "Chọn hình ảnh"
    if (selectImageBtn) {
      selectImageBtn.addEventListener('click', function() {
        fileUpload.click();
      });
    }

    // Khi click vào nút "Thêm hình ảnh"
    if (addMoreImagesBtn) {
      addMoreImagesBtn.addEventListener('click', function() {
        fileUpload.click();
      });
    }

    // Khi chọn file
    if (fileUpload) {
      fileUpload.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
          handleFiles(this.files);
          this.value = ''; // Reset input để có thể chọn cùng file nhiều lần
        }
      });
    }

    // Thêm sự kiện drag and drop cho khu vực upload ảnh
    if (imageUploader) {
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        imageUploader.addEventListener(eventName, preventDefaults, false);
      });
      
      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }
      
      ['dragenter', 'dragover'].forEach(eventName => {
        imageUploader.addEventListener(eventName, highlight, false);
      });
      
      ['dragleave', 'drop'].forEach(eventName => {
        imageUploader.addEventListener(eventName, unhighlight, false);
      });
      
      function highlight() {
        imageUploader.classList.add('bg-blue-50', 'border-blue-300');
      }
      
      function unhighlight() {
        imageUploader.classList.remove('bg-blue-50', 'border-blue-300');
      }
      
      imageUploader.addEventListener('drop', handleDrop, false);
      
      function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
      }
    }

    function handleFiles(files) {
      Array.from(files).forEach(file => {
        uploadFile(file);
      });
    }

    function uploadFile(file) {
      // Kiểm tra kích thước file (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        dtmodalShowToast('error', {
          title: 'Lỗi',
          message: `File ${file.name} vượt quá kích thước cho phép (5MB)`
        });
        return;
      }
      
      const reader = new FileReader();
      
      reader.onload = function(e) {
        const imageId = `image-${imageCounter++}`;
        uploadedImages.set(imageId, {
          file: file,
          src: e.target.result
        });
        
        addImageToGallery(imageId, e.target.result);
      };
      
      reader.readAsDataURL(file);
    }

    function addImageToGallery(imageId, src) {
      const gallery = document.getElementById('image-gallery');
      
      // Ẩn placeholder nếu có ảnh
      if (imageUploader) {
        imageUploader.style.display = uploadedImages.size === 0 ? 'flex' : 'none';
      }
      
      const div = document.createElement('div');
      div.className = 'image-item';
      div.dataset.imageId = imageId;
      
      div.innerHTML = `
        <img src="${src}" alt="Product image">
        <button type="button" class="image-remove-btn" onclick="removeImage('${imageId}')">×</button>
        <input type="hidden" name="product_images[]" value="${imageId}">
      `;
      
      gallery.appendChild(div);
    }

    // Xóa hình ảnh
    window.removeImage = function(imageId) {
      dtmodalCreateModal({
        type: 'warning',
        title: 'Xác nhận xóa ảnh',
        message: 'Bạn có chắc muốn xóa ảnh này không?',
        confirmText: 'Xóa',
        cancelText: 'Hủy',
        onConfirm: function() {
          const imageItem = document.querySelector(`.image-item[data-image-id="${imageId}"]`);
          
          if (imageItem) {
            imageItem.remove();
            uploadedImages.delete(imageId);
            
            // Hiển thị placeholder nếu không còn ảnh
            if (uploadedImages.size === 0 && imageUploader) {
              imageUploader.style.display = 'flex';
            }
            
            dtmodalShowToast('success', {
              title: 'Đã xóa',
              message: 'Hình ảnh đã được xóa thành công'
            });
          }
        }
      });
    }

    if (tabs) {
      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          const tabId = tab.getAttribute('data-tab');
          
          // Xóa active class từ tất cả tab buttons
          tabs.forEach(t => t.classList.remove('active', 'bg-white', 'text-blue-700', 'font-semibold'));
          
          // Thêm active class cho tab được chọn
          tab.classList.add('active', 'bg-white', 'text-blue-700', 'font-semibold');
          
          // Ẩn tất cả các tab content
          tabContents.forEach(content => content.classList.add('hidden'));
          
          // Hiển thị tab content tương ứng
          const activeContent = document.getElementById(`${tabId}-tab`);
          if (activeContent) {
            activeContent.classList.remove('hidden');
          }
        });
      });
    }

    function addTag(event) {
      if (event.key === 'Enter') {
        event.preventDefault();

        const input = document.getElementById('tag-input');
        const value = input.value.trim();

        if (value) {
          const container = document.getElementById('tags-container');
          const hiddenInput = document.getElementById('tags-hidden');

          const tag = document.createElement('div');
          tag.className = 'ap-tag flex items-center gap-1 bg-gray-200 rounded px-2 py-0.5 text-xs text-gray-700';
          tag.innerHTML = `
            ${value}
            <button type="button" class="text-gray-500 hover:text-red-600" onclick="removeTag(this)">×</button>
          `;

          container.insertBefore(tag, input);
          input.value = '';

          updateHiddenTags();
        }
      }
    }

    function removeTag(element) {
      const tag = element.parentElement;
      tag.remove();
      updateHiddenTags();
    }

    function updateHiddenTags() {
      const tags = document.querySelectorAll('#tags-container > div.ap-tag');
      const values = Array.from(tags).map(tag => tag.textContent.trim().slice(0, -1));
      document.getElementById('tags-hidden').value = values.join(',');
    }

    if (nameInput && slugInput && metaTitleInput && metaDescriptionInput) {
      nameInput.addEventListener('input', updateSeoPreview);
      slugInput.addEventListener('input', updateSeoPreview);
      metaTitleInput.addEventListener('input', updateSeoPreview);
      metaDescriptionInput.addEventListener('input', updateSeoPreview);
    }

    function updateSeoPreview() {
      const name = nameInput.value || 'Tên sản phẩm';
      const slug = slugInput.value || 'ten-san-pham';
      const title = metaTitleInput.value || name;
      const description = metaDescriptionInput.value || 'Mô tả sản phẩm sẽ hiển thị ở đây. Đây là phần mô tả ngắn gọn về sản phẩm của bạn để thu hút khách hàng nhấp vào liên kết.';

      seoPreviewTitle.textContent = title;
      seoPreviewUrl.textContent = `www.example.com/san-pham/${slug}`;
      seoPreviewDescription.textContent = description;
    }

    // if (nameInput && slugInput) {
    //   nameInput.addEventListener('blur', () => {
    //     if (!slugInput.value && nameInput.value) {
    //       const slug = nameInput.value
    //         .toLowerCase()
    //         .normalize('NFD')
    //         .replace(/[\u0300-\u036f]/g, '')
    //         // .replace(/[đĐ]/g, 'd')
    //         .replace(/[^a-z0-9]+/g, '-')
    //         .replace(/(^-|-$)/g, '');

    //       slugInput.value = slug;
    //       updateSeoPreview();
    //     }
    //   });
    // }

    let attributeCounter = 3;

    if (addAttributeBtn) {
      addAttributeBtn.addEventListener('click', () => {
        const attributeRow = document.createElement('div');
        attributeRow.className = 'ap-attribute-row flex flex-col md:flex-row gap-4 p-4 border border-gray-200 rounded-md bg-white';
        attributeRow.dataset.attributeId = attributeCounter;

        attributeRow.innerHTML = `
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
            <input type="text" name="attributes[${attributeCounter}][name]" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nhập tên thuộc tính" />
            <div class="mt-2 flex items-center gap-2">
              <input type="checkbox" id="attribute-${attributeCounter}-variant" name="attributes[${attributeCounter}][is_variant]" class="form-checkbox text-blue-600" />
              <label for="attribute-${attributeCounter}-variant" class="text-sm text-gray-700 cursor-pointer">Dùng cho biến thể</label>
            </div>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Giá trị thuộc tính</label>
            <div class="border border-gray-300 rounded-md p-2 space-y-2">
              <div class="flex items-center gap-2">
                <input type="text" name="attributes[${attributeCounter}][values][]" class="flex-grow rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nhập giá trị thuộc tính" />
                <div class="relative">
                  <input type="number" name="attributes[${attributeCounter}][prices][]" min="0" class="w-24 pl-8 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Giá" />
                </div>
                <button type="button" class="text-red-500 hover:text-red-700 delete-value" aria-label="Xóa giá trị">×</button>
              </div>
            </div>
            <button type="button" class="mt-2 text-blue-600 hover:underline add-value-btn" data-attribute-id="${attributeCounter}">Thêm giá trị</button>
          </div>
          <div class="flex items-start mt-4 md:mt-0">
            <button type="button" class="text-red-500 hover:text-red-700 delete-attribute self-start" aria-label="Xóa thuộc tính" data-attribute-id="${attributeCounter}">Xóa</button>
          </div>
        `;

        attributesContainer.appendChild(attributeRow);

        const newAddValueBtn = attributeRow.querySelector('.add-value-btn');
        newAddValueBtn.addEventListener('click', addAttributeValue);

        const newDeleteAttributeBtn = attributeRow.querySelector('.delete-attribute');
        newDeleteAttributeBtn.addEventListener('click', deleteAttribute);

        const newDeleteValueBtn = attributeRow.querySelector('.delete-value');
        newDeleteValueBtn.addEventListener('click', deleteAttributeValue);

        attributeCounter++;
      });
    }

    document.querySelectorAll('.add-value-btn').forEach(btn => {
      btn.addEventListener('click', addAttributeValue);
    });

    function addAttributeValue(event) {
      const btn = event.currentTarget;
      const attributeId = btn.dataset.attributeId;
      const valuesContainer = btn.previousElementSibling;

      const valueItem = document.createElement('div');
      valueItem.className = 'flex items-center gap-2';
      valueItem.innerHTML = `
        <input type="text" name="attributes[${attributeId}][values][]" class="flex-grow rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nhập giá trị thuộc tính" />
        <div class="relative">
          <input type="number" name="attributes[${attributeId}][prices][]" min="0" class="w-24 pl-8 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Giá" />
        </div>
        <button type="button" class="text-red-500 hover:text-red-700 delete-value" aria-label="Xóa giá trị">×</button>
      `;

      valuesContainer.appendChild(valueItem);

      const newDeleteValueBtn = valueItem.querySelector('.delete-value');
      newDeleteValueBtn.addEventListener('click', deleteAttributeValue);
    }

    document.querySelectorAll('.delete-attribute').forEach(btn => {
      btn.addEventListener('click', deleteAttribute);
    });

    function deleteAttribute(event) {
      const btn = event.currentTarget;
      const attributeRow = btn.closest('.ap-attribute-row');
      attributeRow.remove();
    }

    document.querySelectorAll('.delete-value').forEach(btn => {
      btn.addEventListener('click', deleteAttributeValue);
    });

    function deleteAttributeValue(event) {
      const btn = event.currentTarget;
      const valueItem = btn.closest('div.flex.items-center.gap-2');
      valueItem.remove();
    }

    if (addVariantBtn) {
      addVariantBtn.addEventListener('click', () => {
        variantModalBackdrop.classList.remove('hidden');
        variantModal.classList.remove('hidden');
      });
    }

    if (cancelVariantBtn) {
      cancelVariantBtn.addEventListener('click', () => {
        variantModalBackdrop.classList.add('hidden');
        variantModal.classList.add('hidden');
      });
    }

    if (confirmVariantBtn) {
      confirmVariantBtn.addEventListener('click', () => {
        const sku = document.getElementById('variant-sku').value;
        const price = document.getElementById('variant-price').value;
        const originalPrice = document.getElementById('variant-original-price').value;
        const stock = document.getElementById('variant-stock').value;
        const colorSelect = document.getElementById('variant-color');
        const sizeSelect = document.getElementById('variant-size');

        if (!sku || !price || !stock) {
          dtmodalShowToast('warning', {
            title: 'Cảnh báo',
            message: 'Vui lòng điền đầy đủ thông tin bắt buộc'
          });
          return;
        }

        const colorValue = colorSelect.options[colorSelect.selectedIndex].text;
        const sizeValue = sizeSelect.options[sizeSelect.selectedIndex].text;

        const variantsTable = document.getElementById('variants-table');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
          <td class="py-3 px-4 border border-gray-200 font-mono">${sku}</td>
          <td class="py-3 px-4 border border-gray-200">
            <div class="flex flex-wrap gap-1">
              <span class="inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">${'Màu sắc'}: ${colorValue}</span>
              <span class="inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">${'Kích thước'}: ${sizeValue}</span>
            </div>
          </td>
          <td class="py-3 px-4 border border-gray-200">${stock}</td>
          <td class="py-3 px-4 border border-gray-200 text-right">
            <div class="flex justify-end gap-2">
              <button type="button" class="text-gray-700 hover:text-blue-600 edit-variant" aria-label="Sửa biến thể">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                  <path d="M12 20h9" />
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />
                </svg>
              </button>
              <button type="button" class="text-red-600 hover:text-red-800 delete-variant" aria-label="Xóa biến thể">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>
          </td>
        `;

        variantsTable.appendChild(newRow);
        variantModalBackdrop.classList.add('hidden');
        variantModal.classList.add('hidden');

        // Reset form
        document.getElementById('variant-sku').value = '';
        document.getElementById('variant-stock').value = '10';
      });
    }

    if (variantModalBackdrop) {
      variantModalBackdrop.addEventListener('click', () => {
        variantModalBackdrop.classList.add('hidden');
        variantModal.classList.add('hidden');
      });
    }

    // Generate variants button
    if (generateVariantsBtn) {
      generateVariantsBtn.addEventListener('click', () => {
        // Lấy tất cả các thuộc tính
        const attributes = [];
        document.querySelectorAll('[data-attribute-id]').forEach(item => {
          const attributeId = item.getAttribute('data-attribute-id');
          const nameInput = item.querySelector(`input[name="attributes[${attributeId}][name]"]`);
          const valueInputs = item.querySelectorAll(`input[name="attributes[${attributeId}][values][]"]`);
          const isVariant = item.querySelector(`input[name="attributes[${attributeId}][is_variant]"]`);
          
          if (nameInput && valueInputs.length > 0 && isVariant && isVariant.checked) {
            const values = Array.from(valueInputs).map(input => input.value.trim()).filter(val => val);
            
            if (nameInput.value && values.length > 0) {
              attributes.push({
                name: nameInput.value,
                values: values
              });
            }
          }
        });
        
        if (attributes.length === 0) {
          dtmodalShowToast('warning', {
            title: 'Cảnh báo',
            message: 'Vui lòng thêm ít nhất một thuộc tính dùng cho biến thể'
          });
          return;
        }
        
        // Tạo tất cả các tổ hợp có thể
        const generateCombinations = (arrays, current = [], index = 0) => {
          if (index === arrays.length) {
            return [current];
          }
          
          let result = [];
          for (let i = 0; i < arrays[index].values.length; i++) {
            result = result.concat(
              generateCombinations(
                arrays,
                [...current, { name: arrays[index].name, value: arrays[index].values[i] }],
                index + 1
              )
            );
          }
          
          return result;
        };
        
        const combinations = generateCombinations(attributes);
        
        if (combinations.length > 50) {
          dtmodalCreateModal({
            type: 'warning',
            title: 'Cảnh báo',
            message: `Bạn sắp tạo ${combinations.length} biến thể. Số lượng lớn có thể gây chậm hệ thống. Bạn có muốn tiếp tục?`,
            confirmText: 'Tiếp tục',
            cancelText: 'Hủy',
            onConfirm: () => generateVariantsUI(combinations)
          });
        } else {
          generateVariantsUI(combinations);
        }
      });
    }

    function generateVariantsUI(combinations) {
      const variantsTable = document.getElementById('variants-table');
      const baseSku = document.getElementById('sku').value || 'PROD';
      const basePrice = document.getElementById('originalPrice').value || '0';
      
      // Xóa tất cả các biến thể hiện tại
      variantsTable.innerHTML = '';
      
      // Thêm các biến thể mới
      combinations.forEach((combination, index) => {
        const variantCode = combination.map(attr => attr.value.substr(0, 2).toUpperCase()).join('-');
        const sku = `${baseSku}-${variantCode}`;
        
        const newRow = document.createElement('tr');
        
        newRow.innerHTML = `
          <td class="py-3 px-4 border border-gray-200 font-mono">${sku}</td>
          <td class="py-3 px-4 border border-gray-200">
            <div class="flex flex-wrap gap-1">
              ${combination.map(attr => 
                `<span class="inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">${attr.name}: ${attr.value}</span>`
              ).join('')}
            </div>
          </td>
          <td class="py-3 px-4 border border-gray-200">10</td>
          <td class="py-3 px-4 border border-gray-200 text-right">
            <div class="flex justify-end gap-2">
              <button type="button" class="text-gray-700 hover:text-blue-600 edit-variant" aria-label="Sửa biến thể">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                  <path d="M12 20h9" />
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />
                </svg>
              </button>
              <button type="button" class="text-red-600 hover:text-red-800 delete-variant" aria-label="Xóa biến thể">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>
          </td>
        `;
        
        variantsTable.appendChild(newRow);
      });
      
      // Chuyển sang tab biến thể
      const variantsTab = document.querySelector('[data-tab="variants"]');
      if (variantsTab) {
        variantsTab.click();
      }
      
      // Hiển thị thông báo thành công
      dtmodalShowToast('success', {
        title: 'Thành công',
        message: `Đã tạo ${combinations.length} biến thể từ thuộc tính.`
      });
    }

    const form = document.getElementById('add-product-form');
    const saveProductBtn = document.getElementById('save-product-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn');

    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Kiểm tra các trường bắt buộc
        const name = document.getElementById('name').value;
        const originalPrice = document.getElementById('originalPrice').value;
        const sku = document.getElementById('sku').value;
        const stock = document.getElementById('stock').value;
        const category = document.getElementById('category').value;
        
        if (!name || !originalPrice || !sku || !stock || !category) {
          dtmodalShowToast('warning', {
            title: 'Cảnh báo',
            message: 'Vui lòng điền đầy đủ các trường bắt buộc'
          });
          return;
        }
        
        // Xác nhận tạo sản phẩm
        dtmodalCreateModal({
          type: 'info',
          title: 'Xác nhận tạo sản phẩm',
          message: 'Bạn có chắc muốn tạo sản phẩm mới này?',
          confirmText: 'Tạo sản phẩm',
          cancelText: 'Hủy',
          onConfirm: function() {
            dtmodalShowToast('success', {
              title: 'Thành công',
              message: 'Đã tạo sản phẩm mới thành công'
            });
            
            // Chuyển hướng sau 1s
            setTimeout(() => {
              window.location.href = '/admin/products';
            }, 1000);
          }
        });
      });
    }

    if (saveDraftBtn) {
      saveDraftBtn.addEventListener('click', () => {
        dtmodalCreateModal({
          type: 'info',
          title: 'Lưu bản nháp',
          message: 'Bạn có muốn lưu sản phẩm này dưới dạng bản nháp?',
          confirmText: 'Lưu nháp',
          cancelText: 'Hủy',
          onConfirm: function() {
            dtmodalShowToast('success', {
              title: 'Thành công',
              message: 'Đã lưu sản phẩm dưới dạng bản nháp'
            });
          }
        });
      });
    }
  </script>
@endsection

@section('scripts')
<script src="{{ asset('js/modal.js') }}"></script>
@endsection

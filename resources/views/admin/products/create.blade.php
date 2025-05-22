@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="max-w-7xl mx-auto">
  <!-- Page header -->

  <div class="flex items-center justify-between gap-2">
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Thêm Sản Phẩm Mới</h1>
      <p class="text-gray-500 dark:text-gray-400">Nhập thông tin chi tiết để tạo sản phẩm mới</p>
    </div>
    <a href="{{ asset('admin/products') }}" class="btn btn-primary flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" viewBox="0 0 24 24">
      <line x1="19" y1="12" x2="5" y2="12" />
      <polyline points="12 19 5 12 12 5" />
    </svg>

      Quay lại
    </a>
  </div>

  <form id="add-product-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    <!-- Basic Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">
      <div class="flex justify-between items-center p-5 border-b border-gray-200 dark:border-gray-700">
        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Thông tin cơ bản</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Nhập thông tin cơ bản của sản phẩm</p>
        </div>
      </div>
      <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-2 space-y-5">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
              <input type="text" id="name" name="name" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" required placeholder="Nhập tên sản phẩm" value="{{ old('name') }}" />
              @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Danh mục <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" required>
                  <option value="">Chọn danh mục</option>
                  @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                  @endforeach
                </select>
                @error('category_id')
                  <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label for="base_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giá gốc <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-gray-400">₫</span>
                  <input type="number" id="base_price" name="base_price" class="w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" required placeholder="0" min="0" step="0.01" value="{{ old('base_price') }}" />
                </div>
                @error('base_price')
                  <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="preparation_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thời gian chuẩn bị (phút) <span class="text-red-500">*</span></label>
                <input type="number" id="preparation_time" name="preparation_time" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" required placeholder="Nhập thời gian chuẩn bị" min="0" value="{{ old('preparation_time') }}" />
                @error('preparation_time')
                  <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tồn kho <span class="text-red-500">*</span></label>
                <input type="number" id="stock_quantity" name="stock_quantity" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" required placeholder="0" min="0" value="{{ old('stock_quantity') }}" />
                @error('stock_quantity')
                  <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mô tả chi tiết</label>
              <textarea id="description" name="description" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm">{{ old('description') }}</textarea>
              @error('description')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trạng thái</label>
              <div class="flex gap-4">
                <div class="flex items-center">
                  <input type="radio" id="status_active" name="status" value="active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600" {{ old('status', 'active') == 'active' ? 'checked' : '' }} />
                  <label for="status_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Đang bán</label>
                </div>
                <div class="flex items-center">
                  <input type="radio" id="status_inactive" name="status" value="inactive" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600" {{ old('status') == 'inactive' ? 'checked' : '' }} />
                  <label for="status_inactive" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ngừng bán</label>
                </div>
              </div>
              @error('status')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <div class="flex items-center">
                <input type="checkbox" id="featured" name="featured" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded" {{ old('featured') ? 'checked' : '' }} />
                <label for="featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Sản phẩm nổi bật</label>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hình ảnh sản phẩm <span class="text-red-500">*</span></label>
              <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center">
                <div class="flex flex-col items-center justify-center py-8" id="image-placeholder">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Kéo thả hoặc nhấp để tải lên</p>
                  <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="document.getElementById('file-upload').click()">
                    Chọn hình ảnh
                  </button>
                  <input type="file" id="file-upload" name="images[]" multiple accept="image/*" class="hidden" onchange="handleFileUpload(this)" />
                </div>
              </div>
              <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-2" id="image-gallery"></div>
              @error('images')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
              @error('images.*')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Thuộc tính & Biến thể -->
    <div class="mb-6">
      <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex">
          <button type="button" class="tab-btn active py-3 px-4 border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-medium text-sm" data-tab="attributes">Thuộc tính</button>
          <button type="button" class="tab-btn py-3 px-4 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 font-medium text-sm" data-tab="variants">Biến thể</button>
        </nav>
      </div>

      <div class="tab-content active" id="attributes-tab">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mt-4 overflow-hidden">
          <div class="flex justify-between items-center p-5 border-b border-gray-200 dark:border-gray-700">
            <div>
              <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Thuộc tính sản phẩm</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">Thêm các thuộc tính và giá trị cho sản phẩm</p>
            </div>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600" id="add-attribute-btn">
              <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Thêm thuộc tính
            </button>
          </div>
          <div class="p-5" id="attributes-container">
            @if(old('attributes'))
              @foreach(old('attributes') as $key => $attribute)
                <div class="relative p-4 border border-gray-200 dark:border-gray-700 rounded-md mb-4" data-attribute-id="{{ $key }}">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên thuộc tính</label>
                      <input type="text" name="attributes[{{ $key }}][name]" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Ví dụ: Màu sắc, Kích thước..." value="{{ $attribute['name'] ?? '' }}" />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giá trị</label>
                      <input type="text" name="attributes[{{ $key }}][values]" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Nhập các giá trị, phân cách bằng dấu phẩy" value="{{ $attribute['values'] ?? '' }}" />
                    </div>
                  </div>
                  <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="removeAttribute({{ $key }})">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 6h18"></path>
                      <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                      <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      <line x1="10" y1="11" x2="10" y2="17"></line>
                      <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                  </button>
                </div>
              @endforeach
            @endif
          </div>
        </div>
      </div>

      <div class="tab-content hidden" id="variants-tab">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mt-4 overflow-hidden">
          <div class="flex justify-between items-center p-5 border-b border-gray-200 dark:border-gray-700">
            <div>
              <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Biến thể sản phẩm</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">Quản lý các biến thể dựa trên thuộc tính</p>
            </div>
            <div class="flex gap-2">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="generate-variants-btn">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M8 3v3a2 2 0 0 1-2 2H3"></path>
                  <path d="M21 8h-3a2 2 0 0 1-2-2V3"></path>
                  <path d="M3 16h3a2 2 0 0 1 2 2v3"></path>
                  <path d="M16 21v-3a2 2 0 0 1 2-2h3"></path>
                </svg>
                Tạo tự động
              </button>
              <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600" id="add-variant-btn">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Thêm biến thể
              </button>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="variants-table">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Thuộc tính</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Giá</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tồn kho</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ảnh</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trạng thái</th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Thao tác</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @if(old('variants'))
                  @foreach(old('variants') as $key => $variant)
                    <tr data-variant-id="{{ $key }}">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input type="text" name="variants[{{ $key }}][sku]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="SKU" value="{{ $variant['sku'] ?? '' }}" />
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input type="text" name="variants[{{ $key }}][attributes]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Thuộc tính" value="{{ $variant['attributes'] ?? '' }}" />
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input type="number" name="variants[{{ $key }}][price]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Giá" min="0" value="{{ $variant['price'] ?? '' }}" />
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input type="number" name="variants[{{ $key }}][stock]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Tồn kho" min="0" value="{{ $variant['stock'] ?? '' }}" />
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                          Chọn ảnh
                        </button>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <select name="variants[{{ $key }}][status]" class="text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300">
                          <option value="active" {{ ($variant['status'] ?? '') == 'active' ? 'selected' : '' }}>Đang bán</option>
                          <option value="inactive" {{ ($variant['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
                        </select>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button type="button" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="removeVariant({{ $key }})">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                          </svg>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Nút Lưu -->
    <div class="sticky bottom-0 bg-white dark:bg-gray-800 p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end z-10 mt-8">
      <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600" id="save-product-btn">
        <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
          <polyline points="17 21 17 13 7 13 7 21"></polyline>
          <polyline points="7 3 7 8 15 8"></polyline>
        </svg>
        Tạo sản phẩm
      </button>
    </div>
  </form>
</div>

<script>
  // Xử lý tabs
  document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const tabId = btn.getAttribute('data-tab');
        
        // Xóa active class từ tất cả tabs và tab contents
        tabBtns.forEach(t => {
          t.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
          t.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        tabContents.forEach(content => content.classList.add('hidden'));
        
        // Thêm active class cho tab được chọn và tab content tương ứng
        btn.classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById(`${tabId}-tab`).classList.remove('hidden');
      });
    });
    
    // Xử lý thêm thuộc tính
    const addAttributeBtn = document.getElementById('add-attribute-btn');
    const attributesContainer = document.getElementById('attributes-container');
    
    if (addAttributeBtn) {
      addAttributeBtn.addEventListener('click', () => {
        const attributeId = Date.now();
        const attributeHtml = `
          <div class="relative p-4 border border-gray-200 dark:border-gray-700 rounded-md mb-4" data-attribute-id="${attributeId}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên thuộc tính</label>
                <input type="text" name="attributes[${attributeId}][name]" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Ví dụ: Màu sắc, Kích thước..." />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giá trị</label>
                <input type="text" name="attributes[${attributeId}][values]" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Nhập các giá trị, phân cách bằng dấu phẩy" />
              </div>
            </div>
            <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="removeAttribute(${attributeId})">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18"></path>
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
              </svg>
            </button>
          </div>
        `;
        
        attributesContainer.insertAdjacentHTML('beforeend', attributeHtml);
      });
    }
    
    // Xử lý thêm biến thể
    const addVariantBtn = document.getElementById('add-variant-btn');
    const variantsTable = document.getElementById('variants-table').getElementsByTagName('tbody')[0];
    
    if (addVariantBtn) {
      addVariantBtn.addEventListener('click', () => {
        const variantId = Date.now();
        const variantHtml = `
          <tr data-variant-id="${variantId}">
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="text" name="variants[${variantId}][sku]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="SKU" />
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="text" name="variants[${variantId}][attributes]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Thuộc tính" />
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="number" name="variants[${variantId}][price]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Giá" min="0" />
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="number" name="variants[${variantId}][stock]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Tồn kho" min="0" />
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Chọn ảnh
              </button>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <select name="variants[${variantId}][status]" class="text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300">
                <option value="active">Đang bán</option>
                <option value="inactive">Ngừng bán</option>
              </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <button type="button" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="removeVariant(${variantId})">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                  <line x1="10" y1="11" x2="10" y2="17"></line>
                  <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
              </button>
            </td>
          </tr>
        `;
        
        variantsTable.insertAdjacentHTML('beforeend', variantHtml);
      });
    }
    
    // Xử lý tạo biến thể tự động
    const generateVariantsBtn = document.getElementById('generate-variants-btn');
    
    if (generateVariantsBtn) {
      generateVariantsBtn.addEventListener('click', () => {
        // Lấy tất cả các thuộc tính
        const attributes = [];
        document.querySelectorAll('[data-attribute-id]').forEach(item => {
          const attributeId = item.getAttribute('data-attribute-id');
          const nameInput = item.querySelector(`input[name="attributes[${attributeId}][name]"]`);
          const valuesInput = item.querySelector(`input[name="attributes[${attributeId}][values]"]`);
          
          if (nameInput && valuesInput && nameInput.value && valuesInput.value) {
            attributes.push({
              name: nameInput.value,
              values: valuesInput.value.split(',').map(v => v.trim())
            });
          }
        });
        
        if (attributes.length === 0) {
          alert('Vui lòng thêm ít nhất một thuộc tính với giá trị để tạo biến thể.');
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
        
        // Xóa tất cả các biến thể hiện tại
        variantsTable.innerHTML = '';
        
        // Thêm các biến thể mới
        combinations.forEach((combination, index) => {
          const variantId = Date.now() + index;
          const attributeText = combination.map(attr => `${attr.name}: ${attr.value}`).join(', ');
          const sku = `SKU-${index + 1}`;
          
          const variantHtml = `
            <tr data-variant-id="${variantId}">
              <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="variants[${variantId}][sku]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="SKU" value="${sku}" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="variants[${variantId}][attributes]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Thuộc tính" value="${attributeText}" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="variants[${variantId}][price]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Giá" min="0" value="${document.getElementById('base_price').value || 0}" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="variants[${variantId}][stock]" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300" placeholder="Tồn kho" min="0" value="${document.getElementById('stock_quantity').value || 0}" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                  Chọn ảnh
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select name="variants[${variantId}][status]" class="text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300">
                  <option value="active">Đang bán</option>
                  <option value="inactive">Ngừng bán</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <button type="button" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="removeVariant(${variantId})">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                  </svg>
                </button>
              </td>
            </tr>
          `;
          
          variantsTable.insertAdjacentHTML('beforeend', variantHtml);
        });
      });
    }
  });
  
  // Xử lý upload hình ảnh
  function handleFileUpload(input) {
    const files = input.files;
    const gallery = document.getElementById('image-gallery');
    
    if (files.length > 0) {
      document.getElementById('image-placeholder').style.display = 'none';
      gallery.innerHTML = '';
      
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const imageHtml = `
            <div class="relative rounded-md overflow-hidden aspect-square">
              <img src="${e.target.result}" alt="Product Image" class="w-full h-full object-cover" />
              <button type="button" class="absolute top-1 right-1 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 rounded-full p-1 text-gray-700 dark:text-gray-300 hover:text-red-500 dark:hover:text-red-400" onclick="removeImage(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>
          `;
          
          gallery.insertAdjacentHTML('beforeend', imageHtml);
        };
        
        reader.readAsDataURL(file);
      }
    }
  }
  
  // Xóa hình ảnh
  function removeImage(button) {
    const imageItem = button.parentNode;
    imageItem.remove();
    
    const gallery = document.getElementById('image-gallery');
    if (gallery.children.length === 0) {
      document.getElementById('image-placeholder').style.display = 'flex';
    }
  }
  
  // Xóa thuộc tính
  function removeAttribute(attributeId) {
    const attributeItem = document.querySelector(`[data-attribute-id="${attributeId}"]`);
    if (attributeItem) {
      attributeItem.remove();
    }
  }
  
  // Xóa biến thể
  function removeVariant(variantId) {
    const variantRow = document.querySelector(`tr[data-variant-id="${variantId}"]`);
    if (variantRow) {
      variantRow.remove();
    }
  }
</script>
@endsection

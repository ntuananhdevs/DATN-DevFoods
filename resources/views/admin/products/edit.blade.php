@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh sửa sản phẩm')

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
    <h1 class="text-3xl font-extrabold mb-1">Chỉnh Sửa Sản Phẩm</h1>
    <p class="text-gray-500 mb-8">Chỉnh sửa thông tin chi tiết sản phẩm</p>

    <form id="edit-product-form" class="space-y-8" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                        <textarea id="ingredients" name="ingredients" rows="5" placeholder="Nhập danh sách nguyên liệu (mỗi nguyên liệu một dòng)" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">@php
$ingredientsData = $product->ingredients;
if (is_array($ingredientsData)) {
    // Check if it's a structured format (with categories)
    $isStructured = false;
    foreach ($ingredientsData as $key => $value) {
        if (is_array($value)) {
            $isStructured = true;
            break;
        }
    }
    
    if ($isStructured) {
        // Format 1: Structured with categories
        foreach ($ingredientsData as $category => $items) {
            echo $category . ":\n";
            if (is_array($items)) {
                foreach ($items as $item) {
                    echo "- " . $item . "\n";
                }
            }
        }
    } else {
        // Format 2: Simple array
        foreach ($ingredientsData as $ingredient) {
            if (is_string($ingredient)) {
                echo $ingredient . "\n";
            }
        }
    }
} elseif (is_string($ingredientsData)) {
    // Just output as is if it's a string
    echo $ingredientsData;
} else {
    // Handle other cases (null, object, etc.)
    echo '';
}
@endphp</textarea>
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700">Tùy chọn</span>
              <div class="space-y-4 mt-2">
                <div class="flex gap-4">
                  <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="form-checkbox text-blue-600" />
                    <span>Sản phẩm nổi bật</span>
                  </label>
                  
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái sản phẩm</label>
                  <div class="flex gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                      <input type="radio" name="status" value="coming_soon" {{ old('status', $product->status) == 'coming_soon' ? 'checked' : '' }} class="form-radio text-blue-600" />
                      <span>Sắp ra mắt</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                      <input type="radio" name="status" value="selling" {{ old('status', $product->status) == 'selling' ? 'checked' : '' }} class="form-radio text-blue-600" />
                      <span>Đang bán</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                      <input type="radio" name="status" value="discontinued" {{ old('status', $product->status) == 'discontinued' ? 'checked' : '' }} class="form-radio text-blue-600" />
                      <span>Ngừng bán</span>
                    </label>
                  </div>
                </div>

                <div>
                  <label for="release_at" class="block text-sm font-medium text-gray-700">Ngày ra mắt</label>
                  <input type="datetime-local" id="release_at" name="release_at" value="{{ old('release_at', $product->release_at) }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
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
                      <div id="main-image-preview" class="absolute inset-0 w-full h-full {{ $primaryImage ? '' : 'hidden' }}">
                        <img src="{{ $primaryImage ? Storage::disk('s3')->url($primaryImage->img) : '' }}" alt="Main image preview" class="w-full h-full object-cover" />
                      </div>
                      <div id="upload-content" class="flex flex-col items-center justify-center {{ $primaryImage ? 'hidden' : '' }}">
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
      
<!-- Branch Inventory Section -->
<section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
  <header class="px-6 py-4 border-b border-gray-100">
    <h2 class="text-xl font-semibold text-gray-900">Số lượng tại chi nhánh</h2>
    <p class="text-gray-500 text-sm mt-1">Quản lý số lượng sản phẩm tại các chi nhánh</p>
  </header>

  <div class="px-6 py-6">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi nhánh</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
            @if(isset($product->variants) && count($product->variants) > 0)
              @foreach($product->variants as $variant)
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  @php
                    $variantName = [];
                    foreach($variant->productVariantDetails as $detail) {
                      if(isset($detail->variantValue)) {
                        $variantName[] = $detail->variantValue->value;
                      }
                    }
                    echo implode(' / ', $variantName);
                  @endphp
                </th>
              @endforeach
            @else
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
            @endif
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($branches ?? [] as $branch)
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $branch->name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $branch->address }}</td>
              
              @if(isset($product->variants) && count($product->variants) > 0)
                @foreach($product->variants as $variant)
                  @php
                    $stockQuantity = 0;
                    // Try different ways to access the stock quantity
                    if (isset($branchStocks[$branch->id][$variant->id])) {
                      $stockQuantity = $branchStocks[$branch->id][$variant->id];
                    } elseif (isset($branchStocks) && is_array($branchStocks)) {
                      // Look through the array for matching branch and variant
                      foreach ($branchStocks as $stock) {
                        if (isset($stock->branch_id) && isset($stock->product_variant_id) && 
                            $stock->branch_id == $branch->id && $stock->product_variant_id == $variant->id) {
                          $stockQuantity = $stock->stock_quantity;
                          break;
                        }
                      }
                    }
                  @endphp
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input 
                      type="number" 
                      name="branch_stock[{{ $branch->id }}][{{ $variant->id }}]" 
                      min="0" 
                      class="block w-24 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                      value="{{ $stockQuantity }}"
                    >
                  </td>
                @endforeach
              @else
                @php
                  $stockQuantity = 0;
                  $defaultVariantId = isset($product->variants[0]->id) ? $product->variants[0]->id : 0;
                  
                  // Try different ways to access the stock quantity
                  if (isset($branchStocks[$branch->id][$defaultVariantId])) {
                    $stockQuantity = $branchStocks[$branch->id][$defaultVariantId];
                  } elseif (isset($branchStocks[$branch->id][0])) {
                    $stockQuantity = $branchStocks[$branch->id][0];
                  } elseif (isset($branchStocks) && is_array($branchStocks)) {
                    // Look through the array for matching branch
                    foreach ($branchStocks as $stock) {
                      if (isset($stock->branch_id) && $stock->branch_id == $branch->id) {
                        $stockQuantity = $stock->stock_quantity;
                        break;
                      }
                    }
                  }
                @endphp
                <td class="px-6 py-4 whitespace-nowrap">
                  <input 
                    type="number" 
                    name="branch_stock[{{ $branch->id }}][{{ $defaultVariantId }}]" 
                    min="0" 
                    class="block w-24 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    value="{{ $stockQuantity }}"
                  >
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ isset($product->variants) && count($product->variants) > 0 ? count($product->variants) + 2 : 3 }}" class="px-6 py-4 text-center text-sm text-gray-500">Không có chi nhánh nào</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>

      <!-- Save Buttons -->
      <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-4 shadow-sm mt-6">
        <button type="button" id="save-draft-btn" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Lưu nháp</button>
        <button type="submit" id="save-product-btn" class="rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Cập nhật sản phẩm</button>
      </div>
    </form>
  </main>

@endsection

@section('scripts')
  <!-- Debug output -->
  <script>
    console.log('Product data:', @json($product));
    console.log('Product images:', @json($product->images));
    console.log('Primary image:', @json($primaryImage));
    @if($primaryImage)
      console.log('Primary image URL:', "{{ Storage::disk('s3')->url($primaryImage->img) }}");
    @endif
    console.log('Attributes:', @json($product->attributes));
    console.log('Variants:', @json($product->variants));
    console.log('Toppings:', @json($product->toppings));
    console.log('Branch Stocks:', @json($branchStocks ?? []));
    console.log('Branches:', @json($branches ?? []));
    
    // Helper function to find branch stock record
    function findBranchStock(branchId, variantId) {
      @if(isset($branchStocks))
        @if(is_array($branchStocks))
          // If branchStocks is a multi-dimensional array
          if (@json(isset($branchStocks[0]) && is_object($branchStocks[0]))) {
            // If branchStocks is an array of objects
            const stocks = @json($branchStocks);
            return stocks.find(stock => 
              stock.branch_id == branchId && 
              stock.product_variant_id == variantId
            )?.stock_quantity || 0;
          } else {
            // If branchStocks is a nested array
            const stocks = @json($branchStocks);
            return stocks[branchId]?.[variantId] || 0;
          }
        @else
          return 0;
        @endif
      @else
        return 0;
      @endif
    }
  </script>
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

        // Initialize with existing images if available
        @if($primaryImage)
            mainImagePreview.classList.remove('hidden');
            uploadContent.classList.add('hidden');
        @endif

        @if($product->images && count($product->images) > 0)
            @foreach($product->images as $image)
                uploadedImages.push({
                    id: {{ $image->id }},
                    preview: "{{ Storage::disk('s3')->url($image->img) }}",
                    isExisting: true
                });
            @endforeach
            updateImageGallery();
        @endif

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
                            preview: e.target.result,
                            isExisting: false
                        });
                        updateImageGallery();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function updateImageGallery() {
            imageGallery.innerHTML = uploadedImages.map((image, index) => {
                const isExisting = image.isExisting;
                return `
                    <div class="image-item">
                        <img src="${image.preview}" alt="Preview" class="w-full h-32 object-cover rounded-md" />
                        <button type="button" class="image-remove-btn" onclick="removeImage(${index})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        ${isExisting ? `<input type="hidden" name="existing_images[]" value="${image.id}">` : ''}
                    </div>
                `;
            }).join('');
        }

        window.removeImage = function(index) {
            const image = uploadedImages[index];
            if (image.isExisting) {
                // Add a hidden input to track deleted images
                const deletedInput = document.createElement('input');
                deletedInput.type = 'hidden';
                deletedInput.name = 'deleted_images[]';
                deletedInput.value = image.id;
                document.getElementById('edit-product-form').appendChild(deletedInput);
            }
            uploadedImages.splice(index, 1);
            updateImageGallery();
        };

        // Attributes and Variant Values handling
        const attributesContainer = document.getElementById('attributes-container');
        const addAttributeBtn = document.getElementById('add-attribute-btn');
        let attributeCount = 0;

        function createAttributeGroup(index, name = '', values = []) {
            const attributeGroup = document.createElement('div');
            attributeGroup.className = 'attribute-group';
            attributeGroup.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                        <input type="text" name="attributes[${index}][name]" required placeholder="Ví dụ: Size, Màu sắc" value="${name}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.attribute-group').remove()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
                </div>
                <div class="variant-values-container">
                    ${values.length > 0 ? values.map((val, valueIndex) => `
                        <div class="variant-value-row">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Giá trị</label>
                                <input type="text" name="attributes[${index}][values][${valueIndex}][value]" required placeholder="Ví dụ: S, M, L" value="${val.value || ''}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                                ${val.id ? `<input type="hidden" name="attributes[${index}][values][${valueIndex}][id]" value="${val.id}">` : ''}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Giá điều chỉnh</label>
                                <input type="number" name="attributes[${index}][values][${valueIndex}][price_adjustment]" step="0.01" value="${val.price_adjustment || 0}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            </div>
                            <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.variant-value-row').remove()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
                    `).join('') : `
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
                    `}
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

        // Load existing attributes
        @if(isset($product->attributes) && count($product->attributes) > 0)
            @foreach($product->attributes as $index => $attribute)
                attributesContainer.appendChild(createAttributeGroup(
                    {{ $index }}, 
                    {!! json_encode($attribute->name) !!}, 
                    {!! json_encode($attribute->values->map(function($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value,
                            'price_adjustment' => $value->price_adjustment
                        ];
                    })) !!}
                ));
                attributeCount = {{ $index + 1 }};
            @endforeach
        @elseif(isset($product->variants) && count($product->variants) > 0)
            // Extract attributes from variants if the attributes relationship doesn't work
            @php
                $attributesMap = [];
                foreach ($product->variants as $variant) {
                    foreach ($variant->productVariantDetails as $detail) {
                        if (isset($detail->variantValue) && isset($detail->variantValue->attribute)) {
                            $attributeId = $detail->variantValue->attribute->id;
                            $attributeName = $detail->variantValue->attribute->name;
                            
                            if (!isset($attributesMap[$attributeId])) {
                                $attributesMap[$attributeId] = [
                                    'id' => $attributeId,
                                    'name' => $attributeName,
                                    'values' => []
                                ];
                            }
                            
                            // Check if value already exists
                            $valueExists = false;
                            foreach ($attributesMap[$attributeId]['values'] as $value) {
                                if ($value['id'] == $detail->variantValue->id) {
                                    $valueExists = true;
                                    break;
                                }
                            }
                            
                            if (!$valueExists) {
                                $attributesMap[$attributeId]['values'][] = [
                                    'id' => $detail->variantValue->id,
                                    'value' => $detail->variantValue->value,
                                    'price_adjustment' => $detail->variantValue->price_adjustment
                                ];
                            }
                        }
                    }
                }
                $extractedAttributes = array_values($attributesMap);
            @endphp
            
            @foreach($extractedAttributes as $index => $attribute)
                attributesContainer.appendChild(createAttributeGroup(
                    {{ $index }}, 
                    {!! json_encode($attribute['name']) !!}, 
                    {!! json_encode($attribute['values']) !!}
                ));
                attributeCount = {{ $index + 1 }};
            @endforeach
        @else
            // Add default attribute if no attributes exist
            attributesContainer.appendChild(createAttributeGroup(0));
            attributeCount = 1;
        @endif

        // Toppings handling
        const toppingsContainer = document.getElementById('toppings-container');
        const addToppingBtn = document.getElementById('add-topping-btn');
        let toppingCount = 0;

        function createToppingGroup(index, name = '', price = 0, available = true, id = null, image = '') {
            const toppingGroup = document.createElement('div');
            toppingGroup.className = 'border rounded-md p-4 mb-4';
            toppingGroup.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Tên topping</label>
                        <input type="text" name="toppings[${index}][name]" required placeholder="Ví dụ: Sốt mayonnaise" value="${name}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        ${id ? `<input type="hidden" name="toppings[${index}][id]" value="${id}">` : ''}
                    </div>
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Giá (VNĐ)</label>
                        <input type="number" name="toppings[${index}][price]" required min="0" step="1000" placeholder="0" value="${price}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.border').remove()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Topping</label>
                    <div class="flex items-center">
                        <div class="topping-img-preview w-24 h-24 border border-gray-200 rounded-md overflow-hidden bg-gray-50 mr-4 flex items-center justify-center">
                            ${image ? `<img src="${image}" alt="${name}" class="object-cover w-full h-full" />` : `
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-300" width="24" height="24" fill="none" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>`}
                        </div>
                        <div>
                            <input type="file" id="topping-image-${index}" name="topping_images[${index}]" accept="image/*" class="hidden topping-image-input" data-index="${index}" />
                            <button type="button" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm select-topping-image-btn" data-index="${index}">
                                Chọn ảnh
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Định dạng: JPG, PNG, GIF</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="toppings[${index}][available]" value="1" ${available ? 'checked' : ''} class="form-checkbox text-blue-600" />
                        <span class="text-sm text-gray-700">Đang bán</span>
                    </label>
                </div>
            `;
            return toppingGroup;
        }

        addToppingBtn.addEventListener('click', () => {
            toppingsContainer.appendChild(createToppingGroup(toppingCount));
            attachToppingImageHandlers(toppingCount);
            toppingCount++;
        });

        // Helper function to get topping image URL
        function getToppingImageUrl(toppingId) {
            @if(isset($product->toppings) && count($product->toppings) > 0)
                const toppings = @json($product->toppings);
                const topping = toppings.find(t => t.id == toppingId);
                if (topping && topping.image) {
                    // Get the full S3 URL for the image
                    @foreach($product->toppings as $topping)
                        if ({{ $topping->id }} == toppingId && "{{ $topping->image }}") {
                            return "{{ Storage::disk('s3')->url($topping->image ?? 'default-placeholder.jpg') }}";
                        }
                    @endforeach
                }
            @endif
            return '';
        }

        // Attach event handlers for topping image uploads
        function attachToppingImageHandlers(index) {
            const selectBtn = document.querySelector(`.select-topping-image-btn[data-index="${index}"]`);
            const input = document.getElementById(`topping-image-${index}`);
            
            if (selectBtn && input) {
                selectBtn.addEventListener('click', () => {
                    input.click();
                });
                
                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const preview = input.closest('.mb-4').querySelector('.topping-img-preview');
                            if (preview) {
                                preview.innerHTML = `<img src="${e.target.result}" alt="Topping preview" class="object-cover w-full h-full" />`;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        // Load existing toppings
        @if(isset($product->toppings) && count($product->toppings) > 0)
            @foreach($product->toppings as $index => $topping)
                toppingsContainer.appendChild(createToppingGroup(
                    {{ $index }},
                    {!! json_encode($topping->name) !!},
                    {{ $topping->price }},
                    {{ $topping->available ? 'true' : 'false' }},
                    {{ $topping->id }}
                ));
                attachToppingImageHandlers({{ $index }});
                toppingCount = {{ $index + 1 }};
            @endforeach
        @endif

        // Form submission
        const form = document.getElementById('edit-product-form');
        form.addEventListener('submit', function(e) {
            // Convert ingredients textarea to JSON array
            const ingredientsText = document.getElementById('ingredients').value;
            const ingredientsArray = ingredientsText.split('\n').filter(item => item.trim());
            const ingredientsInput = document.createElement('input');
            ingredientsInput.type = 'hidden';
            ingredientsInput.name = 'ingredients_json';
            ingredientsInput.value = JSON.stringify(ingredientsArray);
            form.appendChild(ingredientsInput);
            
            // Validate branch quantities and ensure they're sent as scalar values
            const branchStockInputs = document.querySelectorAll('input[name^="branch_stock"]');
            branchStockInputs.forEach(input => {
                // Ensure quantities are valid numbers
                if (input.value === '' || isNaN(input.value)) {
                    input.value = 0;
                }
            });
            
            // Đảm bảo description luôn gửi lên (kể cả rỗng)
            const description = document.getElementById('description');
            if (!description.value) description.value = '';
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
        
        // Set branch stock values
        const branchStockInputs = document.querySelectorAll('input[name^="branch_stock"]');
        branchStockInputs.forEach(input => {
            const matches = input.name.match(/branch_stock\[(\d+)\]\[(\d+)\]/);
            if (matches && matches.length === 3) {
                const branchId = matches[1];
                const variantId = matches[2];
                const stockQuantity = findBranchStock(branchId, variantId);
                if (stockQuantity > 0) {
                    input.value = stockQuantity;
                }
            }
        });
    });
  </script>
@endsection

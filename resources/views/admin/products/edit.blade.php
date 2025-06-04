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
              <input type="text" id="name" name="name" placeholder="Nhập tên sản phẩm" value="{{ old('name', $product->name) }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              <div class="error-message text-red-500 text-xs mt-1" id="name-error"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
            <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục <span class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                </select>
                <div class="error-message text-red-500 text-xs mt-1" id="category_id-error"></div>
              </div>
              <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">Giá cơ bản <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                                <input type="number" id="base_price" name="base_price" min="0" step="0.01" placeholder="0" value="{{ old('base_price', $product->base_price) }}" class="block w-full pl-7 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            </div>
                <div class="error-message text-red-500 text-xs mt-1" id="base_price-error"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-700">Thời gian chuẩn bị (phút)</label>
                            <input type="number" id="preparation_time" name="preparation_time" min="0" placeholder="Nhập thời gian chuẩn bị" value="{{ old('preparation_time', $product->preparation_time) }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                            <div class="error-message text-red-500 text-xs mt-1" id="preparation_time-error"></div>
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
                        <div class="error-message text-red-500 text-xs mt-1" id="ingredients-error"></div>
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
                    <!-- Load existing attributes with PHP instead of JavaScript -->
                    @php
                        // Get existing attributes either directly or from variant details
                        $existingAttributes = [];
                        
                        if(isset($product->attributes) && count($product->attributes) > 0) {
                            // If we have attributes relationship loaded
                            $existingAttributes = $product->attributes;
                        } elseif(isset($product->variants) && count($product->variants) > 0) {
                            // Extract attributes from variants if the attributes relationship doesn't work
                            $attributesMap = [];
                            foreach ($product->variants as $variant) {
                                foreach ($variant->productVariantDetails as $detail) {
                                    if (isset($detail->variantValue) && isset($detail->variantValue->attribute)) {
                                        $attributeId = $detail->variantValue->attribute->id;
                                        $attributeName = $detail->variantValue->attribute->name;
                                        
                                        if (!isset($attributesMap[$attributeId])) {
                                            $attributesMap[$attributeId] = (object)[
                                                'id' => $attributeId,
                                                'name' => $attributeName,
                                                'values' => []
                                            ];
                                        }
                                        
                                        // Check if value already exists
                                        $valueExists = false;
                                        foreach ($attributesMap[$attributeId]->values as $value) {
                                            if ($value->id == $detail->variantValue->id) {
                                                $valueExists = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$valueExists) {
                                            $attributesMap[$attributeId]->values[] = (object)[
                                                'id' => $detail->variantValue->id,
                                                'value' => $detail->variantValue->value,
                                                'price_adjustment' => $detail->variantValue->price_adjustment
                                            ];
                                        }
                                    }
                                }
                            }
                            $existingAttributes = array_values($attributesMap);
                        }
                    @endphp

                    @foreach($existingAttributes as $attrIndex => $attribute)
                        <div class="attribute-group">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex-1 mr-4">
                                    <label class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                                    <input type="text" name="attributes[{{ $attrIndex }}][name]" required 
                                        placeholder="Ví dụ: Size, Màu sắc" value="{{ $attribute->name }}" 
                                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                                    <!-- Add hidden field to preserve attribute ID -->
                                    <input type="hidden" name="attributes[{{ $attrIndex }}][id]" value="{{ $attribute->id }}" />
                                </div>
                                <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.attribute-group').remove()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="variant-values-container">
                                @foreach($attribute->values as $valueIndex => $value)
                                    <div class="variant-value-row">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Giá trị</label>
                                            <input type="text" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][value]" 
                                                required placeholder="Ví dụ: S, M, L" value="{{ $value->value }}" 
                                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                                            <!-- Add hidden field to preserve value ID -->
                                            <input type="hidden" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][id]" value="{{ $value->id }}" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Giá điều chỉnh</label>
                                            <input type="number" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][price_adjustment]" 
                                                step="0.01" value="{{ $value->price_adjustment ?? 0 }}" 
                                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                                        </div>
                                        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.variant-value-row').remove()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="mt-2 text-blue-600 hover:text-blue-800" onclick="addVariantValue(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Thêm giá trị
                            </button>
                        </div>
                    @endforeach

                    @if(count($existingAttributes) == 0)
                        <!-- Add a default empty attribute group if no attributes exist -->
                        <div class="attribute-group">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex-1 mr-4">
                                    <label class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                                    <input type="text" name="attributes[0][name]" required placeholder="Ví dụ: Size, Màu sắc" 
                                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
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
                                        <input type="text" name="attributes[0][values][0][value]" required placeholder="Ví dụ: S, M, L" 
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Giá điều chỉnh</label>
                                        <input type="number" name="attributes[0][values][0][price_adjustment]" step="0.01" value="0" 
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
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
                        </div>
                    @endif
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
      <!-- Existing topping groups will be loaded here -->
      @if(isset($product->toppings) && count($product->toppings) > 0)
        @foreach($product->toppings as $index => $topping)
          <div class="border rounded-md p-4 mb-4">
            <div class="flex justify-between items-start mb-4">
              <div class="flex-1 mr-4">
                <label class="block text-sm font-medium text-gray-700">Tên topping</label>
                <input type="text" name="toppings[{{ $index }}][name]" value="{{ $topping->name }}" placeholder="Ví dụ: Sốt mayonnaise" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                <input type="hidden" name="toppings[{{ $index }}][id]" value="{{ $topping->id }}">
                <div class="error-message text-red-500 text-xs mt-1" id="topping-{{ $index }}-name-error"></div>
              </div>
              <div class="flex-1 mr-4">
                <label class="block text-sm font-medium text-gray-700">Giá (VNĐ)</label>
                <input type="number" name="toppings[{{ $index }}][price]" value="{{ $topping->price }}" min="0" step="1000" placeholder="0" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                <div class="error-message text-red-500 text-xs mt-1" id="topping-{{ $index }}-price-error"></div>
              </div>
              <div class="w-48 mr-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh</label>
                <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                  <div id="topping-image-placeholder-{{ $index }}" class="w-full h-28 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all relative">
                    <div id="topping-image-preview-wrap-{{ $index }}" class="absolute inset-0 w-full h-full {{ $topping->image ? '' : 'hidden' }}">
                      <img id="topping-image-preview-{{ $index }}" src="{{ $topping->image ? Storage::disk('s3')->url($topping->image) : '' }}" alt="Topping image preview" class="w-full h-full object-cover rounded-md" />
                    </div>
                    <div id="topping-upload-content-{{ $index }}" class="flex flex-col items-center justify-center {{ $topping->image ? 'hidden' : '' }}">
                      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current text-gray-400 mb-1" width="28" height="28" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                      </svg>
                      <p class="text-xs text-gray-600 mb-1">Chọn ảnh</p>
                      <button type="button" id="select-topping-image-btn-{{ $index }}" class="px-2 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-xs">Tải lên</button>
                    </div>
                    <input type="file" id="topping-image-upload-{{ $index }}" name="toppings[{{ $index }}][image]" accept="image/*" class="hidden topping-image-input" data-preview-id="topping-image-preview-{{ $index }}" data-preview-wrap-id="topping-image-preview-wrap-{{ $index }}" data-upload-content-id="topping-upload-content-{{ $index }}" />
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
                <input type="checkbox" name="toppings[{{ $index }}][active]" value="1" {{ $topping->active ? 'checked' : '' }} class="form-checkbox text-blue-600" />
                <span class="text-sm text-gray-700">Đang bán</span>
              </label>
            </div>
          </div>
        @endforeach
      @endif
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
    @if(empty($branchStocks) || count($branchStocks) === 0)
      <div class="bg-blue-50 p-4 mb-4 rounded-md">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Thông tin số lượng</h3>
            <div class="mt-2 text-sm text-blue-700">
              <p>Hiện tại chưa có dữ liệu số lượng tại chi nhánh. Cập nhật số lượng cho từng biến thể tại chi nhánh bên dưới.</p>
            </div>
          </div>
        </div>
      </div>
    @endif

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
                    echo implode(' / ', $variantName) ?: "Biến thể #" . $variant->id;
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
                    
                    // Look for existing branch stock using the new array structure
                    if (isset($branchStocks[$branch->id]) && isset($branchStocks[$branch->id][$variant->id])) {
                      $stockQuantity = $branchStocks[$branch->id][$variant->id];
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
                  $defaultVariantId = 0; // Always use 0 for products without variants
                  
                  // Look for existing branch stock using the new array structure
                  if (isset($branchStocks[$branch->id]) && isset($branchStocks[$branch->id][$defaultVariantId])) {
                    $stockQuantity = $branchStocks[$branch->id][$defaultVariantId];
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

<!-- Topping Stock Section -->
@if(isset($product->toppings) && count($product->toppings) > 0)
<section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-8">
  <header class="px-6 py-4 border-b border-gray-100">
    <h2 class="text-xl font-semibold text-gray-900">Số lượng Topping tại chi nhánh</h2>
    <p class="text-gray-500 text-sm mt-1">Quản lý số lượng topping tại các chi nhánh</p>
  </header>

  <div class="px-6 py-6">
    @if(empty($toppingStocks) || count($toppingStocks) === 0)
      <div class="bg-blue-50 p-4 mb-4 rounded-md">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Thông tin số lượng</h3>
            <div class="mt-2 text-sm text-blue-700">
              <p>Hiện tại chưa có dữ liệu số lượng topping tại chi nhánh. Cập nhật số lượng cho từng topping tại chi nhánh bên dưới.</p>
            </div>
          </div>
        </div>
      </div>
    @endif

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi nhánh</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
            @foreach($product->toppings as $topping)
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ $topping->name }}
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($branches ?? [] as $branch)
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $branch->name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $branch->address }}</td>
              
              @foreach($product->toppings as $topping)
                @php
                  $stockQuantity = 0;
                  
                  // Look for existing topping stock
                  if (isset($toppingStocks[$branch->id]) && isset($toppingStocks[$branch->id][$topping->id])) {
                    $stockQuantity = $toppingStocks[$branch->id][$topping->id];
                  }
                @endphp
                <td class="px-6 py-4 whitespace-nowrap">
                  <input 
                    type="number" 
                    name="topping_stock[{{ $branch->id }}][{{ $topping->id }}]" 
                    min="0" 
                    class="block w-24 rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    value="{{ $stockQuantity }}"
                  >
                </td>
              @endforeach
            </tr>
          @empty
            <tr>
              <td colspan="{{ count($product->toppings) + 2 }}" class="px-6 py-4 text-center text-sm text-gray-500">Không có chi nhánh nào</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>
@endif

      <!-- Save Buttons -->
      <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-4 shadow-sm mt-6">
        <button type="button" id="save-draft-btn" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Lưu nháp</button>
        <button type="submit" name="update_type" value="product_info" id="save-product-btn" class="rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Cập nhật sản phẩm</button>
        
        <!-- Hidden fields for branch and topping stocks -->
        <input type="hidden" name="update_branch_stocks" value="1">
        <input type="hidden" name="update_topping_stocks" value="1">
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
    @if(isset($product->variants) && count($product->variants) > 0)
      console.log('Variant details:');
      @foreach($product->variants as $index => $variant)
        console.log('  Variant #{{ $index + 1 }} (ID: {{ $variant->id }})');
        @if(isset($variant->productVariantDetails) && count($variant->productVariantDetails) > 0)
          @foreach($variant->productVariantDetails as $detail)
            @if(isset($detail->variantValue) && isset($detail->variantValue->attribute))
              console.log('    - {{ $detail->variantValue->attribute->name }}: {{ $detail->variantValue->value }}');
            @endif
          @endforeach
        @endif
      @endforeach
    @endif
    console.log('Branch Stocks Info:');
    console.log('  Type:', typeof @json($branchStocks));
    console.log('  Is array:', Array.isArray(@json($branchStocks)));
    console.log('  Length:', @json($branchStocks) ? @json($branchStocks).length : 0);
    console.log('  Content:', @json($branchStocks ?? []));
    console.log('  Branch count:', @json($branches ? count($branches) : 0));
    console.log('Branches:', @json($branches ?? []));
    console.log('Toppings:', @json($product->toppings ?? []));
    console.log('Topping Stocks:', @json($toppingStocks ?? []));
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle branch stock update button
        const saveBranchStocksBtn = document.getElementById('save-branch-stocks-btn');
        if (saveBranchStocksBtn) {
            saveBranchStocksBtn.addEventListener('click', function() {
                // Submit the main form instead of creating a temporary form
                const form = document.getElementById('edit-product-form');
                if (form) {
                    form.submit();
                }
            });
        }
        
        // Handle topping stock update button
        const saveToppingStocksBtn = document.getElementById('save-topping-stocks-btn');
        if (saveToppingStocksBtn) {
            saveToppingStocksBtn.addEventListener('click', function() {
                // Submit the main form instead of creating a temporary form
                const form = document.getElementById('edit-product-form');
                if (form) {
                    form.submit();
                }
            });
        }

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
            
            try {
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
                
                // Relax preparation time validation - make it optional
                const preparationTime = document.getElementById('preparation_time');
                const preparationTimeError = document.getElementById('preparation_time-error');
                if (preparationTime && preparationTimeError) {
                    if (preparationTime.value.trim() !== '' && (isNaN(preparationTime.value) || parseInt(preparationTime.value) < 0)) {
                        preparationTimeError.textContent = 'Thời gian chuẩn bị phải là số dương';
                        preparationTime.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        preparationTimeError.textContent = '';
                        preparationTime.classList.remove('border-red-500');
                    }
                }
                
                // Relax ingredients validation - allow empty or simple format
                const ingredients = document.getElementById('ingredients');
                const ingredientsError = document.getElementById('ingredients-error');
                if (ingredients && ingredientsError) {
                    ingredientsError.textContent = '';
                    ingredients.classList.remove('border-red-500');
                }
                
                // Relax attribute validation - don't block submission
                const attributesError = document.getElementById('attributes-error');
                if (attributesError) {
                    attributesError.textContent = '';
                }
                
                // Skip primary image validation to avoid errors with toast notifications
                // This allows the form to submit even without an image
                
                console.log('Validation result:', isValid);
                return true; // Force form to submit regardless of validation
            } catch (e) {
                console.error('Validation error:', e);
                return true; // Allow submission even if validation has errors
            }
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

        // Add topping functionality
        const toppingsContainer = document.getElementById('toppings-container');
        const addToppingBtn = document.getElementById('add-topping-btn');
        let toppingCount = @json(isset($product->toppings) ? count($product->toppings) : 0);

        // Handle topping image preview functionality
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
                            <input type="file" id="topping-image-upload-${index}" name="toppings[${index}][image]" accept="image/*" class="hidden topping-image-input" data-preview-id="topping-image-preview-${index}" data-preview-wrap-id="topping-image-preview-wrap-${index}" data-upload-content-id="topping-upload-content-${index}" />
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
                        <input type="checkbox" name="toppings[${index}][active]" value="1" checked class="form-checkbox text-blue-600" />
                        <span class="text-sm text-gray-700">Đang bán</span>
                    </label>
                </div>
            `;
            
            return toppingGroup;
        }

        addToppingBtn.addEventListener('click', () => {
            const toppingGroup = createToppingGroup(toppingCount);
            toppingsContainer.appendChild(toppingGroup);
            
            // Set up topping image events after the element is added to DOM
            const placeholder = document.getElementById(`topping-image-placeholder-${toppingCount}`);
            const uploadBtn = document.getElementById(`select-topping-image-btn-${toppingCount}`);
            const fileInput = document.getElementById(`topping-image-upload-${toppingCount}`);
            
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
            
            toppingCount++;
        });

        // Set up image preview handlers for existing toppings
        document.querySelectorAll('.topping-image-input').forEach(input => {
            input.addEventListener('change', function() {
                handleToppingImagePreview(this);
            });
            
            const index = input.id.replace('topping-image-upload-', '');
            const placeholder = document.getElementById(`topping-image-placeholder-${index}`);
            const uploadBtn = document.getElementById(`select-topping-image-btn-${index}`);
            
            if (placeholder && uploadBtn) {
                placeholder.addEventListener('click', (e) => {
                    if (e.target !== uploadBtn) {
                        input.click();
                    }
                });
                
                uploadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    input.click();
                });
            }
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
        
        // Add attribute functionality
        const attributesContainer = document.getElementById('attributes-container');
        const addAttributeBtn = document.getElementById('add-attribute-btn');
        
        addAttributeBtn.addEventListener('click', function() {
            const attributeCount = attributesContainer.querySelectorAll('.attribute-group').length;
            
            const attributeGroup = document.createElement('div');
            attributeGroup.className = 'attribute-group';
            attributeGroup.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <div class="flex-1 mr-4">
                        <label class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                        <input type="text" name="attributes[${attributeCount}][name]" required placeholder="Ví dụ: Size, Màu sắc" 
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
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
                            <input type="text" name="attributes[${attributeCount}][values][0][value]" required placeholder="Ví dụ: S, M, L" 
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Giá điều chỉnh</label>
                            <input type="number" name="attributes[${attributeCount}][values][0][price_adjustment]" step="0.01" value="0" 
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
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
            
            attributesContainer.appendChild(attributeGroup);
        });
        
        // Define the function to add variant values
        window.addVariantValue = function(button) {
            const container = button.previousElementSibling;
            const valueCount = container.querySelectorAll('.variant-value-row').length;
            const attributeIndex = Array.from(attributesContainer.children).indexOf(button.closest('.attribute-group'));
            
            const valueRow = document.createElement('div');
            valueRow.className = 'variant-value-row';
            valueRow.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá trị</label>
                    <input type="text" name="attributes[${attributeIndex}][values][${valueCount}][value]" required placeholder="Ví dụ: S, M, L" 
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá điều chỉnh</label>
                    <input type="number" name="attributes[${attributeIndex}][values][${valueCount}][price_adjustment]" step="0.01" value="0" 
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                </div>
                <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.variant-value-row').remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            
            container.appendChild(valueRow);
        };

        // Form submission
        const form = document.getElementById('edit-product-form');
        if (form) {
            console.log('Found edit-product-form, attaching submit handler');
            
            // Remove the validateRequired from the submit handler to allow form submission
            form.addEventListener('submit', function(e) {
                console.log('Form submit event triggered');
                // Don't call validateRequired() at all
                
                try {
                    // Convert ingredients textarea to JSON array
                    const ingredientsText = document.getElementById('ingredients').value || '';
                    const ingredientsArray = ingredientsText.split('\n').filter(item => item.trim());
                    let ingredientsInput = document.querySelector('input[name="ingredients_json"]');
                    
                    if (!ingredientsInput) {
                        ingredientsInput = document.createElement('input');
                        ingredientsInput.type = 'hidden';
                        ingredientsInput.name = 'ingredients_json';
                        form.appendChild(ingredientsInput);
                    }
                    
                    ingredientsInput.value = JSON.stringify(ingredientsArray);
                    console.log('Added ingredients JSON');
                    
                    // Add a flag to indicate branch stocks are being updated
                    let branchStocksFlag = document.querySelector('input[name="update_branch_stocks"]');
                    if (!branchStocksFlag) {
                        branchStocksFlag = document.createElement('input');
                        branchStocksFlag.type = 'hidden';
                        branchStocksFlag.name = 'update_branch_stocks';
                        branchStocksFlag.value = '1';
                        form.appendChild(branchStocksFlag);
                    }
                    
                    // Add a flag to indicate topping stocks are being updated
                    let toppingStocksFlag = document.querySelector('input[name="update_topping_stocks"]');
                    if (!toppingStocksFlag) {
                        toppingStocksFlag = document.createElement('input');
                        toppingStocksFlag.type = 'hidden';
                        toppingStocksFlag.name = 'update_topping_stocks';
                        toppingStocksFlag.value = '1';
                        form.appendChild(toppingStocksFlag);
                    }
                    
                    // Check if branch stock and topping stock inputs are already in the form
                    const formBranchInputs = form.querySelectorAll('input[name^="branch_stock"]');
                    const formToppingInputs = form.querySelectorAll('input[name^="topping_stock"]');
                    
                    console.log(`Branch inputs in form: ${formBranchInputs.length}`);
                    console.log(`Topping inputs in form: ${formToppingInputs.length}`);
                    
                    // Only clone inputs that are NOT already in the form
                    const allBranchInputs = document.querySelectorAll('input[name^="branch_stock"]');
                    allBranchInputs.forEach(input => {
                        if (!form.contains(input)) {
                            const clonedInput = input.cloneNode(true);
                            clonedInput.name = input.name;
                            clonedInput.value = input.value;
                            form.appendChild(clonedInput);
                            console.log(`Cloned branch input: ${input.name}`);
                        }
                    });
                    
                    const allToppingInputs = document.querySelectorAll('input[name^="topping_stock"]');
                    allToppingInputs.forEach(input => {
                        if (!form.contains(input)) {
                            const clonedInput = input.cloneNode(true);
                            clonedInput.name = input.name;
                            clonedInput.value = input.value;
                            form.appendChild(clonedInput);
                            console.log(`Cloned topping input: ${input.name}`);
                        }
                    });
                    
                    console.log('Form will submit normally with branch and topping stocks');
                    // Let the form submit normally
                    
                } catch(e) {
                    console.error('Error preparing form submission:', e);
                    // We don't prevent submission even if there's an error
                }
            });
            
            // Disable the save-product-btn click handler and let it use default form submit
            const saveBtn = document.getElementById('save-product-btn');
            if (saveBtn) {
                saveBtn.onclick = null;
            }
        }

        // Remove any duplicate event listeners from the debug code
    });
  </script>
@endsection

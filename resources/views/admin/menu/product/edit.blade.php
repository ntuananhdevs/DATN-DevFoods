@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')

@section('page-style-prd-edit')
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .ingredients-format {
            transition: all 0.3s ease;
        }
        .ingredients-format.hidden {
            display: none;
        }
        .ingredient-category {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f9fafb;
        }
        .ingredient-category:hover {
            border-color: #d1d5db;
        }
        .category-name:focus,
        .category-items:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .remove-category:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }
        #add-category:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }
    </style>
@endsection

<main class="container">
    <h1 class="text-3xl font-extrabold mb-1">Chỉnh Sửa Sản Phẩm</h1>
    <p class="text-gray-500 mb-8">Cập nhật thông tin chi tiết của sản phẩm</p>

    <form id="edit-product-form" class="space-y-8" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                        <label for="name" class="block text-sm font-medium text-gray-700">Tên sản phẩm <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Nhập tên sản phẩm"
                            value="{{ old('name', $product->name) }}"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 @error('name') border-red-500 @enderror" />
                        @error('name')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700"> Danh mục <span
                                    class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full rounded-md border-2 border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 @error('category_id') border-red-500 @enderror">
                                <option value=""> Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                    placeholder="0" value="{{ old('base_price', $product->base_price) }}"
                                    class="block w-full pl-7 rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 @error('base_price') border-red-500 @enderror" />
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
                                placeholder="Nhập thời gian chuẩn bị" value="{{ old('preparation_time', $product->preparation_time) }}"
                                class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 @error('preparation_time') border-red-500 @enderror" />
                            @error('preparation_time')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Nguyên liệu 
                            <span class="text-red-500">*</span>
                        </label>
                        
                        @php
                            $ingredientsText = '';
                            if (!empty($product->ingredients)) {
                                // Laravel's array cast will handle the JSON automatically
                                if (is_array($product->ingredients)) {
                                    $ingredientsText = implode("\n", $product->ingredients);
                                } else {
                                    // Fallback for any string data
                                    $ingredientsText = $product->ingredients;
                                }
                            }
                        @endphp
                        
                        <textarea id="ingredients" name="ingredients" rows="5"
                            placeholder="Nhập danh sách nguyên liệu, mỗi nguyên liệu một dòng&#10;Ví dụ:&#10;thịt bò&#10;rau xà lách&#10;ớt chuông&#10;cà chua"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none px-3 py-2 @error('ingredients') border-red-500 @enderror">{{ old('ingredients', $ingredientsText) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Mỗi dòng là một nguyên liệu riêng biệt</p>
                        
                        @error('ingredients')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700">Mô tả ngắn
                            <span class="text-red-500">*</span></label>
                        </label>
                        <textarea id="short_description" name="short_description" rows="2" placeholder="Nhập mô tả ngắn về sản phẩm" 
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none px-3 py-2 @error('short_description') border-red-500 @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                        @error('short_description')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
                        <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
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
                                        {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                        class="form-checkbox text-blue-600" />
                                    <span>Sản phẩm nổi bật</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái sản phẩm</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="coming_soon"
                                            @if (old('status', $product->status) == 'coming_soon') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Sắp ra mắt</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="selling"
                                            @if (old('status', $product->status) == 'selling') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Đang bán</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="discontinued"
                                            @if (old('status', $product->status) == 'discontinued') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Ngừng bán</span>
                                    </label>
                                </div>
                            </div>

                            <div id="release_at_container" class="hidden">
                                <label for="release_at" class="block text-sm font-medium text-gray-700">Ngày ra mắt</label>
                                <input type="datetime-local" id="release_at" name="release_at"
                                    value="{{ old('release_at', $product->release_at ? $product->release_at->format('Y-m-d\TH:i') : '') }}"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2" />
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
                                        @php
                                            $primaryImage = $product->images->where('is_primary', true)->first();
                                        @endphp
                                        @if($primaryImage)
                                            <div id="main-image-preview" class="absolute inset-0 w-full h-full">
                                                <img src="{{ Storage::disk('s3')->url($primaryImage->img) }}" alt="Current main image"
                                                    class="w-full h-full object-cover" />
                                                <div class="absolute top-2 right-2">
                                                    <button type="button" id="change-primary-image-btn"
                                                        class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors">Thay đổi</button>
                                                </div>
                                            </div>
                                            <div id="upload-content" class="flex flex-col items-center justify-center hidden">
                                        @else
                                            <div id="main-image-preview" class="absolute inset-0 w-full h-full hidden">
                                                <img src="" alt="Main image preview"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div id="upload-content" class="flex flex-col items-center justify-center">
                                        @endif
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
                                    @if($product->images && $product->images->count() > 0)
                                        @foreach($product->images->where('is_primary', false) as $image)
                                            <div class="relative group">
                                                <img src="{{ Storage::disk('s3')->url($image->img) }}" alt="Product image"
                                                    class="w-full h-24 object-cover rounded-md border border-gray-200">
                                                <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button type="button" class="remove-existing-image bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                                                        data-image-id="{{ $image->id }}">
                                                        ×
                                                    </button>
                                                </div>
                                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                            </div>
                                        @endforeach
                                    @endif
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
                    @php
                        // Get attributes from old input or from product variants
                        $attributes = old('attributes', []);
                        if (empty($attributes) && $product->variants->count() > 0) {
                            $attributesData = [];
                            foreach ($product->variants as $variant) {
                                foreach ($variant->productVariantDetails as $detail) {
                                    $attrName = $detail->variantValue->attribute->name;
                                    $attrValue = $detail->variantValue->value;
                                    $attrPrice = $detail->variantValue->price_adjustment;
                                    $attrPriceType = $detail->variantValue->price_type ?? 'fixed';
                                    
                                    // Find existing attribute or create new one
                                    $attrIndex = null;
                                    foreach ($attributesData as $index => $attr) {
                                        if ($attr['name'] === $attrName) {
                                            $attrIndex = $index;
                                            break;
                                        }
                                    }
                                    
                                    if ($attrIndex === null) {
                                        $attributesData[] = [
                                            'name' => $attrName,
                                            'values' => [[
                                                'id' => $detail->variantValue->id,
                                                'value' => $attrValue,
                                                'price_adjustment' => $attrPrice,
                                                'price_type' => $attrPriceType
                                            ]]
                                        ];
                                    } else {
                                        // Check if value already exists
                                        $valueExists = false;
                                        foreach ($attributesData[$attrIndex]['values'] as $val) {
                                            if ($val['value'] === $attrValue) {
                                                $valueExists = true;
                                                break;
                                            }
                                        }
                                        if (!$valueExists) {
                                            $attributesData[$attrIndex]['values'][] = [
                                                'id' => $detail->variantValue->id,
                                                'value' => $attrValue,
                                                'price_adjustment' => $attrPrice,
                                                'price_type' => $attrPriceType
                                            ];
                                        }
                                    }
                                }
                            }
                            $attributes = $attributesData;
                        }
                    @endphp

                    @forelse ($attributes as $attrIndex => $attribute)
                        <div class="p-4 border border-gray-200 rounded-md mb-4 bg-gray-50 attribute-group">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-md font-semibold text-gray-800">Thuộc tính {{ $attrIndex + 1 }}</h3>
                                <button type="button" class="remove-attribute-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa thuộc tính</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Cột trái: Tên thuộc tính (chiếm 1 phần) -->
                                <div class="md:col-span-1">
                                    <label for="attribute_name_{{ $attrIndex }}" class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                                    <input type="text" id="attribute_name_{{ $attrIndex }}" name="attributes[{{ $attrIndex }}][name]" placeholder="VD: Kích thước"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $attribute['name'] ?? '' }}">
                                    @error("attributes.{$attrIndex}.name")
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Cột phải: Giá trị thuộc tính (chiếm 2 phần) -->
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Giá trị thuộc tính</h4>
                                    <div id="attribute_values_container_{{ $attrIndex }}" class="space-y-2">
                                        @php
                                            $values = $attribute['values'] ?? [];
                                            if (empty($values)) {
                                                $values = [[]];
                                            }
                                        @endphp
                                        
                                        @foreach ($values as $valueIndex => $value)
                                            <div class="attribute-value-item p-2 border border-dashed border-gray-300 rounded-md bg-gray-50">
                                                <!-- Hidden input for VariantValue ID -->
                                                @if(isset($value['id']))
                                                    <input type="hidden" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][id]" value="{{ $value['id'] }}">
                                                @endif
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label for="attribute_value_{{ $attrIndex }}_{{ $valueIndex }}" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                                        <input type="text" id="attribute_value_{{ $attrIndex }}_{{ $valueIndex }}" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][value]" placeholder="VD: Nhỏ"
                                                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs" value="{{ $value['value'] ?? '' }}">
                                                        @error("attributes.{$attrIndex}.values.{$valueIndex}.value")
                                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label for="attribute_price_{{ $attrIndex }}_{{ $valueIndex }}" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                                        <input type="number" id="attribute_price_{{ $attrIndex }}_{{ $valueIndex }}" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][price_adjustment]" placeholder="0" step="any"
                                                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs" value="{{ $value['price_adjustment'] ?? 0 }}">
                                                        @error("attributes.{$attrIndex}.values.{$valueIndex}.price_adjustment")
                                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end col-start-2">Xóa</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-attribute-value-btn mt-2 text-xs text-blue-600 hover:text-blue-800" data-index="{{ $attrIndex }}">+ Thêm giá trị</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Default attribute group when no attributes exist -->
                        <div class="p-4 border border-gray-200 rounded-md mb-4 bg-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-md font-semibold text-gray-800">Thuộc tính 1</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Cột trái: Tên thuộc tính (chiếm 1 phần) -->
                                <div class="md:col-span-1">
                                    <label for="attribute_name_0" class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                                    <input type="text" id="attribute_name_0" name="attributes[0][name]" placeholder="VD: Kích thước"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ old('attributes.0.name') }}">
                                    @error('attributes.0.name')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Cột phải: Giá trị thuộc tính (chiếm 2 phần) -->
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Giá trị thuộc tính</h4>
                                    <div id="attribute_values_container_0" class="space-y-2">
                                        <!-- Default attribute value -->
                                        <div class="p-2 border border-dashed border-gray-300 rounded-md bg-gray-50">
                                             <div class="grid grid-cols-2 gap-2">
                                                 <div>
                                                     <label for="attribute_value_0_0" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                                     <input type="text" id="attribute_value_0_0" name="attributes[0][values][0][value]" placeholder="VD: Nhỏ"
                                         class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs" value="{{ old('attributes.0.values.0.value') }}">
                                                     @error('attributes.0.values.0.value')
                                                         <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                     @enderror
                                                 </div>
                                                 <div>
                                                     <label for="attribute_price_0_0" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                                     <input type="number" id="attribute_price_0_0" name="attributes[0][values][0][price_adjustment]" placeholder="0" step="any"
                                         class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs" value="{{ old('attributes.0.values.0.price_adjustment') }}">
                                                     @error('attributes.0.values.0.price_adjustment')
                                                         <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                     @enderror
                                                 </div>
                                             </div>
                                         </div>
                                    </div>
                                    <button type="button" class="add-attribute-value-btn mt-2 text-xs text-blue-600 hover:text-blue-800" data-index="0">+ Thêm giá trị</button>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                @error('attributes')
                    <div class="text-red-500 text-xs mt-2 mb-2">{{ $message }}</div>
                @enderror
                <div class="flex justify-end mt-4">
                    <button type="button" id="add-attribute-btn"
                        class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current" width="16" height="16"
                            fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Thêm thuộc tính
                    </button>
                </div>
            </div>
        </section>
        <!-- Toppings Section -->
        <section id="toppings-section" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Toppings</h2>
                        <p class="text-gray-500 text-sm mt-1">Chọn các topping có sẵn cho sản phẩm</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        Đã chọn: <span id="selected-toppings-count" class="font-semibold text-blue-600">{{ count($product->toppings) }}</span> topping
                    </div>
                </div>
            </header>

            <div class="px-6 py-6">
                <!-- Open Modal Button -->
                <div class="mb-4 flex justify-end">
                    <button type="button" id="open-toppings-modal" 
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <i class="fas fa-plus-circle"></i>
                        Chọn Toppings
                    </button>
                </div>

                <!-- Selected Toppings Display -->
                <div id="selected-toppings-display" class="space-y-2">
                    <h4 class="text-sm font-medium text-gray-700">Toppings đã chọn:</h4>
                    <div id="selected-toppings-tags" class="flex flex-wrap gap-2">
                        <!-- Selected toppings will be displayed here as tags -->
                        @if(count($product->toppings) == 0)
                            <div class="text-gray-500 text-sm italic" id="no-toppings-message">
                                Chưa có topping nào được chọn
                            </div>
                        @endif
                    </div>

                </div>
                <!-- Hidden input for selected toppings -->
                <input type="hidden" id="selected_toppings" name="selected_toppings" value="{{ json_encode($product->toppings->pluck('id')) }}">

            </div>
        </section>

        <!-- Toppings Modal - Simplified -->
        <div id="toppings-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50" aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                Chọn Toppings
                            </h3>
                            <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        <!-- Search -->
                        <div class="mb-4">
                            <input type="text" id="topping-search" placeholder="Tìm kiếm topping..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Control buttons -->
                        <div class="mb-4 flex gap-2">
                            <button type="button" id="modal-select-all" 
                                    class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                Chọn tất cả
                            </button>
                            <button type="button" id="modal-clear-all" 
                                    class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700">
                                Bỏ chọn tất cả
                            </button>
                        </div>

                        <!-- Toppings Grid -->
                        <div id="modal-toppings-list" class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                             <!-- Toppings will be loaded here -->
                             <div class="col-span-full text-center py-8 text-gray-500">
                                 Đang tải...
                             </div>
                         </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t flex justify-end gap-3">
                        <button type="button" id="cancel-modal" 
                                class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                            Hủy
                        </button>
                        <button type="button" id="confirm-toppings" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Xác nhận (<span id="modal-selected-count">0</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Management Section -->
        <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Quản lý tồn kho</h2>
                <p class="text-gray-500 text-sm mt-1">Quản lý số lượng sản phẩm theo chi nhánh</p>
            </header>

            <div class="px-6 py-6">
                <!-- Tab Navigation -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button type="button" id="tab-variants" class="tab-button active px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600" onclick="switchTab('variants')">
                        <i data-lucide="package" class="w-4 h-4 inline mr-2"></i>
                        Biến thể sản phẩm ({{ $product->variants->count() }})
                    </button>
                </div>

                <!-- Variants Tab Content -->
                <div id="content-variants" class="tab-content active">
                    <!-- Branch Filter Controls -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Lọc chi nhánh</h3>
                            <div class="text-sm text-gray-600">
                                Đã chọn: <span id="selected-count">3</span>/<span id="total-count">5</span> chi nhánh
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-4">
                            @foreach($branches as $branch)
                            <label class="flex items-center space-x-2 p-2 border border-gray-200 rounded-md hover:bg-white cursor-pointer">
                                <input type="checkbox" class="branch-checkbox" data-branch-id="{{ $branch->id }}" {{ $loop->index < 3 ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $branch->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="button" id="toggle-all-branches" class="text-sm text-blue-600 hover:text-blue-800" onclick="toggleAllBranches()">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Hiện tất cả chi nhánh
                            </button>
                            <button type="button" class="text-sm text-gray-600 hover:text-gray-800" onclick="toggleInactive()">
                                <i data-lucide="eye-off" class="w-4 h-4 inline mr-1"></i>
                                Hiện tạm dừng
                            </button>
                        </div>
                    </div>

                    <!-- Variants Stock Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 min-w-64">Biến thể</th>
                                    <th class="text-center py-3 px-4 font-medium text-gray-900 min-w-24">Giá bán</th>
                                    @foreach($branches as $branch)
                                    <th class="text-center py-3 px-4 font-medium text-gray-900 min-w-36" data-branch-id="{{ $branch->id }}" {{ $loop->index >= 3 ? 'style=display:none;' : '' }}>
                                        <div class="flex items-center justify-center gap-2">
                                            <i data-lucide="store" class="w-4 h-4"></i>
                                            <div>
                                                <div class="font-medium">{{ $branch->name }}</div>
                                            </div>
                                        </div>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @if($product->variants && $product->variants->count() > 0)
                                    @foreach($product->variants as $variant)
                                    <tr class="border-b border-gray-100 {{ !$variant->active ? 'inactive-variant hidden' : '' }}">
                                        <td class="sticky-column py-4 px-4 border-r border-gray-200">
                                            <div class="flex items-start gap-3">
                                                <div class="space-y-2">
                                                    <div class="font-medium">
                                                        @if($variant->variantValues && $variant->variantValues->count() > 0)
                                                            {{ $variant->variantValues->map(function($value) { return $value->attribute->name . ': ' . $value->value; })->implode(' • ') }}
                                                        @else
                                                            Biến thể mặc định
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-wrap gap-1">
                                                        @if($variant->variantValues && $variant->variantValues->count() > 0)
                                                            @foreach($variant->variantValues as $value)
                                                                <span class="badge-outline px-2 py-1 rounded text-xs">{{ $value->attribute->name }}: {{ $value->value }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="badge-outline px-2 py-1 rounded text-xs">Biến thể mặc định</span>
                                                        @endif
                                                    </div>
                                                    <span class="badge-{{ $variant->active ? 'success' : 'secondary' }} px-2 py-1 rounded text-xs">
                                                         {{ $variant->active ? 'Hoạt động' : 'Tạm dừng' }}
                                                     </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4 font-medium">
                                            @php
                                                $variantPrice = $product->base_price;
                                                if ($variant->variantValues && $variant->variantValues->count() > 0) {
                                                    $variantPrice += $variant->variantValues->sum('price_adjustment');
                                                }
                                            @endphp
                                            {{ number_format($variantPrice, 0, ',', '.') }}₫
                                        </td>
                                        @foreach($branches as $branch)
                                        @php
                                            $quantity = isset($branchStocks[$branch->id][$variant->id]) ? $branchStocks[$branch->id][$variant->id] : 0;
                                        @endphp
                                        <td class="text-center py-4 px-4" data-branch-id="{{ $branch->id }}" {{ $loop->index >= 3 ? 'style=display:none;' : '' }}>
                                            <div class="space-y-2">
                                                <input type="number" min="0" value="{{ $quantity }}" 
                                                    name="variant_stocks[{{ $variant->id }}][{{ $branch->id }}]" 
                                                    class="w-20 text-center mx-auto px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                    onchange="updateStockStatus(this)">
                                                <span class="badge-{{ $quantity > 10 ? 'default' : ($quantity > 0 ? 'secondary' : 'destructive') }} px-2 py-1 rounded text-xs">
                                                    {{ $quantity > 10 ? 'Còn hàng' : ($quantity > 0 ? 'Sắp hết' : 'Hết hàng') }}
                                                </span>
                                            </div>
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ 2 + $branches->count() }}" class="text-center py-8 text-gray-500">
                                            Chưa có biến thể nào được tạo
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </section>

        <!-- Save Buttons -->
        <div class="p-4 flex justify-end gap-2 mt-6">
            <button type="submit" id="save-product-btn"
                class="fixed bottom-4 right-4 rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700 shadow-lg z-50">
                Cập nhật sản phẩm
            </button>
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
        
        // Handle change primary image button
        const changePrimaryImageBtn = document.getElementById('change-primary-image-btn');
        if (changePrimaryImageBtn) {
            changePrimaryImageBtn.addEventListener('click', () => {
                primaryImageUpload.click();
            });
        }

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

        // Attributes Logic - Simplified with Event Delegation
        const attributesContainer = document.getElementById('attributes-container');
        const addAttributeBtn = document.getElementById('add-attribute-btn');
        let attributeCount = document.querySelectorAll('.attribute-group').length;

        function createAttributeGroup(index) {
            const group = document.createElement('div');
            group.classList.add('p-4', 'border', 'border-gray-200', 'rounded-md', 'mb-4', 'bg-gray-50', 'attribute-group');
            group.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-md font-semibold text-gray-800">Thuộc tính ${index + 1}</h3>
                    <button type="button" class="remove-attribute-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa thuộc tính</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label for="attribute_name_${index}" class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                        <input type="text" id="attribute_name_${index}" name="attributes[${index}][name]" placeholder="VD: Kích thước"
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_name" style="display: none;"></div>
                    </div>
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Giá trị thuộc tính</h4>
                        <div id="attribute_values_container_${index}" class="space-y-3">
                            <!-- Default attribute value -->
                            <div class="grid grid-cols-2 gap-2 p-2 border border-dashed border-gray-300 rounded-md bg-gray-50">
                                <div>
                                    <label for="attribute_value_${index}_0" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                    <input type="text" id="attribute_value_${index}_0" name="attributes[${index}][values][0][value]" placeholder="VD: Nhỏ"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_0_value" style="display: none;"></div>
                                </div>
                                <div>
                                    <label for="attribute_price_${index}_0" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                    <input type="number" id="attribute_price_${index}_0" name="attributes[${index}][values][0][price_adjustment]" placeholder="0" step="any"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_0_price_adjustment" style="display: none;"></div>
                                </div>
                                <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end col-start-2">Xóa</button>
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
                document.querySelectorAll('.attribute-group').forEach((group, index) => {
                    const title = group.querySelector('h3');
                    if (title) {
                        title.textContent = `Thuộc tính ${index + 1}`;
                    }
                });
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

        // Function to reindex all attribute groups after deletion
        function reindexAttributeGroups() {
            const attributeGroups = document.querySelectorAll('.attribute-group');
            
            attributeGroups.forEach((group, groupIndex) => {
                // Update group title
                const title = group.querySelector('h3');
                if (title) {
                    title.textContent = `Thuộc tính ${groupIndex + 1}`;
                }
                
                // Update attribute name input
                const nameInput = group.querySelector('input[name*="[name]"]');
                if (nameInput) {
                    const newName = `attributes[${groupIndex}][name]`;
                    const newId = `attribute_name_${groupIndex}`;
                    nameInput.setAttribute('name', newName);
                    nameInput.setAttribute('id', newId);
                    
                    // Update corresponding label
                    const label = group.querySelector(`label[for*="attribute_name"]`);
                    if (label) {
                        label.setAttribute('for', newId);
                    }
                }
                
                // Update values container ID
                const valuesContainer = group.querySelector('[id*="attribute_values_container"]');
                if (valuesContainer) {
                    valuesContainer.setAttribute('id', `attribute_values_container_${groupIndex}`);
                }
                
                // Update add value button data-index
                const addValueBtn = group.querySelector('.add-attribute-value-btn');
                if (addValueBtn) {
                    addValueBtn.setAttribute('data-index', groupIndex);
                }
                
                // Reindex all attribute values in this group
                reindexAttributeValues(group, groupIndex);
            });
        }
        
        // Function to reindex attribute values after deletion
        function reindexAttributeValues(attributeGroup, groupIndex = null) {
            if (groupIndex === null) {
                groupIndex = Array.from(attributeGroup.parentNode.children).indexOf(attributeGroup);
            }
            
            const valueContainers = attributeGroup.querySelectorAll('.attribute-value-item');
            
            valueContainers.forEach((container, valueIndex) => {
                // Update all input names and IDs
                const inputs = container.querySelectorAll('input');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    const id = input.getAttribute('id');
                    
                    if (name) {
                        // Update both group index and value index in the name attribute
                        const newName = name.replace(/attributes\[\d+\]\[values\]\[\d+\]/, `attributes[${groupIndex}][values][${valueIndex}]`);
                        input.setAttribute('name', newName);
                    }
                    
                    if (id) {
                        // Update both group index and value index in the id attribute
                        const newId = id.replace(/_(\d+)_(\d+)$/, `_${groupIndex}_${valueIndex}`);
                        input.setAttribute('id', newId);
                    }
                });
                
                // Update labels
                const labels = container.querySelectorAll('label');
                labels.forEach(label => {
                    const forAttr = label.getAttribute('for');
                    if (forAttr) {
                        const newFor = forAttr.replace(/_(\d+)_(\d+)$/, `_${groupIndex}_${valueIndex}`);
                        label.setAttribute('for', newFor);
                    }
                });
                
                // Update error message divs
                const errorDivs = container.querySelectorAll('.error-message');
                errorDivs.forEach(div => {
                    const id = div.getAttribute('id');
                    if (id) {
                        const newId = id.replace(/_(\d+)_values_(\d+)_/, `_${groupIndex}_values_${valueIndex}_`);
                        div.setAttribute('id', newId);
                    }
                });
            });
        }
        
        function addAttributeValue(attributeIndex) {
            const valuesContainer = document.getElementById(`attribute_values_container_${attributeIndex}`);
            const existingValues = valuesContainer.querySelectorAll('.grid');
            const valueIndex = existingValues.length;

            const valueDiv = document.createElement('div');
            valueDiv.classList.add('attribute-value-item', 'grid', 'grid-cols-2', 'gap-2', 'p-2', 'border', 'border-dashed', 'border-gray-300', 'rounded-md', 'bg-gray-50');
            valueDiv.innerHTML = `
                <div>
                    <label for="attribute_value_${attributeIndex}_${valueIndex}" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                    <input type="text" id="attribute_value_${attributeIndex}_${valueIndex}" name="attributes[${attributeIndex}][values][${valueIndex}][value]" placeholder="VD: Nhỏ"
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${attributeIndex}_values_${valueIndex}_value" style="display: none;"></div>
                </div>
                <div>
                    <label for="attribute_price_${attributeIndex}_${valueIndex}" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                    <input type="number" id="attribute_price_${attributeIndex}_${valueIndex}" name="attributes[${attributeIndex}][values][${valueIndex}][price_adjustment]" placeholder="0" step="any"
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs">
                    <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${attributeIndex}_values_${valueIndex}_price_adjustment" style="display: none;"></div>
                </div>
                <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end col-start-2">Xóa</button>
            `;
            
            // Add event listener for removing this value
            const removeBtn = valueDiv.querySelector('.remove-attribute-value-btn');
            removeBtn.addEventListener('click', () => {
                const valueContainer = removeBtn.closest('.attribute-value-item');
                if (valueContainer) {
                    valueContainer.remove();
                }
            });
            
            valuesContainer.appendChild(valueDiv);
        }

        // Event delegation for all dynamic elements
        document.addEventListener('click', function(e) {
            // Remove attribute group
            if (e.target.classList.contains('remove-attribute-btn') || e.target.classList.contains('remove-attribute-group-btn')) {
                e.target.closest('.attribute-group').remove();
                // Reindex all remaining attribute groups
                reindexAttributeGroups();
            }
            
            // Add attribute value
            if (e.target.classList.contains('add-attribute-value-btn') || e.target.closest('.add-attribute-value-btn')) {
                const btn = e.target.classList.contains('add-attribute-value-btn') ? e.target : e.target.closest('.add-attribute-value-btn');
                const attributeIndex = btn.getAttribute('data-index');
                if (attributeIndex !== null) {
                    addAttributeValue(parseInt(attributeIndex));
                }
            }
            
            // Remove attribute value
            if (e.target.classList.contains('remove-attribute-value-btn') || e.target.closest('.remove-attribute-value-btn')) {
                // Find the parent div that contains the value inputs
                const valueContainer = e.target.closest('.attribute-value-item');
                if (valueContainer) {
                    const attributeGroup = valueContainer.closest('.attribute-group');
                    valueContainer.remove();
                    // Reindex the remaining attribute values in this group
                    reindexAttributeValues(attributeGroup);
                }
            }
            
            // Remove topping
            if (e.target.classList.contains('remove-topping-btn')) {
                e.target.closest('.topping-group').remove();
            }
        });
        
        // Add event listeners to existing attribute groups
        document.querySelectorAll('.attribute-group .remove-attribute-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.attribute-group').remove();
                document.querySelectorAll('.attribute-group').forEach((group, index) => {
                    const title = group.querySelector('h3');
                    if (title) {
                        title.textContent = `Thuộc tính ${index + 1}`;
                    }
                });
            });
        });
        
        // Add event listeners to existing attribute value remove buttons
        document.querySelectorAll('.remove-attribute-value-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const valueContainer = btn.closest('.attribute-value-item');
                if (valueContainer) {
                    valueContainer.remove();
                }
            });
        });

        // Add new attribute group
        if (addAttributeBtn) {
            addAttributeBtn.addEventListener('click', () => {
                const attributeGroup = createAttributeGroup(attributeCount);
                attributesContainer.appendChild(attributeGroup);
                attributeCount++;
            });
        }

        // Toppings Logic
        const toppingsContainer = document.getElementById('toppings-container');
        const addToppingBtn = document.getElementById('add-topping-btn');
        let toppingCount = 0;

        function createToppingGroup(index) {
            const group = document.createElement('div');
            group.classList.add('p-4', 'border', 'border-gray-200', 'rounded-md', 'mb-4', 'bg-gray-50', 'topping-group');
            group.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-md font-semibold text-gray-800">Topping ${index + 1}</h3>
                    <button type="button" class="remove-topping-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa topping</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <div>
                        <label for="topping_name_${index}" class="block text-sm font-medium text-gray-700">Tên topping</label>
                        <input type="text" id="topping_name_${index}" name="toppings[${index}][name]" placeholder="VD: Trân châu đen"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2">
                    </div>
                    <div>
                        <label for="topping_price_${index}" class="block text-sm font-medium text-gray-700">Giá</label>
                        <input type="number" id="topping_price_${index}" name="toppings[${index}][price]" min="0" placeholder="0"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2">
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
                        <div id="topping_image_preview_container_${index}" class="mt-2 w-24 h-24 border border-gray-300 rounded-md overflow-hidden flex items-center justify-center bg-gray-100">
                            <img id="topping_image_preview_${index}" src="#" alt="Preview" class="hidden w-full h-full object-cover">
                            <span id="topping_image_placeholder_${index}" class="text-xs text-gray-400">Xem trước</span>
                        </div>
                    </div>
                </div>
            `;
            return group;
        }

        // Add topping button event
        if (addToppingBtn) {
            addToppingBtn.addEventListener('click', () => {
                const toppingGroup = createToppingGroup(toppingCount);
                toppingsContainer.appendChild(toppingGroup);
                toppingCount++;
            });
        }

        // Handle topping image preview with event delegation
        document.addEventListener('change', function(e) {
            if (e.target.type === 'file' && e.target.name && e.target.name.includes('toppings') && e.target.name.includes('image')) {
                const index = e.target.id.split('_').pop();
                const imagePreview = document.getElementById(`topping_image_preview_${index}`);
                const imagePlaceholder = document.getElementById(`topping_image_placeholder_${index}`);
                
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imagePreview.src = event.target.result;
                        imagePreview.classList.remove('hidden');
                        imagePlaceholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '#';
                    imagePreview.classList.add('hidden');
                    imagePlaceholder.classList.remove('hidden');
                }
            }
        });

        // Initialize counters based on existing data
        toppingCount = document.querySelectorAll('.topping-group').length;
        attributeCount = document.querySelectorAll('.attribute-group').length;

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

        // Handle removing existing images
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-existing-image')) {
                e.preventDefault();
                const imageContainer = e.target.closest('.relative');
                const hiddenInput = imageContainer.querySelector('input[name="existing_images[]"]');
                
                // Create a hidden input to mark this image for deletion
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'deleted_images[]';
                deleteInput.value = e.target.dataset.imageId;
                document.getElementById('edit-product-form').appendChild(deleteInput);
                
                // Remove the image container
                imageContainer.remove();
            }
        });

        // Ingredients Format Toggle Logic
        const formatRadios = document.querySelectorAll('input[name="ingredients_format"]');
        const simpleFormat = document.getElementById('simple-ingredients');
        const structuredFormat = document.getElementById('structured-ingredients');
        const categoryContainer = document.querySelector('#structured-ingredients .space-y-4');
        
        // Toggle between simple and structured format
        formatRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'simple') {
                    simpleFormat.classList.remove('hidden');
                    structuredFormat.classList.add('hidden');
                } else {
                    simpleFormat.classList.add('hidden');
                    structuredFormat.classList.remove('hidden');
                }
            });
        });
        
        // Add category functionality
        document.getElementById('add-category').addEventListener('click', function() {
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'ingredient-category';
            categoryDiv.innerHTML = `
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" placeholder="Tên danh mục (ví dụ: thịt)" 
                           class="category-name flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <button type="button" class="remove-category px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Xóa</button>
                </div>
                <textarea placeholder="Nhập các nguyên liệu trong danh mục này, mỗi nguyên liệu một dòng" 
                          class="category-items w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                          rows="3"></textarea>
            `;
            categoryContainer.appendChild(categoryDiv);
        });
        
        // Remove category functionality (event delegation)
        categoryContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-category')) {
                e.target.closest('.ingredient-category').remove();
            }
        });

        // Form Submission Logic
        const editProductForm = document.getElementById('edit-product-form');
        editProductForm.addEventListener('submit', function(e) {
            // Process ingredients based on selected format
            const selectedFormat = document.querySelector('input[name="ingredients_format"]:checked').value;
            let ingredientsData;
            
            if (selectedFormat === 'simple') {
                // Simple format: convert comma-separated text to array
                const ingredientsText = document.getElementById('ingredients').value;
                ingredientsData = ingredientsText.split(',').map(item => item.trim()).filter(item => item);
            } else {
                // Structured format: convert categories to object
                ingredientsData = {};
                const categories = document.querySelectorAll('.ingredient-category');
                categories.forEach(category => {
                    const categoryName = category.querySelector('.category-name').value.trim();
                    const categoryItems = category.querySelector('.category-items').value
                        .split('\n')
                        .map(item => item.trim())
                        .filter(item => item);
                    
                    if (categoryName && categoryItems.length > 0) {
                        ingredientsData[categoryName] = categoryItems;
                    }
                });
            }
            
            // Create hidden input for processed ingredients
            const ingredientsInput = document.createElement('input');
            ingredientsInput.type = 'hidden';
            ingredientsInput.name = 'ingredients_json';
            ingredientsInput.value = JSON.stringify(ingredientsData);
            editProductForm.appendChild(ingredientsInput);

            // Ensure description is always sent (even if empty)
            const description = document.getElementById('description');
            if (!description.value) description.value = '';
            
            // Collect and submit variant stock data
            const variantStockInputs = document.querySelectorAll('input[name^="variant_stocks["]');
            const variantStocks = {};
            
            // Remove any existing hidden variant stock inputs
            const existingHiddenInputs = editProductForm.querySelectorAll('input[name^="variant_stocks["][type="hidden"]');
            existingHiddenInputs.forEach(input => input.remove());
            
            variantStockInputs.forEach(input => {
                const value = input.value && input.value.trim() !== '' ? parseInt(input.value) || 0 : 0;
                const matches = input.name.match(/variant_stocks\[(\d+)\]\[(\d+)\]/);
                if (matches) {
                    const variantId = matches[1];
                    const branchId = matches[2];
                    
                    // Create hidden input for each variant stock
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `variant_stocks[${variantId}][${branchId}]`;
                    hiddenInput.value = value;
                    editProductForm.appendChild(hiddenInput);
                    
                    if (!variantStocks[variantId]) {
                        variantStocks[variantId] = {};
                    }
                    variantStocks[variantId][branchId] = value;
                }
            });
            
            console.log('Variant Stocks being submitted:', variantStocks);
        });

        // Handle status and release date visibility
        const statusInputs = document.querySelectorAll('input[name="status"]');
        const releaseAtContainer = document.getElementById('release_at_container');

        function toggleReleaseDate() {
            const selectedStatus = document.querySelector('input[name="status"]:checked');
            if (selectedStatus) {
                if (selectedStatus.value === 'coming_soon') {
                    releaseAtContainer.classList.remove('hidden');
                } else {
                    releaseAtContainer.classList.add('hidden');
                }
            }
        }

        statusInputs.forEach(input => {
            input.addEventListener('change', toggleReleaseDate);
        });

        // Initial check
        toggleReleaseDate();

        // Inventory Management JavaScript
        // Tab switching functionality
        window.switchTab = function(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
                button.classList.add('text-gray-500', 'border-transparent');
                button.classList.remove('text-blue-600', 'border-blue-600');
            });
            
            // Show selected tab content
            const selectedContent = document.getElementById(`content-${tabName}`);
            if (selectedContent) {
                selectedContent.classList.add('active');
            }
            
            // Add active class to selected tab button
            const selectedTab = document.getElementById(`tab-${tabName}`);
            if (selectedTab) {
                selectedTab.classList.add('active', 'text-blue-600', 'border-blue-600');
                selectedTab.classList.remove('text-gray-500', 'border-transparent');
            }
        };

        // Branch management
        let showAllBranches = false;

        window.toggleAllBranches = function() {
            showAllBranches = !showAllBranches;
            const button = document.getElementById('toggle-all-branches');
            const checkboxes = document.querySelectorAll('.branch-checkbox');
            
            if (showAllBranches) {
                // Check all branches
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                if (button) {
                    button.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4 inline mr-1"></i> Ẩn bớt chi nhánh';
                }
            } else {
                // Uncheck all branches except first 3
                checkboxes.forEach((checkbox, index) => {
                    checkbox.checked = index < 3;
                });
                if (button) {
                    button.innerHTML = '<i data-lucide="eye" class="w-4 h-4 inline mr-1"></i> Hiện tất cả chi nhánh';
                }
            }
            
            updateBranchDisplay();
        };

        function updateBranchDisplay() {
            const checkboxes = document.querySelectorAll('.branch-checkbox');
            const selectedCount = document.querySelectorAll('.branch-checkbox:checked').length;
            const totalCount = checkboxes.length;
            
            // Update counter
            const selectedCountEl = document.getElementById('selected-count');
            const totalCountEl = document.getElementById('total-count');
            if (selectedCountEl) selectedCountEl.textContent = selectedCount;
            if (totalCountEl) totalCountEl.textContent = totalCount;
            
            // Show/hide branch columns
            checkboxes.forEach(checkbox => {
                const branchId = checkbox.dataset.branchId;
                const columns = document.querySelectorAll(`[data-branch-id="${branchId}"]`);
                
                columns.forEach(column => {
                    if (checkbox.checked) {
                        column.style.display = '';
                    } else {
                        column.style.display = 'none';
                    }
                });
            });
        }

        // Add event listeners to branch checkboxes
        const checkboxes = document.querySelectorAll('.branch-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBranchDisplay);
        });
        
        // Initialize display
         updateBranchDisplay();

         // Initialize Lucide icons
         if (typeof lucide !== 'undefined') {
             lucide.createIcons();
         }

        // Toggle inactive variants visibility
        let showInactive = false;
        window.toggleInactive = function() {
            showInactive = !showInactive;
            const inactiveVariants = document.querySelectorAll('.inactive-variant');
            const button = event.target.closest('button');
            
            if (showInactive) {
                inactiveVariants.forEach(variant => variant.classList.remove('hidden'));
                if (button) {
                    button.innerHTML = '<i data-lucide="eye" class="w-4 h-4 inline mr-1"></i> Ẩn tạm dừng';
                }
            } else {
                inactiveVariants.forEach(variant => variant.classList.add('hidden'));
                if (button) {
                    button.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4 inline mr-1"></i> Hiện tạm dừng';
                }
            }
        };

        // Update stock status based on quantity
        window.updateStockStatus = function(input) {
            const quantity = parseInt(input.value) || 0;
            const badge = input.parentElement.querySelector('.badge-default, .badge-secondary, .badge-success, .badge-destructive');
            
            if (badge) {
                if (quantity === 0) {
                    badge.className = 'badge-destructive px-2 py-1 rounded text-xs';
                    badge.textContent = 'Hết hàng';
                } else if (quantity < 10) {
                    badge.className = 'badge-secondary px-2 py-1 rounded text-xs';
                    badge.textContent = 'Sắp hết';
                } else {
                    badge.className = 'badge-success px-2 py-1 rounded text-xs';
                    badge.textContent = 'Còn hàng';
                }
            }
        };
    });
</script>

<!-- Topping Modal Script -->
<script src="{{ asset('js/admin/topping-modal.js') }}"></script>

<!-- Initialize Existing Toppings -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Đợi cho toppingModal được khởi tạo
        setTimeout(() => {
            if (window.toppingModal) {
                // Lấy danh sách topping đã chọn từ PHP
                const existingToppings = @json($product->toppings);
                
                // Khởi tạo các topping đã chọn trong modal
                if (existingToppings && existingToppings.length > 0) {
                    window.toppingModal.setSelectedToppings(existingToppings);
                }
            }
        }, 300);
    });
</script>

<style>
/* Tab styles */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.tab-button {
    transition: all 0.2s ease;
}

.tab-button:hover {
    color: #374151;
    border-color: #d1d5db;
}

.tab-button.active {
    color: #2563eb;
    border-color: #2563eb;
}

/* Badge styles */
.badge-default {
    background-color: #f3f4f6;
    color: #374151;
}

.badge-secondary {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-success {
    background-color: #d1fae5;
    color: #065f46;
}

.badge-destructive {
    background-color: #fee2e2;
    color: #dc2626;
}

/* Stock input styles */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

/* Table responsive styles */
.overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f9fafb;
}

.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f9fafb;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Line clamp utilities */
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

/* Aspect ratio utility */
.aspect-square {
    aspect-ratio: 1 / 1;
}

/* Topping grid item styles */
.topping-item {
    min-height: 180px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    border-radius: 8px;
    padding: 8px;
}

.topping-item:hover {
    transform: translateY(-2px);
    border-color: #e5e7eb;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.topping-item.selected {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.selection-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-color: #fff;
    border: 2px solid #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.topping-item.selected .selection-indicator {
    opacity: 1;
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.topping-item:hover .selection-indicator {
    opacity: 1;
}

/* No image placeholder */
        .no-image-placeholder {
            background-color: #F3F4F6;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            width: 100%;
            height: 100%;
        }

.no-image-placeholder i {
    color: #9CA3AF;
    font-size: 1.5rem;
}
</style>
@endsection
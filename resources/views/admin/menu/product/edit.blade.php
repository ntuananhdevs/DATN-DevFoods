@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')

@section('page-style-prd-edit')
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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
                    
                        @if(!empty($product->ingredients))
                            @php
                                $ingredients = is_string($product->ingredients) ? json_decode($product->ingredients, true) : $product->ingredients;
                                // Chuyển tất cả nguyên liệu thành chuỗi để hiển thị trong một textarea
                                $allIngredients = [];
                                foreach ($ingredients as $category => $items) {
                                    if (is_array($items)) {
                                        $allIngredients[] = implode(', ', (array)$items);
                                    } else {
                                        $allIngredients[] = $items;
                                    }
                                }
                                $ingredientsText = implode(', ', $allIngredients);
                            @endphp
                    
                            <div class="bg-gray-50 rounded-lg p-4">
                                <textarea id="ingredients" name="ingredients" class="w-full p-2 border-2 border-gray-300 rounded-lg px-3 py-2" rows="6">{{ $ingredientsText }}</textarea>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4">
                                <textarea id="ingredients" name="ingredients" class="w-full p-2 border-2 border-gray-300 rounded-lg px-3 py-2" rows="6" placeholder="Nhập nguyên liệu, phân cách bằng dấu phẩy (ví dụ: thịt bò, hành tây, ớt chuông)"></textarea>
                            </div>
                        @endif
                    
                        {{-- Hidden input để giữ dữ liệu nguyên liệu gốc --}}
                        <input type="hidden" name="ingredients_raw" value="{{ $product->ingredients }}">
                    
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
                                                'value' => $attrValue,
                                                'price_adjustment' => $attrPrice
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
                                                'value' => $attrValue,
                                                'price_adjustment' => $attrPrice
                                            ];
                                        }
                                    }
                                }
                            }
                            $attributes = $attributesData;
                        }
                        if (empty($attributes)) {
                            $attributes = [[]];
                        }
                    @endphp
                    
                    @foreach ($attributes as $attrIndex => $attribute)
                        <div class="p-4 border border-gray-200 rounded-md mb-4 bg-gray-50 attribute-group">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-md font-semibold text-gray-800">Thuộc tính {{ $attrIndex + 1 }}</h3>
                                @if ($attrIndex > 0)
                                    <button type="button" class="remove-attribute-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa thuộc tính</button>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="attribute_name_{{ $attrIndex }}" class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                                    <input type="text" id="attribute_name_{{ $attrIndex }}" name="attributes[{{ $attrIndex }}][name]" placeholder="VD: Kích thước"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2" value="{{ $attribute['name'] ?? '' }}">
                                    @error("attributes.{$attrIndex}.name")
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-span-2">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Giá trị thuộc tính</h4>
                                    <div id="attribute_values_container_{{ $attrIndex }}" class="space-y-3">
                                        @php
                                            $values = $attribute['values'] ?? [];
                                            if (empty($values)) {
                                                $values = [[]];
                                            }
                                        @endphp
                                        
                                        @foreach ($values as $valueIndex => $value)
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-dashed border-gray-300 rounded-md">
                                                <div class="md:col-span-2">
                                                    <label for="attribute_value_{{ $attrIndex }}_{{ $valueIndex }}" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                                    <input type="text" id="attribute_value_{{ $attrIndex }}_{{ $valueIndex }}" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][value]" placeholder="VD: Nhỏ"
                                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs px-3 py-2" value="{{ $value['value'] ?? '' }}">
                                                    @error("attributes.{$attrIndex}.values.{$valueIndex}.value")
                                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label for="attribute_price_{{ $attrIndex }}_{{ $valueIndex }}" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                                    <input type="number" id="attribute_price_{{ $attrIndex }}_{{ $valueIndex }}" name="attributes[{{ $attrIndex }}][values][{{ $valueIndex }}][price_adjustment]" placeholder="0" step="any"
                                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs px-3 py-2" value="{{ $value['price_adjustment'] ?? 0 }}">
                                                    @error("attributes.{$attrIndex}.values.{$valueIndex}.price_adjustment")
                                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @if ($valueIndex > 0 || count($values) > 1)
                                                    <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end md:col-start-3">Xóa</button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-attribute-value-btn mt-2 text-sm text-blue-600 hover:text-blue-800" data-index="{{ $attrIndex }}">+ Thêm giá trị</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                    @php
                        $toppings = old('toppings', $product->toppings->toArray());
                    @endphp
                    
                    @forelse ($toppings as $toppingIndex => $topping)
                        <div class="p-4 border border-gray-200 rounded-md mb-4 bg-gray-50 topping-group">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-md font-semibold text-gray-800">Topping {{ $toppingIndex + 1 }}</h3>
                                <button type="button" class="remove-topping-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa topping</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <div>
                                    <label for="topping_name_{{ $toppingIndex }}" class="block text-sm font-medium text-gray-700">Tên topping</label>
                                    <input type="text" id="topping_name_{{ $toppingIndex }}" name="toppings[{{ $toppingIndex }}][name]" placeholder="VD: Phô mai thêm"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2" value="{{ $topping['name'] ?? '' }}">
                                    @error("toppings.{$toppingIndex}.name")
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="topping_price_{{ $toppingIndex }}" class="block text-sm font-medium text-gray-700">Giá</label>
                                    <input type="number" id="topping_price_{{ $toppingIndex }}" name="toppings[{{ $toppingIndex }}][price]" placeholder="0" step="any"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2" value="{{ $topping['price'] ?? '' }}">
                                    @error("toppings.{$toppingIndex}.price")
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="flex items-center mt-7">
                                    <input type="checkbox" id="topping_available_{{ $toppingIndex }}" name="toppings[{{ $toppingIndex }}][available]" value="1"
                                        {{ ($topping['available'] ?? false) ? 'checked' : '' }}
                                        class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <label for="topping_available_{{ $toppingIndex }}" class="ml-2 text-sm text-gray-700">Có sẵn</label>
                                </div>
                                <div>
                                    <label for="topping_image_{{ $toppingIndex }}" class="block text-sm font-medium text-gray-700">Ảnh topping</label>
                                    <input type="file" id="topping_image_{{ $toppingIndex }}" name="toppings[{{ $toppingIndex }}][image]" accept="image/*"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <div id="topping_image_preview_container_{{ $toppingIndex }}" class="mt-2 w-24 h-24 border border-gray-300 rounded-md overflow-hidden flex items-center justify-center bg-gray-100">
                                        @if(isset($topping['image']) && $topping['image'])
                                            <img id="topping_image_preview_{{ $toppingIndex }}" src="{{ str_starts_with($topping['image'], 'http') ? $topping['image'] : asset('storage/' . $topping['image']) }}" alt="Preview" class="w-full h-full object-cover">
                                        @else
                                            <img id="topping_image_preview_{{ $toppingIndex }}" src="#" alt="Preview" class="hidden w-full h-full object-cover">
                                            <span id="topping_image_placeholder_{{ $toppingIndex }}" class="text-xs text-gray-400">Xem trước</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- No toppings message when empty -->
                    @endforelse
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
                    <button type="button" id="tab-toppings" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" onclick="switchTab('toppings')">
                        <i data-lucide="plus-circle" class="w-4 h-4 inline mr-2"></i>
                        Toppings ({{ $product->toppings->count() }})
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

                <!-- Toppings Tab Content -->
                <div id="content-toppings" class="tab-content">
                    <!-- Toppings Stock Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 min-w-64">Topping</th>
                                    <th class="text-center py-3 px-4 font-medium text-gray-900 min-w-24">Giá</th>
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
                                @if($product->toppings && $product->toppings->count() > 0)
                                    @foreach($product->toppings as $topping)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                @if($topping->image)
                                                <img src="{{ str_starts_with($topping->image, 'http') ? $topping->image : asset('storage/' . $topping->image) }}" alt="{{ $topping->name }}" class="w-10 h-10 rounded-md object-cover">
                                                @else
                                                <div class="w-10 h-10 rounded-md bg-gray-200 flex items-center justify-center">
                                                    <i data-lucide="image" class="w-5 h-5 text-gray-400"></i>
                                                </div>
                                                @endif
                                                <div>
                                                    <div class="font-medium">{{ $topping->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $topping->description ?? 'Không có mô tả' }}</div>
                                                    <span class="badge-{{ $topping->available ? 'default' : 'secondary' }} px-2 py-1 rounded text-xs">
                                                        {{ $topping->available ? 'Hoạt động' : 'Tạm dừng' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4 font-medium">{{ number_format($topping->price) }}₫</td>
                                        @foreach($branches as $branch)
                                        @php
                                            $toppingQuantity = isset($toppingStocks[$branch->id][$topping->id]) ? $toppingStocks[$branch->id][$topping->id] : 0;
                                        @endphp
                                        <td class="text-center py-4 px-4" data-branch-id="{{ $branch->id }}" {{ $loop->index >= 3 ? 'style=display:none;' : '' }}>
                                            <div class="space-y-2">
                                                <input type="number" min="0" value="{{ $toppingQuantity }}" 
                                                    name="topping_stocks[{{ $topping->id }}][{{ $branch->id }}]" 
                                                    class="w-20 text-center mx-auto px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                    onchange="updateStockStatus(this)">
                                                <span class="badge-{{ $toppingQuantity > 10 ? 'default' : ($toppingQuantity > 0 ? 'secondary' : 'destructive') }} px-2 py-1 rounded text-xs">
                                                    {{ $toppingQuantity > 10 ? 'Còn hàng' : ($toppingQuantity > 0 ? 'Sắp hết' : 'Hết hàng') }}
                                                </span>
                                            </div>
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ 2 + $branches->count() }}" class="text-center py-8 text-gray-500">
                                            Chưa có topping nào được tạo
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="attribute_name_${index}" class="block text-sm font-medium text-gray-700">Tên thuộc tính</label>
                        <input type="text" id="attribute_name_${index}" name="attributes[${index}][name]" placeholder="VD: Kích thước"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2">
                    </div>
                    <div class="col-span-2">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Giá trị thuộc tính</h4>
                        <div id="attribute_values_container_${index}" class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-dashed border-gray-300 rounded-md">
                                <div class="md:col-span-2">
                                    <label for="attribute_value_${index}_0" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                    <input type="text" id="attribute_value_${index}_0" name="attributes[${index}][values][0][value]" placeholder="VD: Nhỏ"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs px-3 py-2">
                                </div>
                                <div>
                                    <label for="attribute_price_${index}_0" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                    <input type="number" id="attribute_price_${index}_0" name="attributes[${index}][values][0][price_adjustment]" placeholder="0" step="any" value="0"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs px-3 py-2">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="add-attribute-value-btn mt-2 text-sm text-blue-600 hover:text-blue-800" data-index="${index}">+ Thêm giá trị</button>
                    </div>
                </div>
            `;
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
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs px-3 py-2">
                </div>
                <div>
                    <label for="attribute_price_${attributeIndex}_${valueIndex}" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                    <input type="number" id="attribute_price_${attributeIndex}_${valueIndex}" name="attributes[${attributeIndex}][values][${valueIndex}][price_adjustment]" placeholder="0" step="any" value="0"
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs px-3 py-2">
                </div>
                <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end md:col-start-3">Xóa</button>
            `;
            valuesContainer.appendChild(valueDiv);
        }

        // Event delegation for all dynamic elements
        document.addEventListener('click', function(e) {
            // Remove attribute group
            if (e.target.classList.contains('remove-attribute-btn')) {
                e.target.closest('.attribute-group').remove();
            }
            
            // Add attribute value
            if (e.target.classList.contains('add-attribute-value-btn')) {
                const attributeIndex = e.target.getAttribute('data-index');
                addAttributeValue(parseInt(attributeIndex));
            }
            
            // Remove attribute value
            if (e.target.classList.contains('remove-attribute-value-btn')) {
                e.target.closest('.grid').remove();
            }
            
            // Remove topping
            if (e.target.classList.contains('remove-topping-btn')) {
                e.target.closest('.topping-group').remove();
            }
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

        // Form Submission Logic
        const editProductForm = document.getElementById('edit-product-form');
        editProductForm.addEventListener('submit', function(e) {
            // Convert ingredients textarea to JSON array
            const ingredientsText = document.getElementById('ingredients').value;
            const ingredientsArray = ingredientsText.split(',').map(item => item.trim()).filter(item => item);
            const ingredientsInput = document.createElement('input');
            ingredientsInput.type = 'hidden';
            ingredientsInput.name = 'ingredients_json';
            ingredientsInput.value = JSON.stringify(ingredientsArray);
            editProductForm.appendChild(ingredientsInput);

            // Ensure description is always sent (even if empty)
            const description = document.getElementById('description');
            if (!description.value) description.value = '';
            
            // Collect variant stock data
            const variantStockInputs = document.querySelectorAll('input[name^="variant_stocks["]');
            const variantStocks = {};
            variantStockInputs.forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    const matches = input.name.match(/variant_stocks\[(\d+)\]\[(\d+)\]/);
                    if (matches) {
                        const variantId = matches[1];
                        const branchId = matches[2];
                        if (!variantStocks[variantId]) {
                            variantStocks[variantId] = {};
                        }
                        variantStocks[variantId][branchId] = parseInt(input.value) || 0;
                    }
                }
            });
            
            // Collect topping stock data
            const toppingStockInputs = document.querySelectorAll('input[name^="topping_stocks["]');
            const toppingStocks = {};
            toppingStockInputs.forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    const matches = input.name.match(/topping_stocks\[(\d+)\]\[(\d+)\]/);
                    if (matches) {
                        const toppingId = matches[1];
                        const branchId = matches[2];
                        if (!toppingStocks[toppingId]) {
                            toppingStocks[toppingId] = {};
                        }
                        toppingStocks[toppingId][branchId] = parseInt(input.value) || 0;
                    }
                }
            });
            
            console.log('Variant Stocks:', variantStocks);
            console.log('Topping Stocks:', toppingStocks);
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
</style>
@endsection
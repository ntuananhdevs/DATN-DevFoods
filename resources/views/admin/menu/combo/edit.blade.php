@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Sửa Combo')

@section('content')
<div class="min-h-screen w-full bg-gray-50">
    <div class="w-full p-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold">Sửa Combo: {{ $combo->name }}</h1>
                <p class="text-gray-600">Chỉnh sửa thông tin combo</p>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('admin.combos.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>

        <form action="{{ route('admin.combos.update', $combo) }}" method="POST" enctype="multipart/form-data" id="combo-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Thông tin combo -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border sticky top-6">
                        <div class="p-6 border-b">
                            <h2 class="text-lg font-semibold">Thông tin combo</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Combo *</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $combo->name) }}"
                                    placeholder="Nhập tên combo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror"

                                >
                                @error('name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô Tả</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    placeholder="Mô tả combo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror"
                                >{{ old('description', $combo->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Upload hình ảnh -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh combo</label>

                                <!-- Hình ảnh hiện tại -->
                                @if($combo->image)
                                    <div class="mb-4">
                                        <img id="current-image" src="{{ $combo->image_url }}" alt="{{ $combo->name }}" class="w-full h-32 object-cover rounded-md border">
                                    </div>
                                @endif

                                <!-- Drag and drop area -->
                                <div id="image-upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer">
                                    <div id="upload-content">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="mt-4">
                                            <label for="image" class="cursor-pointer">
                                                <span class="mt-2 block text-sm font-medium text-gray-900">
                                                    Kéo thả hình ảnh vào đây hoặc
                                                </span>
                                                <span class="text-blue-600 hover:text-blue-500">chọn file</span>
                                            </label>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">
                                            PNG, JPG, GIF tối đa 2MB
                                        </p>
                                    </div>

                                    <!-- Preview area -->
                                    <div id="image-preview" class="hidden">
                                        <img id="preview-image" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                        <div class="mt-2">
                                            <button type="button" id="remove-image" class="text-red-600 hover:text-red-500 text-sm">
                                                Xóa hình ảnh
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @error('image')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Giá Bán</label>
                                    <input
                                        type="number"
                                        id="price"
                                        name="price"
                                        value="{{ old('price', $combo->price) }}"
                                        placeholder="0"
                                        min="0"
                                        step="1000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('price') border-red-500 @enderror"
                                    >
                                    <div id="price-error-anchor"></div>
                                    <p class="text-xs text-gray-500 mt-1">Tự động: <span id="auto-price">{{ number_format($combo->original_price, 0, ',', '.') }}₫</span></p>
                                    @error('price')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="original_price" class="block text-sm font-medium text-gray-700 mb-1">Giá Gốc</label>
                                    <input
                                        type="number"
                                        id="original_price"
                                        name="original_price"
                                        value="{{ old('original_price', $combo->original_price) }}"
                                        placeholder="0"
                                        min="0"
                                        step="1000"
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('original_price') border-red-500 @enderror"

                                    >
                                    @error('original_price')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Số lượng cho từng chi nhánh -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng cho từng chi nhánh</label>
                                <div class="space-y-2">
                                    @foreach($branches as $branch)
                                        @php
                                            $branchStock = $combo->comboBranchStocks->firstWhere('branch_id', $branch->id);
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="w-40 text-gray-700">{{ $branch->name }}</span>
                                            <input type="number" min="0" name="branch_quantities[{{ $branch->id }}]" value="{{ old('branch_quantities.' . $branch->id, $branchStock ? $branchStock->quantity : 0) }}" class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="0">
                                        </div>
                                    @endforeach
                                </div>
                                @error('branch_quantities')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Trạng thái -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="status" value="selling" {{ old('status', $combo->status) == 'selling' ? 'checked' : '' }}>
                                        <span class="ml-2">Đang bán</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="status" value="coming_soon" {{ old('status', $combo->status) == 'coming_soon' ? 'checked' : '' }}>
                                        <span class="ml-2">Sắp bán</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="status" value="discontinued" {{ old('status', $combo->status) == 'discontinued' ? 'checked' : '' }}>
                                        <span class="ml-2">Dừng bán</span>
                                    </label>
                                </div>
                                @error('status')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sản phẩm đã chọn -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Sản phẩm trong combo (<span id="selected-count">{{ $combo->comboItems->count() }}</span>)</label>
                                <div id="selected-items" class="space-y-2 max-h-60 overflow-y-auto">
                                    @if($combo->comboItems->count() > 0)
                                        @foreach($combo->comboItems as $item)
                                            <div class="selected-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border"
                                                 data-variant-id="{{ $item->product_variant_id }}">
                                                <div class="flex-1">
                                                    <p class="font-medium text-sm">{{ $item->productVariant->product->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $item->productVariant->variant_description ?: 'Biến thể mặc định' }}</p>
                                                    <p class="text-xs text-green-600">{{ number_format($item->productVariant->price, 0, ',', '.') }}₫ x {{ $item->quantity }}</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex items-center border rounded">
                                                        <button type="button" class="decrease-qty px-2 py-1 text-gray-600 hover:bg-gray-100" data-variant-id="{{ $item->product_variant_id }}">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                            </svg>
                                                        </button>
                                                        <span class="quantity px-3 py-1 text-sm font-medium">{{ $item->quantity }}</span>
                                                        <button type="button" class="increase-qty px-2 py-1 text-gray-600 hover:bg-gray-100" data-variant-id="{{ $item->product_variant_id }}">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <button type="button" class="remove-item text-red-500 hover:text-red-700" data-variant-id="{{ $item->product_variant_id }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-sm text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                            Chưa có sản phẩm nào được chọn
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Tổng giá trị -->
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="font-medium">Tổng giá trị:</span>
                                    <span id="total-price" class="font-bold text-lg text-orange-600">{{ number_format($combo->original_price, 0, ',', '.') }}₫</span>
                                </div>
                                <div class="space-y-2">
                                    <button
                                        type="submit"
                                        id="update-combo-btn"
                                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Cập nhật Combo
                                    </button>
                                    <a href="{{ route('admin.combos.index') }}" class="block w-full text-center border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md hover:bg-gray-50">
                                        Hủy
                                    </a>
                                </div>
                            </div>

                            <!-- Hidden inputs for image -->
                            <input type="file"
                                   class="hidden @error('image') border-red-500 @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="category_id" value="{{ optional($categories->where('name', 'Combo')->first())->id ?? optional($categories->first())->id }}">
                @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <!-- Chọn sản phẩm -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold">Chọn sản phẩm</h2>
                                <select id="category-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="all">Tất cả danh mục</option>
                                    @foreach($products->groupBy('category.name') as $categoryName => $categoryProducts)
                                        <option value="{{ $categoryProducts->first()->category_id }}">{{ $categoryName ?: 'Chưa phân loại' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Search và Filter -->
                                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                                    <div class="relative flex-1">
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input
                                            type="text"
                                            id="search-input"
                                            placeholder="Tìm kiếm sản phẩm..."
                                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        >
                                    </div>
                                </div>

                                <!-- Danh sách sản phẩm -->
                                <div id="products-grid" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    @foreach($products as $product)
                                        <div class="product-card bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all duration-200 cursor-pointer"
                                             data-category-id="{{ $product->category_id }}"
                                             data-product-name="{{ strtolower($product->name) }}"
                                             data-product-id="{{ $product->id }}"
                                             data-product-name-display="{{ $product->name }}"
                                             data-has-variants="{{ $product->variants->count() > 0 ? 'true' : 'false' }}">
                                            <div class="p-4">
                                                <div class="flex items-start gap-4 mb-4">
                                                    <div class="relative">
                                                        @if($product->image_url)
                                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <div class="absolute -top-2 -right-2 bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full font-medium">
                                                            {{ $product->category->name ?? '🍽️' }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="font-semibold text-lg mb-1">{{ $product->name }}</h3>
                                                        <p class="text-sm text-gray-500 capitalize mb-2">{{ $product->category->name ?? 'Chưa phân loại' }}</p>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-sm text-green-600 font-medium">
                                                                Từ {{ number_format($product->base_price, 0, ',', '.') }}₫
                                                            </span>
                                                            @if($product->variants->count() > 0)
                                                                <span class="text-xs text-gray-500">
                                                                    • {{ $product->variants->count() }} lựa chọn
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Phần chọn biến thể (ẩn mặc định) -->
                                                @if($product->variants->count() > 0)
                                                    <div class="variants-section hidden mt-4" data-product-id="{{ $product->id }}">
                                                        <div class="border-t pt-4">
                                                            <div class="flex items-center justify-between mb-3">
                                                                <h4 class="font-medium text-sm">Chọn biến thể:</h4>
                                                                <button type="button"
                                                                        class="back-to-product-btn text-gray-500 hover:text-gray-700 text-xs"
                                                                        data-product-id="{{ $product->id }}">
                                                                    ← Quay lại
                                                                </button>
                                                            </div>
                                                            <div class="space-y-2">
                                                                @foreach($product->variants as $variant)
                                                                    <div class="flex items-center p-2 border border-gray-200 rounded-md hover:bg-gray-50">
                                                                        <div class="flex-1 min-w-0">
                                                                            <p class="text-sm font-medium truncate">{{ $variant->variant_description ?: 'Biến thể không tên' }}</p>
                                                                            <p class="text-xs text-green-600">{{ number_format($variant->price, 0, ',', '.') }}₫</p>
                                                                        </div>
                                                                        <div class="flex-shrink-0 ml-2">
                                                                            <button type="button"
                                                                                    class="add-variant-btn bg-orange-500 hover:bg-orange-600 text-white w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110"
                                                                                    data-product-id="{{ $product->id }}"
                                                                                    data-variant-id="{{ $variant->id }}"
                                                                                    data-product-name="{{ $product->name }}"
                                                                                    data-variant-name="{{ $variant->variant_description ?: 'Biến thể không tên' }}"
                                                                                    data-variant-price="{{ $variant->price }}">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Nút thêm cho sản phẩm không có biến thể -->
                                                    <div class="mt-4 pt-4 border-t">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-sm text-gray-600">Thêm vào combo</span>
                                                            <button type="button"
                                                                    class="add-product-btn bg-orange-500 hover:bg-orange-600 text-white w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110"
                                                                    data-product-id="{{ $product->id }}"
                                                                    data-product-name="{{ $product->name }}"
                                                                    data-product-price="{{ $product->base_price }}">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Biến lưu trữ các sản phẩm đã chọn
let selectedItems = {};

// Khởi tạo dữ liệu từ combo hiện tại
@if($combo->comboItems->count() > 0)
    @foreach($combo->comboItems as $item)
        selectedItems[{{ $item->product_variant_id }}] = {
            productId: {{ $item->productVariant->product_id }},
            variantId: {{ $item->product_variant_id }},
            productName: '{{ $item->productVariant->product->name }}',
            variantName: '{{ $item->productVariant->variant_description ?: "Biến thể mặc định" }}',
            price: {{ $item->productVariant->price }},
            quantity: {{ $item->quantity }}
        };
    @endforeach
@endif

// Hàm cập nhật tổng giá
function updateTotalPrice() {
    let total = 0;
    Object.values(selectedItems).forEach(item => {
        total += item.price * item.quantity;
    });

    document.getElementById('total-price').textContent = new Intl.NumberFormat('vi-VN').format(total) + '₫';
    document.getElementById('auto-price').textContent = new Intl.NumberFormat('vi-VN').format(total) + '₫';
    document.getElementById('original_price').value = total;

    // Cập nhật số lượng sản phẩm đã chọn
    document.getElementById('selected-count').textContent = Object.keys(selectedItems).length;

    // Kiểm tra nút submit
    const submitBtn = document.getElementById('update-combo-btn');
    if (Object.keys(selectedItems).length > 0) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
    // Validate giá bán vs giá gốc mỗi lần tổng giá thay đổi
    validatePriceVsOriginal();
}

// Validate realtime: Giá bán không được lớn hơn giá gốc
function validatePriceVsOriginal() {
    const priceInput = document.getElementById('price');
    const originalPriceInput = document.getElementById('original_price');
    const submitBtn = document.getElementById('update-combo-btn');
    const price = getNumberValue(priceInput);
    const originalPrice = getNumberValue(originalPriceInput);
    let errorElem = document.getElementById('price-error');
    const anchor = document.getElementById('price-error-anchor');
    if (!errorElem) {
        errorElem = document.createElement('p');
        errorElem.id = 'price-error';
        errorElem.className = 'text-red-500 text-sm mt-1';
        anchor.appendChild(errorElem);
    }
    if (price > originalPrice) {
        errorElem.textContent = 'Giá bán không được lớn hơn giá gốc!';
        submitBtn.disabled = true;
    } else {
        errorElem.textContent = '';
        // Chỉ enable nếu có sản phẩm được chọn và không có lỗi
        const hasItems = Object.keys(selectedItems).length > 0;
        submitBtn.disabled = !hasItems;
    }
}

// Hàm render danh sách sản phẩm đã chọn
function renderSelectedItems() {
    const container = document.getElementById('selected-items');

    if (Object.keys(selectedItems).length === 0) {
        container.innerHTML = `
            <p class="text-gray-500 text-sm text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                Chưa có sản phẩm nào được chọn
            </p>
        `;
        return;
    }

    let html = '';
    Object.entries(selectedItems).forEach(([variantId, item]) => {
        html += `
            <div class="selected-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border" data-variant-id="${variantId}">
                <div class="flex-1">
                    <p class="font-medium text-sm">${item.productName}</p>
                    <p class="text-xs text-gray-500">${item.variantName}</p>
                    <p class="text-xs text-green-600">${new Intl.NumberFormat('vi-VN').format(item.price)}₫ x ${item.quantity}</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center border rounded">
                        <button type="button" class="decrease-qty px-2 py-1 text-gray-600 hover:bg-gray-100" data-variant-id="${variantId}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <span class="quantity px-3 py-1 text-sm font-medium">${item.quantity}</span>
                        <button type="button" class="increase-qty px-2 py-1 text-gray-600 hover:bg-gray-100" data-variant-id="${variantId}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    <button type="button" class="remove-item text-red-500 hover:text-red-700" data-variant-id="${variantId}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Hàm hiển thị thông báo
function showNotification(message, type = 'info') {
    // Tạo element thông báo
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg transition-all duration-300 transform translate-x-full cursor-pointer border-2`;

    // Thiết lập màu sắc và icon theo loại thông báo
    let icon = '';
    if (type === 'info') {
        notification.className += ' bg-blue-50 text-blue-800 border-blue-300';
        icon = `<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
    } else if (type === 'success') {
        notification.className += ' bg-green-50 text-green-800 border-green-300';
        icon = `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
    } else if (type === 'warning') {
        notification.className += ' bg-yellow-50 text-yellow-800 border-yellow-300';
        icon = `<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
    } else if (type === 'error') {
        notification.className += ' bg-red-50 text-red-800 border-red-400 animate-shake';
        icon = `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    }

    notification.innerHTML = `
        <div class="flex items-center gap-2">
            ${icon}
            <span class="text-sm font-medium">${message}</span>
        </div>
    `;

    // Cho phép click để đóng ngay
    notification.addEventListener('click', function() {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    });

    // Thêm vào body
    document.body.appendChild(notification);

    // Hiển thị thông báo
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Hiệu ứng rung cho lỗi
const style = document.createElement('style');
style.innerHTML = `@keyframes shake {0% { transform: translateX(0); } 20% { transform: translateX(-8px); } 40% { transform: translateX(8px); } 60% { transform: translateX(-8px); } 80% { transform: translateX(8px); } 100% { transform: translateX(0); }}.animate-shake { animation: shake 0.4s; }`;
document.head.appendChild(style);

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo
    updateTotalPrice();

    // Image upload functionality
    const imageInput = document.getElementById('image');
    const uploadArea = document.getElementById('image-upload-area');
    const uploadContent = document.getElementById('upload-content');
    const imagePreview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const removeImageBtn = document.getElementById('remove-image');
    const currentImage = document.getElementById('current-image');

    // Drag and drop events
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageFile(files[0]);
        }
    });

    // Click to upload
    uploadArea.addEventListener('click', function() {
        imageInput.click();
    });

    // File input change
    imageInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleImageFile(e.target.files[0]);
        }
    });

    // Remove image
    removeImageBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        imageInput.value = '';
        uploadContent.classList.remove('hidden');
        imagePreview.classList.add('hidden');
        if (currentImage) {
            currentImage.style.display = 'block';
        }
    });

    function handleImageFile(file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Vui lòng chọn file hình ảnh hợp lệ (JPEG, PNG, JPG, GIF)', 'error');
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showNotification('Kích thước file không được vượt quá 2MB', 'error');
            return;
        }

        // Create file reader
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            uploadContent.classList.add('hidden');
            imagePreview.classList.remove('hidden');
            if (currentImage) {
                currentImage.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);

        // Set the file to input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        imageInput.files = dataTransfer.files;
    }

    // Xử lý click vào product card
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Không xử lý nếu click vào button
            if (e.target.closest('button')) return;

            const hasVariants = this.dataset.hasVariants === 'true';
            const productId = this.dataset.productId;

            if (hasVariants) {
                // Ẩn tất cả variants sections khác
                document.querySelectorAll('.variants-section').forEach(section => {
                    if (section.dataset.productId !== productId) {
                        section.classList.add('hidden');
                    }
                });

                // Hiển thị variants section của sản phẩm này
                const variantsSection = this.querySelector('.variants-section');
                variantsSection.classList.toggle('hidden');
            }
        });
    });

    // Xử lý nút quay lại
    document.querySelectorAll('.back-to-product-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.dataset.productId;
            const variantsSection = document.querySelector(`.variants-section[data-product-id="${productId}"]`);
            variantsSection.classList.add('hidden');
        });
    });

    // Xử lý thêm variant
    document.querySelectorAll('.add-variant-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const variantId = this.dataset.variantId;
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const variantName = this.dataset.variantName;
            const price = parseFloat(this.dataset.variantPrice);

            // Kiểm tra xem sản phẩm này đã có variant nào được chọn chưa
            const existingVariantKey = Object.keys(selectedItems).find(key =>
                selectedItems[key].productId === productId
            );

            if (existingVariantKey && existingVariantKey !== variantId) {
                 // Nếu đã có variant khác của sản phẩm này, thay thế bằng variant mới
                 const oldVariantName = selectedItems[existingVariantKey].variantName;
                 delete selectedItems[existingVariantKey];
                 selectedItems[variantId] = {
                     productId: productId,
                     variantId: variantId,
                     productName: productName,
                     variantName: variantName,
                     price: price,
                     quantity: 1
                 };

                 // Hiển thị thông báo thay thế
                 showNotification(`Đã thay thế "${oldVariantName}" bằng "${variantName}" cho sản phẩm ${productName}`, 'info');
            } else if (selectedItems[variantId]) {
                // Nếu đã có variant này, tăng số lượng
                selectedItems[variantId].quantity += 1;
            } else {
                // Nếu chưa có variant nào của sản phẩm này, thêm mới
                selectedItems[variantId] = {
                    productId: productId,
                    variantId: variantId,
                    productName: productName,
                    variantName: variantName,
                    price: price,
                    quantity: 1
                };
            }

            renderSelectedItems();
            updateTotalPrice();

            // Ẩn variants section
            const variantsSection = document.querySelector(`.variants-section[data-product-id="${productId}"]`);
            variantsSection.classList.add('hidden');
        });
    });

    // Xử lý thêm sản phẩm không có variant
    document.querySelectorAll('.add-product-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const price = parseFloat(this.dataset.productPrice);
            const variantId = `product_${productId}`; // Tạo ID giả cho sản phẩm không có variant

            // Kiểm tra xem sản phẩm này đã có variant nào được chọn chưa
            const existingVariantKey = Object.keys(selectedItems).find(key =>
                selectedItems[key].productId === productId
            );

            if (existingVariantKey && existingVariantKey !== variantId) {
                 // Nếu đã có variant khác của sản phẩm này, thay thế bằng sản phẩm mặc định
                 const oldVariantName = selectedItems[existingVariantKey].variantName;
                 delete selectedItems[existingVariantKey];
                 selectedItems[variantId] = {
                     productId: productId,
                     variantId: null,
                     productName: productName,
                     variantName: 'Mặc định',
                     price: price,
                     quantity: 1
                 };

                 // Hiển thị thông báo thay thế
                 showNotification(`Đã thay thế "${oldVariantName}" bằng "Mặc định" cho sản phẩm ${productName}`, 'info');
            } else if (selectedItems[variantId]) {
                // Nếu đã có sản phẩm này, tăng số lượng
                selectedItems[variantId].quantity += 1;
            } else {
                // Nếu chưa có variant nào của sản phẩm này, thêm mới
                selectedItems[variantId] = {
                    productId: productId,
                    variantId: null,
                    productName: productName,
                    variantName: 'Mặc định',
                    price: price,
                    quantity: 1
                };
            }

            renderSelectedItems();
            updateTotalPrice();
        });
    });

    // Xử lý tăng/giảm số lượng và xóa item
    document.addEventListener('click', function(e) {
        if (e.target.closest('.increase-qty')) {
            const variantId = e.target.closest('.increase-qty').dataset.variantId;
            if (selectedItems[variantId]) {
                selectedItems[variantId].quantity += 1;
                renderSelectedItems();
                updateTotalPrice();
            }
        }

        if (e.target.closest('.decrease-qty')) {
            const variantId = e.target.closest('.decrease-qty').dataset.variantId;
            if (selectedItems[variantId] && selectedItems[variantId].quantity > 1) {
                selectedItems[variantId].quantity -= 1;
                renderSelectedItems();
                updateTotalPrice();
            }
        }

        if (e.target.closest('.remove-item')) {
            const variantId = e.target.closest('.remove-item').dataset.variantId;
            delete selectedItems[variantId];
            renderSelectedItems();
            updateTotalPrice();
        }
    });

    // Xử lý tìm kiếm
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');

    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        document.querySelectorAll('.product-card').forEach(card => {
            const productName = card.dataset.productName;
            const categoryId = card.dataset.categoryId;

            const matchesSearch = productName.includes(searchTerm);
            const matchesCategory = selectedCategory === 'all' || categoryId === selectedCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);

    // Xử lý submit form
    document.getElementById('combo-form').addEventListener('submit', function(e) {
        // Validate giá bán không được lớn hơn giá gốc
        const priceInput = document.getElementById('price');
        const originalPriceInput = document.getElementById('original_price');
        if (priceInput && originalPriceInput) {
            const price = getNumberValue(priceInput);
            const originalPrice = getNumberValue(originalPriceInput);
            if (price > originalPrice) {
                e.preventDefault();
                showNotification('Giá bán không được lớn hơn giá gốc!', 'error');
                priceInput.focus();
                return false;
            }
        }
        // Tạo hidden inputs cho các sản phẩm đã chọn
        const existingInputs = this.querySelectorAll('input[name^="product_variants"]');
        existingInputs.forEach(input => input.remove());

        Object.entries(selectedItems).forEach(([variantId, item], index) => {
            // Tạo input cho variant ID
            const variantInput = document.createElement('input');
            variantInput.type = 'hidden';
            variantInput.name = `product_variants[${index}][id]`;
            variantInput.value = item.variantId || variantId.replace('product_', ''); // Xử lý cho sản phẩm không có variant
            this.appendChild(variantInput);

            // Tạo input cho quantity
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `product_variants[${index}][quantity]`;
            quantityInput.value = item.quantity;
            this.appendChild(quantityInput);
        });
    });

    // Validate realtime khi nhập giá bán
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = value;
        validatePriceVsOriginal();
    });
    // Validate khi giá gốc thay đổi (tự động update)
    const originalPriceInput = document.getElementById('original_price');
    originalPriceInput.addEventListener('input', validatePriceVsOriginal);
});

function getNumberValue(input) {
    // Lấy giá trị, thay dấu phẩy thành dấu chấm, loại ký tự không phải số hoặc dấu chấm
    let value = input.value.replace(',', '.').replace(/[^0-9.]/g, '');
    return parseFloat(value) || 0;
}
</script>
@endsection

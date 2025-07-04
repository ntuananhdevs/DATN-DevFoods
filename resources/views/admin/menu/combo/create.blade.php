@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Thêm Combo Mới')

@section('content')
    <div class="min-h-screen w-full bg-gray-50">
        <div class="w-full p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold">Tạo Combo Mới</h1>
                    <p class="text-gray-600">Tạo combo mới cho menu của bạn</p>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('admin.combos.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Quay lại
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.combos.store') }}" method="POST" enctype="multipart/form-data" id="combo-form">
                @csrf



                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Thông tin combo -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border sticky top-6">
                            <div class="p-6 border-b">
                                <h2 class="text-lg font-semibold">Thông tin combo</h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Combo
                                        *</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                        placeholder="Nhập tên combo"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror"
                                        >
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Thêm phần upload ảnh -->
                                <div>
                                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Ảnh
                                        Combo</label>
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-orange-400 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <div id="image-preview" class="hidden">
                                                <img id="preview-img" src="" alt="Preview"
                                                    class="mx-auto h-32 w-32 object-cover rounded-lg">
                                            </div>
                                            <div id="upload-placeholder">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="image"
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                                        <span>Tải ảnh lên</span>
                                                    </label>
                                                    <p class="pl-1">hoặc kéo thả</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF tối đa 2MB</p>
                                            </div>
                                            <button type="button" id="remove-image"
                                                class="hidden mt-2 text-sm text-red-600 hover:text-red-500">Xóa ảnh</button>
                                        </div>
                                    </div>
                                    @error('image')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Số lượng cho từng chi nhánh -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng cho từng chi
                                        nhánh</label>
                                    <div class="space-y-2">
                                        @foreach ($branches as $branch)
                                            <div class="flex items-center gap-2">
                                                <span class="w-40 text-gray-700">{{ $branch->name }}</span>
                                                <input type="number" min="0"
                                                    name="branch_quantities[{{ $branch->id }}]"
                                                    value="{{ old('branch_quantities.' . $branch->id, 0) }}"
                                                    class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                                    placeholder="0">
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('branch_quantities')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <!-- Trạng thái -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                    <div class="flex gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status" value="selling"
                                                {{ old('status', 'selling') == 'selling' ? 'checked' : '' }}>
                                            <span class="ml-2">Đang bán</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status" value="coming_soon"
                                                {{ old('status') == 'coming_soon' ? 'checked' : '' }}>
                                            <span class="ml-2">Sắp bán</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status" value="discontinued"
                                                {{ old('status') == 'discontinued' ? 'checked' : '' }}>
                                            <span class="ml-2">Dừng bán</span>
                                        </label>
                                    </div>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô Tả</label>
                                    <div class="flex items-center gap-2 mb-1">
                                        <button type="button" id="auto-description-btn" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">Tự động mô tả</button>
                                        <span class="text-xs text-gray-400">(Lấy tên các sản phẩm đã chọn, nối bằng dấu cộng)</span>
                                    </div>
                                    <textarea
                                        id="description"
                                        name="description"
                                        rows="3"
                                        placeholder="Mô tả combo"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Giá
                                            Bán</label>
                                        <input type="number" id="price" name="price" value="{{ old('price') }}"
                                            placeholder="0" min="0" step="1000"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('price') border-red-500 @enderror"
                                            >
                                        <p class="text-xs text-gray-500 mt-1">Tự động: <span id="auto-price">0₫</span></p>
                                        @error('price')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="original_price"
                                            class="block text-sm font-medium text-gray-700 mb-1">Giá Gốc</label>
                                        <input type="number" id="original_price" name="original_price"
                                            value="{{ old('original_price') }}" placeholder="0" min="0"
                                            step="1000" readonly
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('original_price') border-red-500 @enderror"
                                            >
                                        @error('original_price')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sản phẩm đã chọn -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Sản phẩm trong combo (<span
                                            id="selected-count">0</span>)</label>
                                    <div id="selected-items" class="space-y-2 max-h-60 overflow-y-auto">
                                        <p
                                            class="text-gray-500 text-sm text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                            Chưa có sản phẩm nào được chọn
                                        </p>
                                    </div>
                                </div>

                                <!-- Tổng giá trị -->
                                <div class="border-t pt-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="font-medium">Tổng giá trị:</span>
                                        <span id="total-price" class="font-bold text-lg text-orange-600">0₫</span>
                                    </div>
                                    <div class="space-y-2">
                                        <button type="submit" id="create-combo-btn"
                                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                                            disabled>
                                            Tạo Combo
                                        </button>
                                        <a href="{{ route('admin.combos.index') }}"
                                            class="block w-full text-center border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md hover:bg-gray-50">
                                            Hủy
                                        </a>
                                    </div>
                                </div>



                                <!-- Hidden inputs for image and active status -->
                                <input type="file" class="hidden @error('image') border-red-500 @enderror"
                                    id="image" name="image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="category_id"
                        value="{{ optional($categories->where('name', 'Combo')->first())->id ?? optional($categories->first())->id }}">
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <!-- Chọn sản phẩm -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="p-6 border-b">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-lg font-semibold">Chọn sản phẩm</h2>
                                    <select id="category-filter"
                                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                        <option value="all">Tất cả danh mục</option>
                                        @foreach ($products->groupBy('category.name') as $categoryName => $categoryProducts)
                                            <option value="{{ $categoryProducts->first()->category_id }}">
                                                {{ $categoryName ?: 'Chưa phân loại' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Search và Filter -->
                                    <div class="flex flex-col sm:flex-row gap-4 mb-6">
                                        <div class="relative flex-1">
                                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm..."
                                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                        </div>
                                    </div>

                                    <!-- Danh sách sản phẩm -->
                                    <div id="products-grid" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                        @foreach ($products as $product)
                                            <div class="product-card bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all duration-200 cursor-pointer"
                                                data-category-id="{{ $product->category_id }}"
                                                data-product-name="{{ strtolower($product->name) }}"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name-display="{{ $product->name }}"
                                                data-has-variants="{{ $product->variants->count() > 0 ? 'true' : 'false' }}">
                                                <div class="p-4">
                                                    <div class="flex items-start gap-4 mb-4">
                                                        <div class="relative">
                                                            @if ($product->image_url)
                                                                <img src="{{ $product->image_url }}"
                                                                    alt="{{ $product->name }}"
                                                                    class="w-20 h-20 rounded-lg object-cover">
                                                            @else
                                                                <div
                                                                    class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                                                    <svg class="w-8 h-8 text-gray-400" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                            <div
                                                                class="absolute -top-2 -right-2 bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full font-medium">
                                                                {{ $product->category->name ?? '🍽️' }}
                                                            </div>
                                                        </div>
                                                        <div class="flex-1">
                                                            <h3 class="font-semibold text-lg mb-1">{{ $product->name }}
                                                            </h3>
                                                            <p class="text-sm text-gray-500 capitalize mb-2">
                                                                {{ $product->category->name ?? 'Chưa phân loại' }}</p>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-sm text-green-600 font-medium">
                                                                    Từ
                                                                    {{ number_format($product->base_price, 0, ',', '.') }}₫
                                                                </span>
                                                                @if ($product->variants->count() > 0)
                                                                    <span class="text-xs text-gray-500">
                                                                        • {{ $product->variants->count() }} lựa chọn
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Phần chọn biến thể (ẩn mặc định) -->
                                                    @if ($product->variants->count() > 0)
                                                        <div class="variants-section hidden mt-4"
                                                            data-product-id="{{ $product->id }}">
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
                                                                    @foreach ($product->variants as $variant)
                                                                        <div
                                                                            class="flex items-center p-2 border border-gray-200 rounded-md hover:bg-gray-50">
                                                                            <div class="flex-1 min-w-0">
                                                                                <p class="text-sm font-medium truncate">
                                                                                    {{ $variant->variant_description ?: 'Biến thể không tên' }}
                                                                                </p>
                                                                                <p class="text-xs text-green-600">
                                                                                    {{ number_format($variant->price, 0, ',', '.') }}₫
                                                                                </p>
                                                                            </div>
                                                                            <div class="flex-shrink-0 ml-2">
                                                                                <button type="button"
                                                                                    class="add-variant-btn bg-orange-500 hover:bg-orange-600 text-white w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110"
                                                                                    data-product-id="{{ $product->id }}"
                                                                                    data-variant-id="{{ $variant->id }}"
                                                                                    data-product-name="{{ $product->name }}"
                                                                                    data-variant-name="{{ $variant->variant_description ?: 'Biến thể không tên' }}"
                                                                                    data-variant-price="{{ $variant->price }}">
                                                                                    <svg class="w-4 h-4" fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M12 4v16m8-8H4" />
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
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 4v16m8-8H4" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>



                                    <!-- Empty state -->
                                    <div id="empty-state" class="text-center py-12 hidden">
                                        <div
                                            class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500">Không tìm thấy sản phẩm nào phù hợp</p>
                                        <p class="text-sm text-gray-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
                                    </div>

                                    @error('product_variants')
                                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-red-800 text-sm">{{ $message }}</p>
                                            </div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 28px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input:checked+.slider {
            background-color: #3b82f6;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8fafc;
        }

        .file-upload-area:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
            transform: translateY(-1px);
        }

        .file-upload-area.dragover {
            border-color: #2563eb;
            background-color: #dbeafe;
            transform: scale(1.02);
        }

        .product-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .product-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-item.ring-2 {
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {

            0%,
            100% {
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
            }

            50% {
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
            }
        }

        .variant-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .variant-item:hover {
            background-color: #f1f5f9;
            transform: translateX(2px);
        }

        .selected-variant-display {
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .selected-variants-container {
            max-height: 350px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .selected-variants-container::-webkit-scrollbar {
            width: 6px;
        }

        .selected-variants-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .selected-variants-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .selected-variants-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .selected-variant-item {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 6px;
            transition: all 0.2s ease;
            animation: fadeInUp 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .selected-variant-item:hover {
            transform: translateX(3px);
            box-shadow: 0 3px 10px rgba(14, 165, 233, 0.2);
        }

        .category-section {
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .btn-secondary {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            color: #475569;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
            transform: translateY(-1px);
            color: #334155;
        }

        .form-input {
            transition: all 0.2s ease;
        }

        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .change-variant-btn {
            transition: all 0.2s ease;
        }

        .change-variant-btn:hover {
            transform: rotate(90deg);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .product-item {
                margin-bottom: 1rem;
            }

            .grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .selected-variants-container {
                max-height: 250px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Selected variants storage
            let selectedVariants = {};
            let selectedCount = 0;

            // DOM elements
            const selectedItemsContainer = document.getElementById('selected-items');
            const selectedCountSpan = document.getElementById('selected-count');
            const totalPriceSpan = document.getElementById('total-price');
            const autoPriceSpan = document.getElementById('auto-price');
            const originalPriceInput = document.getElementById('original_price');
            const createComboBtn = document.getElementById('create-combo-btn');
            const searchInput = document.getElementById('search-input');
            const categoryFilter = document.getElementById('category-filter');
            const productsGrid = document.getElementById('products-grid');
            const emptyState = document.getElementById('empty-state');

            // Handle product card click
            document.addEventListener('click', function(e) {
                // Check if click is on product card but not on buttons
                const productCard = e.target.closest('.product-card');
                if (productCard && !e.target.closest('.add-variant-btn') && !e.target.closest(
                        '.add-product-btn') && !e.target.closest('.back-to-product-btn') && !e.target
                    .closest('.variants-section')) {
                    const productId = productCard.dataset.productId;
                    const hasVariants = productCard.dataset.hasVariants === 'true';

                    if (hasVariants) {
                        // Show variants section
                        const variantsSection = document.querySelector(
                            `.variants-section[data-product-id="${productId}"]`);
                        if (variantsSection) {
                            variantsSection.classList.remove('hidden');
                        }
                    }
                }
            });

            // Handle add product button click (for products without variants)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.add-product-btn')) {
                    const btn = e.target.closest('.add-product-btn');
                    const productId = btn.dataset.productId;
                    const productName = btn.dataset.productName;
                    const productPrice = parseFloat(btn.dataset.productPrice);
                    const variantId = btn.dataset.variantId; // Sử dụng variant_id thực tế
                    const variantName = 'Mặc định';

                    // Kiểm tra xem variant này đã được chọn chưa
                    if (selectedVariants[variantId]) {
                        // Nếu đã có, tăng số lượng
                        selectedVariants[variantId].quantity += 1;
                    } else {
                        // Nếu chưa có, thêm mới
                        selectedVariants[variantId] = {
                            productId: productId,
                            variantId: variantId, // Thêm variantId thực tế
                            productName: productName,
                            variantName: variantName,
                            price: productPrice,
                            quantity: 1
                        };
                    }

                    updateSelectedItems();
                }
            });

            // Handle back to product button click
            document.addEventListener('click', function(e) {
                if (e.target.closest('.back-to-product-btn')) {
                    const btn = e.target.closest('.back-to-product-btn');
                    const productId = btn.dataset.productId;

                    // Hide variants section
                    const variantsSection = document.querySelector(
                        `.variants-section[data-product-id="${productId}"]`);
                    if (variantsSection) {
                        variantsSection.classList.add('hidden');
                    }
                }
            });

            // Add variant buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.add-variant-btn')) {
                    const btn = e.target.closest('.add-variant-btn');
                    const productId = btn.dataset.productId;
                    const productName = btn.dataset.productName;
                    const variantId = btn.dataset.variantId; // Sử dụng variant_id thực tế
                    const variantName = btn.dataset.variantName;
                    const variantPrice = parseFloat(btn.dataset.variantPrice);

                    // Kiểm tra xem variant này đã được chọn chưa
                    if (selectedVariants[variantId]) {
                        // Nếu đã có, tăng số lượng
                        selectedVariants[variantId].quantity += 1;
                    } else {
                        // Nếu chưa có, thêm mới
                        selectedVariants[variantId] = {
                            productId: productId,
                            variantId: variantId, // Thêm variantId thực tế
                            productName: productName,
                            variantName: variantName,
                            price: variantPrice,
                            quantity: 1
                        };
                    }

                    updateSelectedItems();
                }
            });

            // Function to add variant to selected (for compatibility)
            function addVariantToSelected(productId, variantId, productName, variantName, variantPrice) {
                const id = variantId || `${productId}_default`;
                const price = parseFloat(variantPrice);

                // Kiểm tra xem variant này đã được chọn chưa
                if (selectedVariants[id]) {
                    // Nếu đã có variant này, tăng số lượng
                    selectedVariants[id].quantity += 1;
                } else {
                    // Nếu chưa có, thêm mới
                    selectedVariants[id] = {
                        productId: productId,
                        productName: productName,
                        variantName: variantName,
                        price: price,
                        quantity: 1
                    };
                }

                updateSelectedItems();
            }

            // Remove variant function
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-variant-btn')) {
                    const btn = e.target.closest('.remove-variant-btn');
                    const variantId = btn.dataset.variantId;
                    delete selectedVariants[variantId];
                    updateSelectedItems();
                }
            });

            // Update quantity function
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('variant-quantity-input')) {
                    const variantId = e.target.dataset.variantId;
                    const quantity = parseInt(e.target.value) || 1;

                    if (selectedVariants[variantId]) {
                        selectedVariants[variantId].quantity = Math.max(1, quantity);
                        // Update the corresponding hidden input
                        const hiddenQuantityInput = document.querySelector(
                            `input[name*="[quantity]"][value="${selectedVariants[variantId].quantity}"]`
                        );
                        if (hiddenQuantityInput) {
                            hiddenQuantityInput.value = Math.max(1, quantity);
                        }
                        updateSelectedItems();
                    }
                }
            });

            // Update selected items display
            function updateSelectedItems() {
                const variants = Object.values(selectedVariants);
                selectedCount = variants.length;
                selectedCountSpan.textContent = selectedCount;

                if (variants.length === 0) {
                    selectedItemsContainer.innerHTML = `
                    <p class="text-gray-500 text-sm text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                        Chưa có sản phẩm nào được chọn
                    </p>
                `;
                    totalPriceSpan.textContent = '0₫';
                    autoPriceSpan.textContent = '0₫';
                    originalPriceInput.value = 0;
                    createComboBtn.disabled = true;
                    validatePriceVsOriginal();
                    return;
                }

                let totalPrice = 0;
                let html = '';

                variants.forEach((variant, index) => {
                    const itemTotal = variant.price * variant.quantity;
                    totalPrice += itemTotal;
                    const variantId = Object.keys(selectedVariants).find(key => selectedVariants[key] ===
                        variant);

                    html += `
                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-medium text-sm">${variant.productName}</h4>
                            <p class="text-xs text-gray-500">${variant.variantName}</p>
                            <p class="text-xs text-green-600">${formatPrice(variant.price)} × ${variant.quantity}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                class="variant-quantity-input w-16 px-2 py-1 border border-gray-300 rounded text-center text-xs"
                                min="1"
                                value="${variant.quantity}"
                                data-variant-id="${variantId}"
                            >
                            <button
                                type="button"
                                class="remove-variant-btn text-red-500 hover:text-red-700 text-xs"
                                data-variant-id="${variantId}"
                            >
                                Xóa
                            </button>
                        </div>
                        <input type="hidden" name="product_variants[${index}][id]" value="${variantId}">
                        <input type="hidden" name="product_variants[${index}][quantity]" value="${variant.quantity}">
                    </div>
                `;
                });

                selectedItemsContainer.innerHTML = html;
                totalPriceSpan.textContent = formatPrice(totalPrice);
                autoPriceSpan.textContent = formatPrice(totalPrice);
                originalPriceInput.value = totalPrice;
                validatePriceVsOriginal();
            }

            // Format price function
            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN').format(price) + '₫';
            }

            // Search functionality
            searchInput.addEventListener('input', function() {
                filterProducts();
            });

            // Category filter functionality
            categoryFilter.addEventListener('change', function() {
                filterProducts();
            });

            // Filter products function
            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;
                const productCards = productsGrid.querySelectorAll('.product-card');
                let visibleCount = 0;

                productCards.forEach(card => {
                    const productName = card.dataset.productName;
                    const categoryId = card.dataset.categoryId;

                    const matchesSearch = productName.includes(searchTerm);
                    const matchesCategory = selectedCategory === 'all' || categoryId === selectedCategory;

                    if (matchesSearch && matchesCategory) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide empty state
                if (visibleCount === 0) {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            }

            // Price input formatting
            const priceInput = document.getElementById('price');
            priceInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = value;
                validatePriceVsOriginal();
            });
            // Validate realtime: Giá bán không được lớn hơn giá gốc
            function validatePriceVsOriginal() {
                const price = parseInt(priceInput.value.replace(/[^0-9]/g, '')) || 0;
                const originalPrice = parseInt(originalPriceInput.value.replace(/[^0-9]/g, '')) || 0;
                let errorElem = document.getElementById('price-error');
                if (!errorElem) {
                    errorElem = document.createElement('p');
                    errorElem.id = 'price-error';
                    errorElem.className = 'text-red-500 text-sm mt-1';
                    if (priceInput.nextElementSibling) {
                        priceInput.parentNode.insertBefore(errorElem, priceInput.nextElementSibling);
                    } else {
                        priceInput.parentNode.appendChild(errorElem);
                    }
                }
                if (price > originalPrice) {
                    errorElem.textContent = 'Giá bán không được lớn hơn giá gốc!';
                    createComboBtn.disabled = true;
                } else {
                    errorElem.textContent = '';
                    // Chỉ enable nếu có sản phẩm được chọn và không có lỗi
                    const variants = Object.values(selectedVariants);
                    createComboBtn.disabled = variants.length === 0;
                }
            }
            // Gọi validate khi thay đổi giá gốc (auto update)
            originalPriceInput.addEventListener('input', validatePriceVsOriginal);

            // Image upload preview
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const uploadPlaceholder = document.getElementById('upload-placeholder');
            const removeImageBtn = document.getElementById('remove-image');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Vui lòng chọn file ảnh!');
                        return;
                    }

                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Kích thước file không được vượt quá 2MB!');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        uploadPlaceholder.classList.add('hidden');
                        removeImageBtn.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Remove image
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                previewImg.src = '';
                imagePreview.classList.add('hidden');
                uploadPlaceholder.classList.remove('hidden');
                removeImageBtn.classList.add('hidden');
            });

            // Drag and drop functionality
            const dropZone = imageInput.closest('.border-dashed');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('border-orange-400', 'bg-orange-50');
            }

            function unhighlight(e) {
                dropZone.classList.remove('border-orange-400', 'bg-orange-50');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    imageInput.files = files;
                    imageInput.dispatchEvent(new Event('change'));
                }
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
                    icon = `<svg class=\"w-5 h-5 text-blue-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg>`;
                } else if (type === 'success') {
                    notification.className += ' bg-green-50 text-green-800 border-green-300';
                    icon = `<svg class=\"w-5 h-5 text-green-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"/></svg>`;
                } else if (type === 'warning') {
                    notification.className += ' bg-yellow-50 text-yellow-800 border-yellow-300';
                    icon = `<svg class=\"w-5 h-5 text-yellow-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg>`;
                } else if (type === 'error') {
                    notification.className += ' bg-red-50 text-red-800 border-red-400 animate-shake';
                    icon = `<svg class=\"w-5 h-5 text-red-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M6 18L18 6M6 6l12 12\"/></svg>`;
                }

                notification.innerHTML = `
                    <div class=\"flex items-center gap-2\">
                        ${icon}
                        <span class=\"text-sm font-medium\">${message}</span>
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

            // Tự động mô tả: lấy tên các sản phẩm đã chọn nối bằng dấu cộng
            const autoDescBtn = document.getElementById('auto-description-btn');
            if (autoDescBtn) {
                autoDescBtn.addEventListener('click', function() {
                    const variants = Object.values(selectedVariants);
                    const names = variants.map(v => v.productName).filter((v, i, arr) => arr.indexOf(v) === i); // loại trùng tên
                    document.getElementById('description').value = names.join(' + ');
                });
            }

            // Initialize
            updateSelectedItems();

            // Đảm bảo chỉ gắn event submit một lần
            const comboForm = document.getElementById('combo-form');
            if (comboForm) {
                comboForm.addEventListener('submit', function(e) {
                    const priceInput = document.getElementById('price');
                    const originalPriceInput = document.getElementById('original_price');
                    if (!priceInput || !originalPriceInput) return;
                    const price = parseInt(priceInput.value.replace(/[^0-9]/g, '')) || 0;
                    const originalPrice = parseInt(originalPriceInput.value.replace(/[^0-9]/g, '')) || 0;
                    if (price > originalPrice) {
                        e.preventDefault();
                        showNotification('Giá bán không được lớn hơn giá gốc!', 'error');
                        priceInput.focus();
                        return false;
                    }
                });
            }

            // Hiệu ứng rung cho lỗi
            const style = document.createElement('style');
            style.innerHTML = `@keyframes shake {0% { transform: translateX(0); } 20% { transform: translateX(-8px); } 40% { transform: translateX(8px); } 60% { transform: translateX(-8px); } 80% { transform: translateX(8px); } 100% { transform: translateX(0); }}.animate-shake { animation: shake 0.4s; }`;
            document.head.appendChild(style);
        });
    </script>
@endpush
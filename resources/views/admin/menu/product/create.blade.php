@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Thêm sản phẩm mới')

@section('content')

@section('page-style-prd-edit')
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
    <style>
        /* Topping Selection Styles */
        .topping-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .topping-card:hover {
            border-color: #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .topping-card.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .topping-card.selected:hover {
            border-color: #2563eb;
        }
        
        .topping-checkbox {
            transform: scale(1.2);
        }
        
        .topping-image img {
            object-fit: cover;
        }
        
        #toppings-list .col-md-6 {
            margin-bottom: 1rem;
        }

        /* Modal Styles */
        .modal-backdrop {
            backdrop-filter: blur(4px);
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

        /* Topping Tags Styles */
        .topping-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #3b82f6;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .topping-tag:hover {
            background-color: #2563eb;
        }

        .topping-tag .remove-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .topping-tag .remove-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .topping-tag .remove-btn i {
            font-size: 10px;
        }

        /* Modal Animation */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }

        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Enhanced topping card styles */
        .topping-card {
            position: relative;
            overflow: hidden;
        }

        .topping-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .topping-card.selected::before {
            opacity: 1;
        }

        .topping-card:hover::before {
            opacity: 0.5;
        }

        /* Search input improvements */
        #topping-search {
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%236b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>');
            background-repeat: no-repeat;
            background-position: 12px center;
            background-size: 16px 16px;
            padding-left: 40px;
        }

        #topping-search:focus {
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%233b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>');
            outline: none;
            box-shadow: 0 0 0 2px rgb(59 130 246 / 0.5);
            border-color: #3b82f6;
        }
    </style>
@endsection

<main class="container">
    <h1 class="text-3xl font-extrabold mb-1">Thêm Sản Phẩm Mới</h1>
    <p class="text-gray-500 mb-8">Nhập thông tin chi tiết để tạo sản phẩm mới</p>

    <form id="add-product-form" class="space-y-8" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

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
                        <label for="name" class="block text-sm font-medium text-gray-700">Tên sản phẩm <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Nhập tên sản phẩm"
                            value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror" />
                        @error('name')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh mục <span
                                    class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white sm:text-sm @error('category_id') border-red-500 @enderror">
                                <option value="">Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    placeholder="0" value="{{ old('base_price') }}"
                                    class="block w-full pl-7 rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('base_price') border-red-500 @enderror" />
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
                                placeholder="Nhập thời gian chuẩn bị" value="{{ old('preparation_time') }}"
                                class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('preparation_time') border-red-500 @enderror" />
                            @error('preparation_time')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Nguyên liệu 
                            <span class="text-red-500">*</span>
                        </label>
                        
                        <textarea id="ingredients" name="ingredients" rows="5"
                            placeholder="Nhập danh sách nguyên liệu, mỗi nguyên liệu một dòng&#10;Ví dụ:&#10;thịt bò&#10;rau xà lách&#10;ớt chuông&#10;cà chua"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none px-3 py-2 @error('ingredients') border-red-500 @enderror">{{ old('ingredients') }}</textarea>
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
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none px-3 py-2 @error('short_description') border-red-500 @enderror">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả chi tiết</label>
                        <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
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
                                        {{ old('is_featured') ? 'checked' : '' }}
                                        class="form-checkbox text-blue-600" />
                                    <span>Sản phẩm nổi bật</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái sản phẩm</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="coming_soon"
                                            @if (old('status', 'selling') == 'coming_soon') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Sắp ra mắt</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="selling"
                                            @if (old('status', 'selling') == 'selling') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Đang bán</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="discontinued"
                                            @if (old('status', 'selling') == 'discontinued') checked @endif
                                            class="form-radio text-blue-600" />
                                        <span>Ngừng bán</span>
                                    </label>
                                </div>
                            </div>

                            <div id="release_at_container" class="hidden">
                                <label for="release_at" class="block text-sm font-medium text-gray-700">Ngày ra mắt</label>
                                <input type="datetime-local" id="release_at" name="release_at"
                                    value="{{ old('release_at') }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
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
                                <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                                    <div id="image-placeholder"
                                        class="w-full h-80 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer transition-all relative">
                                        <div id="main-image-preview" class="absolute inset-0 w-full h-full hidden">
                                            <img src="" alt="Main image preview"
                                                class="w-full h-full object-cover" />
                                        </div>
                                        <div id="upload-content" class="flex flex-col items-center justify-center">
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
                    <!-- Default attribute group -->
                    <div class="p-4 border border-gray-200 rounded-md mb-4 bg-gray-50 attribute-group">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-md font-semibold text-gray-800">Thuộc tính 1</h3>
                            <button type="button" class="remove-attribute-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa thuộc tính</button>
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
                                <div id="attribute_values_container_0" class="space-y-3">
                                    <!-- Default attribute value -->
                                    <div class="attribute-value-item grid grid-cols-2 gap-2 p-2 border border-dashed border-gray-300 rounded-md bg-gray-50">
                                        <div>
                                            <label for="attribute_value_0_0" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                            <input type="text" id="attribute_value_0_0" name="attributes[0][values][0][value]" placeholder="VD: Nhỏ"
                                                class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="{{ old('attributes.0.values.0.value') }}">
                                            @error('attributes.0.values.0.value')
                                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="attribute_price_0_0" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                            <input type="number" id="attribute_price_0_0" name="attributes[0][values][0][price_adjustment]" placeholder="0" step="any"
                                                class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="{{ old('attributes.0.values.0.price_adjustment') }}">
                                            @error('attributes.0.values.0.price_adjustment')
                                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end col-start-2">Xóa</button>
                                    </div>
                                </div>
                                <button type="button" class="add-attribute-value-btn mt-2 text-xs text-blue-600 hover:text-blue-800" data-index="0">+ Thêm giá trị</button>
                            </div>
                        </div>
                    </div>
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
        <section id="toppings-section" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <header class="px-6 py-4 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Toppings</h2>
                        <p class="text-gray-500 text-sm mt-1">Chọn các topping có sẵn cho sản phẩm</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        Đã chọn: <span id="selected-toppings-count" class="font-semibold text-blue-600">0</span> topping
                    </div>
                </div>
            </header>

            <div class="px-6 py-6">
                <!-- Open Modal Button -->
                <div class="mb-4">
                    <button type="button" id="open-toppings-modal" 
                    class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <i class="fas fa-plus-circle"></i>
                        Chọn Toppings
                    </button>
                </div>

                <!-- Selected Toppings Display -->
                <div id="selected-toppings-display" class="space-y-2">
                    <h4 class="text-sm font-medium text-gray-700">Toppings đã chọn:</h4>
                    <div id="selected-toppings-tags" class="flex flex-wrap gap-2">
                        <!-- Selected toppings will be displayed here as tags -->
                        <div class="text-gray-500 text-sm italic" id="no-toppings-message">
                            Chưa có topping nào được chọn
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hidden input for selected toppings -->
            <input type="hidden" id="selected_toppings" name="selected_toppings" value="[]">
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
        <!-- Save Buttons -->
        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-4 shadow-sm mt-6">
            <button type="button" id="save-draft-btn"
                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">Lưu nháp</button>
            <button type="submit" id="save-product-btn"
                class="rounded-md bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Tạo sản phẩm</button>
        </div>
    </form>
</main>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent multiple initialization
        if (window.productFormInitialized) {
            return;
        }
        window.productFormInitialized = true;
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

        // Attributes and Variant Values Logic
        const attributesContainer = document.getElementById('attributes-container');
        const addAttributeBtn = document.getElementById('add-attribute-btn');
        let attributeCount = 1; // Start from 1 since we have a default attribute

        function createAttributeGroup(index) {
            const group = document.createElement('div');
            group.classList.add('p-4', 'border', 'border-gray-200', 'rounded-md', 'mb-4', 'bg-gray-50');
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
                            <div class="attribute-value-item grid grid-cols-2 gap-2 p-2 border border-dashed border-gray-300 rounded-md bg-gray-50">
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

            // Event listeners will be handled by event delegation in the main container

            return group;
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

            // Event listener will be handled by event delegation

            valuesContainer.appendChild(valueDiv);
        }

        function reindexAttributeGroups() {
            const attributeGroups = attributesContainer.querySelectorAll('.p-4.border.border-gray-200');
            attributeGroups.forEach((group, index) => {
                // Update group title
                const title = group.querySelector('h3');
                if (title) {
                    title.textContent = `Thuộc tính ${index + 1}`;
                }
                
                // Update attribute name input
                const nameInput = group.querySelector('input[name*="[name]"]');
                if (nameInput) {
                    const currentName = nameInput.getAttribute('name');
                    const newName = currentName.replace(/attributes\[\d+\]/, `attributes[${index}]`);
                    nameInput.setAttribute('name', newName);
                    
                    const currentId = nameInput.getAttribute('id');
                    if (currentId) {
                        const newId = currentId.replace(/attribute_name_\d+/, `attribute_name_${index}`);
                        nameInput.setAttribute('id', newId);
                    }
                }
                
                // Update values container ID
                const valuesContainer = group.querySelector('[id*="attribute_values_container_"]');
                if (valuesContainer) {
                    const newId = `attribute_values_container_${index}`;
                    valuesContainer.setAttribute('id', newId);
                }
                
                // Update add value button data-index
                const addValueBtn = group.querySelector('.add-attribute-value-btn');
                if (addValueBtn) {
                    addValueBtn.setAttribute('data-index', index);
                }
                
                // Reindex all attribute values in this group
                reindexAttributeValues(group, index);
            });
        }

        // Add event listener for the main "Add Attribute" button
        if (addAttributeBtn) {
            addAttributeBtn.addEventListener('click', () => {
                const attributeGroup = createAttributeGroup(attributeCount);
                attributesContainer.appendChild(attributeGroup);
                attributeCount++;
                updateDeleteButtonVisibility();
            });
        }

        // Initial call to set delete button visibility on page load
        updateDeleteButtonVisibility();

        // Function to update delete button visibility
        function updateDeleteButtonVisibility() {
            const attributeGroups = document.querySelectorAll('#attributes-container > .p-4.border.border-gray-200');
            
            attributeGroups.forEach(group => {
                const removeAttributeBtn = group.querySelector('.remove-attribute-btn');
                const attributeValues = group.querySelectorAll('.attribute-value-item');
                
                // Hide attribute delete button if only 1 attribute exists
                if (removeAttributeBtn) {
                    if (attributeGroups.length <= 1) {
                        removeAttributeBtn.style.display = 'none';
                    } else {
                        removeAttributeBtn.style.display = 'inline-block';
                    }
                }
                
                // Hide value delete buttons if only 1 value exists in this attribute
                attributeValues.forEach(valueItem => {
                    const removeValueBtn = valueItem.querySelector('.remove-attribute-value-btn');
                    if (removeValueBtn) {
                        if (attributeValues.length <= 1) {
                            removeValueBtn.style.display = 'none';
                        } else {
                            removeValueBtn.style.display = 'inline-block';
                        }
                    }
                });
            });
        }

        // Use event delegation for attribute-related buttons to avoid conflicts
        if (attributesContainer) {
            attributesContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-attribute-btn')) {
                    const attributeGroup = e.target.closest('.p-4.border.border-gray-200');
                    if (attributeGroup) {
                        attributeGroup.remove();
                        reindexAttributeGroups();
                        updateDeleteButtonVisibility();
                    }
                } else if (e.target.classList.contains('add-attribute-value-btn')) {
                    const attributeIndex = e.target.getAttribute('data-index') || 0;
                    addAttributeValue(parseInt(attributeIndex));
                    updateDeleteButtonVisibility();
                } else if (e.target.classList.contains('remove-attribute-value-btn')) {
                    const valueContainer = e.target.closest('.attribute-value-item');
                    if (valueContainer) {
                        const attributeGroup = valueContainer.closest('.p-4.border.border-gray-200');
                        valueContainer.remove();
                        reindexAttributeValues(attributeGroup);
                        updateDeleteButtonVisibility();
                    }
                }
            });
        }

        // Toppings Logic
        const toppingsContainer = document.getElementById('toppings-container');
        const addToppingBtn = document.getElementById('add-topping-btn');
        let toppingCount = 0;

        function createToppingGroup(index) {
            const group = document.createElement('div');
            group.classList.add('p-4', 'border', 'border-gray-200', 'rounded-md', 'mb-4', 'bg-gray-50');
            group.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-md font-semibold text-gray-800">Topping ${index + 1}</h3>
                    <button type="button" class="remove-topping-btn text-red-500 hover:text-red-700 font-medium text-sm">× Xóa topping</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <div>
                        <label for="topping_name_${index}" class="block text-sm font-medium text-gray-700">Tên topping</label>
                        <input type="text" id="topping_name_${index}" name="toppings[${index}][name]" placeholder="VD: Trân châu đen"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_toppings_${index}_name" style="display: none;"></div>
                    </div>
                    <div>
                        <label for="topping_price_${index}" class="block text-sm font-medium text-gray-700">Giá</label>
                        <input type="number" id="topping_price_${index}" name="toppings[${index}][price]" min="0" placeholder="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_toppings_${index}_price" style="display: none;"></div>
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
                        <div class="text-red-500 text-xs mt-1 error-message" id="error_toppings_${index}_image" style="display: none;"></div>
                        <div id="topping_image_preview_container_${index}" class="mt-2 w-24 h-24 border border-gray-300 rounded-md overflow-hidden flex items-center justify-center bg-gray-100">
                            <img id="topping_image_preview_${index}" src="#" alt="Preview" class="hidden w-full h-full object-cover">
                            <span id="topping_image_placeholder_${index}" class="text-xs text-gray-400">Xem trước</span>
                        </div>
                    </div>
                </div>
            `;
            // Event listener will be handled by event delegation

            const imageInput = group.querySelector(`#topping_image_${index}`);
            const imagePreview = group.querySelector(`#topping_image_preview_${index}`);
            const imagePlaceholder = group.querySelector(`#topping_image_placeholder_${index}`);

            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        imagePlaceholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '#';
                    imagePreview.classList.add('hidden');
                    imagePlaceholder.classList.remove('hidden');
                }
            });

            return group;
        }

        // Add event listener for the main "Add Topping" button
        if (addToppingBtn) {
            addToppingBtn.addEventListener('click', () => {
                const toppingGroup = createToppingGroup(toppingCount);
                toppingsContainer.appendChild(toppingGroup);
                toppingCount++;
            });
        }

        // Use event delegation for topping-related buttons to avoid conflicts
        if (toppingsContainer) {
            toppingsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-topping-btn')) {
                    const toppingGroup = e.target.closest('.p-4.border.border-gray-200');
                    if (toppingGroup) {
                        toppingGroup.remove();
                    }
                }
            });
        }

        // Restore old topping data if validation fails
        @if (old('toppings'))
            const oldToppings = @json(old('toppings'));
            if (Array.isArray(oldToppings)) {
                oldToppings.forEach((topping, index) => {
                    const toppingGroup = createToppingGroup(index);
                    toppingsContainer.appendChild(toppingGroup);
                    const nameInput = toppingGroup.querySelector(
                        `input[name="toppings[${index}][name]"]`);
                    const priceInput = toppingGroup.querySelector(
                        `input[name="toppings[${index}][price]"]`);
                    const availableInput = toppingGroup.querySelector(
                        `input[name="toppings[${index}][available]"]`);
                    if (nameInput) nameInput.value = topping.name || '';
                    if (priceInput) priceInput.value = topping.price || '';
                    if (availableInput) availableInput.checked = topping.available === '1' || topping
                        .available === true || topping.available === 1;
                });
                toppingCount = oldToppings.length;
            }
        @endif

        // Restore old attribute data if validation fails
        @if (old('attributes'))
            const oldAttributes = @json(old('attributes'));
            if (Array.isArray(oldAttributes)) {
                oldAttributes.forEach((attribute, index) => {
                    let attributeGroup;
                    
                    // Use existing default attribute group for index 0, create new ones for others
                    if (index === 0) {
                        attributeGroup = attributesContainer.querySelector('.p-4.border.border-gray-200');
                    } else {
                        attributeGroup = createAttributeGroup(index);
                        attributesContainer.appendChild(attributeGroup);
                    }
                    
                    if (attributeGroup) {
                        // Set attribute name
                        const nameInput = attributeGroup.querySelector(`input[name="attributes[${index}][name]"]`);
                        if (nameInput) nameInput.value = attribute.name || '';
                        
                        // Set attribute values
                        if (attribute.values && Array.isArray(attribute.values)) {
                            const valuesContainer = attributeGroup.querySelector(`#attribute_values_container_${index}`);
                            // Clear default value first
                            valuesContainer.innerHTML = '';
                            
                            attribute.values.forEach((valueData, valueIndex) => {
                                const valueDiv = document.createElement('div');
                                valueDiv.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-3', 'p-3', 'border', 'border-dashed', 'border-gray-300', 'rounded-md');
                                valueDiv.innerHTML = `
                                    <div class="md:col-span-2">
                                        <label for="attribute_value_${index}_${valueIndex}" class="block text-xs font-medium text-gray-600">Tên giá trị</label>
                                        <input type="text" id="attribute_value_${index}_${valueIndex}" name="attributes[${index}][values][${valueIndex}][value]" placeholder="VD: Nhỏ"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="${valueData.value || ''}">
                                        <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_${valueIndex}_value" style="display: none;"></div>
                                    </div>
                                    <div>
                                        <label for="attribute_price_${index}_${valueIndex}" class="block text-xs font-medium text-gray-600">Giá (+/-)</label>
                                        <input type="number" id="attribute_price_${index}_${valueIndex}" name="attributes[${index}][values][${valueIndex}][price_adjustment]" placeholder="0" step="any"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-xs" value="${valueData.price_adjustment || ''}">
                                        <div class="text-red-500 text-xs mt-1 error-message" id="error_attributes_${index}_values_${valueIndex}_price_adjustment" style="display: none;"></div>
                                    </div>
                                    <button type="button" class="remove-attribute-value-btn text-red-500 hover:text-red-700 text-xs self-center justify-self-end md:col-start-3">Xóa</button>
                                `;
                                
                                // Add event listener for removing this value
                                const removeBtn = valueDiv.querySelector('.remove-attribute-value-btn');
                                removeBtn.addEventListener('click', () => {
                                    valueDiv.remove();
                                });
                                
                                valuesContainer.appendChild(valueDiv);
                            });
                        }
                    }
                });
                attributeCount = Math.max(oldAttributes.length, 1); // Ensure at least 1
            }
        @endif

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

        // Handle status and release date visibility
        const statusInputs = document.querySelectorAll('input[name="status"]');
        const releaseAtContainer = document.getElementById('release_at_container');

        function toggleReleaseDate() {
            const selectedStatus = document.querySelector('input[name="status"]:checked');
            if (selectedStatus && releaseAtContainer) {
                if (selectedStatus.value === 'coming_soon') {
                    releaseAtContainer.classList.remove('hidden');
                } else {
                    releaseAtContainer.classList.add('hidden');
                }
            }
        }

        if (statusInputs.length > 0 && releaseAtContainer) {
            statusInputs.forEach(input => {
                input.addEventListener('change', toggleReleaseDate);
            });
            
            // Initial check
            toggleReleaseDate();
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
    });
</script>

@endsection

@push('scripts')
<!-- Topping Modal Script -->
<script src="{{ asset('js/admin/topping-modal.js') }}"></script>

<script>
    // Khởi tạo ToppingModal
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Kiểm tra các element quan trọng
        console.log('Checking elements:');
        console.log('add-attribute-btn:', document.getElementById('add-attribute-btn'));
        console.log('attributes-container:', document.getElementById('attributes-container'));
        console.log('open-toppings-modal:', document.getElementById('open-toppings-modal'));
        console.log('release_at_container:', document.getElementById('release_at_container'));
        console.log('ingredients format radios:', document.querySelectorAll('input[name="ingredients_format"]').length);
        
        // Khởi tạo topping modal - tránh duplicate initialization
        try {
            if (!window.toppingModal) {
                window.toppingModal = new ToppingModal();
            }
            
            // Override confirmSelection để cập nhật count (chỉ làm một lần)
            if (window.toppingModal && !window.toppingModal.countOverridden) {
                const originalConfirmSelection = window.toppingModal.confirmSelection;
                window.toppingModal.confirmSelection = function() {
                    originalConfirmSelection.call(this);
                    
                    // Cập nhật số lượng toppings đã chọn
                    const countElement = document.getElementById('selected-toppings-count');
                    if (countElement) {
                        countElement.textContent = this.selectedToppings.size;
                    }
                };
                window.toppingModal.countOverridden = true;
            }
            
            console.log('ToppingModal initialized successfully');
        } catch (error) {
            console.error('Error initializing ToppingModal:', error);
        }
    });
</script>
@endpush
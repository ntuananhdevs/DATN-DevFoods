@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Tạo mã giảm giá')
@section('description', 'Tạo mã giảm giá mới cho hệ thống')

@section('content')
<style>
    /* Custom input styles */
    input[type="text"],
    input[type="number"],
    input[type="date"],
    input[type="time"],
    input[type="datetime-local"],
    textarea,
    select {
        transition: all 0.2s ease;
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }

    .dark input[type="text"],
    .dark input[type="number"],
    .dark input[type="date"],
    .dark input[type="time"],
    .dark input[type="datetime-local"],
    .dark textarea,
    .dark select {
        background: hsl(var(--background));
        border-color: hsl(var(--border));
        color: hsl(var(--foreground));
    }

    input[type="text"]:hover,
    input[type="number"]:hover,
    input[type="date"]:hover,
    input[type="time"]:hover,
    input[type="datetime-local"]:hover,
    textarea:hover,
    select:hover {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .dark input[type="text"]:hover,
    .dark input[type="number"]:hover,
    .dark input[type="date"]:hover,
    .dark input[type="time"]:hover,
    .dark input[type="datetime-local"]:hover,
    .dark textarea:hover,
    .dark select:hover {
        border-color: hsl(var(--primary));
        box-shadow: 0 0 0 2px hsl(var(--primary) / 0.2);
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    input[type="time"]:focus,
    input[type="datetime-local"]:focus,
    textarea:focus,
    select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        outline: none;
    }

    .dark input[type="text"]:focus,
    .dark input[type="number"]:focus,
    .dark input[type="date"]:focus,
    .dark input[type="time"]:focus,
    .dark input[type="datetime-local"]:focus,
    .dark textarea:focus,
    .dark select:focus {
        border-color: hsl(var(--primary));
        box-shadow: 0 0 0 3px hsl(var(--primary) / 0.3);
    }

    .form-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }

    .dark .form-section {
        background: hsl(var(--card));
        border: 1px solid hsl(var(--border));
    }

    .form-section h3 {
        color: #2d3748;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dark .form-section h3 {
        color: hsl(var(--foreground));
    }

    .form-section h3 i {
        color: #667eea;
    }

    .dark .form-section h3 i,
    .dark .form-section h3 svg {
        color: hsl(var(--primary));
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
        position: relative;
        padding-top: 16px;
    }

    .dark .checkbox-group {
        background: hsl(var(--background));
        border-color: hsl(var(--border));
    }

    .checkbox-group:hover {
        border-color: #667eea;
    }

    .dark .checkbox-group:hover {
        border-color: hsl(var(--primary));
        background: hsl(var(--accent) / 0.2);
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .checkbox-group label {
        margin: 0;
        cursor: pointer;
        flex: 1;
    }

    .dark .checkbox-group label {
        color: hsl(var(--foreground));
    }

    .tag-badge {
        position: absolute;
        top: 0;
        right: 0;
        padding: 2px 6px;
        border-bottom-left-radius: 6px;
        font-size: 0.65rem;
        line-height: 1;
        display: flex;
        align-items: center;
        z-index: 1;
    }

    .days-of-week {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    
    .ranks-selection {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    select[multiple] {
        height: auto;
    }

    .dark .text-muted,
    .dark small.text-muted {
        color: hsl(var(--muted-foreground)) !important;
    }

    .dark .bg-gray-50 {
        background: hsl(var(--accent));
    }

    .dark .bg-white {
        background: hsl(var(--card));
    }

    .dark .border-gray-200,
    .dark .border-gray-300 {
        border-color: hsl(var(--border));
    }

    .dark .text-gray-400,
    .dark .text-gray-500,
    .dark .text-gray-700 {
        color: hsl(var(--muted-foreground));
    }

    .dark .text-danger {
        color: hsl(var(--destructive));
    }

    .dark .bg-red-100 {
        background: hsl(var(--destructive) / 0.2);
    }

    .dark .text-red-700 {
        color: hsl(var(--destructive-foreground));
    }

    .dark .border-red-400 {
        border-color: hsl(var(--destructive));
    }

    /* Special badges and notifications */
    .dark .bg-yellow-50 {
        background: hsl(var(--warning) / 0.2);
    }

    .dark .border-yellow-200 {
        border-color: hsl(var(--warning) / 0.5);
    }

    .dark .text-yellow-800,
    .dark .text-yellow-600 {
        color: hsl(var(--warning-foreground));
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4 p-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Tạo mã giảm giá</h2>
                <p class="text-muted-foreground">Tạo mã giảm giá mới cho hệ thống</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.discount_codes.index') }}" class="btn btn-outline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-100 dark:bg-red-950/30 border border-red-400 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m15 9-6 6"></path>
                    <path d="m9 9 6 6"></path>
                </svg>
                <div>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card border rounded-lg overflow-hidden bg-card">
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Thông tin mã giảm giá</h3>
        </div>

        <form method="POST" action="{{ route('admin.discount_codes.store') }}">
            @csrf
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 16v-4"></path>
                                <path d="M12 8h.01"></path>
                            </svg>
                            Thông tin cơ bản
                        </h3>
                        
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
                            <small class="text-muted">Ví dụ: SUMMER2023, WELCOME10</small>
                            @error('code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên mã giảm giá <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Settings -->
                    <div class="form-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                            Cài đặt giảm giá
                        </h3>
                        
                        <div class="form-group mb-3">
                            <label for="discount_type" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                            <select name="discount_type" id="discount_type" class="form-control" required>
                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                                <option value="fixed_amount" {{ old('discount_type') == 'fixed_amount' ? 'selected' : '' }}>Số tiền cố định</option>
                                <option value="free_shipping" {{ old('discount_type') == 'free_shipping' ? 'selected' : '' }}>Miễn phí vận chuyển</option>
                            </select>
                            @error('discount_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="discount_value" class="form-label">Giá trị giảm giá <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" id="discount_value" class="form-control" step="0.01" min="0" value="{{ old('discount_value') }}" required>
                            @error('discount_value')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="min_requirement_type" class="form-label">Điều kiện tối thiểu</label>
                            <div class="flex gap-2">
                                <select name="min_requirement_type" id="min_requirement_type" class="form-control w-1/2">
                                    <option value="" {{ old('min_requirement_type') == '' ? 'selected' : '' }}>Không áp dụng</option>
                                    <option value="order_amount" {{ old('min_requirement_type') == 'order_amount' ? 'selected' : '' }}>Đơn hàng tối thiểu</option>
                                    <option value="product_price" {{ old('min_requirement_type') == 'product_price' ? 'selected' : '' }}>Giá sản phẩm tối thiểu</option>
                                </select>
                                <input type="number" name="min_requirement_value" id="min_requirement_value" class="form-control w-1/2" step="0.01" min="0" value="{{ old('min_requirement_value') }}" placeholder="Nhập giá trị...">
                            </div>
                            @error('min_requirement_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @error('min_requirement_value')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_discount_amount" class="form-label">Số tiền giảm tối đa</label>
                            <input type="number" name="max_discount_amount" id="max_discount_amount" class="form-control" step="0.01" min="0" value="{{ old('max_discount_amount') }}">
                            <small class="text-muted">Áp dụng khi loại giảm giá là phần trăm</small>
                            @error('max_discount_amount')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Applicable Items -->
                    <div class="form-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            Phạm vi áp dụng
                        </h3>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Phạm vi áp dụng</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                <div class="checkbox-group">
                                    <input type="radio" name="applicable_scope" id="applicable_scope_all" value="all_branches" {{ old('applicable_scope') == 'all_branches' ? 'checked' : '' }} checked>
                                    <label for="applicable_scope_all">Tất cả chi nhánh</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="radio" name="applicable_scope" id="applicable_scope_specific" value="specific_branches" {{ old('applicable_scope') == 'specific_branches' ? 'checked' : '' }}>
                                    <label for="applicable_scope_specific">Chi nhánh cụ thể</label>
                                </div>
                            </div>
                            @error('applicable_scope')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3" id="branch_selection" style="{{ old('applicable_scope') == 'specific_branches' ? '' : 'display: none;' }}">
                            <label class="form-label font-medium">Chọn chi nhánh</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto p-2 border rounded bg-white dark:bg-card">
                                    @foreach($branches as $branch)
                                        <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                                            <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                CN
                                            </span>
                                            <input type="checkbox" name="branch_ids[]" id="branch_{{ $branch->id }}" value="{{ $branch->id }}" 
                                                {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                            <label for="branch_{{ $branch->id }}">
                                                {{ $branch->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-right mt-2">
                                    <span class="text-sm text-blue-600 cursor-pointer select-all-branches">Chọn tất cả</span> | 
                                    <span class="text-sm text-red-600 cursor-pointer unselect-all-branches">Bỏ chọn tất cả</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Áp dụng cho</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                <div class="checkbox-group">
                                    <input type="radio" name="applicable_items" id="applicable_items_all" value="all_items" {{ old('applicable_items') == 'all_items' ? 'checked' : '' }} checked>
                                    <label for="applicable_items_all">Tất cả sản phẩm</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="radio" name="applicable_items" id="applicable_items_products" value="specific_products" {{ old('applicable_items') == 'specific_products' ? 'checked' : '' }}>
                                    <label for="applicable_items_products">Sản phẩm cụ thể</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="radio" name="applicable_items" id="applicable_items_variants" value="specific_variants" {{ old('applicable_items') == 'specific_variants' ? 'checked' : '' }}>
                                    <label for="applicable_items_variants">Biến thể sản phẩm cụ thể</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="radio" name="applicable_items" id="applicable_items_categories" value="specific_categories" {{ old('applicable_items') == 'specific_categories' ? 'checked' : '' }}>
                                    <label for="applicable_items_categories">Danh mục cụ thể</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="radio" name="applicable_items" id="applicable_items_combos" value="combos_only" {{ old('applicable_items') == 'combos_only' ? 'checked' : '' }}>
                                    <label for="applicable_items_combos">Chỉ áp dụng cho combo</label>
                                </div>
                            </div>
                            @error('applicable_items')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="products_selection" class="form-group mb-3" style="{{ old('applicable_items') == 'specific_products' ? '' : 'display: none;' }}">
                            <label class="form-label font-medium">Chọn sản phẩm</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="relative mb-2">
                                    <input type="text" id="product_search" placeholder="Tìm kiếm sản phẩm..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto p-2 border rounded bg-white dark:bg-card">
                                    @foreach($products as $product)
                                        <div class="product-item checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                                            <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                SP
                                            </span>
                                            <input type="checkbox" name="product_ids[]" id="product_{{ $product->id }}" value="{{ $product->id }}">
                                            <label for="product_{{ $product->id }}">
                                                {{ $product->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-right mt-2">
                                    <span class="text-sm text-blue-600 cursor-pointer select-all-products">Chọn tất cả</span> | 
                                    <span class="text-sm text-red-600 cursor-pointer unselect-all-products">Bỏ chọn tất cả</span>
                                </div>
                            </div>
                        </div>

                        <div id="categories_selection" class="form-group mb-3" style="{{ old('applicable_items') == 'specific_categories' ? '' : 'display: none;' }}">
                            <label class="form-label font-medium">Chọn danh mục</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto p-2 border rounded bg-white dark:bg-card">
                                    @foreach($categories as $category)
                                        <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                                            <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                DM
                                            </span>
                                            <input type="checkbox" name="category_ids[]" id="category_{{ $category->id }}" value="{{ $category->id }}">
                                            <label for="category_{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-right mt-2">
                                    <span class="text-sm text-blue-600 cursor-pointer select-all-categories">Chọn tất cả</span> | 
                                    <span class="text-sm text-red-600 cursor-pointer unselect-all-categories">Bỏ chọn tất cả</span>
                                </div>
                            </div>
                        </div>

                        <div id="combos_selection" class="form-group mb-3" style="{{ old('applicable_items') == 'combos_only' ? '' : 'display: none;' }}">
                            <label class="form-label font-medium">Chọn combo</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto p-2 border rounded bg-white dark:bg-card">
                                    @foreach($combos as $combo)
                                        <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                                            <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                Combo
                                            </span>
                                            <input type="checkbox" name="combo_ids[]" id="combo_{{ $combo->id }}" value="{{ $combo->id }}">
                                            <label for="combo_{{ $combo->id }}">
                                                {{ $combo->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-right mt-2">
                                    <span class="text-sm text-blue-600 cursor-pointer select-all-combos">Chọn tất cả</span> | 
                                    <span class="text-sm text-red-600 cursor-pointer unselect-all-combos">Bỏ chọn tất cả</span>
                                </div>
                            </div>
                        </div>

                        <div id="variants_selection" class="form-group mb-3" style="{{ old('applicable_items') == 'specific_variants' ? '' : 'display: none;' }}">
                            <label class="form-label font-medium">Chọn biến thể sản phẩm</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="relative mb-2">
                                    <input type="text" id="variant_search" placeholder="Tìm kiếm biến thể sản phẩm..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto p-2 border rounded bg-white dark:bg-card" id="variants_container">
                                    <!-- Variant items will be loaded dynamically -->
                                    <div class="col-span-full p-4 text-center">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                                        <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tải danh sách biến thể sản phẩm...</p>
                                    </div>
                                </div>
                                <div class="text-right mt-2">
                                    <span class="text-sm text-blue-600 cursor-pointer select-all-variants">Chọn tất cả</span> | 
                                    <span class="text-sm text-red-600 cursor-pointer unselect-all-variants">Bỏ chọn tất cả</span>
                                </div>
                            </div>
                        </div>
                        </div>

                    <!-- Usage Settings -->
                    <div class="form-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                            Cài đặt sử dụng
                        </h3>
                        
                        <div class="form-group mb-3">
                            <label class="form-label font-medium">Hạng thành viên áp dụng</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
                                    @php
                                        $ranks = [
                                            1 => ['name' => 'Đồng', 'color' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200'],
                                            2 => ['name' => 'Bạc', 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'],
                                            3 => ['name' => 'Vàng', 'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-200'],
                                            4 => ['name' => 'Bạch Kim', 'color' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-200'],
                                            5 => ['name' => 'Kim Cương', 'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200']
                                        ];
                                        $selectedRanks = old('applicable_ranks', []);
                                        if (!is_array($selectedRanks)) $selectedRanks = [];
                                    @endphp
                                    @foreach ($ranks as $rankValue => $rank)
                                        <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 transition-colors">
                                            <input type="checkbox" name="applicable_ranks[]" id="rank_{{ $rankValue }}" value="{{ $rankValue }}" {{ in_array($rankValue, $selectedRanks) ? 'checked' : '' }}>
                                            <label for="rank_{{ $rankValue }}" class="flex items-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $rank['color'] }} mr-2">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $rank['name'] }}
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4 border-t pt-4">
                                    <div class="bg-white dark:bg-card p-3 rounded border border-gray-200 dark:border-border">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" name="rank_exclusive" id="rank_exclusive" value="1" 
                                                    {{ old('rank_exclusive') ? 'checked' : '' }}
                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 dark:text-primary border-gray-300 dark:border-border rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="rank_exclusive" class="font-medium text-gray-700 dark:text-foreground">Áp dụng giới hạn cho hạng đã chọn</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('applicable_ranks')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                    </div>
                        
                        <div class="form-group mb-3">
                            <label for="usage_type" class="form-label">Loại sử dụng</label>
                            <select name="usage_type" id="usage_type" class="form-control">
                                <option value="public" {{ old('usage_type') == 'public' ? 'selected' : '' }}>Công khai</option>
                                <option value="personal" {{ old('usage_type') == 'personal' ? 'selected' : '' }}>Riêng tư</option>
                            </select>
                            <small class="text-muted">Mã riêng tư chỉ dành cho người dùng được chỉ định</small>
                            @error('usage_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- User Selection for Personal Discount Codes -->
                        <div id="users_selection" class="form-group mb-3" style="display: none;">
                            <label class="form-label font-medium">Chọn người dùng được áp dụng</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="relative mb-2">
                                    <input type="text" id="user_search" placeholder="Tìm kiếm người dùng..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-2 border rounded bg-white dark:bg-card">
                                    @php
                                        // Get all non-admin users from the database
                                        $users = \App\Models\User::whereDoesntHave('roles', function($query) {
                                                $query->where('name', 'admin');
                                            })
                                            ->orderBy('full_name')
                                            ->get();
                                        
                                        // For create page, there are no assigned users yet
                                        $assignedUsers = [];
                                        
                                        // Parse applicable_ranks to ensure it's an array
                                        $selectedRanks = old('applicable_ranks', []);
                                        if (!is_array($selectedRanks)) $selectedRanks = [];
                                    @endphp
                                    
                                    @foreach($users as $user)
                                        @php
                                            $userRankId = $user->user_rank_id ?? 0;
                                            $isEligible = empty($selectedRanks) || in_array($userRankId, $selectedRanks);
                                            
                                            // Skip this user if they're not eligible
                                            if (!$isEligible) continue;
                                            
                                            $rankName = '';
                                            $rankClass = 'bg-gray-100 text-gray-800';
                                            
                                            if ($userRankId == 1) {
                                                $rankName = 'Đồng';
                                                $rankClass = 'bg-amber-100 text-amber-800';
                                            } elseif ($userRankId == 2) {
                                                $rankName = 'Bạc';
                                                $rankClass = 'bg-gray-100 text-gray-800';
                                            } elseif ($userRankId == 3) {
                                                $rankName = 'Vàng';
                                                $rankClass = 'bg-yellow-100 text-yellow-800';
                                            } elseif ($userRankId == 4) {
                                                $rankName = 'Bạch Kim';
                                                $rankClass = 'bg-indigo-100 text-indigo-800';
                                            } elseif ($userRankId == 5) {
                                                $rankName = 'Kim Cương';
                                                $rankClass = 'bg-blue-100 text-blue-800';
                                            }
                                        @endphp
                                        <div class="user-item checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                                            <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium {{ $rankClass }} @if($userRankId == 1) dark:bg-amber-950 dark:text-amber-200 @elseif($userRankId == 2) dark:bg-gray-800 dark:text-gray-200 @elseif($userRankId == 3) dark:bg-yellow-950 dark:text-yellow-200 @elseif($userRankId == 4) dark:bg-indigo-950 dark:text-indigo-200 @elseif($userRankId == 5) dark:bg-blue-950 dark:text-blue-200 @else dark:bg-gray-800 dark:text-gray-200 @endif">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                {{ $rankName ?: 'Chưa xếp hạng' }}
                                            </span>
                                            <input type="checkbox" name="assigned_users[]" id="user_{{ $user->id }}" value="{{ $user->id }}">
                                            <label for="user_{{ $user->id }}" class="flex flex-col">
                                                <span class="font-medium">{{ $user->full_name }}</span>
                                                <span class="text-xs text-gray-500">{{ $user->email }}</span>
                                                <span class="text-xs text-gray-500">{{ $user->phone ?? 'Không có SĐT' }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @php
                                    // Count eligible users only
                                    $eligibleUsers = 0;
                                    foreach($users as $user) {
                                        $userRankId = $user->user_rank_id ?? 0;
                                        if(empty($selectedRanks) || in_array($userRankId, $selectedRanks)) {
                                            $eligibleUsers++;
                                        }
                                    }
                                @endphp
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500">Đang hiển thị {{ $eligibleUsers }} người dùng hợp lệ (trong tổng số {{ $users->count() }} người dùng)</span>
                                    <div>
                                        <span class="text-sm text-blue-600 cursor-pointer select-all-users">Chọn tất cả</span> | 
                                        <span class="text-sm text-red-600 cursor-pointer unselect-all-users">Bỏ chọn tất cả</span>
                                    </div>
                                </div>
                                
                                <div class="mt-3 bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-950/20 dark:border-yellow-900 dark:text-yellow-200 p-3 rounded">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p>
                                            <strong>Lưu ý:</strong> Chỉ hiển thị những người dùng có hạng thành viên phù hợp với các hạng đã chọn. 
                                            Người dùng không đủ điều kiện sẽ không được hiển thị trong danh sách.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_total_usage" class="form-label">Số lần sử dụng tối đa</label>
                            <input type="number" name="max_total_usage" id="max_total_usage" class="form-control" min="0" value="{{ old('max_total_usage') }}">
                            <small class="text-muted">Để trống nếu không giới hạn</small>
                            @error('max_total_usage')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_usage_per_user" class="form-label">Số lần sử dụng tối đa mỗi người dùng</label>
                            <input type="number" name="max_usage_per_user" id="max_usage_per_user" class="form-control" min="1" value="{{ old('max_usage_per_user', 1) }}">
                            @error('max_usage_per_user')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Schedule & Status -->
                    <div class="form-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Lịch trình & Trạng thái
                        </h3>
                        
                        <div class="form-group mb-3">
                            <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" required>
                            <small class="text-muted">Định dạng: YYYY-MM-DD HH:MM</small>
                            @error('start_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', now()->addDays(30)->format('Y-m-d\TH:i')) }}" required>
                            <small class="text-muted">Định dạng: YYYY-MM-DD HH:MM</small>
                            @error('end_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Các ngày áp dụng trong tuần</label>
                            <div class="days-of-week">
                                @php
                                    $days = [
                                        1 => 'Thứ Hai',
                                        2 => 'Thứ Ba',
                                        3 => 'Thứ Tư',
                                        4 => 'Thứ Năm',
                                        5 => 'Thứ Sáu',
                                        6 => 'Thứ Bảy',
                                        0 => 'Chủ Nhật',
                                    ];
                                    $selectedDays = old('valid_days_of_week', []);
                                @endphp
                                @foreach ($days as $dayValue => $dayName)
                                    <div class="checkbox-group">
                                        <input type="checkbox" name="valid_days_of_week[]" id="day_{{ $dayValue }}" value="{{ $dayValue }}" {{ in_array($dayValue, $selectedDays) ? 'checked' : '' }}>
                                        <label for="day_{{ $dayValue }}">{{ $dayName }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Nếu không chọn ngày nào, mã sẽ áp dụng mọi ngày trong tuần</small>
                            @error('valid_days_of_week')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="valid_from_time" class="form-label">Giờ áp dụng trong ngày</label>
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-950/20 dark:border-yellow-900 dark:text-yellow-200 p-3 rounded mb-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>
                                        <strong>Lưu ý:</strong> Các trường này dùng để giới hạn thời gian trong ngày mà mã giảm giá có hiệu lực (ví dụ: chỉ áp dụng từ 9:00 đến 17:00 hàng ngày). 
                                        Khác với ngày bắt đầu và kết thúc ở trên là thời gian tổng thể mã giảm giá có hiệu lực.
                                    </p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="valid_from_time" class="form-label">Giờ bắt đầu trong ngày</label>
                                    <input type="time" name="valid_from_time" id="valid_from_time" class="form-control" value="{{ old('valid_from_time') }}">
                                    <small class="text-muted">Để trống nếu áp dụng cả ngày</small>
                                    @error('valid_from_time')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="valid_to_time" class="form-label">Giờ kết thúc trong ngày</label>
                                    <input type="time" name="valid_to_time" id="valid_to_time" class="form-control" value="{{ old('valid_to_time') }}">
                                    <small class="text-muted">Để trống nếu áp dụng cả ngày</small>
                                    @error('valid_to_time')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="checkbox-group">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active">Kích hoạt</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="checkbox-group">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label for="is_featured">Hiển thị nổi bật</label>
                            </div>
                            @error('is_featured')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="display_order" class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="display_order" id="display_order" class="form-control" min="0" value="{{ old('display_order', 0) }}">
                            @error('display_order')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 border-t flex items-center justify-end gap-3">
                <a href="{{ route('admin.discount_codes.index') }}" class="btn btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17,21 17,13 7,13 7,21"></polyline>
                        <polyline points="7,3 7,8 15,8"></polyline>
                    </svg>
                    Tạo mã giảm giá
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="module" src="{{ asset('js/pages/admin/discount_codes/initCreate.js') }}"></script>

<!-- Add hidden elements to store selected IDs -->
<script id="selected_products_data" type="application/json">
    []
</script>
<script id="selected_categories_data" type="application/json">
    []
</script>
<script id="selected_combos_data" type="application/json">
    []
</script>
<script id="selected_variants_data" type="application/json">
    []
</script>
<script id="selected_users_data" type="application/json">
    []
</script>
@endsection
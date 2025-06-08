@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh sửa mã giảm giá')
@section('description', 'Chỉnh sửa thông tin mã giảm giá')

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

    .form-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
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

    .form-section h3 i {
        color: #667eea;
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
    }

    .checkbox-group:hover {
        border-color: #667eea;
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
</style>

<div class="fade-in flex flex-col gap-4 pb-4 p-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-edit-3">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Chỉnh sửa: {{ $discountCode->code }}</h2>
                <p class="text-muted-foreground">Chỉnh sửa thông tin mã giảm giá</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.discount_codes.show', $discountCode) }}" class="btn btn-outline flex items-center">
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
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
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
    <div class="card border rounded-lg overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Thông tin mã giảm giá</h3>
        </div>

        <form method="POST" action="{{ route('admin.discount_codes.update', $discountCode->id) }}">
            @csrf
            @method('PUT')
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
                            <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $discountCode->code) }}" required>
                            <small class="text-muted">Ví dụ: SUMMER2023, WELCOME10</small>
                            @error('code')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên mã giảm giá <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $discountCode->name) }}" required>
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $discountCode->description) }}</textarea>
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
                                <option value="percentage" {{ old('discount_type', $discountCode->discount_type) == 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                                <option value="fixed_amount" {{ old('discount_type', $discountCode->discount_type) == 'fixed_amount' ? 'selected' : '' }}>Số tiền cố định</option>
                                <option value="free_shipping" {{ old('discount_type', $discountCode->discount_type) == 'free_shipping' ? 'selected' : '' }}>Miễn phí vận chuyển</option>
                            </select>
                            @error('discount_type')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="discount_value" class="form-label">Giá trị giảm giá <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" id="discount_value" class="form-control" step="0.01" min="0" value="{{ old('discount_value', $discountCode->discount_value) }}" required>
                            @error('discount_value')
                            <div class="text-danger">{{ $message }} </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="min_order_amount" class="form-label">Số tiền đơn hàng tối thiểu</label>
                            <input type="number" name="min_order_amount" id="min_order_amount" class="form-control" step="0.01" min="0" value="{{ old('min_order_amount', $discountCode->min_order_amount) }}">
                            @error('min_order_amount')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_discount_amount" class="form-label">Số tiền giảm tối đa</label>
                            <input type="number" name="max_discount_amount" id="max_discount_amount" class="form-control" step="0.01" min="0" value="{{ old('max_discount_amount', $discountCode->max_discount_amount) }}">
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
                            <label for="applicable_items" class="form-label">Áp dụng cho</label>
                            <select name="applicable_items" id="applicable_items" class="form-control">
                                <option value="all_items" {{ old('applicable_items', $discountCode->applicable_items) == 'all_items' ? 'selected' : '' }}>Tất cả sản phẩm</option>
                                <option value="specific_products" {{ old('applicable_items', $discountCode->applicable_items) == 'specific_products' ? 'selected' : '' }}>Sản phẩm cụ thể</option>
                                <option value="specific_categories" {{ old('applicable_items', $discountCode->applicable_items) == 'specific_categories' ? 'selected' : '' }}>Danh mục cụ thể</option>
                                <option value="combos_only" {{ old('applicable_items', $discountCode->applicable_items) == 'combos_only' ? 'selected' : '' }}>Chỉ áp dụng cho combo</option>
                            </select>
                            @error('applicable_items')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="products_selection" class="form-group mb-3" style="{{ old('applicable_items', $discountCode->applicable_items) == 'specific_products' ? '' : 'display: none;' }}">
                            <label for="items" class="form-label">Chọn sản phẩm</label>
                            <select name="items[]" class="form-control" multiple size="6">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                        {{ in_array($product->id, $selectedProducts ?? []) ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Giữ phím Ctrl (hoặc Command trên Mac) để chọn nhiều sản phẩm</small>
                        </div>

                        <div id="categories_selection" class="form-group mb-3" style="{{ old('applicable_items', $discountCode->applicable_items) == 'specific_categories' ? '' : 'display: none;' }}">
                            <label for="items" class="form-label">Chọn danh mục</label>
                            <select name="items[]" class="form-control" multiple size="6">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ in_array($category->id, $selectedCategories ?? []) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Giữ phím Ctrl (hoặc Command trên Mac) để chọn nhiều danh mục</small>
                        </div>

                        <div id="combos_selection" class="form-group mb-3" style="{{ old('applicable_items', $discountCode->applicable_items) == 'combos_only' ? '' : 'display: none;' }}">
                            <label for="items" class="form-label">Chọn combo</label>
                            <select name="items[]" class="form-control" multiple size="6">
                                @foreach($combos as $combo)
                                    <option value="{{ $combo->id }}" 
                                        {{ in_array($combo->id, $selectedCombos ?? []) ? 'selected' : '' }}>
                                        {{ $combo->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Giữ phím Ctrl (hoặc Command trên Mac) để chọn nhiều combo</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="applicable_scope" class="form-label">Phạm vi áp dụng</label>
                            <select name="applicable_scope" id="applicable_scope" class="form-control">
                                <option value="all_branches" {{ old('applicable_scope', $discountCode->applicable_scope) == 'all_branches' ? 'selected' : '' }}>Tất cả chi nhánh</option>
                                <option value="specific_branches" {{ old('applicable_scope', $discountCode->applicable_scope) == 'specific_branches' ? 'selected' : '' }}>Chi nhánh cụ thể</option>
                            </select>
                            @error('applicable_scope')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3" id="branch_selection" style="{{ old('applicable_scope', $discountCode->applicable_scope) == 'specific_branches' ? '' : 'display: none;' }}">
                            <label for="branch_ids" class="form-label">Chọn chi nhánh</label>
                            <select name="branch_ids[]" id="branch_ids" class="form-control" multiple size="5">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" 
                                        {{ in_array($branch->id, $selectedBranches ?? []) ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Giữ phím Ctrl (hoặc Command trên Mac) để chọn nhiều chi nhánh</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Hạng thành viên áp dụng</label>
                            <div class="ranks-selection">
                                @php
                                    $ranks = [
                                        1 => 'Đồng',
                                        2 => 'Bạc',
                                        3 => 'Vàng',
                                        4 => 'Bạch Kim',
                                        5 => 'Kim Cương'
                                    ];
                                    // Parse applicable_ranks to ensure it's an array
                                    $rawRanks = old('applicable_ranks', $discountCode->applicable_ranks ?? []);
                                    $selectedRanks = is_string($rawRanks) ? json_decode($rawRanks, true) ?? [] : (array) $rawRanks;
                                @endphp
                                @foreach ($ranks as $rankValue => $rankName)
                                    <div class="checkbox-group">
                                        <input type="checkbox" name="applicable_ranks[]" id="rank_{{ $rankValue }}" value="{{ $rankValue }}" {{ in_array($rankValue, $selectedRanks) ? 'checked' : '' }}>
                                        <label for="rank_{{ $rankValue }}">{{ $rankName }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="checkbox-group mt-2">
                                <input type="checkbox" name="rank_exclusive" id="rank_exclusive" value="1" {{ old('rank_exclusive', $discountCode->rank_exclusive ?? false) ? 'checked' : '' }}>
                                <label for="rank_exclusive">Chỉ áp dụng cho các hạng đã chọn</label>
                            </div>
                            @error('applicable_ranks')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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
                            <label for="usage_type" class="form-label">Loại sử dụng</label>
                            <select name="usage_type" id="usage_type" class="form-control">
                                <option value="public" {{ old('usage_type', $discountCode->usage_type) == 'public' ? 'selected' : '' }}>Công khai</option>
                                <option value="personal" {{ old('usage_type', $discountCode->usage_type) == 'personal' ? 'selected' : '' }}>Riêng tư</option>
                            </select>
                            <small class="text-muted">Mã riêng tư chỉ dành cho người dùng được chỉ định</small>
                            @error('usage_type')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_total_usage" class="form-label">Số lần sử dụng tối đa</label>
                            <input type="number" name="max_total_usage" id="max_total_usage" class="form-control" min="0" value="{{ old('max_total_usage', $discountCode->max_total_usage) }}">
                            <small class="text-muted">Để trống nếu không giới hạn</small>
                            @error('max_total_usage')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_usage_per_user" class="form-label">Số lần sử dụng tối đa mỗi người dùng</label>
                            <input type="number" name="max_usage_per_user" id="max_usage_per_user" class="form-control" min="1" value="{{ old('max_usage_per_user', $discountCode->max_usage_per_user) }}">
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
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $discountCode->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $discountCode->end_date->format('Y-m-d')) }}" required>
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
                                // Parse valid_days_of_week để đảm bảo là mảng
                                $rawDays = old('valid_days_of_week', $discountCode->valid_days_of_week ?? []);
                                $selectedDays = is_string($rawDays) ? json_decode($rawDays, true) ?? [] : (array) $rawDays;
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
                            <label for="valid_from_time" class="form-label">Giờ bắt đầu</label>
                            <input type="time" name="valid_from_time" id="valid_from_time" class="form-control" value="{{ old('valid_from_time', $discountCode->valid_from_time ? $discountCode->valid_from_time->format('H:i') : '') }}">
                            @error('valid_from_time')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="valid_to_time" class="form-label">Giờ kết thúc</label>
                            <input type="time" name="valid_to_time" id="valid_to_time" class="form-control" value="{{ old('valid_to_time', $discountCode->valid_to_time ? $discountCode->valid_to_time->format('H:i') : '') }}">
                            @error('valid_to_time')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="checkbox-group">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $discountCode->is_active) ? 'checked' : '' }}>
                                <label for="is_active">Kích hoạt</label>
                            </div>
                            @error('is_active')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="checkbox-group">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $discountCode->is_featured) ? 'checked' : '' }}>
                                <label for="is_featured">Hiển thị nổi bật</label>
                            </div>
                            @error('is_featured')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="display_order" class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="display_order" id="display_order" class="form-control" min="0" value="{{ old('display_order', $discountCode->display_order) }}">
                            @error('display_order')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 border-t flex items-center justify-end gap-3">
                <a href="{{ route('admin.discount_codes.show', $discountCode) }}" class="btn btn-outline">
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
                    Cập nhật mã giảm giá
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Branch selection toggle
        const applicableScopeSelect = document.getElementById('applicable_scope');
        const branchSelectionDiv = document.getElementById('branch_selection');
        
        // Items selection toggle
        const applicableItemsSelect = document.getElementById('applicable_items');
        const productsSelectionDiv = document.getElementById('products_selection');
        const categoriesSelectionDiv = document.getElementById('categories_selection');
        const combosSelectionDiv = document.getElementById('combos_selection');
        
        // Initial state check
        toggleBranchSelection();
        toggleItemsSelection();
        
        // Add event listeners for changes
        applicableScopeSelect.addEventListener('change', toggleBranchSelection);
        applicableItemsSelect.addEventListener('change', toggleItemsSelection);
        
        function toggleBranchSelection() {
            if (applicableScopeSelect.value === 'specific_branches') {
                branchSelectionDiv.style.display = 'block';
            } else {
                branchSelectionDiv.style.display = 'none';
            }
        }
        
        function toggleItemsSelection() {
            // Hide all selection divs first
            productsSelectionDiv.style.display = 'none';
            categoriesSelectionDiv.style.display = 'none';
            combosSelectionDiv.style.display = 'none';
            
            // Show the appropriate selection div based on selected value
            switch(applicableItemsSelect.value) {
                case 'specific_products':
                    productsSelectionDiv.style.display = 'block';
                    break;
                case 'specific_categories':
                    categoriesSelectionDiv.style.display = 'block';
                    break;
                case 'combos_only':
                    combosSelectionDiv.style.display = 'block';
                    break;
            }
        }
    });
</script>
@endsection
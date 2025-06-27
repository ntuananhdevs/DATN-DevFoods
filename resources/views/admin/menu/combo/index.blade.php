@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý Combo')
@section('description', 'Quản lý danh sách combo của bạn')

@section('content')
<style>
    /* Custom input styles */
    input[type="text"],
    input[type="number"],
    input[type="date"],
    select {
        transition: all 0.2s ease;
    }

    input[type="text"]:hover,
    input[type="number"]:hover,
    input[type="date"]:hover,
    select:hover {
        border-color: #3b82f6;
        /* Blue-500 from Tailwind */
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #2563eb;
        /* Blue-600 */
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        outline: none;
    }

    /* Enhanced tag styling */
    .status-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }

    .status-tag.success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .status-tag.failed {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .status-tag.featured {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-tag:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Price slider styling */
    .price-range-container {
        margin: 10px 0;
        padding: 10px 0;
    }
    
    .price-slider {
        position: relative;
        height: 4px;
        background: #e5e7eb;
        margin: 20px 10px 30px;
        border-radius: 2px;
    }
    
    .price-slider-track {
        position: absolute;
        height: 100%;
        background: #3b82f6;
        border-radius: 2px;
    }
    
    .price-slider-handle {
        position: absolute;
        width: 16px;
        height: 16px;
        background: #2563eb;
        border: 2px solid #fff;
        border-radius: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 2;
    }
    
    .price-inputs {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
    }
    
    .price-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
    }
    
    .price-display {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        margin-top: 5px;
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package">
                    <path d="m7.5 4.27 9 5.15"></path>
                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                    <path d="m3.3 7 8.7 5 8.7-5"></path>
                    <path d="M12 22V12"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý Combo</h2>
                <p class="text-muted-foreground">Quản lý danh sách combo của bạn</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="dropdown relative">
                <button class="btn btn-outline flex items-center" id="exportDropdown" onclick="toggleDropdown('exportMenu')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Xuất
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                    <div class="p-2">
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M8 13h2"></path>
                                <path d="M8 17h2"></path>
                                <path d="M14 13h2"></path>
                                <path d="M14 17h2"></path>
                            </svg>
                            Xuất Excel
                        </a>
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Xuất PDF
                        </a>
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M8 13h8"></path>
                                <path d="M8 17h8"></path>
                            </svg>
                            Xuất CSV
                        </a>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.combos.create') }}" class="btn btn-primary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Thêm mới
            </a>
        </div>
    </div>

    <!-- Card containing table -->
    <div class="card border rounded-lg overflow-hidden">
        <!-- Table header -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách combo</h3>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" placeholder="Tìm kiếm theo tên combo, SKU..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput" value="{{ request('search') }}" onkeyup="handleSearch(event)">
            </div>
            <div class="flex items-center gap-2">
                <!-- View Toggle Buttons -->
                <div class="flex items-center border rounded-md p-1 bg-gray-50">
                    <button id="tableViewBtn" class="flex items-center px-3 py-1 rounded text-sm font-medium transition-colors bg-white shadow-sm border">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M9 3v18"></path>
                            <path d="M15 3v18"></path>
                            <path d="M3 9h18"></path>
                            <path d="M3 15h18"></path>
                        </svg>
                        Bảng
                    </button>
                    <button id="cardViewBtn" class="flex items-center px-3 py-1 rounded text-sm font-medium transition-colors text-gray-600 hover:text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                        </svg>
                        Thẻ
                    </button>
                </div>
                <button class="btn btn-outline flex items-center" id="selectAllButton" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span>Chọn tất cả</span>
                </button>
                <div class="dropdown relative">
                    <button class="btn btn-outline flex items-center" id="actionsDropdown" onclick="toggleDropdown('actionsMenu')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <circle cx="12" cy="12" r="2"></circle>
                            <circle cx="12" cy="5" r="2"></circle>
                            <circle cx="12" cy="19" r="2"></circle>
                        </svg>
                        Thao tác
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div id="actionsMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                        <div class="p-2">
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-green-500">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                                Kích hoạt đã chọn
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(0)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-red-500">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m15 9-6 6"></path>
                                    <path d="m9 9 6 6"></path>
                                </svg>
                                Vô hiệu hóa đã chọn
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedFeatured(1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-yellow-500">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                Đặt nổi bật
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedFeatured(0)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-gray-500">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                Bỏ nổi bật
                            </a>
                        </div>
                    </div>
                </div>
                <button class="btn btn-outline flex items-center" onclick="toggleModal('filterModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Lọc
                </button>
            </div>
        </div>

        <!-- Table content -->
        <div class="overflow-x-auto" id="tableView">
            @forelse($combos as $combo)
                @if($loop->first)
                    <table class="w-full">
                        <thead class="border-b">
                            <tr class="text-left">
                                <th class="p-4 font-medium text-muted-foreground">
                                    <input type="checkbox" class="rounded border-gray-300" id="selectAll">
                                </th>
                                <th class="p-4 font-medium text-muted-foreground">SKU</th>
                                <th class="p-4 font-medium text-muted-foreground">Hình ảnh</th>
                                <th class="p-4 font-medium text-muted-foreground">Tên combo</th>
                                <th class="p-4 font-medium text-muted-foreground">Sản phẩm</th>
                                <th class="p-4 font-medium text-muted-foreground">Số lượng</th>
                                <th class="p-4 font-medium text-muted-foreground">Giá</th>
                                <th class="p-4 font-medium text-muted-foreground">Trạng thái</th>
                                <th class="p-4 font-medium text-muted-foreground">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                <tr class="border-b hover:bg-muted/50 transition-colors" data-combo-id="{{ $combo->id }}">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 combo-checkbox" value="{{ $combo->id }}">
                    </td>
                    <td class="p-4 font-mono text-sm text-muted-foreground">{{ $combo->sku }}</td>
                    <td class="p-4">
                        @if($combo->image)
                            <img src="{{ $combo->image_url }}" 
                                 alt="{{ $combo->name }}" 
                                 class="w-12 h-12 rounded-lg object-cover border">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-muted flex items-center justify-center border">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                                    <circle cx="9" cy="9" r="2"></circle>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="font-medium">{{ $combo->name }}</div>
                        @if($combo->description)
                            <div class="text-sm text-muted-foreground mt-1">{{ Str::limit($combo->description, 40) }}</div>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="space-y-1">
                            @if($combo->comboItems && $combo->comboItems->count() > 0)
                                @foreach($combo->comboItems->take(3) as $item)
                                    <div class="flex items-center gap-2 text-sm">
                                        @if($item->productVariant && $item->productVariant->product)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                {{ $item->productVariant->product->name }}
                                                @if($item->productVariant->variant_attribute_value_1)
                                                    - {{ $item->productVariant->variant_attribute_value_1 }}
                                                @endif
                                                @if($item->productVariant->variant_attribute_value_2)
                                                    - {{ $item->productVariant->variant_attribute_value_2 }}
                                                @endif
                                                <span class="ml-1 text-gray-600">x{{ $item->quantity }}</span>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                                @if($combo->comboItems->count() > 3)
                                    <div class="text-xs text-muted-foreground">
                                        +{{ $combo->comboItems->count() - 3 }} sản phẩm khác
                                    </div>
                                @endif
                            @else
                                <span class="text-sm text-muted-foreground">Chưa có sản phẩm</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   value="{{ $combo->quantity ?? 0 }}" 
                                   min="0" 
                                   class="w-20 px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   onchange="updateQuantity({{ $combo->id }}, this.value)"
                                   id="quantity-{{ $combo->id }}">
                            <button type="button" 
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    onclick="updateQuantity({{ $combo->id }}, document.getElementById('quantity-{{ $combo->id }}').value)"
                                    title="Cập nhật số lượng">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                    <path d="M21 3v5h-5"></path>
                                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                    <path d="M3 21v-5h5"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-green-600">{{ number_format($combo->price) }}đ</div>
                    </td>
                    <td class="p-4">
                        <span class="status-tag {{ $combo->active ? 'success' : 'failed' }}">
                            {{ $combo->active ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.combos.show', $combo->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground transition-colors" 
                               title="Xem chi tiết">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            <a href="{{ route('admin.combos.edit', $combo->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground transition-colors" 
                               title="Chỉnh sửa">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                    <path d="m15 5 4 4"></path>
                                </svg>
                            </a>
                            <button type="button" 
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground transition-colors" 
                                    title="Chuyển trạng thái"
                                    onclick="toggleStatus({{ $combo->id }}, '{{ $combo->status }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $combo->status === 'active' ? 'text-green-500' : 'text-gray-400' }}">
                                    <rect width="14" height="8" x="5" y="8" rx="4" ry="4"></rect>
                                    <path d="{{ $combo->status === 'active' ? 'm13 8-2 2-2-2' : 'm9 12 2 2 4-4' }}"></path>
                                </svg>
                            </button>
                            <button type="button" 
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-input bg-background hover:bg-destructive hover:text-destructive-foreground transition-colors" 
                                    title="Xóa"
                                    onclick="confirmDelete({{ $combo->id }}, '{{ $combo->name }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @if($loop->last)
                        </tbody>
                    </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-4 border-t flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Hiển thị {{ $combos->firstItem() }} đến {{ $combos->lastItem() }} trong tổng số {{ $combos->total() }} kết quả
                        </div>
                        <div>
                            {{ $combos->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            @empty
                <div class="p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted-foreground mb-4">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">Không có combo nào</h3>
                    <p class="text-muted-foreground mb-4">Hãy tạo combo đầu tiên cho cửa hàng của bạn.</p>
                    <a href="{{ route('admin.combos.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                        Thêm combo
                    </a>
                </div>
            @endforelse
        </div>
        
        <!-- Card Grid View -->
        <div id="cardView" class="hidden p-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Tổng Combo</div>
                    <div class="text-2xl font-bold">{{ $combos->total() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Combo Hoạt Động</div>
                    <div class="text-2xl font-bold text-green-600">{{ $combos->where('active', 1)->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Combo Tạm Dừng</div>
                    <div class="text-2xl font-bold text-red-600">{{ $combos->where('active', 0)->count() }}</div>
                </div>
            </div>
            
            <!-- Combo Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($combos as $combo)
                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="relative">
                        @if($combo->image)
                            <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                                    <circle cx="9" cy="9" r="2"></circle>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                                </svg>
                            </div>
                        @endif
                        <span class="absolute top-2 right-2 {{ $combo->active ? 'bg-green-500' : 'bg-red-500' }} text-white text-xs px-2 py-1 rounded-full">
                            {{ $combo->active ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                        @if($combo->original_price && $combo->original_price > $combo->price)
                            <span class="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full">
                                -{{ round((($combo->original_price - $combo->price) / $combo->original_price) * 100) }}%
                            </span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $combo->name }}</h3>
                        @if($combo->description)
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($combo->description, 60) }}</p>
                        @endif
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-orange-600">{{ number_format($combo->price) }}₫</span>
                                @if($combo->original_price && $combo->original_price > $combo->price)
                                    <span class="text-sm text-gray-500 line-through">{{ number_format($combo->original_price) }}₫</span>
                                @endif
                            </div>
                            @if($combo->comboItems && $combo->comboItems->count() > 0)
                                <div class="text-sm text-gray-600">
                                    <strong>Bao gồm:</strong>
                                    <ul class="mt-1 space-y-1">
                                        @foreach($combo->comboItems->take(2) as $item)
                                            @if($item->productVariant && $item->productVariant->product)
                                                <li class="flex justify-between">
                                                    <span>
                                                        {{ $item->productVariant->product->name }}
                                                        @if($item->productVariant->variant_attribute_value_1)
                                                            ({{ $item->productVariant->variant_attribute_value_1 }})
                                                        @endif
                                                    </span>
                                                    <span>x{{ $item->quantity }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                        @if($combo->comboItems->count() > 2)
                                            <li class="text-xs text-gray-500">+{{ $combo->comboItems->count() - 2 }} sản phẩm khác</li>
                                        @endif
                                    </ul>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">
                                    <em>Chưa có sản phẩm nào</em>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 border-t flex gap-2">
                        <a href="{{ route('admin.combos.edit', $combo->id) }}" class="flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                <path d="m15 5 4 4"></path>
                            </svg>
                            Sửa
                        </a>
                        <button type="button" onclick="deleteCombo({{ $combo->id }})" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-red-50 text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" x2="10" y1="11" y2="17"></line>
                                <line x1="14" x2="14" y1="11" y2="17"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 mb-4">
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có combo nào</h3>
                    <p class="text-gray-500 mb-4">Bắt đầu bằng cách tạo combo đầu tiên của bạn.</p>
                    <a href="{{ route('admin.combos.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                        Tạo combo đầu tiên
                    </a>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination for Card View -->
            @if($combos->hasPages())
                <div class="flex justify-center mt-8">
                    {{ $combos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium">Bộ lọc nâng cao</h3>
            <button onclick="toggleModal('filterModal')" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m18 6-12 12"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <form method="GET" action="{{ route('admin.combos.index') }}" class="space-y-4">
            <div>
                <label for="filter_category" class="block text-sm font-medium mb-2">Danh mục</label>
                <select id="filter_category" name="category_id" class="w-full border rounded-md px-3 py-2">
                    <option value="">Tất cả danh mục</option>
                    @if(isset($categories))
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label for="filter_status" class="block text-sm font-medium mb-2">Trạng thái</label>
                <select id="filter_status" name="status" class="w-full border rounded-md px-3 py-2">
                    <option value="">Tất cả</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>
            <div>
                <label for="filter_featured" class="block text-sm font-medium mb-2">Nổi bật</label>
                <select id="filter_featured" name="is_featured" class="w-full border rounded-md px-3 py-2">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Nổi bật</option>
                    <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Không nổi bật</option>
                </select>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="submit" class="btn btn-primary flex-1">Áp dụng</button>
                <a href="{{ route('admin.combos.index') }}" class="btn btn-outline flex-1 text-center">Đặt lại</a>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-red-600">Xác nhận xóa</h3>
            <button onclick="toggleModal('deleteModal')" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m18 6-12 12"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-6">
            <p class="text-gray-700 mb-2">Bạn có chắc chắn muốn xóa combo <strong id="comboName"></strong>?</p>
            <p class="text-sm text-red-600">Hành động này không thể hoàn tác!</p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="toggleModal('deleteModal')" class="btn btn-outline flex-1">Hủy</button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-destructive w-full">Xóa</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Modal functions
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
    }

    // Dropdown functions
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.classList.toggle('hidden');
    }

    // Search functionality with AJAX
    function handleSearch() {
        performSearch();
    }
    
    // Variables for search functionality
    let searchTimeout = null;
    let currentPage = {{ $combos->currentPage() ?? 1 }};
    let currentSearch = '{{ request('search') }}';

    // Hàm tải dữ liệu combo
    function loadCombos(page = 1, search = currentSearch) {
        currentPage = page;
        currentSearch = search;

        // Hiển thị loading
        const tableBody = document.querySelector('#tableView tbody');
        const cardGrid = document.querySelector('#cardView .grid');
        if (tableBody) tableBody.classList.add('loading');
        if (cardGrid) cardGrid.classList.add('loading');

        const params = new URLSearchParams();
        params.append('page', page);
        if (search) params.append('search', search);
        params.append('ajax', '1');
        
        // Get filter values
        const filterStatus = document.getElementById('filter_status');
        const filterCategory = document.getElementById('filter_category');
        const filterFeatured = document.getElementById('filter_featured');
        
        if (filterStatus && filterStatus.value) {
            params.append('status', filterStatus.value === 'active' ? '1' : '0');
        }
        if (filterCategory && filterCategory.value) {
            params.append('category_id', filterCategory.value);
        }
        if (filterFeatured && filterFeatured.value) {
            params.append('is_featured', filterFeatured.value);
        }

        fetch(`{{ route('admin.combos.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateComboDisplay(data.combos, data.stats);
                if (data.pagination) {
                    updatePagination(data.pagination);
                }
                updateURL(page, search);
            } else {
                showErrorAlert('Lỗi tải dữ liệu', data.message || 'Vui lòng thử lại sau');
            }
        })
        .catch(error => {
            console.error('Load error:', error);
            showErrorAlert('Lỗi tải dữ liệu', 'Vui lòng thử lại sau');
        })
        .finally(() => {
            // Ẩn loading khi hoàn thành
            if (tableBody) tableBody.classList.remove('loading');
            if (cardGrid) cardGrid.classList.remove('loading');
        });
    }

    function showErrorAlert(title, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                confirmButtonColor: '#4361ee',
            });
        } else {
            alert(title + ': ' + message);
        }
    }

    // Xử lý tìm kiếm
    function handleSearch(event) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchValue = event.target.value;
            loadCombos(1, searchValue);
        }, 500);
    }

    // Perform AJAX search (legacy support)
    function performSearch() {
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value : '';
        loadCombos(1, searchValue);
    }
    
    // Render table rows from combo data
    function renderTableRows(combos) {
        if (!combos || combos.length === 0) {
            return '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Không có combo nào được tìm thấy</td></tr>';
        }
        
        return combos.map(combo => {
            const imageHtml = combo.image 
                ? `<img src="{{ asset('storage/') }}/${combo.image}" alt="${escapeHtml(combo.name)}" class="w-12 h-12 object-cover rounded-lg mr-3">`
                : '<div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-image text-gray-400"></i></div>';
            
            const statusClass = combo.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            const statusText = combo.status === 'active' ? 'Hoạt động' : 'Không hoạt động';
            const featuredIcon = combo.is_featured ? '<i class="fas fa-star text-yellow-500"></i>' : '<i class="fas fa-star text-gray-300"></i>';
            const toggleClass = combo.status === 'active' ? 'red' : 'green';
            const toggleIcon = combo.status === 'active' ? 'ban' : 'check';
            const toggleTitle = combo.status === 'active' ? 'Vô hiệu hóa' : 'Kích hoạt';
            
            return `
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="combo-checkbox" value="${combo.id}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            ${imageHtml}
                            <div>
                                <div class="text-sm font-medium text-gray-900">${escapeHtml(combo.name)}</div>
                                <div class="text-sm text-gray-500">SKU: ${escapeHtml(combo.sku || 'N/A')}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${escapeHtml(combo.category?.name || 'N/A')}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${formatPrice(combo.price)}đ</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm text-gray-900">${combo.combo_items_count || 0}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        ${featuredIcon}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(combo.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="/admin/combos/${combo.id}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                             <a href="/admin/combos/${combo.id}/edit" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                            <button onclick="toggleComboStatus(${combo.id}, '${combo.status}')" class="text-${toggleClass}-600 hover:text-${toggleClass}-900 transition-colors" title="${toggleTitle}"><i class="fas fa-${toggleIcon}"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }
    
    // Render card grid from combo data
    function renderCardGrid(combos) {
        if (!combos || combos.length === 0) {
            return '<div class="text-center py-12"><i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i><h3 class="text-lg font-medium text-gray-900 mb-2">Không có combo nào</h3><p class="text-gray-500">Chưa có combo nào được tạo hoặc không có combo nào phù hợp với bộ lọc hiện tại.</p></div>';
        }
        
        return combos.map(combo => {
            const imageHtml = combo.image 
                ? `<img src="{{ asset('storage/') }}/${combo.image}" alt="${escapeHtml(combo.name)}" class="w-full h-48 object-cover rounded-lg">`
                : '<div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-400 text-4xl"></i></div>';
            
            const featuredBadge = combo.is_featured 
                ? '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><i class="fas fa-star mr-1"></i>Nổi bật</span>'
                : '';
            
            const statusClass = combo.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            const statusText = combo.status === 'active' ? 'Hoạt động' : 'Không hoạt động';
            const toggleClass = combo.status === 'active' ? 'red' : 'green';
            const toggleIcon = combo.status === 'active' ? 'ban' : 'check';
            
            return `
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" class="combo-checkbox mr-3" value="${combo.id}">
                            <h3 class="text-lg font-semibold text-gray-800">${escapeHtml(combo.name)}</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            ${featuredBadge}
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                                ${statusText}
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        ${imageHtml}
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-600 text-sm mb-2">${escapeHtml(limitText(combo.description, 100))}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-green-600">${formatPrice(combo.price)}đ</span>
                            <span class="text-sm text-gray-500">Danh mục: ${escapeHtml(combo.category?.name || 'N/A')}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500"><i class="fas fa-utensils mr-1"></i>${combo.combo_items_count || 0} món</span>
                            <span class="text-sm text-gray-500"><i class="fas fa-calendar mr-1"></i>${formatDate(combo.created_at, 'd/m/Y')}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="/admin/combos/${combo.id}" class="text-blue-600 hover:text-blue-800 transition-colors"><i class="fas fa-eye"></i></a>
                             <a href="/admin/combos/${combo.id}/edit" class="text-yellow-600 hover:text-yellow-800 transition-colors"><i class="fas fa-edit"></i></a>
                            <button onclick="toggleComboStatus(${combo.id}, '${combo.status}')" class="text-${toggleClass}-600 hover:text-${toggleClass}-800 transition-colors"><i class="fas fa-${toggleIcon}"></i></button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // Helper functions
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price || 0);
    }
    
    function formatDate(dateString, format = 'd/m/Y H:i') {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (format === 'd/m/Y') {
            return date.toLocaleDateString('vi-VN');
        }
        return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
    }
    
    function limitText(text, limit) {
        if (!text) return '';
        return text.length > limit ? text.substring(0, limit) + '...' : text;
    }

    // Update combo display
    function updateComboDisplay(combos, stats) {
        // Render table HTML
        const tableHtml = renderTableRows(combos);
        
        // Render card HTML
        const cardHtml = renderCardGrid(combos);
        
        // Update table view
        const tableView = document.getElementById('tableView');
        if (tableView) {
            const tableBody = tableView.querySelector('tbody');
            if (tableBody) {
                tableBody.innerHTML = tableHtml;
            }
        }
        
        // Update card view
        const cardView = document.getElementById('cardView');
        if (cardView) {
            const cardContainer = cardView.querySelector('.grid');
            if (cardContainer) {
                cardContainer.innerHTML = cardHtml;
            }
            
            // Update stats in card view
            const statsContainers = cardView.querySelectorAll('.grid .bg-white');
            if (statsContainers.length >= 3 && stats) {
                statsContainers[0].querySelector('.text-2xl').textContent = stats.total;
                statsContainers[1].querySelector('.text-2xl').textContent = stats.active;
                statsContainers[2].querySelector('.text-2xl').textContent = stats.inactive;
            }
        }
    }
    
    // Update URL without page reload
    function updateURL(page = 1, search = '') {
        const url = new URL(window.location);
        url.searchParams.delete('page');
        url.searchParams.delete('search');
        
        if (page > 1) {
            url.searchParams.set('page', page);
        }
        if (search) {
            url.searchParams.set('search', search);
        }
        
        window.history.pushState({}, '', url);
    }

    // Update pagination controls
    function updatePagination(paginationData) {
        const paginationContainer = document.querySelector('.pagination');
        if (paginationContainer && paginationData) {
            paginationContainer.innerHTML = generatePaginationHTML(paginationData);
            
            // Add event listeners to pagination links
            paginationContainer.querySelectorAll('a[data-page]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    loadCombos(page, currentSearch);
                });
            });
        }
    }

    // Generate pagination HTML
    function generatePaginationHTML(data) {
        if (!data || data.last_page <= 1) return '';
        
        let html = '<nav class="flex items-center justify-between">';
        html += '<div class="flex-1 flex justify-between sm:hidden">';
        
        // Previous button (mobile)
        if (data.current_page > 1) {
            html += `<a href="#" data-page="${data.current_page - 1}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Trước</a>`;
        }
        
        // Next button (mobile)
        if (data.current_page < data.last_page) {
            html += `<a href="#" data-page="${data.current_page + 1}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sau</a>`;
        }
        
        html += '</div>';
        html += '<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">';
        html += `<div><p class="text-sm text-gray-700">Hiển thị <span class="font-medium">${data.from || 0}</span> đến <span class="font-medium">${data.to || 0}</span> trong <span class="font-medium">${data.total}</span> kết quả</p></div>`;
        html += '<div><span class="relative z-0 inline-flex shadow-sm rounded-md">';
        
        // Previous button (desktop)
        if (data.current_page > 1) {
            html += `<a href="#" data-page="${data.current_page - 1}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">‹</a>`;
        }
        
        // Page numbers
        const startPage = Math.max(1, data.current_page - 2);
        const endPage = Math.min(data.last_page, data.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === data.current_page) {
                html += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-orange-50 text-sm font-medium text-orange-600">${i}</span>`;
            } else {
                html += `<a href="#" data-page="${i}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">${i}</a>`;
            }
        }
        
        // Next button (desktop)
        if (data.current_page < data.last_page) {
            html += `<a href="#" data-page="${data.current_page + 1}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">›</a>`;
        }
        
        html += '</span></div></div></nav>';
        return html;
    }

    // Select all functionality
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.combo-checkbox');
        
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkedItems = document.querySelectorAll('.combo-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        
        if (bulkActions) {
            if (checkedItems.length > 0) {
                bulkActions.classList.remove('hidden');
            } else {
                bulkActions.classList.add('hidden');
            }
        }
    }

    // Bulk status update functions
    function updateSelectedStatus(status) {
        const checkedItems = document.querySelectorAll('.combo-checkbox:checked');
        const ids = Array.from(checkedItems).map(item => item.value);
        
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một combo');
            return;
        }
        
        const statusText = status === 1 ? 'kích hoạt' : 'vô hiệu hóa';
        if (confirm(`Bạn có chắc chắn muốn ${statusText} ${ids.length} combo đã chọn?`)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/combos/bulk-update-status';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);
            
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function updateSelectedFeatured(featured) {
        const checkedItems = document.querySelectorAll('.combo-checkbox:checked');
        const ids = Array.from(checkedItems).map(item => item.value);
        
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một combo');
            return;
        }
        
        const featuredText = featured === 1 ? 'đặt nổi bật' : 'bỏ nổi bật';
        if (confirm(`Bạn có chắc chắn muốn ${featuredText} ${ids.length} combo đã chọn?`)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/combos/bulk-update-featured';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            const featuredInput = document.createElement('input');
            featuredInput.type = 'hidden';
            featuredInput.name = 'is_featured';
            featuredInput.value = featured;
            form.appendChild(featuredInput);
            
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }

function confirmDelete(id, name) {
    document.getElementById('comboName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/combos/delete/${id}`;
    
    // Update delete form to use AJAX
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.onsubmit = function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = deleteForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div> Đang xóa...';
        
        // Send AJAX request
        fetch(deleteForm.action, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            toggleModal('deleteModal');
            
            if (data.success) {
                showToast(data.message || 'Đã xóa combo thành công');
                // Refresh the search results
                performSearch();
            } else {
                showToast(data.message || 'Có lỗi xảy ra khi xóa combo', 'error');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showToast('Có lỗi xảy ra khi xóa combo', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    };
    
    toggleModal('deleteModal');
}

function toggleStatus(id, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const statusText = newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa';
    
    if (confirm(`Bạn có muốn ${statusText} combo này?`)) {
        fetch(`/admin/combos/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Đã cập nhật trạng thái thành công!');
                // Refresh the search results instead of reloading page
                loadCombos(currentPage, currentSearch);
            } else {
                showToast('Lỗi: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra khi thay đổi trạng thái.', 'error');
        });
    }
}

// Auto submit form when filter changes
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleSelectAll);
    }

    // Update bulk actions when checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('combo-checkbox')) {
            updateBulkActions();
        }
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
            const modals = document.querySelectorAll('[id$="Modal"]');
            modals.forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                }
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[onclick*="toggleDropdown"]')) {
            const dropdowns = document.querySelectorAll('[id$="Menu"]');
            dropdowns.forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
        }
    });

    // Search input functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loadCombos(1, e.target.value);
            }
        });
    }
    
    // Filter functionality
    const filterStatus = document.getElementById('filter_status');
    const filterCategory = document.getElementById('filter_category');
    const filterFeatured = document.getElementById('filter_featured');
    
    if (filterStatus) {
        filterStatus.addEventListener('change', () => loadCombos(1, currentSearch));
    }
    if (filterCategory) {
        filterCategory.addEventListener('change', () => loadCombos(1, currentSearch));
    }
    if (filterFeatured) {
        filterFeatured.addEventListener('change', () => loadCombos(1, currentSearch));
    }
    
    
    // Update filter form to prevent default submission
    const filterForm = document.querySelector('form[action*="combos"]');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadCombos(1, currentSearch);
        });
    }
    
    // Update combo quantity
    function updateQuantity(comboId, quantity) {
        console.log('updateQuantity called:', comboId, quantity);
        
        // Validate quantity
        if (quantity < 0) {
            showToast('Số lượng không thể âm', 'error');
            return;
        }
        
        // Show loading state
        const quantityInput = document.getElementById(`quantity-${comboId}`);
        const originalValue = quantityInput.value;
        quantityInput.disabled = true;
        
        console.log('Sending request to:', `/admin/combos/${comboId}/update-quantity`);
        console.log('Request body:', { quantity: parseInt(quantity) });
        
        fetch(`/admin/combos/${comboId}/update-quantity`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantity: parseInt(quantity)
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                showToast(data.message, 'success');
                
                // Update status display if changed
                const statusElement = quantityInput.closest('tr').querySelector('.status-tag');
                if (statusElement) {
                    statusElement.className = `status-tag ${data.combo.active ? 'success' : 'failed'}`;
                    statusElement.textContent = data.combo.status_text;
                }
                
                // Update quantity input
                quantityInput.value = data.combo.quantity;
            } else {
                showToast(data.message || 'Có lỗi xảy ra', 'error');
                quantityInput.value = originalValue;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi cập nhật số lượng', 'error');
            quantityInput.value = originalValue;
        })
        .finally(() => {
            quantityInput.disabled = false;
        });
    }
    
    // Show toast notification
    function showToast(message, type = 'success') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // View toggle functionality
    const tableViewBtn = document.getElementById('tableViewBtn');
    const cardViewBtn = document.getElementById('cardViewBtn');
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const selectAllButton = document.getElementById('selectAllButton');
    
    // Switch to table view
    tableViewBtn.addEventListener('click', function() {
        tableView.classList.remove('hidden');
        cardView.classList.add('hidden');
        tableViewBtn.classList.add('bg-orange-500', 'text-white');
        tableViewBtn.classList.remove('bg-white', 'text-gray-700');
        cardViewBtn.classList.remove('bg-orange-500', 'text-white');
        cardViewBtn.classList.add('bg-white', 'text-gray-700');
        selectAllButton.classList.remove('hidden');
    });
    
    // Switch to card view
    cardViewBtn.addEventListener('click', function() {
        cardView.classList.remove('hidden');
        tableView.classList.add('hidden');
        cardViewBtn.classList.add('bg-orange-500', 'text-white');
        cardViewBtn.classList.remove('bg-white', 'text-gray-700');
        tableViewBtn.classList.remove('bg-orange-500', 'text-white');
        tableViewBtn.classList.add('bg-white', 'text-gray-700');
        selectAllButton.classList.add('hidden');
    });
});
</script>
@endpush
@endsection
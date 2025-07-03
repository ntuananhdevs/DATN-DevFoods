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

    /* ... các style khác ... */
    .status-tag.selling {
        background-color: #dcfce7; /* xanh lá nhạt */
        color: #15803d; /* xanh lá đậm */
    }
    .status-tag.coming_soon {
        background-color: #fef3c7; /* cam nhạt */
        color: #d97706; /* cam đậm */
    }
    .status-tag.discontinued {
        background-color: #f3f4f6; /* xám nhạt */
        color: #6b7280; /* xám đậm */
    }
    </style>

<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 flex-1">
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
        <div class="flex items-center gap-2 justify-end">
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
                    <button id="tableViewBtn" class="flex items-center px-3 py-1 rounded text-sm font-medium transition-colors bg-white shadow-sm border" onclick="toggleView('table')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M9 3v18"></path>
                            <path d="M15 3v18"></path>
                            <path d="M3 9h18"></path>
                            <path d="M3 15h18"></path>
                        </svg>
                        Bảng
                    </button>
                    <button id="cardViewBtn" class="flex items-center px-3 py-1 rounded text-sm font-medium transition-colors text-gray-600 hover:text-gray-900" onclick="toggleView('card')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                        </svg>
                        Thẻ
                    </button>
                </div>
                <button id="selectAllButton" class="btn btn-outline flex items-center" type="button" style="display: none;" onclick="handleSelectAllButton()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span id="selectAllButtonText">Chọn tất cả</span>
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
                                Đang bán
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(2)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-yellow-500">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                Sắp bán
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(0)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-red-500">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m15 9-6 6"></path>
                                    <path d="m9 9 6 6"></path>
                                </svg>
                                Dừng bán
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


                    <td class="p-4">
                        <div class="font-medium text-green-600">{{ number_format($combo->price) }}đ</div>
                    </td>
                    <td class="p-4">
                        <span class="status-tag {{ $combo->status }}">
                            {{ $combo->status_text }}
                        </span>
                    </td>

                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.combos.show', $combo->id) }}"
                               class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                               title="Xem chi tiết">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
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
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Tổng Combo</div>
                    <div class="text-2xl font-bold">{{ $combos->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Đang bán</div>
                    <div class="text-2xl font-bold text-green-600">{{ $combos->where('status', 'selling')->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Sắp bán</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $combos->where('status', 'coming_soon')->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Dừng bán</div>
                    <div class="text-2xl font-bold text-gray-600">{{ $combos->where('status', 'discontinued')->count() }}</div>
                </div>
            </div>

            <!-- Combo Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 combo-grid">
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
                        <span class="absolute top-2 right-2 status-tag {{ $combo->status }} text-xs">
                            {{ $combo->status_text }}
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
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($combo->description, 60) }}</p>
                        @endif
                        <div class="mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-orange-600">{{ number_format($combo->price) }}₫</span>
                                @if($combo->original_price && $combo->original_price > $combo->price)
                                    <span class="text-sm text-gray-500 line-through">{{ number_format($combo->original_price) }}₫</span>
                                @endif
                            </div>

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
                    <div class="p-4 border-t flex gap-2">
                        <a href="{{ route('admin.combos.edit', $combo->id) }}" class="flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                <path d="m15 5 4 4"></path>
                            </svg>
                            Sửa
                        </a>
                        <a href="{{ route('admin.combos.show', $combo->id) }}" class="flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors" title="Xem chi tiết">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Xem chi tiết
                        </a>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Không có combo nào</h3>
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
                <label for="filter_status" class="block text-sm font-medium mb-2">Trạng thái</label>
                <select id="filter_status" name="status" class="w-full border rounded-md px-3 py-2">
                    <option value="">Tất cả</option>
                    <option value="selling" {{ request('status') === 'selling' ? 'selected' : '' }}>Đang bán</option>
                    <option value="coming_soon" {{ request('status') === 'coming_soon' ? 'selected' : '' }}>Sắp bán</option>
                    <option value="discontinued" {{ request('status') === 'discontinued' ? 'selected' : '' }}>Dừng bán</option>
                </select>
            </div>
            <div class="flex gap-2">
                <div class="flex-1">
                    <label for="price_from" class="block text-sm font-medium mb-2">Giá từ</label>
                    <input type="number" id="price_from" name="price_from" class="w-full border rounded-md px-3 py-2" placeholder="Tối thiểu" value="{{ request('price_from') }}">
                </div>
                <div class="flex-1">
                    <label for="price_to" class="block text-sm font-medium mb-2">Giá đến</label>
                    <input type="number" id="price_to" name="price_to" class="w-full border rounded-md px-3 py-2" placeholder="Tối đa" value="{{ request('price_to') }}">
                </div>
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

<!-- Status Change Confirmation Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-blue-600">Xác nhận thay đổi trạng thái</h3>
            <button onclick="toggleModal('statusModal')" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m18 6-12 12"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-6">
            <p class="text-gray-700 mb-2" id="statusModalMessage"></p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="toggleModal('statusModal')" class="btn btn-outline flex-1">Hủy</button>
            <button type="button" id="statusModalConfirmBtn" class="btn btn-primary flex-1">Xác nhận</button>
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

    // View toggle functionality
    function toggleView(viewType) {
        const tableView = document.getElementById('tableView');
        const cardView = document.getElementById('cardView');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardViewBtn = document.getElementById('cardViewBtn');

        if (viewType === 'table') {
            tableView.classList.remove('hidden');
            cardView.classList.add('hidden');
            tableViewBtn.classList.add('bg-white', 'shadow-sm', 'border');
            tableViewBtn.classList.remove('text-gray-600', 'hover:text-gray-900');
            cardViewBtn.classList.remove('bg-white', 'shadow-sm', 'border');
            cardViewBtn.classList.add('text-gray-600', 'hover:text-gray-900');
        } else {
            tableView.classList.add('hidden');
            cardView.classList.remove('hidden');
            cardViewBtn.classList.add('bg-white', 'shadow-sm', 'border');
            cardViewBtn.classList.remove('text-gray-600', 'hover:text-gray-900');
            tableViewBtn.classList.remove('bg-white', 'shadow-sm', 'border');
            tableViewBtn.classList.add('text-gray-600', 'hover:text-gray-900');
        }
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
        const priceFrom = document.getElementById('price_from');
        const priceTo = document.getElementById('price_to');

        if (filterStatus && filterStatus.value) {
            params.append('status', filterStatus.value);
        }
        if (priceFrom && priceFrom.value) {
            params.append('price_from', priceFrom.value);
        }
        if (priceTo && priceTo.value) {
            params.append('price_to', priceTo.value);
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
                ? `<img src="{{ asset('storage/') }}/${combo.image}" alt="${escapeHtml(combo.name)}" class="w-12 h-12 object-cover rounded-lg border">`
                : '<div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center border"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg></div>';

            const statusClass = combo.status;
            const statusText = combo.status_text;

            const comboItemsHtml = (combo.combo_items && combo.combo_items.length > 0)
                ? combo.combo_items.slice(0, 3).map(item => {
                    if (item.product_variant && item.product_variant.product) {
                        let productName = item.product_variant.product.name;
                        let variant1 = item.product_variant.variant_attribute_value_1 ? ` - ${item.product_variant.variant_attribute_value_1}` : '';
                        let variant2 = item.product_variant.variant_attribute_value_2 ? ` - ${item.product_variant.variant_attribute_value_2}` : '';
                        return `<div class="flex items-center gap-2 text-sm">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                ${escapeHtml(productName)}${escapeHtml(variant1)}${escapeHtml(variant2)}
                                <span class="ml-1 text-gray-600">x${item.quantity}</span>
                            </span>
                        </div>`;
                    }
                    return '';
                }).join('') + (combo.combo_items.length > 3
                    ? `<div class="text-xs text-muted-foreground">+${combo.combo_items.length - 3} sản phẩm khác</div>`
                    : '')
                : '<span class="text-sm text-muted-foreground">Chưa có sản phẩm</span>';

            return `
                <tr class="border-b hover:bg-muted/50 transition-colors" data-combo-id="${combo.id}">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 combo-checkbox" value="${combo.id}">
                    </td>
                    <td class="p-4 font-mono text-sm text-muted-foreground">${escapeHtml(combo.sku || 'N/A')}</td>
                    <td class="p-4">
                        ${imageHtml}
                    </td>
                    <td class="p-4">
                        <div class="font-medium">${escapeHtml(combo.name)}</div>
                        ${combo.description ? `<div class="text-sm text-muted-foreground mt-1">${escapeHtml(limitText(combo.description, 40))}</div>` : ''}
                    </td>
                    <td class="p-4">
                        <div class="space-y-1">
                            ${comboItemsHtml}
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-green-600">${formatPrice(combo.price)}đ</div>
                    </td>
                    <td class="p-4">
                        <span class="status-tag ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <a href="${combo.show_url}" class="flex items-center justify-center rounded-md hover:bg-accent p-2" title="Xem chi tiết">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Render card grid from combo data
    function renderCardGrid(combos) {
        if (!combos || combos.length === 0) {
            return '<div class="col-span-full text-center py-12"><svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 mb-4"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg><h3 class="text-lg font-medium text-gray-900 mb-2">Không có combo nào</h3><p class="text-gray-500">Chưa có combo nào được tạo hoặc không có combo nào phù hợp với bộ lọc hiện tại.</p></div>';
        }

        return combos.map(combo => {
            const imageHtml = combo.image
                ? `<img src="{{ asset('storage/') }}/${combo.image}" alt="${escapeHtml(combo.name)}" class="w-full h-48 object-cover rounded-lg">`
                : '<div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg></div>';

            const statusClass = 'status-tag ' + combo.status;
            const statusText = combo.status_text;
            const toggleClass = combo.active ? 'red' : 'green';
            const toggleIcon = combo.active ? 'ban' : 'check';

            const comboItemsHtml = (combo.combo_items && combo.combo_items.length > 0)
                ? combo.combo_items.slice(0, 2).map(item => {
                    const productName = (item.product_variant && item.product_variant.product)
                        ? item.product_variant.product.name
                        : 'N/A';
                    const variant1 = (item.product_variant && item.product_variant.variant_attribute_value_1)
                        ? ` (${item.product_variant.variant_attribute_value_1})`
                        : '';
                    return `<li class="flex justify-between"><span>${productName}${variant1}</span><span>x${item.quantity}</span></li>`;
                }).join('') + (combo.combo_items.length > 2 ? `<li class="text-xs text-gray-500">+${combo.combo_items.length - 2} sản phẩm khác</li>` : '')
                : '<li class="text-sm text-gray-500"><em>Chưa có sản phẩm nào</em></li>';

            return `
                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="relative">
                        ${imageHtml}
                        <span class="absolute top-2 right-2 ${statusClass} text-xs px-2 py-1 rounded-full">
                            ${statusText}
                        </span>
                        ${combo.original_price && combo.original_price > combo.price ? `<span class="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full">-${Math.round(((combo.original_price - combo.price) / combo.original_price) * 100)}%</span>` : ''}
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">${escapeHtml(combo.name)}</h3>
                        ${combo.description ? `<p class="text-gray-600 text-sm mb-2">${escapeHtml(limitText(combo.description, 60))}</p>` : ''}
                        <div class="mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-orange-600">${formatPrice(combo.price)}₫</span>
                                ${combo.original_price && combo.original_price > combo.price ? `<span class="text-sm text-gray-500 line-through">${formatPrice(combo.original_price)}₫</span>` : ''}
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <strong>Bao gồm:</strong>
                            <ul class="mt-1 space-y-1">
                                ${comboItemsHtml}
                            </ul>
                        </div>
                    </div>
                    <div class="p-4 border-t flex gap-2">
                        <a href="${combo.show_url}" class="flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                <path d="m15 5 4 4"></path>
                            </svg>
                            Xem chi tiết
                        </a>
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

    function showToast(message, type = 'success') {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        } else {
            alert(message);
        }
    }

    // Update combo display
    function updateComboDisplay(combos, stats) {
        // Store combo data globally for access by other functions
        window.comboData = combos;

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
            const cardGrids = cardView.querySelectorAll('.grid');
            const comboGrid = cardGrids[1]; // grid thứ 2 là grid combo
            if (comboGrid) {
                comboGrid.innerHTML = cardHtml;
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
        updateSelectAllButtonText();
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
        updateSelectAllButtonText();
    }

    // Nút chọn tất cả ngoài toolbar
    function handleSelectAllButton() {
        const checkboxes = document.querySelectorAll('.combo-checkbox');
        const selectAll = document.getElementById('selectAllButton');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => { cb.checked = !allChecked; });
        updateBulkActions();
        updateSelectAllButtonText();
    }

    function updateSelectAllButtonText() {
        const checkboxes = document.querySelectorAll('.combo-checkbox');
        const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
        const selectAllBtn = document.getElementById('selectAllButton');
        const text = document.getElementById('selectAllButtonText');
        if (!selectAllBtn || !text) return;
        if (checkboxes.length === 0) {
            selectAllBtn.style.display = 'none';
        } else {
            selectAllBtn.style.display = '';
            if (checked === checkboxes.length) {
                text.textContent = 'Bỏ chọn tất cả';
            } else {
                text.textContent = 'Chọn tất cả';
            }
        }
    }

    // Bulk status update functions
    function updateSelectedStatus(status) {
        // status: 1 = selling, 0 = discontinued, 2 = coming_soon
        let action;
        let actionText;
        if (status === 1) { action = 'activate'; actionText = 'kích hoạt'; }
        else if (status === 0) { action = 'deactivate'; actionText = 'dừng bán'; }
        else if (status === 2) { action = 'coming_soon'; actionText = 'chuyển sang sắp bán'; }

        const checkedItems = document.querySelectorAll('.combo-checkbox:checked');
        const ids = Array.from(checkedItems).map(item => item.value);

        if (ids.length === 0) {
            dtmodalShowToast('warning', {
                title: 'Cảnh báo',
                message: 'Vui lòng chọn ít nhất một combo'
            });
            return;
        }

        // Sử dụng modal xác nhận từ modal.js
        dtmodalCreateModal({
            type: 'warning',
            title: 'Xác nhận thay đổi trạng thái',
            message: `Bạn có chắc chắn muốn <strong>${actionText}</strong> ${ids.length} combo đã chọn?`,
            confirmText: 'Xác nhận',
            cancelText: 'Hủy',
            onConfirm: function() {
                fetch('/admin/combos/bulk-update-status', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: action,
                        ids: ids
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: data.message || 'Cập nhật trạng thái thành công!'
                        });
                        // Bỏ chọn tất cả sau khi thao tác xong
                        document.querySelectorAll('.combo-checkbox').forEach(cb => cb.checked = false);
                        updateBulkActions();
                        updateSelectAllButtonText();
                        performSearch();
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: data.message || 'Có lỗi xảy ra khi cập nhật trạng thái.'
                        });
                    }
                })
                .catch(error => {
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Có lỗi xảy ra khi cập nhật trạng thái.'
                    });
                });
            }
        });
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

        // Find the combo data to get the delete URL
        const comboData = window.comboData ? window.comboData.find(c => c.id == id) : null;
        const deleteUrl = comboData ? comboData.delete_url : `/admin/combos/delete/${id}`;

        document.getElementById('deleteForm').action = deleteUrl;

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

        // Sử dụng modal xác nhận từ modal.js
        dtmodalCreateModal({
            type: 'warning',
            title: 'Xác nhận thay đổi trạng thái',
            message: `Bạn có muốn <strong>${statusText}</strong> combo này?`,
            confirmText: 'Xác nhận',
            cancelText: 'Hủy',
            onConfirm: function() {
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
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: 'Đã cập nhật trạng thái thành công!'
                        });
                        loadCombos(currentPage, currentSearch);
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: data.message || 'Có lỗi xảy ra khi cập nhật trạng thái.'
                        });
                    }
                })
                .catch(error => {
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Có lỗi xảy ra khi thay đổi trạng thái.'
                    });
                });
            }
        });
    }

    // Auto submit form when filter changes
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle button event listeners
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardViewBtn = document.getElementById('cardViewBtn');

        if (tableViewBtn) {
            tableViewBtn.addEventListener('click', function() {
                toggleView('table');
            });
        }

        if (cardViewBtn) {
            cardViewBtn.addEventListener('click', function() {
                toggleView('card');
            });
        }

        // Select all checkbox functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', toggleSelectAll);
        }

        // Update bulk actions when checkboxes change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('combo-checkbox')) {
                updateBulkActions();
                updateSelectAllButtonText();
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
        const priceFrom = document.getElementById('price_from');
        const priceTo = document.getElementById('price_to');

        if (filterStatus) {
            filterStatus.addEventListener('change', () => loadCombos(1, currentSearch));
        }
        if (priceFrom) {
            priceFrom.addEventListener('input', () => loadCombos(1, currentSearch));
        }
        if (priceTo) {
            priceTo.addEventListener('input', () => loadCombos(1, currentSearch));
        }

        // Update filter form to prevent default submission
        const filterForm = document.querySelector('form[action*="combos"]');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loadCombos(1, currentSearch);
            });
        }

        // Quick update quantity (debounced, trạng thái input, toast)
        let quickQuantityTimeout = null;
        let quickQuantityLoading = false;

        function handleQuickQuantity(input) {
            if (quickQuantityLoading) return;
            const comboId = input.getAttribute('data-combo-id');
            let newQuantity = input.value === '' ? null : parseInt(input.value);

            quickQuantityLoading = true;
            input.disabled = true;
            input.classList.add('opacity-50');

            fetch(`/admin/combos/${comboId}/quick-update-quantity`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                    setTimeout(() => {
                        input.classList.remove('border-green-500');
                    }, 1000);

                    // Cập nhật trạng thái hoạt động trên giao diện
                    const row = input.closest('tr');
                    if (row) {
                        const statusTd = row.querySelector('td:nth-child(8) .status-tag');
                        if (statusTd) {
                            if (data.active) {
                                statusTd.textContent = 'Hoạt động';
                                statusTd.classList.remove('failed');
                                statusTd.classList.add('success');
                            } else {
                                statusTd.textContent = 'Không hoạt động';
                                statusTd.classList.remove('success');
                                statusTd.classList.add('failed');
                            }
                        }
                    }

                    // Hiển thị toast ngay lập tức (tương tự session flash)
                    if (newQuantity === 0 || newQuantity === null) {
                        showToast('Combo đã được dừng (hết hàng)', 'info');
                    } else {
                        showToast('Cập nhật số lượng thành công!');
                    }
                } else {
                    input.classList.remove('border-green-500');
                    input.classList.add('border-red-500');
                    showToast(data.message || 'Cập nhật thất bại', 'error');
                }
            })
            .catch(() => {
                input.classList.remove('border-green-500');
                input.classList.add('border-red-500');
                showToast('Có lỗi xảy ra khi cập nhật số lượng', 'error');
            })
            .finally(() => {
                input.disabled = false;
                input.classList.remove('opacity-50');
                quickQuantityLoading = false;
            });
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quick-quantity-input')) {
                if (quickQuantityTimeout) clearTimeout(quickQuantityTimeout);
                quickQuantityTimeout = setTimeout(() => {
                    handleQuickQuantity(e.target);
                }, 500);
            }
        });
        document.addEventListener('blur', function(e) {
            if (e.target.classList.contains('quick-quantity-input')) {
                if (quickQuantityTimeout) {
                    clearTimeout(quickQuantityTimeout);
                    handleQuickQuantity(e.target);
                }
            }
        }, true);

        updateSelectAllButtonText();
    });
</script>
@endpush
@endsection

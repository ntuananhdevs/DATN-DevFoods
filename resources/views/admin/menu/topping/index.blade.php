@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản lý Topping')
@section('description', 'Quản lý danh sách topping của bạn')

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

<div class="flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-circle">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M8 12h8"></path>
                    <path d="M12 8v8"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý Topping</h2>
                <p class="text-muted-foreground">Quản lý danh sách topping của bạn</p>
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
            <a href="{{ route('admin.toppings.create') }}" class="btn btn-primary flex items-center mr-2">
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
            <h3 class="text-lg font-medium">Danh sách topping</h3>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" placeholder="Tìm kiếm theo tên topping..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput">
            </div>
            <div class="flex items-center gap-2">
                <button class="btn btn-outline flex items-center" id="selectAllButton">
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
        <div class="overflow-x-auto">
            @forelse($toppings as $topping)
                @if($loop->first)
                    <table class="w-full">
                        <thead class="border-b">
                            <tr class="text-left">
                                <th class="p-4 font-medium text-muted-foreground">
                                    <input type="checkbox" class="rounded border-gray-300" id="selectAll">
                                </th>
                                <th class="p-4 font-medium text-muted-foreground">Mã topping</th>
                                <th class="p-4 font-medium text-muted-foreground">Hình ảnh</th>
                                <th class="p-4 font-medium text-muted-foreground">Tên topping</th>
                                <th class="p-4 font-medium text-muted-foreground">Giá</th>
                                <th class="p-4 font-medium text-muted-foreground">Mô tả</th>
                                <th class="p-4 font-medium text-muted-foreground">Trạng thái</th>
                                <th class="p-4 font-medium text-muted-foreground">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                <tr class="border-b hover:bg-muted/50 transition-colors">
                    <td class="p-4">
                        <input type="checkbox" class="rounded border-gray-300 row-checkbox" value="{{ $topping->id }}">
                    </td>
                    <td class="p-4">
                        <div class="font-mono text-sm">{{ $topping->sku ?? 'N/A' }}</div>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center">
                            @if($topping->image)
                                <img src="{{ Storage::disk('s3')->url($topping->image) }}" alt="{{ $topping->name }}" class="w-20 h-20 rounded-lg object-cover border">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-muted flex items-center justify-center border">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                                        <circle cx="9" cy="9" r="2"></circle>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-medium">{{ $topping->name }}</div>
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-green-600">{{ number_format($topping->price) }}đ</div>
                    </td>
                    <td class="p-4">
                        <div class="text-sm text-muted-foreground max-w-xs truncate">{{ $topping->description ?? 'Không có mô tả' }}</div>
                    </td>
                    <td class="p-4">
                        @if($topping->active == 1)
                            <span class="status-tag success">Hoạt động</span>
                        @else
                            <span class="status-tag failed">Không hoạt động</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.toppings.show', $topping->id) }}" class="btn btn-outline btn-sm" title="Xem chi tiết">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            <a href="{{ route('admin.toppings.stock', $topping->id) }}" class="btn btn-outline btn-sm" title="Quản lý tồn kho">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                    <path d="m3.3 7 8.7 5 8.7-5"></path>
                                    <path d="M12 22V12"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.toppings.edit', $topping->id) }}" class="btn btn-outline btn-sm" title="Chỉnh sửa">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                    <path d="m15 5 4 4"></path>
                                </svg>
                            </a>
                            <button type="button" class="btn btn-outline btn-sm text-red-600 hover:bg-red-50" title="Xóa" onclick="confirmDelete({{ $topping->id }}, '{{ $topping->name }}')">
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
                @endif
            @empty
                <div class="flex flex-col items-center justify-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground mb-4">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 12h8"></path>
                        <path d="M12 8v8"></path>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">Chưa có topping nào</h3>
                    <p class="text-muted-foreground mb-4 text-center">Bắt đầu bằng cách tạo topping đầu tiên cho cửa hàng của bạn.</p>
                    <a href="{{ route('admin.toppings.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                        Thêm topping đầu tiên
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pagination-container flex items-center justify-between px-4 py-4 border-t">
            <div class="text-sm text-muted-foreground">
                Hiển thị <span id="paginationStart">{{ $toppings->firstItem() }}</span> đến <span id="paginationEnd">{{ $toppings->lastItem() }}</span> của <span id="paginationTotal">{{ $toppings->total() }}</span> mục
            </div>
            <div class="flex items-center justify-end space-x-2 ml-auto" id="paginationControls">
                @unless($toppings->onFirstPage())
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage({{ $toppings->currentPage() - 1 }})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                @endunless

                @foreach ($toppings->getUrlRange(1, $toppings->lastPage()) as $page => $url)
                <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium {{ $toppings->currentPage() == $page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted' }}" onclick="changePage({{ $page }})">
                    {{ $page }}
                </button>
                @endforeach

                @unless($toppings->currentPage() === $toppings->lastPage())
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage({{ $toppings->currentPage() + 1 }})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
                @endunless
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium">Bộ lọc nâng cao</h3>
                <button type="button" onclick="toggleModal('filterModal')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.toppings.index') }}" class="space-y-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status" class="w-full border rounded-md px-3 py-2">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="price-range-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Khoảng giá</label>
                    <div class="price-inputs">
                        <input type="number" name="min_price" placeholder="Giá từ" class="price-input" value="{{ request('min_price') }}">
                        <input type="number" name="max_price" placeholder="Giá đến" class="price-input" value="{{ request('max_price') }}">
                    </div>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="submit" class="btn btn-primary flex-1">Áp dụng bộ lọc</button>
                    <a href="{{ route('admin.toppings.index') }}" class="btn btn-outline flex-1 text-center">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-red-600">Xác nhận xóa</h3>
                <button type="button" onclick="toggleModal('deleteModal')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-gray-700">Bạn có chắc chắn muốn xóa topping <strong id="toppingName"></strong>?</p>
                <p class="text-red-600 text-sm mt-2">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="toggleModal('deleteModal')" class="btn btn-outline flex-1">Hủy</button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-full">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle dropdown menus
function toggleDropdown(menuId) {
    const menu = document.getElementById(menuId);
    menu.classList.toggle('hidden');
    
    // Close other dropdowns
    const allDropdowns = document.querySelectorAll('[id$="Menu"]');
    allDropdowns.forEach(dropdown => {
        if (dropdown.id !== menuId) {
            dropdown.classList.add('hidden');
        }
    });
}

// Toggle modals
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('[id$="Menu"]');
    const buttons = document.querySelectorAll('[id$="Dropdown"]');
    
    let clickedOnDropdown = false;
    buttons.forEach(button => {
        if (button.contains(event.target)) {
            clickedOnDropdown = true;
        }
    });
    
    if (!clickedOnDropdown) {
        dropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const toppingName = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        if (toppingName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Select all functionality
document.getElementById('selectAllButton').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
    selectAllCheckbox.checked = !allChecked;
    
    this.querySelector('span').textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn tất cả';
});

// Individual checkbox change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('row-checkbox')) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        const selectAllButton = document.getElementById('selectAllButton');
        
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        selectAllCheckbox.checked = checkedCount === checkboxes.length;
        
        if (checkedCount === checkboxes.length) {
            selectAllButton.querySelector('span').textContent = 'Bỏ chọn tất cả';
        } else {
            selectAllButton.querySelector('span').textContent = 'Chọn tất cả';
        }
    }
});

// Bulk status update
function updateSelectedStatus(status) {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Vui lòng chọn ít nhất một topping để thực hiện thao tác.');
        return;
    }
    
    const statusText = status === 1 ? 'kích hoạt' : 'vô hiệu hóa';
    if (confirm(`Bạn có chắc chắn muốn ${statusText} ${selectedIds.length} topping đã chọn?`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.toppings.bulk-update-status") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = JSON.stringify(selectedIds);
        form.appendChild(idsInput);
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDelete(id, name) {
    document.getElementById('toppingName').textContent = name;
    document.getElementById('deleteForm').action = `{{ url('admin/toppings/delete') }}/${id}`;
    toggleModal('deleteModal');
}

// Auto-submit form when filters change
$('#status').on('change', function() {
    $(this).closest('form').submit();
});
</script>
@endpush
@endsection
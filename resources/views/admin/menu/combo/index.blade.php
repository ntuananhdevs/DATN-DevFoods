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
                <input type="text" placeholder="Tìm kiếm theo tên combo..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput">
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
        <div class="overflow-x-auto">
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
                            <img src="{{ asset('storage/' . $combo->image) }}" 
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

    // Search functionality
    function handleSearch() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = searchInput.closest('form');
        if (searchForm) {
            searchForm.submit();
        }
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Thành công!');
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi thay đổi trạng thái.');
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
                handleSearch();
            }
        });
    }
});
</script>
@endpush
@endsection
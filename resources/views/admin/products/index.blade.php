@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Danh sách sản phẩm')
@section('description', 'Quản lý danh sách sản phẩm của bạn')

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
        border-color: #3b82f6; /* Blue-500 from Tailwind */
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #2563eb; /* Blue-600 */
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

    /* Slider styling */
    .noUi-target {
        border: none;
        box-shadow: none;
        background: #e5e7eb; /* Gray-200 */
    }
    .noUi-connect {
        background: #3b82f6; /* Blue-500 */
    }
    .noUi-handle {
        background: #2563eb; /* Blue-600 */
        border: 2px solid #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-boxes">
                    <path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path>
                    <path d="m7 16.5-4.74-2.85"></path>
                    <path d="m7 16.5 5-3"></path>
                    <path d="M7 16.5v5.17"></path>
                    <path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path>
                    <path d="m17 16.5-5-3"></path>
                    <path d="m17 16.5 4.74-2.85"></path>
                    <path d="M17 16.5v5.17"></path>
                    <path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path>
                    <path d="M12 8 7.26 5.15"></path>
                    <path d="m12 8 4.74-2.85"></path>
                    <path d="M12 13.5V8"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý sản phẩm</h2>
                <p class="text-muted-foreground">Quản lý danh sách sản phẩm của bạn</p>
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
            <a href="{{ asset('admin/products/create') }}" class="btn btn-primary flex items-center">
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
            <h3 class="text-lg font-medium">Danh sách sản phẩm</h3>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" placeholder="Tìm kiếm theo tên, mã sản phẩm..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput">
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

        <!-- Table container -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="py-3 px-4 text-left">
                            <div class="flex items-center">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left font-medium">
                            <div class="flex items-center">
                                ID
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                    <path d="m18 8-6 6-6-6"></path>
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Hình ảnh</th>
                        <th class="py-3 px-4 text-left font-medium">
                            <div class="flex items-center">
                                Tên sản phẩm
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                    <path d="m18 8-6 6-6-6"></path>
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Danh mục</th>
                        <th class="py-3 px-4 text-right font-medium">
                            <div class="flex items-center justify-end">
                                Giá
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                    <path d="m18 8-6 6-6-6"></path>
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-center font-medium">
                            <div class="flex items-center justify-center">
                                Tồn kho
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                    <path d="m18 8-6 6-6-6"></path>
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @forelse($products as $product)
                    <tr class="border-b">
                        <td class="py-3 px-4">
                            <input type="checkbox" class="row-checkbox rounded border-gray-300" value="{{ $product->id }}">
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $product->id }}</td>
                        <td class="py-3 px-4">
                            <div class="h-12 w-12 rounded-md bg-muted flex items-center justify-center overflow-hidden">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium">{{ $product->name }}</div>
                        </td>
                        <td class="py-3 px-4">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-right">{{ number_format($product->base_price, 0, ',', '.') }} đ</td>
                        <td class="py-3 px-4 text-center">
                            @if ($product->stock)
                                <span class="status-tag success">
                                    <i class="fas fa-check mr-1"></i> Còn hàng
                                </span>
                            @else
                                <span class="status-tag failed">
                                    <i class="fas fa-times mr-1"></i> Hết hàng
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-tag {{ $product->stock ? 'success' : 'failed' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    @if ($product->stock)
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <path d="m9 11 3 3L22 4"></path>
                                    @else
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m15 9-6 6"></path>
                                        <path d="m9 9 6 6"></path>
                                    @endif
                                </svg>
                                @if ($product->stock)
                                    Đang bán
                                @else
                                    Khóa
                                @endif
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center space-x-1">
                                <a href="{{ route('admin.products.show', $product->id) }}" 
                                   class="flex items-center justify-center rounded-md hover:bg-accent p-2" 
                                   title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="flex items-center justify-center rounded-md hover:bg-accent p-2" 
                                   title="Chỉnh sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                        <path d="m15 5 4 4"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                            onclick="dtmodalConfirmDelete({
                                                title: 'Xác nhận xóa sản phẩm',
                                                subtitle: 'Bạn có chắc chắn muốn xóa sản phẩm này?',
                                                message: 'Hành động này không thể hoàn tác.',
                                                itemName: '{{ $product->name }}',
                                                onConfirm: () => this.closest('form').submit()
                                            })"
                                            title="Xóa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="data-table-empty" id="dataTableEmpty">
                                    <div class="data-table-empty-icon">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <h3>Không có sản phẩm nào</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination and info -->
        <div class="flex items-center justify-between px-4 py-4 border-t">
            <div class="text-sm text-muted-foreground">
                Hiển thị <span id="paginationStart">{{ $products->firstItem() }}</span> đến <span id="paginationEnd">{{ $products->lastItem() }}</span> của <span id="paginationTotal">{{ $products->total() }}</span> mục
            </div>
            <div class="flex items-center space-x-2" id="paginationControls">
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted {{ $products->onFirstPage() ? 'disabled opacity-50 cursor-not-allowed' : '' }}" onclick="changePage({{ $products->currentPage() - 1 }})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium {{ $products->currentPage() == $page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted' }}" onclick="changePage({{ $page }})">{{ $page }}</button>
                @endforeach
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted {{ $products->hasMorePages() ? '' : 'disabled opacity-50 cursor-not-allowed' }}" onclick="changePage({{ $products->currentPage() + 1 }})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-background rounded-lg shadow-lg w-full max-w-lg mx-4">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-medium">Lọc sản phẩm nâng cao</h3>
            <button type="button" class="text-muted-foreground hover:text-foreground" onclick="toggleModal('filterModal')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <form id="filterForm">
            <div class="p-4 space-y-6">
                <!-- Category Filter -->
                <div class="space-y-2">
                    <label for="filter_category" class="text-sm font-medium">Danh mục</label>
                    <select id="filter_category" name="category_id" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Price Range Slider -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Khoảng giá</label>
                    <div class="flex items-center gap-4">
                        <input type="text" id="price_min_display" class="w-24 border rounded-md px-3 py-2 bg-background text-sm" value="{{ number_format($minPrice, 0, ',', '.') }} ₫" readonly>
                        <div id="price_range" class="w-full"></div>
                        <input type="text" id="price_max_display" class="w-24 border rounded-md px-3 py-2 bg-background text-sm" value="{{ number_format($maxPrice, 0, ',', '.') }} ₫" readonly>
                    </div>
                    <input type="hidden" name="price_min" id="price_min" value="{{ $minPrice }}">
                    <input type="hidden" name="price_max" id="price_max" value="{{ $maxPrice }}">
                </div>
                <!-- Stock Status -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Tình trạng kho</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="stock_status[]" value="in_stock" class="rounded border-gray-300 mr-2">
                            Còn hàng
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="stock_status[]" value="out_of_stock" class="rounded border-gray-300 mr-2">
                            Hết hàng
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="stock_status[]" value="low_stock" class="rounded border-gray-300 mr-2">
                            Sắp hết hàng (< 10)
                        </label>
                    </div>
                </div>
                <!-- Date Added -->
                <div class="space-y-2">
                    <label for="date_added" class="text-sm font-medium">Ngày thêm</label>
                    <input type="date" id="date_added" name="date_added" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                </div>
            </div>
            <div class="flex items-center justify-end p-4 border-t space-x-2">
                <button type="button" class="btn btn-outline" onclick="resetFilters()">Xóa bộ lọc</button>
                <button type="button" class="btn btn-outline" onclick="toggleModal('filterModal')">Đóng</button>
                <button type="submit" class="btn btn-primary">Áp dụng</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Utility function for debouncing
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Toggle dropdown
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(function(dropdown) {
                if (!dropdown.contains(event.target)) {
                    dropdown.querySelector('.dropdown-menu')?.classList.add('hidden');
                }
            });
        });

        // Toggle modal
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const selectAllButton = document.getElementById('selectAllButton');
            
            const newCheckedState = !selectAllCheckbox.checked;
            selectAllCheckbox.checked = newCheckedState;
            
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = newCheckedState;
            });
            
            selectAllButton.querySelector('span').textContent = newCheckedState ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
        }

        // Update select all checkbox state based on row checkboxes
        function updateSelectAllState() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const selectAllButton = document.getElementById('selectAllButton');
            const allChecked = Array.from(rowCheckboxes).every(checkbox => checkbox.checked);
            const someChecked = Array.from(rowCheckboxes).some(checkbox => checkbox.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllButton.querySelector('span').textContent = allChecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
        }

        // Update selected status
        function updateSelectedStatus(status) {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm');
                return;
            }
            
            fetch('/admin/products/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids, status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Đã cập nhật trạng thái cho ${ids.length} sản phẩm`);
                    fetchProducts();
                } else {
                    alert('Có lỗi xảy ra khi cập nhật trạng thái');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
        }

        // Fetch products with filters
        function fetchProducts(page = 1) {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const searchInput = document.getElementById('searchInput').value;
            formData.append('search', searchInput);
            formData.append('page', page);

            fetch('/admin/products/filter', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                updateTable(data.products);
                updatePagination(data.pagination);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi khi tải dữ liệu sản phẩm');
            });
        }

        // Update table with new data
        function updateTable(products) {
            const tbody = document.getElementById('productTableBody');
            tbody.innerHTML = '';

            if (products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="data-table-empty" id="dataTableEmpty">
                                <div class="data-table-empty-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h3>Không có sản phẩm nào</h3>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            products.forEach(product => {
                const row = `
                    <tr class="border-b">
                        <td class="py-3 px-4">
                            <input type="checkbox" class="row-checkbox rounded border-gray-300" value="${product.id}">
                        </td>
                        <td class="py-3 px-4 font-medium">${product.id}</td>
                        <td class="py-3 px-4">
                            <div class="h-12 w-12 rounded-md bg-muted flex items-center justify-center overflow-hidden">
                                <img src="${product.image}" alt="${product.name}" class="h-full w-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium">${product.name}</div>
                        </td>
                        <td class="py-3 px-4">${product.category?.name ?? 'N/A'}</td>
                        <td class="py-3 px-4 text-right">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.base_price)}</td>
                        <td class="py-3 px-4 text-center">
                            ${product.stock > 0 ? 
                                `<span class="status-tag success"><i class="fas fa-check mr-1"></i> Còn hàng</span>` :
                                `<span class="status-tag failed"><i class="fas fa-times mr-1"></i> Hết hàng</span>`}
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-tag ${product.stock > 0 ? 'success' : 'failed'}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    ${product.stock > 0 ? 
                                        `<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path>` :
                                        `<circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path>`}
                                </svg>
                                ${product.stock > 0 ? 'Đang bán' : 'Khóa'}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center space-x-1">
                                <a href="/admin/products/${product.id}" class="flex items-center justify-center rounded-md hover:bg-accent p-2" title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="/admin/products/${product.id}/edit" class="flex items-center justify-center rounded-md hover:bg-accent p-2" title="Chỉnh sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                        <path d="m15 5 4 4"></path>
                                    </svg>
                                </a>
                                <form action="/admin/products/${product.id}" method="POST" class="delete-form">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent" onclick="dtmodalConfirmDelete({
                                        title: 'Xác nhận xóa sản phẩm',
                                        subtitle: 'Bạn có chắc chắn muốn xóa sản phẩm này?',
                                        message: 'Hành động này không thể hoàn tác.',
                                        itemName: '${product.name}',
                                        onConfirm: () => this.closest('form').submit()
                                    })" title="Xóa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            // Re-attach event listeners to new checkboxes
            document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectAllState);
            });
        }

        // Update pagination
        function updatePagination(pagination) {
            const start = document.getElementById('paginationStart');
            const end = document.getElementById('paginationEnd');
            const total = document.getElementById('paginationTotal');
            const controls = document.getElementById('paginationControls');

            start.textContent = pagination.from || 1;
            end.textContent = pagination.to || 0;
            total.textContent = pagination.total || 0;

            controls.innerHTML = `
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted ${pagination.current_page === 1 ? 'disabled opacity-50 cursor-not-allowed' : ''}" onclick="changePage(${pagination.current_page - 1})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                ${Array.from({ length: pagination.last_page }, (_, i) => i + 1).map(page => `
                    <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium ${pagination.current_page === page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'}" onclick="changePage(${page})">${page}</button>
                `).join('')}
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted ${pagination.current_page === pagination.last_page ? 'disabled opacity-50 cursor-not-allowed' : ''}" onclick="changePage(${pagination.current_page + 1})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
            `;
        }

        // Change page
        function changePage(page) {
            if (page < 1 || page > {{ $products->lastPage() }}) return;
            fetchProducts(page);
        }

        // Reset filters
        function resetFilters() {
            const form = document.getElementById('filterForm');
            form.reset();
            const priceRange = document.getElementById('price_range');
            priceRange.noUiSlider.set([{{ $minPrice }}, {{ $maxPrice }}]);
            updatePriceDisplay({{ $minPrice }}, {{ $maxPrice }});
            fetchProducts();
        }

        // Update price display
        function updatePriceDisplay(min, max) {
            const priceMinDisplay = document.getElementById('price_min_display');
            const priceMaxDisplay = document.getElementById('price_max_display');
            const priceMin = document.getElementById('price_min');
            const priceMax = document.getElementById('price_max');
            
            priceMinDisplay.value = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(min);
            priceMaxDisplay.value = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(max);
            priceMin.value = min;
            priceMax.value = max;
        }

        // Initialize price range slider
        document.addEventListener('DOMContentLoaded', function() {
            const priceRange = document.getElementById('price_range');
            if (priceRange) {
                noUiSlider.create(priceRange, {
                    start: [{{ $minPrice }}, {{ $maxPrice }}],
                    connect: true,
                    range: {
                        'min': {{ $minPrice }},
                        'max': {{ $maxPrice }}
                    },
                    step: 10000,
                    behaviour: 'drag',
                    format: {
                        to: value => Math.round(value),
                        from: value => Number(value)
                    }
                });

                priceRange.noUiSlider.on('update', function(values, handle) {
                    updatePriceDisplay(values[0], values[1]);
                });
            }

            // Initialize select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectAllButton = document.getElementById('selectAllButton');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');

            selectAllButton.addEventListener('click', toggleSelectAll);

            selectAllCheckbox.addEventListener('change', toggleSelectAll);

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectAllState);
            });

            // Debounced search
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(function() {
                    fetchProducts();
                }, 300));
            }

            // Filter form submission
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetchProducts();
                    toggleModal('filterModal');
                });
            }
        });

        // Confirm delete action
        function dtmodalConfirmDelete(options) {
            if (confirm(`${options.title}\n${options.subtitle}\n${options.message}\nSản phẩm: ${options.itemName}`)) {
                options.onConfirm();
            }
        }
    </script>
@endsection
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

<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag">
                    <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                    <path d="M3 6h18"></path>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
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
                        <th class="py-3 px-4 text-left font-medium">
                            <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300">
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Mã sản phẩm</th>
                        <th class="py-3 px-4 text-left font-medium">Hình ảnh</th>
                        <th class="py-3 px-4 text-left font-medium">Tên sản phẩm</th>
                        <th class="py-3 px-4 text-left font-medium">Danh mục</th>
                        <th class="py-3 px-4 text-right font-medium">Giá</th>
                        <th class="py-3 px-4 text-center font-medium">Tồn kho</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @forelse($products as $product)
                    <tr class="border-b">
                        <td class="py-3 px-4">
                            <input type="checkbox" name="selected_products[]" value="{{ $product->id }}" class="product-checkbox rounded border-gray-300">
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $product->sku }}</td>
                        <td class="py-3 px-4">
                            <div class="h-12 w-12 rounded-md bg-muted flex items-center justify-center overflow-hidden">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium">{{ $product->name }}</div>
                            <div class="text-sm text-muted-foreground">{{ $product->sku }}</div>
                        </td>
                        <td class="py-3 px-4">{{ $product->category->name ?? 'N/A' }}</td>
                       
                        <td class="py-3 px-4 text-right">
                            {{ number_format($product->base_price, 0, ',', '.') }} đ
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $totalStock = $product->variants->sum(function($variant) {
                                    return $variant->branchStocks->sum('stock_quantity');
                                });
                            @endphp
                            <span class="status-tag {{ $totalStock > 0 ? 'success' : 'failed' }}">
                                {{ $totalStock }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-tag {{ $product->available ? 'success' : 'failed' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    @if ($product->available)
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                    @else
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m15 9-6 6"></path>
                                    <path d="m9 9 6 6"></path>
                                    @endif
                                </svg>
                                {{ $product->available ? 'Đang bán' : 'Khóa' }}
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
                        <td colspan="9" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                    <path d="M3 6h18"></path>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                <h3 class="text-lg font-medium">Không có sản phẩm nào</h3>
                                <p class="text-sm">Hãy thêm sản phẩm mới để bắt đầu</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
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
            <h3 class="text-lg font-medium">Lọc sản phẩm</h3>
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

                <!-- Price Range -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Khoảng giá</label>
                    <div class="price-range-container">
                        <div class="price-slider" id="priceSlider">
                            <div class="price-slider-track" id="priceTrack"></div>
                            <div class="price-slider-handle" id="minHandle" data-handle="min"></div>
                            <div class="price-slider-handle" id="maxHandle" data-handle="max"></div>
                        </div>
                        <div class="price-display">
                            <span id="minPriceDisplay">{{ number_format($minPrice, 0, ',', '.') }} đ</span>
                            <span id="maxPriceDisplay">{{ number_format($maxPrice, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                    <div class="price-inputs">
                        <input type="text" id="minPriceInput" class="price-input" placeholder="Giá tối thiểu">
                        <input type="text" id="maxPriceInput" class="price-input" placeholder="Giá tối đa">
                    </div>
                    <input type="hidden" name="price_min" id="price_min" value="{{ $minPrice }}">
                    <input type="hidden" name="price_max" id="price_max" value="{{ $maxPrice }}">
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Trạng thái</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="status[]" value="available" class="rounded border-gray-300 mr-2">
                            Đang bán
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="status[]" value="unavailable" class="rounded border-gray-300 mr-2">
                            Không bán
                        </label>
                    </div>
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
    // ----- Modal Toggle -----
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }
    
    // ----- Reset Filters -----
    function resetFilters() {
        const form = document.getElementById('filterForm');
        form.reset();
        
        // Reset price slider
        const minPrice = {{ $minPrice }};
        const maxPrice = {{ $maxPrice }};
        
        if (window.priceSlider) {
            window.priceSlider.minValue = minPrice;
            window.priceSlider.maxValue = maxPrice;
            window.priceSlider.updateVisual();
        }
        
        // Reset hidden inputs
        document.getElementById('price_min').value = minPrice;
        document.getElementById('price_max').value = maxPrice;
        
        // Close the filter modal
        toggleModal('filterModal');
        
        // Reload the page with default filters
        window.location.href = '{{ route("admin.products.index") }}';
    }
    
    // ----- Price Range Slider -----
    class PriceRangeSlider {
        constructor(config) {
            this.min = config.min || 0;
            this.max = config.max || 1000000;
            this.step = config.step || 10000;
            this.minValue = config.minValue || this.min;
            this.maxValue = config.maxValue || this.max;
            this.slider = document.getElementById(config.sliderId);
            this.track = document.getElementById(config.trackId);
            this.minHandle = document.getElementById(config.minHandleId);
            this.maxHandle = document.getElementById(config.maxHandleId);
            this.isDragging = false;
            this.activeHandle = null;
            
            this.init();
        }
        
        init() {
            this.updateVisual();
            this.attachEvents();
        }
        
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price) + ' đ';
        }
        
        updateVisual() {
            const range = this.max - this.min;
            const minPct = ((this.minValue - this.min) / range) * 100;
            const maxPct = ((this.maxValue - this.min) / range) * 100;
            
            this.minHandle.style.left = `${minPct}%`;
            this.maxHandle.style.left = `${maxPct}%`;
            this.track.style.left = `${minPct}%`;
            this.track.style.width = `${maxPct - minPct}%`;
            
            document.getElementById('minPriceDisplay').textContent = this.formatPrice(this.minValue);
            document.getElementById('maxPriceDisplay').textContent = this.formatPrice(this.maxValue);
            document.getElementById('price_min').value = this.minValue;
            document.getElementById('price_max').value = this.maxValue;
            
            // Update the inputs
            const minInput = document.getElementById('minPriceInput');
            const maxInput = document.getElementById('maxPriceInput');
            
            if (minInput) {
                minInput.value = this.formatPrice(this.minValue);
            }
            
            if (maxInput) {
                maxInput.value = this.formatPrice(this.maxValue);
            }
        }
        
        attachEvents() {
            // Mouse events
            this.minHandle.addEventListener('mousedown', e => {
                e.preventDefault();
                this.startDrag('min');
            });
            
            this.maxHandle.addEventListener('mousedown', e => {
                e.preventDefault();
                this.startDrag('max');
            });
            
            document.addEventListener('mousemove', e => this.handleMouseMove(e));
            document.addEventListener('mouseup', () => this.handleMouseUp());
            
            // Touch events
            this.minHandle.addEventListener('touchstart', e => {
                e.preventDefault();
                this.startDrag('min');
            });
            
            this.maxHandle.addEventListener('touchstart', e => {
                e.preventDefault();
                this.startDrag('max');
            });
            
            document.addEventListener('touchmove', e => this.handleMouseMove(e.touches[0]));
            document.addEventListener('touchend', () => this.handleMouseUp());
            
            // Click on track
            this.slider.addEventListener('click', e => {
                if (this.isDragging) return;
                
                const val = this.getValueFromPosition(e.clientX);
                const dMin = Math.abs(val - this.minValue);
                const dMax = Math.abs(val - this.maxValue);
                
                if (dMin < dMax) {
                    this.minValue = Math.min(val, this.maxValue - this.step);
                } else {
                    this.maxValue = Math.max(val, this.minValue + this.step);
                }
                
                this.updateVisual();
            });
            
            // Manual inputs
            const minInput = document.getElementById('minPriceInput');
            const maxInput = document.getElementById('maxPriceInput');
            
            if (minInput) {
                minInput.addEventListener('blur', e => {
                    const v = this.parsePrice(e.target.value);
                    if (!isNaN(v)) {
                        this.minValue = Math.max(this.min, Math.min(v, this.maxValue - this.step));
                        this.updateVisual();
                    }
                });
            }
            
            if (maxInput) {
                maxInput.addEventListener('blur', e => {
                    const v = this.parsePrice(e.target.value);
                    if (!isNaN(v)) {
                        this.maxValue = Math.min(this.max, Math.max(v, this.minValue + this.step));
                        this.updateVisual();
                    }
                });
            }
        }
        
        startDrag(handle) {
            this.isDragging = true;
            this.activeHandle = handle;
            document.body.style.cursor = 'grabbing';
        }
        
        handleMouseMove(e) {
            if (!this.isDragging) return;
            
            const val = this.getValueFromPosition(e.clientX);
            
            if (this.activeHandle === 'min') {
                this.minValue = Math.min(Math.max(val, this.min), this.maxValue - this.step);
            } else {
                this.maxValue = Math.max(Math.min(val, this.max), this.minValue + this.step);
            }
            
            this.updateVisual();
        }
        
        handleMouseUp() {
            if (this.isDragging) {
                this.isDragging = false;
                this.activeHandle = null;
                document.body.style.cursor = 'default';
            }
        }
        
        getValueFromPosition(x) {
            const rect = this.slider.getBoundingClientRect();
            let pct = (x - rect.left) / rect.width;
            pct = Math.min(Math.max(pct, 0), 1);
            
            const val = this.min + pct * (this.max - this.min);
            return Math.round(val / this.step) * this.step;
        }
        
        parsePrice(str) {
            return parseInt(str.replace(/[^\d]/g, ''), 10);
        }
        
        getValues() {
            return { min: this.minValue, max: this.maxValue };
        }
        
        reset() {
            this.minValue = this.min;
            this.maxValue = this.max;
            this.updateVisual();
        }
    }
    
    // ----- Initialize on DOM Ready -----
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize price slider
        window.priceSlider = new PriceRangeSlider({
            min: {{ $minPrice }},
            max: {{ $maxPrice }},
            minValue: {{ request('price_min', $minPrice) }},
            maxValue: {{ request('price_max', $maxPrice) }},
            step: 10000,
            sliderId: 'priceSlider',
            trackId: 'priceTrack',
            minHandleId: 'minHandle',
            maxHandleId: 'maxHandle'
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown > div:not(.hidden)');
            dropdowns.forEach(dropdown => {
                const isClickInside = dropdown.contains(event.target) || 
                                     dropdown.previousElementSibling.contains(event.target);
                
                if (!isClickInside) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    });
    
    // Function to handle pagination
    function changePage(page) {
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }

    // ----- Checkbox Functionality -----
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const selectAllButton = document.getElementById('selectAllButton');
        const actionsDropdown = document.getElementById('actionsMenu');

        // Handle select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectAllButton();
        });

        // Handle individual checkboxes
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllCheckbox();
                updateSelectAllButton();
            });
        });

        // Update select all checkbox state
        function updateSelectAllCheckbox() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === productCheckboxes.length;
        }

        // Update select all button state
        function updateSelectAllButton() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            selectAllButton.disabled = checkedBoxes.length === 0;
        }

        // Handle select all button click
        selectAllButton.addEventListener('click', function() {
            const allChecked = selectAllCheckbox.checked;
            selectAllCheckbox.checked = !allChecked;
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
            updateSelectAllButton();
        });

        // Handle action buttons
        document.querySelectorAll('#actionsMenu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.getAttribute('onclick');
                if (action) {
                    const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
                    if (checkedBoxes.length === 0) {
                        alert('Vui lòng chọn ít nhất một sản phẩm');
                        return;
                    }
                    eval(action);
                }
            });
        });
    });

    // ----- Update Selected Status -----
    function updateSelectedStatus(status) {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const productIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (productIds.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm');
            return;
        }

        if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái của các sản phẩm đã chọn?')) {
            fetch('{{ ("admin.products.update-status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_ids: productIds,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Có lỗi xảy ra khi cập nhật trạng thái');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật trạng thái');
            });
        }
    }
</script>
@endsection
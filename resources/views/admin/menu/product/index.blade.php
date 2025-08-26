@extends('layouts.admin.contentLayoutMaster')

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

    /* Stock badge styling */
    .stock-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }

    .stock-badge.out-of-stock {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .stock-badge.low-stock {
        background-color: #fef3c7;
        color: #d97706;
    }

    .stock-badge.in-stock {
        background-color: #dcfce7;
        color: #15803d;
    }

    .product-status {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }

    .product-status.selling {
        background-color: #dcfce7;
        color: #15803d;
    }

    .product-status.coming-soon {
        background-color: #fef3c7;
        color: #d97706;
    }

    .product-status.discontinued {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Loading states for AJAX search */
    .loading {
        position: relative;
    }

    .loading::after {
        content: '';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translateY(-50%) rotate(0deg); }
        100% { transform: translateY(-50%) rotate(360deg); }
    }

    .search-error {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-64 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                    <div class="p-3">
                        <!-- Category Selection -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-2">Chọn danh mục:</label>
                            <select id="exportCategorySelect" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Branch Selection -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-2">Chọn chi nhánh:</label>
                            <select id="exportBranchSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tất cả chi nhánh</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Export Options -->
                        <div class="border-t pt-2">
                            <button onclick="exportProducts('excel')" class="flex items-center w-full rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <path d="M8 13h2"></path>
                                    <path d="M8 17h2"></path>
                                    <path d="M14 13h2"></path>
                                    <path d="M14 17h2"></path>
                                </svg>
                                Xuất Excel
                            </button>
                            <button onclick="exportProducts('pdf')" class="flex items-center w-full rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Xuất PDF
                            </button>
                            <button onclick="exportProducts('csv')" class="flex items-center w-full rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <path d="M8 13h8"></path>
                                    <path d="M8 17h8"></path>
                                </svg>
                                Xuất CSV
                            </button>
                        </div>
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

                        <th class="py-3 px-4 text-left font-medium">Mã sản phẩm</th>
                        <th class="py-3 px-4 text-left font-medium">Hình ảnh</th>
                        <th class="py-3 px-4 text-left font-medium">Tên sản phẩm</th>
                        <th class="py-3 px-4 text-left font-medium">Danh mục</th>
                        <th class="py-3 px-4 text-center font-medium">Giá</th>
                        <th class="py-3 px-4 text-center font-medium">Tồn kho</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @forelse($products as $product)
                    <tr class="border-b">

                        <td class="py-3 px-4 font-medium">{{ $product->sku }}</td>
                        <td class="py-3 px-4">
                            <div class="h-12 w-12 rounded-md bg-muted flex items-center justify-center overflow-hidden" style="width:100px; height:60px; border-radius:4px; background:#f3f4f6;">
                                @php
                                    $primaryImg = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                @endphp
                                @if($primaryImg)
                                    <img src="{{ Storage::disk('s3')->url($primaryImg->img) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:5px;" />
                                @else
                                    <span class="text-xs text-gray-400">No image</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium">{{ $product->name }}</div>
                        </td>
                        <td class="py-3 px-4">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-right">
                            {{ number_format($product->base_price, 0, ',', '.') }} đ
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $totalStock = 0;
                                foreach ($product->variants as $variant) {
                                    $totalStock += $variant->branchStocks->sum('stock_quantity');
                                }
                                if ($totalStock == 0) {
                                    $stockClass = 'out-of-stock';
                                    $stockText = 'Hết hàng';
                                } elseif ($totalStock > 0 && $totalStock < 10) {
                                    $stockClass = 'low-stock';
                                    $stockText = 'Sắp hết ('.$totalStock.')';
                                } else {
                                    $stockClass = 'in-stock';
                                    $stockText = $totalStock;
                                }
                            @endphp
                            <span class="stock-badge {{ $stockClass }}">
                                {{ $stockText }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($product->trashed())
                                <span class="product-status deleted">
                                    Đã xóa
                                </span>
                            @else
                                @php
                                    switch ($product->status) {
                                        case 'selling':
                                            $statusText = 'Đang bán';
                                            $statusClass = 'selling';
                                            break;
                                        case 'coming_soon':
                                            $statusText = 'Sắp ra mắt';
                                            $statusClass = 'coming-soon';
                                            break;
                                        case 'discontinued':
                                        default:
                                            $statusText = 'Ngừng bán';
                                            $statusClass = 'discontinued';
                                            break;
                                    }
                                @endphp
                                <span class="product-status {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center space-x-1">
                                @if($product->trashed())
                                    <!-- Nút khôi phục -->
                                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="restore-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent text-green-600"
                                            onclick="dtmodalConfirmRestore({
                                                    title: 'Xác nhận khôi phục sản phẩm',
                                                    subtitle: 'Bạn có chắc chắn muốn khôi phục sản phẩm này?',
                                                    itemName: '{{ $product->name }}',
                                                    onConfirm: () => this.closest('form').submit()
                                                })"
                                            title="Khôi phục">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                                <path d="M21 3v5h-5"></path>
                                                <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                                <path d="M8 16l-5 5v-5h5"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    <!-- Nút xóa hoàn toàn -->
                                    <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST" class="force-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent text-red-600"
                                            onclick="dtmodalConfirmForceDelete({
                                                    title: 'Xác nhận xóa hoàn toàn',
                                                    subtitle: 'Bạn có chắc chắn muốn xóa hoàn toàn sản phẩm này?',
                                                    message: 'Hành động này không thể hoàn tác và sẽ xóa vĩnh viễn sản phẩm khỏi hệ thống.',
                                                    itemName: '{{ $product->name }}',
                                                    onConfirm: () => this.closest('form').submit()
                                                })"
                                            title="Xóa hoàn toàn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"></path>
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <!-- Nút chỉnh sửa -->
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                        class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                        title="Chỉnh sửa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                    <!-- Nút xóa mềm -->
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                            onclick="dtmodalConfirmDelete({
                                                    title: 'Xác nhận ẩn sản phẩm',
                                                    subtitle: 'Bạn có chắc chắn muốn ẩn sản phẩm này?',
                                                    message: 'Sản phẩm sẽ được ẩn khỏi danh sách nhưng vẫn có thể khôi phục.',
                                                    itemName: '{{ $product->name }}',
                                                    onConfirm: () => this.closest('form').submit()
                                                })"
                                            title="Ẩn sản phẩm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"></path>
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
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
        <div class="pagination-container flex items-center justify-between px-4 py-4 border-t">
            <div class="text-sm text-muted-foreground">
                Hiển thị <span id="paginationStart">{{ $products->firstItem() }}</span> đến <span id="paginationEnd">{{ $products->lastItem() }}</span> của <span id="paginationTotal">{{ $products->total() }}</span> mục
            </div>
            <div class="flex items-center justify-end space-x-2 ml-auto" id="paginationControls">
                @unless($products->onFirstPage())
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage({{ $products->currentPage() - 1 }})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                @endunless

                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium {{ $products->currentPage() == $page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted' }}" onclick="changePage({{ $page }})">
                    {{ $page }}
                </button>
                @endforeach

                @unless($products->currentPage() === $products->lastPage())
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage({{ $products->currentPage() + 1 }})">
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
<div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-background rounded-lg shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-3 border-b">
            <h3 class="text-lg font-medium">Lọc sản phẩm</h3>
            <button type="button" class="text-muted-foreground hover:text-foreground" onclick="toggleModal('filterModal')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <form id="filterForm">
            <div class="p-4">
                <!-- Row 1: Category and Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="filter_category" class="text-sm font-medium block mb-1">Danh mục</label>
                        <select id="filter_category" name="category_id" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_date_added" class="text-sm font-medium block mb-1">Ngày thêm</label>
                        <select id="filter_date_added" name="date_added" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                            <option value="">Tất cả</option>
                            <option value="today" {{ request('date_added') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                            <option value="week" {{ request('date_added') == 'week' ? 'selected' : '' }}>Tuần này</option>
                            <option value="month" {{ request('date_added') == 'month' ? 'selected' : '' }}>Tháng này</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Price Range -->
                <div class="mb-4">
                    <label class="text-sm font-medium block mb-2">Khoảng giá</label>
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
                    <div class="price-inputs mt-2">
                        <input type="text" id="minPriceInput" class="price-input" placeholder="Giá tối thiểu">
                        <input type="text" id="maxPriceInput" class="price-input" placeholder="Giá tối đa">
                    </div>
                    <input type="hidden" name="price_min" id="price_min" value="{{ $minPrice }}">
                    <input type="hidden" name="price_max" id="price_max" value="{{ $maxPrice }}">
                </div>

                <!-- Row 3: Status Filters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium block mb-2">Tình trạng kho</label>
                        <div class="space-y-1">
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="stock_status[]" value="in_stock" class="rounded border-gray-300 mr-2" {{ in_array('in_stock', request('stock_status', [])) ? 'checked' : '' }}>
                                Còn hàng
                            </label>
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="stock_status[]" value="low_stock" class="rounded border-gray-300 mr-2" {{ in_array('low_stock', request('stock_status', [])) ? 'checked' : '' }}>
                                Sắp hết hàng
                            </label>
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="stock_status[]" value="out_of_stock" class="rounded border-gray-300 mr-2" {{ in_array('out_of_stock', request('stock_status', [])) ? 'checked' : '' }}>
                                Hết hàng
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium block mb-2">Trạng thái sản phẩm</label>
                        <div class="space-y-1">
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="status[]" value="available" class="rounded border-gray-300 mr-2" {{ in_array('available', request('status', [])) ? 'checked' : '' }}>
                                Đang bán
                            </label>
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="status[]" value="unavailable" class="rounded border-gray-300 mr-2" {{ in_array('unavailable', request('status', [])) ? 'checked' : '' }}>
                                Không bán
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="filter_deleted_status" class="text-sm font-medium block mb-2">Trạng thái hiển thị</label>
                        <select id="filter_deleted_status" name="deleted_status" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                            <option value="" {{ request('deleted_status') === '' ? 'selected' : '' }}>Tất cả sản phẩm</option>
                            <option value="active" {{ request('deleted_status') === 'active' ? 'selected' : '' }}>Sản phẩm đang hoạt động</option>
                            <option value="deleted" {{ request('deleted_status') === 'deleted' ? 'selected' : '' }}>Sản phẩm đã ẩn</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end p-3 border-t space-x-2 bg-gray-50">
                <button type="button" id="resetFilters" class="btn btn-outline text-sm px-3 py-1.5">Xóa bộ lọc</button>
                <button type="button" class="btn btn-outline text-sm px-3 py-1.5" onclick="toggleModal('filterModal')">Đóng</button>
                <button type="button" id="applyFilters" class="btn btn-primary text-sm px-3 py-1.5">Áp dụng</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ----- Dropdown Toggle -----
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    // ----- Modal Toggle -----
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }
    
    // Reset filters function is now handled by FilterManager class
    
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
            this.updateVisual(false);
            this.attachEvents();
        }
        
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price) + ' đ';
        }
        
        updateVisual(triggerEvent = false) {
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
            
            // Only trigger change event when explicitly requested
            if (triggerEvent) {
                this.triggerChangeEvent();
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
                
                this.updateVisual(true);
            });
            
            // Manual inputs
            const minInput = document.getElementById('minPriceInput');
            const maxInput = document.getElementById('maxPriceInput');
            
            if (minInput) {
                minInput.addEventListener('blur', e => {
                    const v = this.parsePrice(e.target.value);
                    if (!isNaN(v)) {
                        this.minValue = Math.max(this.min, Math.min(v, this.maxValue - this.step));
                        this.updateVisual(true);
                    }
                });
            }
            
            if (maxInput) {
                maxInput.addEventListener('blur', e => {
                    const v = this.parsePrice(e.target.value);
                    if (!isNaN(v)) {
                        this.maxValue = Math.min(this.max, Math.max(v, this.minValue + this.step));
                        this.updateVisual(true);
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
            
            // Don't trigger events during drag
            this.updateVisual(false);
        }
        
        handleMouseUp() {
            if (this.isDragging) {
                this.isDragging = false;
                this.activeHandle = null;
                document.body.style.cursor = 'default';
                
                // Trigger event only when drag ends
                this.triggerChangeEvent();
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
            this.updateVisual(true);
        }
        
        setValue(min, max) {
            this.minValue = Math.max(this.min, Math.min(min, this.max));
            this.maxValue = Math.min(this.max, Math.max(max, this.min));
            this.updateVisual(true);
        }
        
        updateRange(newMin, newMax) {
            // Validate input values
            const validMin = (newMin !== null && newMin !== undefined && !isNaN(newMin)) ? newMin : 0;
            const validMax = (newMax !== null && newMax !== undefined && !isNaN(newMax)) ? newMax : 1000000;
            
            // Ensure min is not greater than max
            const finalMin = Math.min(validMin, validMax);
            const finalMax = Math.max(validMin, validMax);
            
            // Update range boundaries
            this.min = finalMin;
            this.max = finalMax;
            
            // Only reset current values if they are outside the new range
            // This preserves user's current selection when possible
            if (this.minValue < finalMin) {
                this.minValue = finalMin;
            }
            if (this.maxValue > finalMax) {
                this.maxValue = finalMax;
            }
            
            // Ensure current values are within bounds
            this.minValue = Math.max(finalMin, Math.min(this.minValue, finalMax));
            this.maxValue = Math.min(finalMax, Math.max(this.maxValue, finalMin));
            
            this.updateVisual(false);
        }
        
        triggerChangeEvent() {
            // Dispatch custom event for filter manager
            const event = new CustomEvent('priceRangeChanged', {
                detail: { min: this.minValue, max: this.maxValue }
            });
            document.dispatchEvent(event);
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
            step: 1,
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

        // ----- Checkbox Functionality -----
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const selectAllButton = document.getElementById('selectAllButton');

        // Handle select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });

        // Handle select all button click
        selectAllButton.addEventListener('click', function() {
            const isAllChecked = Array.from(productCheckboxes).every(cb => cb.checked);
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = !isAllChecked;
            });
            selectAllCheckbox.checked = !isAllChecked;
        });

        // Handle individual checkboxes
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
    });
    
    // Function to handle pagination
    function changePage(page) {
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }

    // Function to handle export
    function exportProducts(type) {
        try {
            const categorySelect = document.getElementById('exportCategorySelect');
            const selectedCategory = categorySelect ? categorySelect.value : '';
            
            const branchSelect = document.getElementById('exportBranchSelect');
            const selectedBranch = branchSelect ? branchSelect.value : '';
            
            // Get current filter values
            const urlParams = new URLSearchParams(window.location.search);
            const priceMin = urlParams.get('price_min') || '';
            const priceMax = urlParams.get('price_max') || '';
            const stockStatus = urlParams.get('stock_status') || '';
            
            // Build export URL
            const exportUrl = new URL('{{ route("admin.products.export") }}', window.location.origin);
            exportUrl.searchParams.set('type', type);
            
            if (selectedCategory) {
                exportUrl.searchParams.set('category_id', selectedCategory);
            }
            
            if (selectedBranch) {
                exportUrl.searchParams.set('branch_id', selectedBranch);
            }
            
            if (priceMin) {
                exportUrl.searchParams.set('price_min', priceMin);
            }
            
            if (priceMax) {
                exportUrl.searchParams.set('price_max', priceMax);
            }
            
            if (stockStatus) {
                exportUrl.searchParams.set('stock_status', stockStatus);
            }
            
            // Hide export menu
            const exportMenu = document.getElementById('exportMenu');
            if (exportMenu) {
                exportMenu.classList.add('hidden');
            }
            
            // Show loading message
            dtmodalShowToast('info', {
                title: 'Đang xử lý...',
                message: `Đang chuẩn bị file ${type.toUpperCase()} để tải xuống...`
            });
            
            // Use fetch to check for errors first
            fetch(exportUrl.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Có lỗi xảy ra khi xuất dữ liệu');
                    });
                }
                
                // If successful, trigger download
                const link = document.createElement('a');
                link.href = exportUrl.toString();
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                dtmodalShowToast('success', {
                    title: 'Thành công!',
                    message: `File ${type.toUpperCase()} đã được tải xuống.`
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                dtmodalShowToast('error', {
                    title: 'Lỗi!',
                    message: error.message || 'Có lỗi xảy ra khi xuất dữ liệu.'
                });
            });
            
        } catch (error) {
            console.error('Export error:', error);
            dtmodalShowToast('error', {
                title: 'Lỗi!',
                message: 'Có lỗi xảy ra khi xuất dữ liệu.'
            });
        }
    }
    


    // ----- Update Selected Status -----
   
</script>

<!-- Include AJAX Search JavaScript -->
<script src="{{ asset('js/admin/menu/product.js') }}"></script>

<!-- Include AJAX Filter JavaScript -->
<script src="{{ asset('js/admin/menu/filter.js') }}"></script>
@endsection
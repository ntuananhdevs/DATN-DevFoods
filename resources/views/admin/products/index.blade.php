@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Danh sách sản phẩm')
@section('description', 'Quản lý danh sách sản phẩm của bạn')


@section('content')
<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Header chính -->
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

    <!-- Card chứa bảng -->
    <div class="card border rounded-lg overflow-hidden">
        <!-- Tiêu đề bảng -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách sản phẩm</h3>
        </div>

        <!-- Thanh công cụ -->
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" placeholder="Tìm kiếm theo tên, mã sản phẩm..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9">
            </div>
            <div class="flex items-center gap-2">
                <button class="btn btn-outline flex items-center" onclick="toggleSelectAll()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    Chọn tất cả
                </button>
                <div class="dropdown relative">
                    <button class="btn btn-outline flex items-center" id="actionsDropdown" onclick="toggleDropdown('actionsMenu')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"></path>
                            <path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path>
                            <path d="M12 2v2"></path>
                            <path d="M12 22v-2"></path>
                            <path d="m17 20.66-1-1.73"></path>
                            <path d="M11 10.27 7 3.34"></path>
                            <path d="m20.66 17-1.73-1"></path>
                            <path d="m3.34 7 1.73 1"></path>
                            <path d="M14 12h8"></path>
                            <path d="M2 12h2"></path>
                            <path d="m20.66 7-1.73 1"></path>
                            <path d="m3.34 17 1.73-1"></path>
                            <path d="m17 3.34-1 1.73"></path>
                            <path d="m7 20.66-1-1.73"></path>
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
                <button class="btn btn-outline flex items-center" data-toggle="modal" data-target="#filterModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Lọc
                </button>
            </div>
        </div>

        <!-- Container bảng -->
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
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b">
                        <td class="py-3 px-4">
                            <input type="checkbox" class="row-checkbox rounded border-gray-300" value="1">
                        </td>
                        <td class="py-3 px-4 font-medium">1</td>
                        <td class="py-3 px-4">
                            <div class="h-12 w-12 rounded-md bg-muted flex items-center justify-center overflow-hidden">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }} class="h-full w-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium">{{ $product->name }}</div>
                        </td>
                        <td class="py-3 px-4">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-right">{{ number_format($product->base_price, 0, ',', '.') }} đ</td>
                        <td class="py-3 px-4 text-center">
                            @if ($product->stock)
                                <span class="data-table-status data-table-status-success">
                                    <i class="fas fa-check"></i> Còn hàng
                                </span>
                            @else
                                <span class="data-table-status data-table-status-failed">
                                    <i class="fas fa-times"></i> Hết hàng
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                                @if ($product->stock)
                                    <span class="data-table-status data-table-status-success">
                                        <i class="fas fa-check"></i> Đang bán 
                                    </span>
                                @else
                                    <span class="data-table-status data-table-status-failed">
                                        <i class="fas fa-times"></i> Khóa
                                    </span>
                                @endif
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center space-x-1">
                            <a href="{{ route('admin.products.show', $product->id) }}" 
                               class="flex items-center justify-center rounded-md hover:bg-accent p-2" 
                               title="Xem chi tiết">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                                    stroke-linejoin="round">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

        <!-- Phân trang và thông tin -->
        <div class="flex items-center justify-between px-4 py-4 border-t">
            <div class="text-sm text-muted-foreground">
                Hiển thị <span>1</span> đến <span>3</span> của <span>100</span> mục
            </div>
            <div class="flex items-center space-x-2">
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                <button class="h-8 min-w-8 rounded-md bg-primary px-2 text-xs font-medium text-primary-foreground">1</button>
                <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium hover:bg-muted">2</button>
                <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium hover:bg-muted">3</button>
                <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium hover:bg-muted">4</button>
                <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium hover:bg-muted">5</button>
                <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lọc -->
<div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-background rounded-lg shadow-lg w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-medium">Lọc sản phẩm</h3>
            <button type="button" class="text-muted-foreground hover:text-foreground" onclick="toggleModal('filterModal')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <form>
            <div class="p-4 space-y-4">
                <div class="space-y-2">
                    <label for="filter_category" class="text-sm font-medium">Danh mục</label>
                    <select id="filter_category" name="category_id" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                        <option value="">Tất cả danh mục</option>
                        <option value="1">Điện thoại</option>
                        <option value="2">Laptop</option>
                        <option value="3">Phụ kiện</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label for="filter_price_min" class="text-sm font-medium">Giá tối thiểu</label>
                    <input type="number" id="filter_price_min" name="price_min" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                </div>
                <div class="space-y-2">
                    <label for="filter_price_max" class="text-sm font-medium">Giá tối đa</label>
                    <input type="number" id="filter_price_max" name="price_max" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                </div>
                <div class="space-y-2">
                    <label for="filter_stock" class="text-sm font-medium">Tình trạng</label>
                    <select id="filter_stock" name="stock_status" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                        <option value="">Tất cả</option>
                        <option value="in_stock">Còn hàng</option>
                        <option value="out_of_stock">Hết hàng</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 border-t space-x-2">
                <button type="button" class="btn btn-outline" onclick="toggleModal('filterModal')">Đóng</button>
                <button type="submit" class="btn btn-primary">Áp dụng</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Toggle dropdown
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(function(dropdown) {
                if (!dropdown.parentElement.contains(event.target)) {
                    dropdown.classList.add('hidden');
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
            
            rowCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }

        // Update selected status
        function updateSelectedStatus(status) {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm');
                return;
            }
            
            // Here you would normally send an AJAX request to update the status
            console.log(`Updating status to ${status} for products: ${ids.join(', ')}`);
            alert(`Đã cập nhật trạng thái cho ${ids.length} sản phẩm`);
        }

        // Handle search
        function handleSearch(event) {
            if (event.key === 'Enter') {
                // Here you would normally submit the search form
                console.log(`Searching for: ${event.target.value}`);
            }
        }

        // Initialize select all checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', toggleSelectAll);
            }
        });

        // Confirm delete action
        function confirmDelete(button) {
            const form = button.closest('form');
            const itemName = form.querySelector('input[name="item_name"]').value;

            if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${itemName}" không?`)) {
                form.submit();
            }
        }

        function handleDeleteProduct(button, productName) {
            if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${productName}" không?`)) {
                const form = button.closest('form');
                form.submit();
            }
        }
    </script>
@endsection
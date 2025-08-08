@extends('layouts.branch.contentLayoutMaster')
@use('Illuminate\Support\Facades\Storage')

@section('title', 'Danh sách sản phẩm')
@section('description', 'Quản lý danh sách sản phẩm của bạn')

@section('content')
    <style>
        /* ... giữ nguyên phần style như file admin ... */
    </style>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span class="inline-block bg-primary/10 p-2 rounded"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag">
                        <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <path d="M3 6h18"></path>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg></span>
                Quản lý sản phẩm
            </h1>
            <div class="text-muted-foreground text-sm">Quản lý danh sách sản phẩm của bạn</div>
        </div>
        <div>
            <a class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90">+ Thêm mới</a>
        </div>
    </div>
    <div class="card border rounded-lg overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách sản phẩm</h3>
        </div>
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <input type="text" placeholder="Tìm kiếm theo tên, mã sản phẩm..."
                    class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput">
            </div>
            <div class="flex items-center gap-2">
                <button class="btn btn-outline flex items-center" id="selectAllButton">
                    <span>Chọn tất cả</span>
                </button>
                <button class="btn btn-outline flex items-center">Lọc</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="py-3 px-4"><input type="checkbox"></th>
                        <th class="py-3 px-4">Mã sản phẩm</th>
                        <th class="py-3 px-4">Hình ảnh</th>
                        <th class="py-3 px-4">Tên sản phẩm</th>
                        <th class="py-3 px-4">Danh mục</th>
                        <th class="py-3 px-4 text-right">Giá</th>
                        <th class="py-3 px-4 text-center">Tồn kho</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-b">
                            <td class="py-3 px-4"><input type="checkbox"></td>
                            <td class="py-3 px-4">{{ $product->sku }}</td>
                            <td class="py-3 px-4">
                                <img src="{{ $product->primaryImage ? $product->primaryImage->url : asset('images/no-image.png') }}"
                                    alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                            </td>
                            <td class="py-3 px-4">{{ $product->name }}</td>
                            <td class="py-3 px-4">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-right">{{ number_format($product->base_price) }} VNĐ</td>
                            <td class="py-3 px-4 text-center">
                                <span
                                    class="stock-badge in-stock">{{ $product->branchStocks->sum('stock_quantity') ?? 0 }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span
                                    class="product-status selling">{{ $product->status == 'selling' ? 'Đang bán' : 'Ngừng bán' }}</span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('branch.products.show', $product->slug) }}" class="btn btn-outline btn-sm" title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>

                                    <a class="btn btn-outline btn-sm" title="Chỉnh sửa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                    </a>
  
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

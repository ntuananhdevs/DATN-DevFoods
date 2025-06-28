@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Dashboard Chi nhánh')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        📍 Dashboard chi nhánh: <span class="text-primary">{{ $branch->name ?? 'N/A' }}</span>
    </h2>

    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition">
            <div class="text-sm text-gray-500 mb-1">💰 Doanh thu</div>
            <div class="text-3xl font-extrabold text-green-600">{{ number_format($totalRevenue) }} VNĐ</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition">
            <div class="text-sm text-gray-500 mb-1">📦 Đơn hàng</div>
            <div class="text-3xl font-extrabold text-blue-500">{{ $orderCount }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition">
            <div class="text-sm text-gray-500 mb-1">🛒 Sản phẩm</div>
            <div class="text-3xl font-extrabold text-purple-500">{{ $productCount }}</div>
        </div>
    </div>

    <!-- Biểu đồ và top sản phẩm -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Doanh thu theo danh mục -->
        <div class="bg-white shadow-md rounded-2xl p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">📊 Doanh thu theo danh mục</h3>
            <p class="text-sm text-gray-500 mb-4">Phân bổ doanh thu theo danh mục sản phẩm</p>
            <div class="flex items-center justify-center bg-gray-50 rounded-lg min-h-[220px]">
                <span class="text-gray-400">[Biểu đồ Placeholder]</span>
            </div>
        </div>

        <!-- Top sản phẩm bán chạy -->
        <div class="bg-white shadow-md rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">🔥 Top sản phẩm bán chạy</h3>
                <p class="text-sm text-gray-500 mb-4">Các sản phẩm có doanh số cao nhất</p>
            </div>
            <ol class="divide-y divide-gray-100 mb-4">
                @foreach ($topProducts as $i => $product)
                    <li class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold">
                                {{ $i + 1 }}
                            </span>
                            <div>
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $product->category_name ?? '—' }}
                                    @if (isset($product->price))
                                        • {{ number_format($product->price) }}đ
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right min-w-[100px]">
                            <div class="font-semibold text-gray-700">{{ $product->sold }} đã bán</div>
                            <div class="text-xs text-gray-400">
                                {{ isset($product->revenue) ? number_format($product->revenue / 1_000_000, 2) . ' triệu' : '' }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
            <a href="#"
                class="block w-full text-center border border-gray-300 rounded-lg py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Xem tất cả sản phẩm &rarr;
            </a>
        </div>
    </div>
@endsection

@extends('layouts.driver.masterLayout') {{-- Đảm bảo layout này đã tải Tailwind CSS --}}

@section('title', 'Danh sách Đơn hàng')

@section('content')
    <div class="pt-4 p-4">
        {{-- Form tìm kiếm --}}
        <form action="{{ route('driver.orders.index') }}" method="GET" class="mb-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Tìm kiếm theo ID, địa chỉ, khách hàng..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            {{-- Giữ nguyên input hidden cho status để khi tìm kiếm không mất tab đang chọn --}}
            <input type="hidden" name="tab" value="{{ $currentTab }}">
        </form>

        {{-- Tabs trạng thái đơn hàng --}}
        <div class="flex space-x-2 mb-4 overflow-x-auto pb-2 custom-scrollbar">
            @foreach ($tabConfig as $key => $config)
                <a href="{{ route('driver.orders.index', ['tab' => $key, 'search' => request('search')]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition 
                    {{ $currentTab == $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $config['label'] }} ({{ $config['count'] }})
                </a>
            @endforeach
        </div>

        {{-- Danh sách đơn hàng --}}
        <div class="space-y-3">
            @forelse ($orders as $order)
                {{-- Sử dụng các accessor từ Order model --}}
                <a href="{{ route('driver.orders.show', $order->id) }}"
                    class="block bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden cursor-pointer hover:shadow-md transition-shadow">

                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    {{-- Badge trạng thái sử dụng accessor --}}
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-hidden focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-white"
                                        style="background-color: {{ $order->status_color }};">
                                        <i class="{{ $order->status_icon }} w-3 h-3 mr-1"></i> {{-- Font Awesome Icon --}}
                                        {{ $order->status_text }}
                                    </div>
                                    <span class="text-sm text-gray-500">#{{ $order->order_code }}</span>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-user w-4 h-4 text-gray-500">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <span class="font-medium">{{ $order->customer->name ?? 'Khách vãng lai' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-phone w-4 h-4 text-gray-500">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                            </path>
                                        </svg>
                                        <span
                                            class="text-sm text-gray-600">{{ $order->customer->phone ?? 'Không có SĐT' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-map-pin w-4 h-4 text-gray-500">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        <span
                                            class="text-sm text-gray-600 line-clamp-1">{{ $order->delivery_address }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clock w-4 h-4 text-gray-500">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        @if ($order->status === 'delivered' || $order->status === 'item_received')
                                            <span class="text-sm text-gray-600">
                                                Đã giao lúc:
                                                {{ \Carbon\Carbon::parse($order->actual_delivery_time)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-600">
                                                Dự kiến:
                                                {{ \Carbon\Carbon::parse($order->estimated_delivery_time)->format('H:i, d/m') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}&nbsp;₫</div>
                                <div class="text-xs text-gray-500">Phí ship:
                                    {{ number_format($order->delivery_fee, 0, ',', '.') }}&nbsp;₫</div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center text-gray-500 py-16">
                    <i class="fas fa-box-open text-5xl mb-4 text-gray-300"></i>
                    <p class="font-medium">Không có đơn hàng nào</p>
                    <p class="text-sm">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
                </div>
            @endforelse
        </div>

        {{-- Phân trang --}}
        @if ($orders->hasPages())
            <div class="mt-6">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        /* Optional: Custom scrollbar for tab navigation */
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

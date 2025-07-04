@extends('layouts.driver.masterLayout')

@section('title', 'Danh sách Đơn hàng')

@section('content')
<div class="pt-4 p-4">
    {{-- Form tìm kiếm và các tab trạng thái giữ nguyên, chúng đã responsive khá tốt --}}
    <form action="{{ route('driver.orders.index') }}" method="GET" class="mb-4">
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo ID, địa chỉ..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
        <input type="hidden" name="status" value="{{ $initialStatus }}">
    </form>

    <div class="flex space-x-2 mb-4 overflow-x-auto pb-2">
        @php
            // Lấy text từ accessor, không cần định nghĩa lại ở đây
        @endphp
        @foreach($tabStatuses as $status)
            <a href="{{ route('driver.orders.index', ['status' => $status, 'search' => request('search')]) }}" class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition {{ $initialStatus == $status ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{-- Lấy text trạng thái trực tiếp từ hàm static hoặc định nghĩa sẵn --}}
                {{ \App\Models\Order::getStatusText($status) }}
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $initialStatus == $status ? 'bg-white text-blue-600' : 'bg-gray-200 text-gray-600' }}">{{ $statusCounts[$status] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    {{-- CẬP NHẬT: Danh sách đơn hàng sử dụng accessor và giao diện responsive --}}
    <div class="space-y-3">
        @forelse($orders as $order)
        <a href="{{ route('driver.orders.show', $order->id) }}" class="flex items-center space-x-4 bg-white p-3 rounded-lg shadow-sm hover:shadow-md hover:ring-2 hover:ring-blue-500 transition-all duration-200">
            
            {{-- Phần icon, sử dụng status_color và status_icon từ accessor --}}
            <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center text-white text-xl" style="background-color: {{ $order->status_color }};">
                <i class="{{ $order->status_icon }}"></i>
            </div>

            {{-- Phần thông tin chính --}}
            <div class="flex-grow min-w-0">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-800">Đơn #{{ $order->id }}</span>
                    <span class="text-sm font-bold text-green-600">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                </div>
                {{-- Địa chỉ được rút gọn tự động --}}
                <p class="text-sm text-gray-600 truncate">
                    {{ $order->delivery_address }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{-- Dùng accessor status_text --}}
                    {{ $order->status_text }}
                </p>
            </div>

            <div class="flex-shrink-0 text-gray-300">
                <i class="fas fa-chevron-right"></i>
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

    @if ($orders->hasPages())
    <div class="mt-6">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection


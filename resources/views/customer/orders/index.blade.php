@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Lịch sử đơn hàng')

@section('content')
@section('styles')
    <style>
        .pagination-container {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding-bottom: 1rem;
        }

        .pagination-item {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .pagination-item:not(.active):hover {
            background-color: #F3F4F6;
        }

        .pagination-item.active {
            background-color: #F97316;
            color: white;
        }

        .pagination-item.disabled {
            color: #9CA3AF;
            cursor: not-allowed;
        }
    </style>
@endsection
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <div class="mb-6">
            <a href="{{ route('customer.profile') }}" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium">
                <i class="fas fa-arrow-left mr-2 h-4 w-4"></i>
                Quay lại trang cá nhân
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold">Lịch sử đơn hàng</h1>
            </div>
            
            <div class="p-6">
                @if($orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div class="border rounded-lg p-4 transition-shadow hover:shadow-md">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">#{{ $order->order_code ?? $order->id }}</h4>
                                        <p class="text-sm text-gray-500">{{ $order->order_date->format('H:i') }} - {{ $order->order_date->isToday() ? 'Hôm nay' : $order->order_date->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full capitalize" style="background-color: {{ $order->status_color['bg'] ?? '#f3f4f6' }}; color: {{ $order->status_color['text'] ?? '#374151' }};">
                                        {{ $order->status_text }}
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500 mb-1 font-medium">{{ $order->branch->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-700">
                                        {{ $order->orderItems->map(fn($item) => (optional(optional($item->productVariant)->product)->name ?? optional($item->combo)->name ?? 'Sản phẩm') . ' x' . $item->quantity)->implode(', ') }}
                                    </p>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-lg text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('customer.orders.show', $order) }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">
                                            Chi tiết
                                        </a>
                                        {{-- ====== NEW BUTTON LOGIC ====== --}}
                                        @if($order->status == 'awaiting_confirmation')
                                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST" class="cancel-order-form">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">Hủy đơn</button>
                                            </form>

                                        {{-- Case 2: Order has been delivered by the driver --}}
                                        @elseif($order->status == 'delivered')
                                            <a href="#" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200">Chưa nhận được hàng</a>
                                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="item_received">
                                                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-orange-500 hover:bg-orange-600">Xác nhận đã nhận hàng</button>
                                            </form>

                                        {{-- Case 3: Customer has confirmed they received the item --}}
                                        @elseif($order->status == 'item_received')
                                            <a href="#" class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                                <i class="fas fa-star mr-2"></i>Đánh giá
                                            </a>
                                        @endif
                                        {{-- ====== END NEW BUTTON LOGIC ====== --}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-12">
                        <p>Bạn chưa có đơn hàng nào.</p>
                    </div>
                @endif
            </div>
            
            <div class="pagination-container">
                @if ($orders->hasPages())
                    {{-- Previous Page Link --}}
                    @if ($orders->onFirstPage())
                        <span class="pagination-item disabled">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="pagination-item">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <span class="pagination-item active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="pagination-item">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="pagination-item disabled">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Lắng nghe sự kiện submit trên TẤT CẢ các form hủy đơn
    document.querySelectorAll('.cancel-order-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            // Ngăn form submit ngay lập tức
            event.preventDefault();

            Swal.fire({
                title: 'Bạn chắc chắn muốn hủy?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý hủy',
                cancelButtonText: 'Không'
            }).then((result) => {
                // Nếu người dùng bấm "Đồng ý", thì submit form
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
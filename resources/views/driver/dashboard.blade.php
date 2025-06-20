@extends('layouts.driver.masterLayout')

@section('title', 'Dashboard Tài xế')

@section('content')
<div class="pt-4 p-4 space-y-4">
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                    {{-- DYNAMIC AVATAR --}}
                    @if($driver->avatar)
                        <img src="{{ Storage::disk('s3')->url($driver->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-user text-gray-600"></i>
                    @endif
                </div>
                <div>
                    {{-- DYNAMIC DRIVER NAME --}}
                    <h2 class="font-semibold">Xin chào, {{ $driver->full_name }}</h2>
                    <div class="flex items-center space-x-2">
                        {{-- DYNAMIC STATUS TEXT --}}
                        <span class="driver-status-text text-sm text-gray-500">{{ $driver->is_available ? 'Bạn đang Online' : 'Bạn đang Offline' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell animate-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
                    {{-- @if($unreadNotificationsCount > 0)
                        <span class="absolute -top-2 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">{{ $unreadNotificationsCount }}</span>
                    @endif --}}
                </a>
                <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div><h3 class="font-medium">Trạng thái làm việc</h3><p class="driver-status-text text-sm text-gray-500">{{ $driver->is_available ? 'Bạn đang Online' : 'Bạn đang Offline' }}</p></div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="statusToggle" @if($driver->is_available) checked @endif>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            </label>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2"><h3 class="font-medium">Thu nhập</h3><a href="{{ route('driver.earnings') }}" class="text-blue-600 text-sm">Chi tiết</a></div>
        <div id="period-buttons" class="flex justify-between space-x-4 mb-4">
            <button data-period="today" class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-medium">Hôm nay</button>
            <button data-period="week" class="px-3 py-1 text-gray-500 text-sm">Tuần này</button>
            <button data-period="month" class="px-3 py-1 text-gray-500 text-sm">Tháng này</button>
        </div>
        <div class="text-center">
            <div id="earnings-value" class="text-3xl font-bold text-green-600">{{ number_format($totalEarnedToday, 0, ',', '.') }} đ</div>
            <div id="earnings-count" class="text-sm text-gray-500">{{ $ordersDeliveredToday->count() }} đơn đã giao</div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3"><h3 class="font-medium">Đơn hàng đang xử lý</h3><a href="{{ route('driver.orders.index') }}" class="text-blue-600 text-sm">Xem tất cả</a></div>
        <div class="space-y-3">
            @forelse($processingOrders as $order)
                <div class="flex items-center space-x-3 p-3 bg-orange-50 rounded-lg">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center"><i class="fas fa-shipping-fast text-white text-sm"></i></div>
                    <div class="flex-1">
                        <div class="font-medium">Đơn #{{ $order->id }}</div>
                        <div class="text-sm text-gray-500 truncate">{{ $order->delivery_address }}</div>
                    </div>
                    {{-- Giả sử Order model có accessor status_text --}}
                    <span class="bg-orange-600 text-white px-2 py-1 rounded text-xs">{{ $order->status_text }}</span>
                </div>
            @empty
                 <p class="text-center text-sm text-gray-500 py-3">Không có đơn hàng nào đang xử lý.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3"><h3 class="font-medium">Đơn hàng mới có sẵn</h3></div>
        <div id="available-orders-list" class="space-y-3">
             @forelse($availableOrders as $order)
                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center"><i class="fas fa-box text-white text-sm"></i></div>
                    <div class="flex-1"><div class="font-medium">Đơn #{{ $order->id }}</div><div class="text-sm text-gray-500 truncate">{{ $order->delivery_address }}</div></div>
                    <a href="{{ route('driver.orders.show', $order->id) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Nhận đơn</a>
                </div>
            @empty
                <p class="text-center text-sm text-gray-500 py-3 no-order-message">Hiện không có đơn hàng mới.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Thêm một chút hiệu ứng cho đơn hàng mới từ Pusher --}}
<style>
    @keyframes pulse-fade-in { 0% { background-color: #eff6ff; opacity: 0; } 50% { background-color: #dbeafe; opacity: 1; } 100% { background-color: #eff6ff; opacity: 1; } }
    .animate-pulse-fade-in { animation: pulse-fade-in 1.5s ease-in-out; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Logic cho bật/tắt trạng thái
    const statusToggle = document.getElementById('statusToggle');
    const statusTextElements = document.querySelectorAll('.driver-status-text'); // Sửa lại để dùng class
    if (statusToggle && statusTextElements.length > 0) {
        statusToggle.addEventListener('change', function() {
            fetch("{{ route('driver.status.toggle') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ is_available: this.checked })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusTextElements.forEach(el => {
                        el.textContent = data.is_available ? 'Bạn đang Online' : 'Bạn đang Offline';
                    });
                }
            }).catch(error => console.error('Status Toggle Error:', error));
        });
    }

    // Logic cho query thu nhập
    const periodButtons = document.querySelectorAll('#period-buttons button');
    if (periodButtons.length > 0) {
        periodButtons.forEach(button => {
            button.addEventListener('click', function() {
                periodButtons.forEach(btn => btn.classList.remove('bg-orange-100', 'text-orange-600'));
                this.classList.add('bg-orange-100', 'text-orange-600');
                const period = this.dataset.period;
                fetch(`{{ route('driver.earnings.query') }}?period=${period}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('earnings-value').textContent = data.earnings;
                        document.getElementById('earnings-count').textContent = data.order_count;
                    }).catch(error => console.error('Earnings Query Error:', error));
            });
        });
    }

    // Logic cho Pusher
    const availableOrdersList = document.getElementById('available-orders-list');
    
    if (window.Echo && availableOrdersList) {
        window.Echo.channel('available-orders')
            .listen('.new-order-event', (eventData) => { // Đặt tên biến là `eventData` cho rõ ràng
                
                // Dùng eventData ở đây
                console.log('Đã nhận được đơn hàng mới:', eventData.order);
                
                const noOrderMsg = availableOrdersList.querySelector('.no-order-message');
                if (noOrderMsg) {
                    noOrderMsg.remove();
                }

                // Dùng eventData ở đây
                const order = eventData.order;
                const orderShowUrl = `/driver/orders/${order.id}`; // Tạo URL động

                const newOrderHtml = `
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200 animate-pulse-fade-in">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-box text-white text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">Đơn #${order.id}</div>
                            <div class="text-sm text-gray-500 truncate">${order.delivery_address}</div>
                        </div>
                        <a href="${orderShowUrl}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Nhận đơn</a>
                    </div>
                `;
                
                availableOrdersList.insertAdjacentHTML('afterbegin', newOrderHtml);
            });
    }
});
</script>
@endpush
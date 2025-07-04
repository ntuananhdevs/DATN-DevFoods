@extends('layouts.driver.masterLayout')

@section('title', 'Dashboard Tài xế')

@section('content')
<div class="pt-4 p-4 space-y-4">
    {{-- Card Header --}}
    <div class="bg-white rounded-lg p-4 shadow-sm">
        {{-- CẬP NHẬT: Thêm 'flex-wrap' để các phần tử có thể xuống dòng trên màn hình siêu nhỏ --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($driver->avatar)
                        <img src="{{ Storage::disk('s3')->url($driver->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-user text-gray-600 text-xl"></i>
                    @endif
                </div>
                <div>
                    <h2 class="font-semibold text-lg">Xin chào, {{ $driver->full_name }}</h2>
                    <div class="flex items-center space-x-2">
                        <span class="driver-status-text text-sm text-gray-500">{{ $driver->is_available ? 'Bạn đang Online' : 'Bạn đang Offline' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="relative text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell animate-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
                    {{-- Ví dụ về notification badge --}}
                    {{-- <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center border-2 border-white">5</span> --}}
                </a>
                <a href="#" class="text-gray-500 hover:text-gray-800"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>
            </div>
        </div>
    </div>

    {{-- Card Trạng thái làm việc (giữ nguyên, đã ổn) --}}
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div><h3 class="font-medium">Trạng thái làm việc</h3><p class="driver-status-text text-sm text-gray-500">{{ $driver->is_available ? 'Bạn đang Online' : 'Bạn đang Offline' }}</p></div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="statusToggle" @if($driver->is_available) checked @endif>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            </label>
        </div>
    </div>

    {{-- Card Thu nhập --}}
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3"><h3 class="font-medium">Thu nhập</h3><a href="{{ route('driver.earnings') }}" class="text-blue-600 text-sm font-medium">Chi tiết</a></div>
        {{-- CẬP NHẬT RESPONSIVE: Dùng grid trên mobile, chuyển sang flex trên desktop (sm) --}}
        {{-- Mobile: Hiển thị 3 cột. Desktop: Hiển thị hàng ngang --}}
        <div id="period-buttons" class="grid grid-cols-3 gap-2 sm:flex sm:justify-between sm:space-x-4 mb-4">
            <button data-period="today" class="px-3 py-2 bg-orange-100 text-orange-600 rounded-lg text-sm font-medium transition-all">Hôm nay</button>
            <button data-period="week" class="px-3 py-2 text-gray-500 rounded-lg text-sm font-medium transition-all">Tuần này</button>
            <button data-period="month" class="px-3 py-2 text-gray-500 rounded-lg text-sm font-medium transition-all">Tháng này</button>
        </div>
        <div class="text-center">
            <div id="earnings-value" class="text-3xl font-bold text-green-600">{{ number_format($totalEarnedToday, 0, ',', '.') }} đ</div>
            <div id="earnings-count" class="text-sm text-gray-500">{{ $ordersDeliveredToday->count() }} đơn đã giao</div>
        </div>
    </div>

    {{-- Card Đơn hàng đang xử lý --}}
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3"><h3 class="font-medium">Đơn hàng đang xử lý</h3><a href="{{ route('driver.orders.index') }}" class="text-blue-600 text-sm font-medium">Xem tất cả</a></div>
        <div class="space-y-3">
            @forelse($processingOrders as $order)
                <a href="{{ route('driver.orders.show', $order->id) }}" class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-truck text-white text-lg"></i></div>
                    {{-- CẬP NHẬT: Thêm min-w-0 để truncate hoạt động tốt --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-800">Đơn #{{ $order->id }}</div>
                        <div class="text-sm text-gray-500 truncate">{{ $order->delivery_address }}</div>
                    </div>
                    {{-- CẬP NHẬT: Thêm flex-shrink-0 để badge không bị co lại --}}
                    <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0">{{ $order->status_text }}</span>
                </a>
            @empty
                 <p class="text-center text-sm text-gray-500 py-3">Không có đơn hàng nào đang xử lý.</p>
            @endforelse
        </div>
    </div>

    {{-- Card Đơn hàng mới --}}
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3"><h3 class="font-medium">Đơn hàng mới có sẵn</h3></div>
        <div id="available-orders-list" class="space-y-3">
             @forelse($availableOrders as $order)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-box text-white text-lg"></i></div>
                    {{-- CẬP NHẬT: Thêm min-w-0 để truncate hoạt động tốt --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-800">Đơn #{{ $order->id }}</div>
                        <div class="text-sm text-gray-500 truncate">{{ $order->delivery_address }}</div>
                    </div>
                    <a href="{{ route('driver.orders.show', $order->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 flex-shrink-0">Nhận đơn</a>
                </div>
            @empty
                <p class="text-center text-sm text-gray-500 py-3 no-order-message">Hiện không có đơn hàng mới.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Giữ nguyên JS, chỉ cần thay đổi CSS và HTML --}}
<style>
    @keyframes pulse-fade-in { 0% { background-color: #eff6ff; opacity: 0; } 50% { background-color: #dbeafe; opacity: 1; } 100% { background-color: #f9fafb; opacity: 1; } }
    .animate-pulse-fade-in { animation: pulse-fade-in 1.5s ease-in-out; }
</style>
<script>
// Giữ nguyên toàn bộ script của bạn, nó đã hoạt động tốt
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const statusToggle = document.getElementById('statusToggle');
    const statusTextElements = document.querySelectorAll('.driver-status-text');
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

    const periodButtons = document.querySelectorAll('#period-buttons button');
    if (periodButtons.length > 0) {
        periodButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Cập nhật lại class active
                periodButtons.forEach(btn => {
                    btn.classList.remove('bg-orange-100', 'text-orange-600');
                    btn.classList.add('text-gray-500');
                });
                this.classList.add('bg-orange-100', 'text-orange-600');
                this.classList.remove('text-gray-500');

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

    const availableOrdersList = document.getElementById('available-orders-list');
    if (window.Echo && availableOrdersList) {
        window.Echo.channel('available-orders')
            .listen('.new-order-event', (eventData) => {
                console.log('Đã nhận được đơn hàng mới:', eventData.order);
                const noOrderMsg = availableOrdersList.querySelector('.no-order-message');
                if (noOrderMsg) noOrderMsg.remove();
                
                const order = eventData.order;
                const orderShowUrl = `/driver/orders/${order.id}`;
                const newOrderHtml = `
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-blue-200 animate-pulse-fade-in">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-box text-white text-lg"></i></div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-800">Đơn #${order.id}</div>
                            <div class="text-sm text-gray-500 truncate">${order.delivery_address}</div>
                        </div>
                        <a href="${orderShowUrl}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 flex-shrink-0">Nhận đơn</a>
                    </div>
                `;
                availableOrdersList.insertAdjacentHTML('afterbegin', newOrderHtml);
            });
    }
});
</script>
@endpush
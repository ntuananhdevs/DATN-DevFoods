@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đặt Hàng Thành Công')

@section('head')
<style>
    /* Add a subtle background pattern */
    body {
        background-color: #f9fafb;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e5e7eb' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .container {
        max-width: 780px;
        margin: 0 auto;
    }
    
    .success-check-circle {
        animation: scale-in 0.5s ease-out forwards;
    }

    .success-check-icon {
        animation: draw-check 0.5s 0.2s ease-out forwards;
        stroke-dasharray: 80;
        stroke-dashoffset: 80;
    }

    @keyframes scale-in {
        from { transform: scale(0.5); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    @keyframes draw-check {
        to { stroke-dashoffset: 0; }
    }
    
    .timeline {
        position: relative;
        padding-left: 10px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 31px; /* Center align with the icon */
        top: 10px;
        bottom: 10px;
        width: 3px;
        background: #e5e7eb;
        border-radius: 2px;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 70px; /* More space for icon and text */
        margin-bottom: 2.5rem; /* More spacing */
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-icon-wrapper {
        position: absolute;
        left: 10px;
        top: -5px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: #f9fafb; /* Same as body bg */
    }

    .timeline-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.3s ease;
    }
    
    .timeline-item.completed .timeline-icon {
        background: #10b981;
        color: white;
        box-shadow: 0 0 0 4px #d1fae5;
    }
    
    .timeline-item.current .timeline-icon {
        background: #f97316;
        color: white;
        animation: pulse-orange 2s infinite;
        box-shadow: 0 0 0 4px #ffedd5;
    }
    
    .timeline-item.pending .timeline-icon {
        background: #d1d5db;
        color: #6b7280;
    }
    
    @keyframes pulse-orange {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 4px #ffedd5; }
        50% { transform: scale(1.05); box-shadow: 0 0 0 8px #ffedd5; }
    }
    
    .info-card {
        background: white;
        border-radius: 1rem; /* Softer rounding */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-4px);
    }

    /* Keep the product item subtle */
    .product-item {
        border: none;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 0;
        padding: 16px 0;
        background: transparent;
        transition: all 0.3s ease;
    }
    .product-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .product-item:first-child {
        padding-top: 0;
    }
    
    .delivery-time-card {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: white;
        border-radius: 1rem;
        padding: 24px;
        text-align: center;
        box-shadow: 0 10px 25px -3px rgba(22, 163, 74, 0.3);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-current {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .follow-order-btn {
        animation: btnPulse 2.5s infinite;
    }
    @keyframes btnPulse {
        0%,100% { box-shadow: 0 0 0 0 rgba(249,115,22,0.6); }
        50% { box-shadow: 0 0 0 10px rgba(249,115,22,0); }
    }
</style>
@endsection

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white min-h-screen py-8 flex flex-col items-center">
    <!-- Header Block -->
    <div class="w-full max-w-xl flex flex-col items-center mb-10">
        <div class="success-check-circle w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4 shadow-md">
            <svg class="w-12 h-12" fill="none" stroke="#10b981" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="success-check-icon"></path>
            </svg>
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight mb-2 text-center">Đặt Hàng Thành Công!</h1>
        <p class="text-gray-500 text-base md:text-lg text-center mb-4">Cảm ơn bạn đã tin tưởng chúng tôi. Đơn hàng của bạn đang được chuẩn bị với sự tận tâm nhất.</p>
        <!-- Mã đơn hàng + Nút -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full justify-center sm:flex-nowrap">
            <div class="flex items-center bg-white rounded-full px-4 py-2 border border-gray-200 shadow-sm whitespace-nowrap">
                <span class="text-sm text-gray-500">Mã đơn hàng:</span>
                <span id="orderCode" class="font-mono text-base font-bold text-green-700 px-2 py-1 bg-gray-50 rounded-full select-all ml-2">{{ $order->order_code }}</span>
                <button onclick="copyOrderCode()" class="ml-2 px-2 py-1 rounded bg-green-100 hover:bg-green-200 text-green-700 text-xs font-semibold transition">Copy</button>
            </div>
            <div class="flex gap-3 mt-2 sm:mt-0">
                @auth
                <a href="{{ route('customer.orders.show', $order) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-full inline-flex items-center justify-center transition-all shadow text-sm whitespace-nowrap">
                    <i class="fas fa-eye mr-2"></i>
                    Theo dõi đơn hàng
                </a>
                @endauth
                <a href="{{ route('products.index') }}" 
                   class="bg-white hover:bg-gray-50 text-gray-800 font-semibold py-2 px-6 rounded-full inline-flex items-center justify-center transition-all border border-gray-300 shadow text-sm whitespace-nowrap">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Tiếp tục mua hàng
                </a>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Left Column -->
        <div class="flex flex-col gap-6">
            <!-- Card: Delivery Time -->
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col gap-2">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 mr-3">
                        <i class="fas fa-clock text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <div class="text-base font-bold text-green-700">Thời Gian Giao Hàng</div>
                        <div class="text-xs text-gray-500">Dự kiến giao trong hôm nay</div>
                    </div>
                </div>
                <div class="bg-green-50 rounded-xl p-4 flex flex-col items-center">
                    <div class="text-xl font-bold text-green-600 mb-1">{{ $timeRange['start'] ?? '--:--' }} - {{ $timeRange['end'] ?? '--:--' }}</div>
                    <div class="text-gray-500 text-sm">{{ $timeRange['date'] ?? '' }}</div>
                    <div class="mt-2"><span class="inline-block px-3 py-1 rounded-full bg-green-200 text-green-800 text-xs font-semibold">@if($order->status == 'preparing') Đang chuẩn bị @elseif($order->status == 'delivering') Đang giao hàng @elseif($order->status == 'completed') Đã giao @else Chờ xác nhận @endif</span></div>
                </div>
            </div>
            <!-- Card: Delivery Address -->
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col gap-2">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 mr-3">
                        <i class="fas fa-map-marker-alt text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <div class="text-base font-bold text-blue-700">Địa Chỉ Giao Hàng</div>
                        <div class="text-xs text-gray-500">Thông tin người nhận</div>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-xl p-4">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-phone-alt text-blue-400 mr-2"></i>
                        <span class="text-gray-700 font-semibold text-sm">{{ $order->address->phone_number ?? $order->guest_phone }}</span>
                    </div>
                    <div class="text-gray-700 text-sm">
                        @if($order->customer_id && $order->address)
                            {{ $order->address->full_name }}<br>
                            {{ $order->address->full_address }}
                        @else
                            {{ $order->guest_name }}<br>
                            {{ $order->delivery_address }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Column -->
        <div class="flex flex-col gap-6">
            <!-- Card: Payment Details -->
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col gap-2">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 mr-3">
                        <i class="fas fa-file-invoice-dollar text-purple-600 text-lg"></i>
                    </div>
                    <div>
                        <div class="text-base font-bold text-purple-700">Chi Tiết Thanh Toán</div>
                        <div class="text-xs text-gray-500">Tổng quan đơn hàng</div>
                    </div>
                </div>
                <div class="bg-purple-50 rounded-xl p-4">
                    <div class="flex justify-between py-1">
                        <span class="text-gray-600 text-sm">Tạm tính</span>
                        <span class="font-medium text-gray-900 text-sm">{{ number_format($order->subtotal) }}đ</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-600 text-sm">Phí vận chuyển</span>
                        <span class="font-medium text-gray-900 text-sm">{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee) . 'đ' : 'Miễn phí' }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between py-1 text-green-600">
                        <span class="font-medium text-sm">Giảm giá</span>
                        <span class="font-medium text-sm">-{{ number_format($order->discount_amount) }}đ</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center py-2 mt-2 border-t border-purple-200">
                        <span class="text-base font-bold text-gray-800">Tổng cộng</span>
                        <span class="text-xl font-bold text-purple-600">{{ number_format($order->total_amount) }}đ</span>
                    </div>
                    <div class="mt-2">
                        @if($order->payment_method == 'COD')
                        <div class="flex items-center text-yellow-700 text-xs bg-yellow-100 rounded px-2 py-1">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Thanh toán khi nhận hàng (COD) <span class="ml-2">Vui lòng chuẩn bị đủ tiền khi nhận hàng</span>
                        </div>
                        @endif
                    </div>
                    <div class="mt-2">
                        <h3 class="font-medium text-gray-800 mb-1 text-xs">Phương thức thanh toán</h3>
                        <p class="text-xs text-gray-500 bg-gray-50 p-2 rounded-lg">{{ $order->payment_method_text }}</p>
                    </div>
                </div>
            </div>
            <!-- Card: Support -->
            <div class="bg-gray-900 rounded-2xl shadow p-6 flex flex-col gap-2">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-800 mr-3">
                        <i class="fas fa-headset text-white text-lg"></i>
                    </div>
                    <div>
                        <div class="text-base font-bold text-white">Cần Hỗ Trợ?</div>
                        <div class="text-xs text-gray-300">Đội ngũ hỗ trợ 24/7 luôn sẵn sàng giúp đỡ bạn</div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="flex items-center text-white mb-1">
                        <i class="fas fa-phone-alt mr-2"></i>
                        <span>Hotline: 1900 1234</span>
                    </div>
                    <div class="flex items-center text-white">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>support@fastfood.vn</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function copyOrderCode() {
    const code = document.getElementById('orderCode').innerText;
    navigator.clipboard.writeText(code);
    alert('Đã copy mã đơn hàng!');
}
</script>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create celebration confetti
    createConfetti();
    
    // Add smooth scroll to timeline
    if (window.location.hash) {
        document.querySelector(window.location.hash)?.scrollIntoView({
            behavior: 'smooth'
        });
    }
    
    function createConfetti() {
        const colors = ['#f97316', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899'];
        const confettiCount = 150;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 3 + 's';
            confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
            document.body.appendChild(confetti);
            
            // Remove after animation
            setTimeout(() => {
                confetti.remove();
            }, 6000);
        }
    }
    
    // Auto-refresh status every 30 seconds (optional)
    // setInterval(() => {
    //     fetch(`/api/orders/{{ $order->order_code }}/status`)
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.status !== '{{ $order->status }}') {
    //                 location.reload();
    //             }
    //         })
    //         .catch(console.error);
    // }, 30000);
});
</script>
@endsection
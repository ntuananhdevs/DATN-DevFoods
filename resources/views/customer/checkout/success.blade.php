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
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Header Success Message -->
        <div class="text-center mb-12">
            <div class="success-check-circle w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-16 h-16" fill="none" stroke="#10b981" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="success-check-icon"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight mb-2">Đặt Hàng Thành Công!</h1>
            <p class="text-gray-500 text-lg">Cảm ơn bạn đã tin tưởng. Đơn hàng của bạn đang được xử lý.</p>
            <div class="mt-4 inline-flex items-center bg-white rounded-full p-2 border border-gray-200 shadow-sm">
                <span class="text-sm text-gray-500 ml-2">Mã đơn hàng:</span>
                <span class="font-mono text-lg font-bold text-orange-600 ml-2 mr-2 px-3 py-1 bg-gray-50 rounded-full">{{ $order->order_code }}</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Delivery & Timeline Combined Card -->
                <div class="info-card p-6 lg:p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Estimated Time -->
                        @if($timeRange)
                        <div class="text-center md:text-left">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center justify-center md:justify-start">
                                <i class="fas fa-shipping-fast text-green-500 mr-3 text-xl"></i>
                                Giao Hàng Dự Kiến
                            </h3>
                            <div class="text-4xl font-bold text-green-600 mb-1">
                                {{ $timeRange['start'] }} - {{ $timeRange['end'] }}
                            </div>
                            <div class="text-gray-500">{{ $timeRange['date'] }}</div>
                        </div>
                        @endif
                        
                        <!-- Delivery Address -->
                        <div class="text-center md:text-left border-t md:border-t-0 md:border-l border-gray-200 pt-6 md:pt-0 md:pl-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center justify-center md:justify-start">
                                <i class="fas fa-map-marker-alt text-orange-500 mr-3 text-xl"></i>
                                Giao Tới Địa Chỉ
                            </h3>
                            @if($order->customer_id && $order->address)
                                <p class="text-gray-700 font-semibold">{{ $order->address->full_name }} - {{ $order->address->phone_number }}</p>
                                <p class="text-gray-500 text-sm">{{ $order->address->full_address }}</p>
                            @else
                                <p class="text-gray-700 font-semibold">{{ $order->guest_name }} - {{ $order->guest_phone }}</p>
                                <p class="text-gray-500 text-sm">{{ $order->delivery_address }}</p>
                            @endif
                        </div>
                    </div>
                    
                </div>

                {{-- ================= Order Items Detail (Removed) ================= --}}
                {{--
                <div class="info-card p-6 lg:p-8">
                    <h2 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-shopping-basket text-orange-500 mr-3"></i>
                        Các Món Trong Đơn
                    </h2>
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                            <div class="product-item"> ... </div>
                        @endforeach
                    </div>
                </div>
                --}}
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                
                <!-- Order Summary -->
                <div class="info-card p-6 lg:p-8 sticky top-8">
                    <h2 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-receipt text-orange-500 mr-3"></i>
                        Thanh Toán
                    </h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Tạm tính</span>
                            <span class="font-medium text-gray-900">{{ number_format($order->subtotal) }}đ</span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Phí vận chuyển</span>
                            <span class="font-medium text-gray-900">{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee) . 'đ' : 'Miễn phí' }}</span>
                        </div>
                        
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between py-2 border-b border-gray-100 text-green-600">
                            <span class="font-medium">Giảm giá</span>
                            <span class="font-medium">-{{ number_format($order->discount_amount) }}đ</span>
                        </div>
                        @endif

                        <div class="pt-4">
                            <div class="flex justify-between items-center bg-orange-50 p-3 rounded-lg">
                                <span class="text-base font-bold text-gray-800">Tổng cộng</span>
                                <span class="text-xl font-bold text-orange-600">{{ number_format($order->total_amount) }}đ</span>
                            </div>
                        </div>

                        <div class="pt-4">
                            <h3 class="font-medium text-gray-800 mb-2">Phương thức thanh toán</h3>
                            <p class="text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">{{ $order->payment_method_text }}</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="info-card p-6">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-headset text-blue-500 mr-2"></i>
                        Cần hỗ trợ?
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Liên hệ với chúng tôi nếu bạn cần hỗ trợ về đơn hàng.</p>
                    <div class="space-y-2 text-sm">
                        <a href="tel:19001234" class="flex items-center text-blue-600 hover:underline">
                            <i class="fas fa-phone w-4 mr-2"></i>
                            <span>Hotline: 1900 1234</span>
                        </a>
                        <a href="mailto:support@fastfood.vn" class="flex items-center text-blue-600 hover:underline">
                            <i class="fas fa-envelope w-4 mr-2"></i>
                            <span>support@fastfood.vn</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-12 text-center">
            <div class="inline-flex flex-col sm:flex-row gap-4">
                @auth
                <a href="{{ route('customer.orders.show', $order) }}" 
                   class="follow-order-btn bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white font-semibold py-3 px-10 rounded-full inline-flex items-center justify-center transition-all transform hover:scale-105 shadow-xl">
                    <i class="fas fa-eye mr-2"></i>
                    Theo dõi đơn hàng
                </a>
                @endauth
                
                <a href="{{ route('products.index') }}" 
                   class="bg-white hover:bg-gray-50 text-gray-800 font-semibold py-3 px-8 rounded-lg inline-flex items-center justify-center transition-all border border-gray-300 transform hover:scale-105">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Tiếp tục mua hàng
                </a>
            </div>
        </div>
    </div>
</div>
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
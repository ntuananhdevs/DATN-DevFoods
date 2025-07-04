@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đặt Hàng Thành Công')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
    }
    
    .step {
        @apply relative flex flex-col items-center;
    }
    
    .step::before {
        content: '';
        position: absolute;
        top: 30px;
        left: -50%;
        width: 100%;
        height: 2px;
        background-color: #E5E7EB;
        z-index: -1;
    }
    
    .step:first-child::before {
        display: none;
    }
    
    .step.active .step-number {
        @apply bg-orange-500 text-white border-orange-500;
    }
    
    .step.active ~ .step::before {
        @apply bg-gray-200;
    }
    
    .step-number {
        @apply flex items-center justify-center w-12 h-12 rounded-full border-2 border-orange-500 bg-white text-orange-500 font-bold;
    }
</style>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <!-- Order Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-500 text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Đặt Hàng Thành Công!</h1>
            <p class="text-gray-600 mb-4">
                Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được xác nhận và đang được xử lý.
            </p>
            <p class="font-medium">
                Mã đơn hàng: <span class="text-orange-500">{{ $order->order_number }}</span>
            </p>
        </div>
        
        <!-- Order Progress -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold mb-6">Trạng thái đơn hàng</h2>
            <div class="flex justify-between items-center mb-8">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="text-sm font-medium mt-2">Đơn hàng đã đặt</div>
                    <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i, d/m/Y') }}</div>
                </div>
                
                <div class="step {{ in_array($order->status, ['confirmed', 'processing', 'shipping', 'completed']) ? 'active' : '' }}">
                    <div class="step-number">2</div>
                    <div class="text-sm font-medium mt-2">Xác nhận</div>
                    <div class="text-xs text-gray-500">Đang xử lý</div>
                </div>
                
                <div class="step {{ in_array($order->status, ['processing', 'shipping', 'completed']) ? 'active' : '' }}">
                    <div class="step-number">3</div>
                    <div class="text-sm font-medium mt-2">Chuẩn bị</div>
                    <div class="text-xs text-gray-500">Đang xử lý</div>
                </div>
                
                <div class="step {{ in_array($order->status, ['shipping', 'completed']) ? 'active' : '' }}">
                    <div class="step-number">4</div>
                    <div class="text-sm font-medium mt-2">Giao hàng</div>
                    <div class="text-xs text-gray-500">Đang xử lý</div>
                </div>
                
                <div class="step {{ $order->status === 'completed' ? 'active' : '' }}">
                    <div class="step-number">5</div>
                    <div class="text-sm font-medium mt-2">Hoàn tất</div>
                    <div class="text-xs text-gray-500">Đang xử lý</div>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Trạng thái hiện tại:</p>
                <div class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full font-medium text-sm">
                    @switch($order->status)
                        @case('pending')
                            Chờ xác nhận
                            @break
                        @case('confirmed')
                            Đã xác nhận
                            @break
                        @case('processing')
                            Đang chuẩn bị
                            @break
                        @case('shipping')
                            Đang giao hàng
                            @break
                        @case('completed')
                            Hoàn tất
                            @break
                        @case('cancelled')
                            Đã hủy
                            @break
                        @default
                            Chờ xử lý
                    @endswitch
                </div>
            </div>
        </div>
        
        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold mb-6">Chi tiết đơn hàng</h2>
            
            <div class="space-y-4 mb-6">
                @foreach($order->orderItems as $item)
                    <div class="flex justify-between border-b pb-4">
                        <div class="flex">
                            
                            
                        </div>
                        <div class="text-right">
                            <p class="font-medium">{{ number_format($item->unit_price) }}đ</p>
                            <p class="text-sm text-gray-600">{{ number_format($item->total_price) }}đ</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="space-y-2 border-b pb-4 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tạm tính:</span>
                    <span>{{ number_format($order->subtotal) }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phí vận chuyển:</span>
                    <span>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee) . 'đ' : 'Miễn phí' }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Giảm giá:</span>
                    <span>-{{ number_format($order->discount_amount) }}đ</span>
                </div>
                @endif
            </div>
            
            <div class="flex justify-between font-bold text-lg mb-6">
                <span>Tổng cộng:</span>
                <span class="text-orange-500">{{ number_format($order->total_amount) }}đ</span>
            </div>
        </div>
        
        <!-- Shipping Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Thông tin giao hàng</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium mb-2">Địa chỉ giao hàng</h3>
                    @if($order->customer_id)
                        <!-- Thông tin khách đã đăng nhập -->
                        <p class="text-gray-600">{{ $order->customer->name }}</p>
                        <p class="text-gray-600">{{ $order->customer->phone }}</p>
                        @if($order->address_id)
                            <p class="text-gray-600">{{ $order->address->address }}, {{ $order->address->district }}, {{ $order->address->city }}</p>
                        @else
                            <p class="text-gray-600">{{ $order->delivery_address }}</p>
                        @endif
                    @else
                        <!-- Thông tin khách chưa đăng nhập -->
                        <p class="text-gray-600">{{ $order->guest_name }}</p>
                        <p class="text-gray-600">{{ $order->guest_phone }}</p>
                        <p class="text-gray-600">{{ $order->guest_address }}, {{ $order->guest_ward }}, {{ $order->guest_district }}, {{ $order->guest_city }}</p>
                    @endif
                </div>
                
                <div>
                    <h3 class="font-medium mb-2">Phương thức thanh toán</h3>
                    <p class="text-gray-600">
                        {{ $order->paymentMethodText }}
                    </p>
                </div>
            </div>
            
            @if($order->notes)
                <div class="mt-6 p-3 bg-gray-50 rounded-lg">
                    <h3 class="font-medium mb-2">Ghi chú đơn hàng:</h3>
                    <p class="text-gray-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg inline-flex items-center justify-center transition-colors">
                <i class="fas fa-home mr-2"></i>
                Quay về trang chủ
            </a>
            <a href="{{ route('products.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-6 rounded-lg inline-flex items-center justify-center transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Tiếp tục mua hàng
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hiệu ứng confetti khi trang tải xong
    createConfetti();
    
    function createConfetti() {
        const confettiCount = 200;
        const container = document.createElement('div');
        container.style.position = 'fixed';
        container.style.top = '0';
        container.style.left = '0';
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.pointerEvents = 'none';
        container.style.zIndex = '1000';
        document.body.appendChild(container);
        
        const colors = ['#f97316', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'];
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'absolute';
            confetti.style.width = Math.random() * 10 + 5 + 'px';
            confetti.style.height = Math.random() * 5 + 3 + 'px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.top = '-50px';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.opacity = Math.random() + 0.5;
            confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
            container.appendChild(confetti);
            
            // Animation
            const duration = Math.random() * 3 + 2;
            const delay = Math.random() * 2;
            
            confetti.style.animation = `fall ${duration}s ease-in ${delay}s forwards`;
            
            // Add keyframes
            const style = document.createElement('style');
            style.innerHTML = `
                @keyframes fall {
                    0% {
                        transform: translateY(0) rotate(${Math.random() * 360}deg);
                    }
                    100% {
                        transform: translateY(${window.innerHeight + 100}px) rotate(${Math.random() * 360}deg);
                    }
                }
            `;
            document.head.appendChild(style);
            
            // Remove after animation
            setTimeout(() => {
                confetti.remove();
            }, (duration + delay) * 1000);
        }
        
        // Remove container after all confetti are gone
        setTimeout(() => {
            container.remove();
        }, 6000);
    }
});
</script>
@endsection
@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đặt Hàng Thành Công')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-green-500 text-3xl"></i>
            </div>
            
            <h1 class="text-3xl font-bold mb-4">Đặt Hàng Thành Công!</h1>
            
            <p class="text-lg text-gray-600 mb-6">
                Cảm ơn bạn đã đặt hàng tại FastFood. Đơn hàng của bạn đã được xác nhận và đang được xử lý.
            </p>
            
            <div class="border-t border-b border-gray-200 py-6 mb-6">
                <div class="flex justify-between mb-2">
                    <span class="font-medium">Mã đơn hàng:</span>
                    <span class="font-bold">#FF123456</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Ngày đặt hàng:</span>
                    <span>19/05/2025</span>
                </div>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <h2 class="text-xl font-bold mb-4">Thông Tin Đơn Hàng</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Burger Gà Cay x 2</span>
                        <span>120.000₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Khoai Tây Chiên x 1</span>
                        <span>30.000₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Coca Cola x 2</span>
                        <span>40.000₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span>15.000₫</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t">
                        <span>Tổng cộng</span>
                        <span class="text-orange-500">205.000₫</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-orange-50 p-6 rounded-lg mb-6">
                <h2 class="text-xl font-bold mb-4">Thông Tin Giao Hàng</h2>
                
                <div class="text-left space-y-2">
                    <div>
                        <span class="font-medium">Người nhận:</span>
                        <span class="ml-2">Nguyễn Văn A</span>
                    </div>
                    <div>
                        <span class="font-medium">Số điện thoại:</span>
                        <span class="ml-2">0901234567</span>
                    </div>
                    <div>
                        <span class="font-medium">Địa chỉ:</span>
                        <span class="ml-2">123 Đường ABC, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</span>
                    </div>
                    <div>
                        <span class="font-medium">Phương thức thanh toán:</span>
                        <span class="ml-2">Thanh toán khi nhận hàng (COD)</span>
                    </div>
                    <div>
                        <span class="font-medium">Thời gian giao hàng dự kiến:</span>
                        <span class="ml-2">30-45 phút</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="/track-order" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-truck mr-2"></i>
                    Theo Dõi Đơn Hàng
                </a>
                <a href="/menu" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-utensils mr-2"></i>
                    Tiếp Tục Mua Hàng
                </a>
            </div>
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
@extends('layouts.customer.fullLayoutMaster')


@section('title', 'FastFood - Theo Dõi Đơn Hàng')

@section('content')
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Theo Dõi Đơn Hàng</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Kiểm tra trạng thái đơn hàng của bạn một cách dễ dàng
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <!-- Form tìm kiếm đơn hàng -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
            <h2 class="text-2xl font-bold mb-4">Tìm Kiếm Đơn Hàng</h2>
            
            <form id="track-order-form" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="order_id" class="block text-sm font-medium mb-1">Mã đơn hàng</label>
                        <input type="text" id="order_id" name="order_id" placeholder="Ví dụ: #FF123456" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-1">Số điện thoại đặt hàng</label>
                        <input type="tel" id="phone" name="phone" placeholder="Ví dụ: 0901234567" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-md transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        Tìm Kiếm
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Kết quả theo dõi đơn hàng (mặc định ẩn, hiển thị sau khi tìm kiếm) -->
        <div id="order-result" class="hidden">
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Đơn Hàng #FF123456</h2>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Đang giao hàng</span>
                </div>
                
                <div class="grid md:grid-cols-2 gap-8 mb-6">
                    <div>
                        <h3 class="text-lg font-bold mb-3">Thông Tin Đơn Hàng</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ngày đặt hàng:</span>
                                <span>19/05/2025 - 20:15</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phương thức thanh toán:</span>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tổng tiền:</span>
                                <span class="font-medium">205.000₫</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Thời gian giao hàng dự kiến:</span>
                                <span>19/05/2025 - 21:00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold mb-3">Thông Tin Giao Hàng</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Người nhận:</span>
                                <span>Nguyễn Văn A</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Số điện thoại:</span>
                                <span>0901234567</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Địa chỉ:</span>
                                <span>123 Đường ABC, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ghi chú:</span>
                                <span>Gọi trước khi giao hàng</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-bold mb-4">Trạng Thái Đơn Hàng</h3>
                    
                    <div class="space-y-6">
                        <div class="tracking-step completed">
                            <div class="flex items-center">
                                <div class="step-icon w-6 h-6 rounded-full bg-green-500 flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">Đơn hàng đã được xác nhận</h4>
                                    <p class="text-sm text-gray-500">19/05/2025 - 20:17</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tracking-step completed">
                            <div class="flex items-center">
                                <div class="step-icon w-6 h-6 rounded-full bg-green-500 flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">Đơn hàng đang được chuẩn bị</h4>
                                    <p class="text-sm text-gray-500">19/05/2025 - 20:25</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tracking-step active">
                            <div class="flex items-center">
                                <div class="step-icon w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                                    <i class="fas fa-truck text-white text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">Đơn hàng đang được giao</h4>
                                    <p class="text-sm text-gray-500">19/05/2025 - 20:40</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tracking-step pending">
                            <div class="flex items-center">
                                <div class="step-icon w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                    <i class="fas fa-home text-white text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-500">Đơn hàng đã được giao</h4>
                                    <p class="text-sm text-gray-500">Dự kiến: 19/05/2025 - 21:00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <h3 class="text-lg font-bold mb-4">Chi Tiết Đơn Hàng</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-3 text-left">Sản phẩm</th>
                                <th class="py-3 text-center">Số lượng</th>
                                <th class="py-3 text-center">Đơn giá</th>
                                <th class="py-3 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden mr-3">
                                            <img src="/placeholder.svg?height=100&width=100" alt="Burger Gà Cay" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-medium">Burger Gà Cay</h4>
                                            <p class="text-sm text-gray-500">Size: Lớn</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center">2</td>
                                <td class="py-4 text-center">60.000₫</td>
                                <td class="py-4 text-right">120.000₫</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden mr-3">
                                            <img src="/placeholder.svg?height=100&width=100" alt="Khoai Tây Chiên" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-medium">Khoai Tây Chiên</h4>
                                            <p class="text-sm text-gray-500">Size: Vừa</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center">1</td>
                                <td class="py-4 text-center">30.000₫</td>
                                <td class="py-4 text-right">30.000₫</td>
                            </tr>
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden mr-3">
                                            <img src="/placeholder.svg?height=100&width=100" alt="Coca Cola" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-medium">Coca Cola</h4>
                                            <p class="text-sm text-gray-500">Size: Lớn</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center">2</td>
                                <td class="py-4 text-center">20.000₫</td>
                                <td class="py-4 text-right">40.000₫</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-t">
                                <td colspan="3" class="py-4 text-right font-medium">Tạm tính:</td>
                                <td class="py-4 text-right">190.000₫</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-2 text-right font-medium">Phí vận chuyển:</td>
                                <td class="py-2 text-right">15.000₫</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-4 text-right font-bold text-lg">Tổng cộng:</td>
                                <td class="py-4 text-right font-bold text-lg text-orange-500">205.000₫</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold mb-4">Vị Trí Người Giao Hàng</h3>
                
                <div class="relative h-[300px] rounded-lg overflow-hidden mb-4">
                    <!-- Placeholder for map -->
                    <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                        <p class="text-gray-500">Bản đồ theo dõi người giao hàng sẽ hiển thị ở đây</p>
                    </div>
                </div>
                
                <div class="bg-orange-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full overflow-hidden mr-3">
                            <img src="/placeholder.svg?height=100&width=100" alt="Người giao hàng" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-medium">Trần Văn B</h4>
                            <p class="text-sm text-gray-500">Người giao hàng</p>
                        </div>
                        <div class="ml-auto">
                            <a href="tel:0909123456" class="inline-flex items-center justify-center w-10 h-10 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors">
                                <i class="fas fa-phone"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Thông báo không tìm thấy đơn hàng (mặc định ẩn) -->
        <div id="order-not-found" class="hidden bg-white p-8 rounded-lg shadow-sm text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
            
            <h2 class="text-2xl font-bold mb-2">Không Tìm Thấy Đơn Hàng</h2>
            
            <p class="text-gray-600 mb-6">
                Chúng tôi không thể tìm thấy đơn hàng với thông tin bạn cung cấp. Vui lòng kiểm tra lại mã đơn hàng và số điện thoại.
            </p>
            
            <button id="try-again-button" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-md transition-colors">
                Thử Lại
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trackOrderForm = document.getElementById('track-order-form');
    const orderResult = document.getElementById('order-result');
    const orderNotFound = document.getElementById('order-not-found');
    const tryAgainButton = document.getElementById('try-again-button');
    
    // Xử lý form tìm kiếm
    trackOrderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const orderId = document.getElementById('order_id').value.trim();
        const phone = document.getElementById('phone').value.trim();
        
        // Kiểm tra dữ liệu nhập
        if (!orderId && !phone) {
            alert('Vui lòng nhập mã đơn hàng hoặc số điện thoại');
            return;
        }
        
        // Giả lập tìm kiếm
        const isFound = orderId.includes('FF123456') || phone.includes('0901234567');
        
        // Hiển thị kết quả sau 1 giây (giả lập tìm kiếm)
        const submitButton = trackOrderForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang tìm kiếm...';
        
        setTimeout(function() {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
            
            if (isFound) {
                orderResult.classList.remove('hidden');
                orderNotFound.classList.add('hidden');
                
                // Cuộn đến kết quả
                orderResult.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Giả lập cập nhật vị trí người giao hàng
                simulateDeliveryTracking();
            } else {
                orderResult.classList.add('hidden');
                orderNotFound.classList.remove('hidden');
                
                // Cuộn đến thông báo
                orderNotFound.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 1000);
    });
    
    // Nút thử lại
    tryAgainButton.addEventListener('click', function() {
        orderNotFound.classList.add('hidden');
        document.getElementById('order_id').focus();
        
        // Cuộn lên form
        trackOrderForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    
    // Giả lập cập nhật vị trí người giao hàng
    function simulateDeliveryTracking() {
        // Trong thực tế, đây sẽ là một kết nối WebSocket hoặc polling để cập nhật vị trí thời gian thực
        
        // Giả lập cập nhật trạng thái đơn hàng
        const steps = document.querySelectorAll('.tracking-step');
        let currentStep = 2; // Bắt đầu từ "Đang giao hàng"
        
        // Cập nhật trạng thái sau 10 giây
        setTimeout(function() {
            if (currentStep < steps.length - 1) {
                steps[currentStep].classList.remove('active');
                steps[currentStep].classList.add('completed');
                
                currentStep++;
                
                steps[currentStep].classList.remove('pending');
                steps[currentStep].classList.add('active');
                
                // Cập nhật icon
                const icon = steps[currentStep].querySelector('.step-icon i');
                icon.className = 'fas fa-check text-white text-xs';
                
                // Cập nhật text
                const title = steps[currentStep].querySelector('h4');
                title.classList.remove('text-gray-500');
                
                const time = steps[currentStep].querySelector('p');
                time.textContent = '19/05/2025 - 20:55';
                
                // Cập nhật trạng thái đơn hàng
                const orderStatus = document.querySelector('.flex.justify-between.items-center span');
                orderStatus.textContent = 'Đã giao hàng';
                orderStatus.className = 'px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium';
            }
        }, 10000);
    }
});
</script>
@endsection
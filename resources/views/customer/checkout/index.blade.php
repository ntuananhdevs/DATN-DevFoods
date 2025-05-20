@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Thanh Toán')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
    }
</style>
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Thanh Toán</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Hoàn tất đơn hàng của bạn chỉ với vài bước đơn giản
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Thông tin thanh toán - 2 cột -->
        <div class="md:col-span-2">
            <h2 class="text-2xl font-bold mb-6">Thông Tin Thanh Toán</h2>
            
            <form id="checkout-form" class="space-y-6">
                <!-- Thông tin cá nhân -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4">Thông Tin Cá Nhân</h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="fullname" class="block text-sm font-medium mb-1">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" id="fullname" name="fullname" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="email" class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                
                <!-- Địa chỉ giao hàng -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4">Địa Chỉ Giao Hàng</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-medium mb-1">Địa chỉ <span class="text-red-500">*</span></label>
                            <input type="text" id="address" name="address" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium mb-1">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
                                <select id="city" name="city" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                    <option value="hcm">TP. Hồ Chí Minh</option>
                                    <option value="hn">Hà Nội</option>
                                    <option value="dn">Đà Nẵng</option>
                                    <option value="ct">Cần Thơ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div>
                                <label for="district" class="block text-sm font-medium mb-1">Quận/Huyện <span class="text-red-500">*</span></label>
                                <select id="district" name="district" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Chọn Quận/Huyện</option>
                                </select>
                            </div>
                            <div>
                                <label for="ward" class="block text-sm font-medium mb-1">Phường/Xã <span class="text-red-500">*</span></label>
                                <select id="ward" name="ward" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Chọn Phường/Xã</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="note" class="block text-sm font-medium mb-1">Ghi chú đơn hàng</label>
                            <textarea id="note" name="note" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Phương thức thanh toán -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4">Phương Thức Thanh Toán</h3>
                    
                    <div class="space-y-3">
                        <div class="payment-option border rounded-lg p-4 cursor-pointer hover:border-orange-500 transition-colors" data-payment-method="cod">
                            <div class="flex items-center">
                                <input type="radio" id="payment_cod" name="payment_method" value="cod" checked class="mr-2">
                                <label for="payment_cod" class="flex items-center cursor-pointer">
                                    <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                                    <span class="font-medium">Thanh toán khi nhận hàng (COD)</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="payment-option border rounded-lg p-4 cursor-pointer hover:border-orange-500 transition-colors" data-payment-method="card">
                            <div class="flex items-center">
                                <input type="radio" id="payment_card" name="payment_method" value="card" class="mr-2">
                                <label for="payment_card" class="flex items-center cursor-pointer">
                                    <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                                    <span class="font-medium">Thanh toán bằng thẻ tín dụng/ghi nợ</span>
                                </label>
                            </div>
                            
                            <div id="card-payment-form" class="mt-4 pl-6 hidden">
                                <div class="space-y-3">
                                    <div>
                                        <label for="card_number" class="block text-sm font-medium mb-1">Số thẻ</label>
                                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="card_expiry" class="block text-sm font-medium mb-1">Ngày hết hạn</label>
                                            <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        </div>
                                        <div>
                                            <label for="card_cvv" class="block text-sm font-medium mb-1">CVV</label>
                                            <input type="text" id="card_cvv" name="card_cvv" placeholder="123" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="card_name" class="block text-sm font-medium mb-1">Tên chủ thẻ</label>
                                        <input type="text" id="card_name" name="card_name" placeholder="NGUYEN VAN A" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="payment-option border rounded-lg p-4 cursor-pointer hover:border-orange-500 transition-colors" data-payment-method="momo">
                            <div class="flex items-center">
                                <input type="radio" id="payment_momo" name="payment_method" value="momo" class="mr-2">
                                <label for="payment_momo" class="flex items-center cursor-pointer">
                                    <i class="fas fa-wallet text-pink-500 mr-2"></i>
                                    <span class="font-medium">Thanh toán qua Ví MoMo</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="payment-option border rounded-lg p-4 cursor-pointer hover:border-orange-500 transition-colors" data-payment-method="bank">
                            <div class="flex items-center">
                                <input type="radio" id="payment_bank" name="payment_method" value="bank" class="mr-2">
                                <label for="payment_bank" class="flex items-center cursor-pointer">
                                    <i class="fas fa-university text-gray-700 mr-2"></i>
                                    <span class="font-medium">Chuyển khoản ngân hàng</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Tóm tắt đơn hàng - 1 cột -->
        <div>
            <div class="bg-white p-6 rounded-lg shadow-sm sticky top-4">
                <h2 class="text-2xl font-bold mb-6">Đơn Hàng Của Bạn</h2>
                
                <div class="border-b pb-4 mb-4">
                    <div class="flex justify-between font-medium mb-2">
                        <span>Sản phẩm</span>
                        <span>Tạm tính</span>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <div>
                                <span class="block">Burger Gà Cay x 2</span>
                                <span class="text-sm text-gray-500">Size: Lớn</span>
                            </div>
                            <span>120.000₫</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <div>
                                <span class="block">Khoai Tây Chiên x 1</span>
                                <span class="text-sm text-gray-500">Size: Vừa</span>
                            </div>
                            <span>30.000₫</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <div>
                                <span class="block">Coca Cola x 2</span>
                                <span class="text-sm text-gray-500">Size: Lớn</span>
                            </div>
                            <span>40.000₫</span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2 border-b pb-4 mb-4">
                    <div class="flex justify-between">
                        <span>Tạm tính</span>
                        <span>190.000₫</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span>15.000₫</span>
                    </div>
                </div>
                
                <div class="flex justify-between font-bold text-lg mb-6">
                    <span>Tổng cộng</span>
                    <span class="text-orange-500">205.000₫</span>
                </div>
                
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" placeholder="Mã giảm giá" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <button class="absolute right-0 top-0 h-full px-4 bg-gray-100 text-gray-600 rounded-r-md hover:bg-gray-200 transition-colors">
                            Áp dụng
                        </button>
                    </div>
                </div>
                
                <button type="submit" form="checkout-form" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-md transition-colors flex items-center justify-center">
                    <i class="fas fa-lock mr-2"></i>
                    Đặt Hàng
                </button>
                
                <div class="mt-4 text-center text-sm text-gray-500">
                    <p>Bằng cách đặt hàng, bạn đồng ý với</p>
                    <a href="/terms" class="text-orange-500 hover:underline">Điều khoản dịch vụ</a> và
                    <a href="/privacy" class="text-orange-500 hover:underline">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn phương thức thanh toán
    const paymentOptions = document.querySelectorAll('.payment-option');
    const cardPaymentForm = document.getElementById('card-payment-form');
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Cập nhật radio button
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Cập nhật giao diện
            paymentOptions.forEach(opt => {
                opt.classList.remove('border-orange-500', 'bg-orange-50');
            });
            this.classList.add('border-orange-500', 'bg-orange-50');
            
            // Hiển thị/ẩn form thẻ
            const paymentMethod = this.getAttribute('data-payment-method');
            if (paymentMethod === 'card') {
                cardPaymentForm.classList.remove('hidden');
            } else {
                cardPaymentForm.classList.add('hidden');
            }
        });
    });
    
    // Xử lý chọn địa chỉ
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    
    // Dữ liệu mẫu
    const locationData = {
        'hcm': {
            name: 'TP. Hồ Chí Minh',
            districts: {
                'q1': {
                    name: 'Quận 1',
                    wards: ['Phường Bến Nghé', 'Phường Bến Thành', 'Phường Đa Kao']
                },
                'q3': {
                    name: 'Quận 3',
                    wards: ['Phường 1', 'Phường 2', 'Phường 3']
                },
                'tb': {
                    name: 'Quận Tân Bình',
                    wards: ['Phường 1', 'Phường 2', 'Phường 3']
                }
            }
        },
        'hn': {
            name: 'Hà Nội',
            districts: {
                'hbt': {
                    name: 'Quận Hai Bà Trưng',
                    wards: ['Phường Bách Khoa', 'Phường Bạch Mai', 'Phường Đồng Tâm']
                },
                'cg': {
                    name: 'Quận Cầu Giấy',
                    wards: ['Phường Dịch Vọng', 'Phường Mai Dịch', 'Phường Nghĩa Đô']
                }
            }
        }
    };
    
    // Cập nhật quận/huyện khi chọn tỉnh/thành phố
    citySelect.addEventListener('change', function() {
        const cityValue = this.value;
        
        // Xóa các option cũ
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        
        if (cityValue && locationData[cityValue]) {
            const districts = locationData[cityValue].districts;
            
            for (const [key, district] of Object.entries(districts)) {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = district.name;
                districtSelect.appendChild(option);
            }
        }
    });
    
    // Cập nhật phường/xã khi chọn quận/huyện
    districtSelect.addEventListener('change', function() {
        const cityValue = citySelect.value;
        const districtValue = this.value;
        
        // Xóa các option cũ
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        
        if (cityValue && districtValue && locationData[cityValue] && locationData[cityValue].districts[districtValue]) {
            const wards = locationData[cityValue].districts[districtValue].wards;
            
            wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.toLowerCase().replace(/\s+/g, '_');
                option.textContent = ward;
                wardSelect.appendChild(option);
            });
        }
    });
    
    // Xử lý form thanh toán
    const checkoutForm = document.getElementById('checkout-form');
    
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Kiểm tra form
        let isValid = true;
        const requiredFields = checkoutForm.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                
                // Thêm thông báo lỗi
                const errorMessage = field.parentElement.querySelector('.error-message');
                if (!errorMessage) {
                    const message = document.createElement('p');
                    message.className = 'error-message text-red-500 text-sm mt-1';
                    message.textContent = 'Vui lòng điền thông tin này';
                    field.parentElement.appendChild(message);
                }
            } else {
                field.classList.remove('border-red-500');
                
                // Xóa thông báo lỗi nếu có
                const errorMessage = field.parentElement.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        });
        
        if (isValid) {
            // Hiển thị trạng thái đang xử lý
            const submitButton = checkoutForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
            
            // Giả lập gửi form
            setTimeout(function() {
                window.location.href = '/order-success';
            }, 2000);
        }
    });
});
</script>
@endsection
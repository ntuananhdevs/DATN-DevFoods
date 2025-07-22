@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Liên Hệ')

@section('content')
{{-- <div class="bg-gradient-to-r from-orange-500 to-red-500 py-12 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Liên Hệ Với Chúng Tôi</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi hoặc
            góp ý nào.
        </p>
    </div>
</div> --}}


    @php
        $contactsBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('contacts');
    @endphp
    @include('components.banner', ['banners' => $contactsBanner])

    <div class="max-w-[1240px] mx-auto w-full">

<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-3 gap-8 mb-12">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="flex justify-center">
                <i class="fas fa-phone text-orange-500 text-3xl mb-4"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Điện Thoại</h3>
            <p class="font-medium mb-1">1900 1234</p>
            <p class="text-gray-500 text-sm">Thứ 2 - Chủ Nhật: 7:00 - 22:00</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="flex justify-center">
                <i class="fas fa-envelope text-orange-500 text-3xl mb-4"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Email</h3>
            <p class="font-medium mb-1">info@fastfood.com</p>
            <p class="text-gray-500 text-sm">Chúng tôi sẽ phản hồi trong vòng 24 giờ</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="flex justify-center">
                <i class="fas fa-map-marker-alt text-orange-500 text-3xl mb-4"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Địa Chỉ</h3>
            <p class="font-medium mb-1">123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
            <p class="text-gray-500 text-sm">Văn phòng chính</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-12 mb-12">
        <div>
            <h2 class="text-2xl font-bold mb-4">Gửi Tin Nhắn Cho Chúng Tôi</h2>
            <hr class="border-t border-gray-200 mb-6">

            <form class="space-y-4" id="contact-form">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">
                            Họ và tên
                        </label>
                        <input type="text" id="name" placeholder="Nhập họ và tên" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1">
                            Email
                        </label>
                        <input type="email" id="email" placeholder="Nhập email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium mb-1">
                        Số điện thoại
                    </label>
                    <input type="tel" id="phone" placeholder="Nhập số điện thoại" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium mb-1">
                        Chủ đề
                    </label>
                    <input type="text" id="subject" placeholder="Nhập chủ đề" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium mb-1">
                        Tin nhắn
                    </label>
                    <textarea id="message" placeholder="Nhập tin nhắn" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Gửi Tin Nhắn
                </button>
            </form>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">Giờ Mở Cửa</h2>
            <hr class="border-t border-gray-200 mb-6">

            <div class="bg-orange-50 p-6 rounded-lg mb-8">
                <div class="flex items-center mb-4">
                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                    <h3 class="font-bold">Giờ Phục Vụ</h3>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Thứ 2 - Thứ 6</span>
                        <span class="font-medium">7:00 - 22:00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Thứ 7</span>
                        <span class="font-medium">8:00 - 23:00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Chủ Nhật</span>
                        <span class="font-medium">8:00 - 22:00</span>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Vị Trí Cửa Hàng</h2>
            <hr class="border-t border-gray-200 mb-6">

            <div class="relative h-[300px] rounded-lg overflow-hidden">
                <!-- Placeholder for map -->
                <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                    <p class="text-gray-500">Bản đồ Google Maps sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-orange-50 p-8 rounded-xl">
        <h2 class="text-2xl font-bold mb-6 text-center">Câu Hỏi Thường Gặp</h2>

        <div class="space-y-6 max-w-3xl mx-auto">
            <div>
                <h3 class="font-bold text-lg mb-2">Làm thế nào để đặt hàng trực tuyến?</h3>
                <p class="text-gray-600">
                    Bạn có thể đặt hàng trực tuyến thông qua website hoặc ứng dụng di động của chúng tôi. Chỉ cần chọn các món ăn bạn muốn, thêm vào giỏ hàng và tiến hành thanh toán.
                </p>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-2">Phí giao hàng là bao nhiêu?</h3>
                <p class="text-gray-600">
                    Phí giao hàng phụ thuộc vào khoảng cách từ cửa hàng đến địa điểm giao hàng. Đơn hàng trên 100.000đ sẽ được miễn phí giao hàng trong bán kính 5km.
                </p>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-2">Tôi có thể thay đổi hoặc hủy đơn hàng không?</h3>
                <p class="text-gray-600">
                    Bạn có thể thay đổi hoặc hủy đơn hàng trong vòng 5 phút sau khi đặt hàng bằng cách gọi đến số hotline 1900 1234. Sau thời gian này, chúng tôi không thể đảm bảo việc thay đổi hoặc hủy đơn hàng.
                </p>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-2">FastFood có cung cấp dịch vụ đặt tiệc không?</h3>
                <p class="text-gray-600">
                    Có, chúng tôi cung cấp dịch vụ đặt tiệc cho các sự kiện, sinh nhật, họp mặt với nhiều gói ưu đãi hấp dẫn. Vui lòng liên hệ trực tiếp với chúng tôi để được tư vấn chi tiết.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" id="success-modal">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-500 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Gửi Tin Nhắn Thành Công!</h2>
            <p class="text-gray-600 mb-6">
                Cảm ơn bạn đã liên hệ với chúng tôi. Chúng tôi sẽ phản hồi trong thời gian sớm nhất.
            </p>
            <button id="close-modal" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-md font-medium transition-colors">
                Đóng
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const successModal = document.getElementById('success-modal');
    const closeModalButton = document.getElementById('close-modal');
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        
        if (!name || !email || !phone || !subject || !message) {
            showToast('Vui lòng điền đầy đủ thông tin');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showToast('Email không hợp lệ');
            return;
        }
        
        // Phone validation
        const phoneRegex = /^[0-9]{10,11}$/;
        if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
            showToast('Số điện thoại không hợp lệ');
            return;
        }
        
        // Show success modal
        successModal.classList.remove('hidden');
        
        // Reset form
        contactForm.reset();
    });
    
    // Close modal
    closeModalButton.addEventListener('click', function() {
        successModal.classList.add('hidden');
    });
    
    // Close modal when clicking outside
    successModal.addEventListener('click', function(e) {
        if (e.target === successModal) {
            successModal.classList.add('hidden');
        }
    });
    
    // Simple toast notification function
    function showToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        
        // Add to DOM
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Hide and remove toast after 3 seconds
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});
</script>
@endsection
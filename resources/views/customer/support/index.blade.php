@extends('layouts.customer.fullLayoutMaster')
@section('title', 'FastFood - Trung Tâm Hỗ Trợ')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
    @php
        $supportBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('supports');
    @endphp
    @include('components.banner', ['banners' => $supportBanner])

<div class="container mx-auto px-4 py-12">
    <!-- Search Box -->
    <div class=" mx-auto mb-12">
        <div class="relative">
            <input type="text" id="support-search" placeholder="Tìm kiếm câu trả lời nhanh..." class="w-full px-6 py-4 pl-14 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 text-lg">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <i class="fas fa-search text-orange-500 text-xl"></i>
            </div>
            <button class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-full transition-colors">
                Tìm kiếm
            </button>
        </div>
    </div>

    <!-- Support Options -->
    <div class="grid md:grid-cols-3 gap-8 mb-16">
        <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition-transform hover:scale-105">
            <div class="h-3 bg-orange-500"></div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-orange-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Chat Trực Tuyến</h3>
                <p class="text-gray-600 mb-4">
                    Trò chuyện trực tiếp với đội ngũ hỗ trợ của chúng tôi để được giải đáp nhanh chóng.
                </p>
                <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Bắt đầu chat
                </button>
                <p class="text-sm text-gray-500 mt-3">
                    Thời gian hoạt động: 7:00 - 22:00
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition-transform hover:scale-105">
            <div class="h-3 bg-orange-500"></div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-ticket-alt text-orange-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Gửi Yêu Cầu Hỗ Trợ</h3>
                <p class="text-gray-600 mb-4">
                    Tạo yêu cầu hỗ trợ và nhận phản hồi qua email trong vòng 24 giờ.
                </p>
                <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Tạo yêu cầu
                </button>
                <p class="text-sm text-gray-500 mt-3">
                    Thời gian phản hồi: 24 giờ
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition-transform hover:scale-105">
            <div class="h-3 bg-orange-500"></div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone-alt text-orange-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Gọi Điện Thoại</h3>
                <p class="text-gray-600 mb-4">
                    Liên hệ trực tiếp với đội ngũ chăm sóc khách hàng của chúng tôi.
                </p>
                <a href="tel:19001234" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors">
                    1900 1234
                </a>
                <p class="text-sm text-gray-500 mt-3">
                    Thời gian hoạt động: 7:00 - 22:00
                </p>
            </div>
        </div>
    </div>

    <!-- Popular Topics -->
    <div class="mb-16">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2">Chủ Đề Phổ Biến</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Tìm câu trả lời nhanh chóng cho những vấn đề thường gặp
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="/faq#order" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-shopping-cart text-orange-500"></i>
                    </div>
                    <h3 class="font-bold text-lg">Đặt Hàng & Thanh Toán</h3>
                </div>
                <p class="text-gray-600 mb-3">
                    Thông tin về cách đặt hàng, phương thức thanh toán và xử lý đơn hàng.
                </p>
                <span class="text-orange-500 font-medium flex items-center">
                    Xem thêm <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <a href="/faq#delivery" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-truck text-orange-500"></i>
                    </div>
                    <h3 class="font-bold text-lg">Giao Hàng</h3>
                </div>
                <p class="text-gray-600 mb-3">
                    Thông tin về phí giao hàng, thời gian giao hàng và theo dõi đơn hàng.
                </p>
                <span class="text-orange-500 font-medium flex items-center">
                    Xem thêm <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <a href="/faq#account" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-orange-500"></i>
                    </div>
                    <h3 class="font-bold text-lg">Tài Khoản & Ưu Đãi</h3>
                </div>
                <p class="text-gray-600 mb-3">
                    Thông tin về tài khoản, chương trình thành viên và các ưu đãi.
                </p>
                <span class="text-orange-500 font-medium flex items-center">
                    Xem thêm <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <a href="/faq#product" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-utensils text-orange-500"></i>
                    </div>
                    <h3 class="font-bold text-lg">Sản Phẩm & Dịch Vụ</h3>
                </div>
                <p class="text-gray-600 mb-3">
                    Thông tin về menu, thành phần, dinh dưỡng và các dịch vụ đặc biệt.
                </p>
                <span class="text-orange-500 font-medium flex items-center">
                    Xem thêm <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>
        </div>
    </div>

    <!-- Support Ticket Form -->
    <div class="bg-gray-50 rounded-2xl p-8 mb-16">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold mb-2">Gửi Yêu Cầu Hỗ Trợ</h2>
                <p class="text-gray-600">
                    Điền thông tin bên dưới và chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất
                </p>
            </div>

            <form id="support-form" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Họ và tên <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Nhập họ và tên" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" placeholder="Nhập email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" placeholder="Nhập số điện thoại" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label for="order_id" class="block text-sm font-medium mb-2">Mã đơn hàng (nếu có)</label>
                        <input type="text" id="order_id" name="order_id" placeholder="Nhập mã đơn hàng" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label for="issue_type" class="block text-sm font-medium mb-2">Loại vấn đề <span class="text-red-500">*</span></label>
                    <select id="issue_type" name="issue_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Chọn loại vấn đề</option>
                        <option value="order_issue">Vấn đề về đơn hàng</option>
                        <option value="delivery_issue">Vấn đề về giao hàng</option>
                        <option value="product_issue">Vấn đề về sản phẩm</option>
                        <option value="account_issue">Vấn đề về tài khoản</option>
                        <option value="payment_issue">Vấn đề về thanh toán</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium mb-2">Mô tả vấn đề <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" placeholder="Mô tả chi tiết vấn đề bạn đang gặp phải" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div>
                    <label for="attachment" class="block text-sm font-medium mb-2">Đính kèm tệp (nếu có)</label>
                    <div class="border border-dashed border-gray-300 rounded-lg p-4">
                        <div class="flex items-center justify-center">
                            <label for="file-upload" class="cursor-pointer bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>
                                Chọn tệp
                                <input id="file-upload" name="attachment" type="file" class="hidden">
                            </label>
                            <span id="file-name" class="ml-3 text-sm text-gray-500">Chưa có tệp nào được chọn</span>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">
                            Hỗ trợ định dạng: JPG, PNG, PDF. Kích thước tối đa: 5MB
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <input type="checkbox" id="privacy_policy" name="privacy_policy" class="mt-1">
                    <label for="privacy_policy" class="ml-2 text-sm text-gray-600">
                        Tôi đồng ý với <a href="/privacy-policy" class="text-orange-500 hover:underline">Chính sách bảo mật</a> và cho phép FastFood xử lý thông tin của tôi để giải quyết vấn đề.
                    </label>
                </div>

                <div>
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Gửi yêu cầu hỗ trợ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Self-Help Resources -->
    <div class="mb-16">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2">Tài Nguyên Hỗ Trợ</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Khám phá các tài nguyên hữu ích để tự giải quyết vấn đề
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=600" alt="Hướng dẫn sử dụng" class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Hướng Dẫn Sử Dụng</h3>
                    <p class="text-gray-600 mb-4">
                        Tìm hiểu cách sử dụng ứng dụng và website FastFood một cách hiệu quả nhất.
                    </p>
                    <a href="/guides" class="text-orange-500 font-medium hover:underline flex items-center">
                        Xem hướng dẫn <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=600" alt="Video hướng dẫn" class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Video Hướng Dẫn</h3>
                    <p class="text-gray-600 mb-4">
                        Xem các video hướng dẫn chi tiết về cách sử dụng các tính năng của FastFood.
                    </p>
                    <a href="/video-guides" class="text-orange-500 font-medium hover:underline flex items-center">
                        Xem video <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=600" alt="Câu hỏi thường gặp" class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Câu Hỏi Thường Gặp</h3>
                    <p class="text-gray-600 mb-4">
                        Tìm câu trả lời cho những câu hỏi thường gặp về FastFood và dịch vụ của chúng tôi.
                    </p>
                    <a href="/faq" class="text-orange-500 font-medium hover:underline flex items-center">
                        Xem FAQ <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Support -->
    <div class="bg-orange-50 rounded-2xl p-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Kết Nối Với Cộng Đồng FastFood</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-8">
            Tham gia cộng đồng FastFood để chia sẻ kinh nghiệm, nhận hỗ trợ từ những người dùng khác và cập nhật những tin tức mới nhất.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="#" class="bg-white hover:bg-gray-50 px-6 py-3 rounded-lg shadow-sm transition-colors flex items-center">
                <i class="fab fa-facebook text-blue-600 text-xl mr-2"></i>
                Facebook
            </a>
            <a href="#" class="bg-white hover:bg-gray-50 px-6 py-3 rounded-lg shadow-sm transition-colors flex items-center">
                <i class="fab fa-instagram text-pink-600 text-xl mr-2"></i>
                Instagram
            </a>
            <a href="#" class="bg-white hover:bg-gray-50 px-6 py-3 rounded-lg shadow-sm transition-colors flex items-center">
                <i class="fab fa-youtube text-red-600 text-xl mr-2"></i>
                YouTube
            </a>
            <a href="#" class="bg-white hover:bg-gray-50 px-6 py-3 rounded-lg shadow-sm transition-colors flex items-center">
                <i class="fab fa-tiktok text-black text-xl mr-2"></i>
                TikTok
            </a>
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
            <h2 class="text-2xl font-bold mb-2">Yêu Cầu Đã Được Gửi!</h2>
            <p class="text-gray-600 mb-6">
                Cảm ơn bạn đã liên hệ với chúng tôi. Yêu cầu hỗ trợ của bạn đã được ghi nhận. Chúng tôi sẽ phản hồi trong thời gian sớm nhất.
            </p>
            <p class="text-gray-600 mb-6">
                Mã yêu cầu của bạn: <span class="font-bold" id="ticket-id">SUP-12345</span>
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
    // File upload preview
    const fileUpload = document.getElementById('file-upload');
    const fileName = document.getElementById('file-name');
    
    fileUpload.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileName.textContent = this.files[0].name;
        } else {
            fileName.textContent = 'Chưa có tệp nào được chọn';
        }
    });
    
    // Support form submission
    const supportForm = document.getElementById('support-form');
    const successModal = document.getElementById('success-modal');
    const closeModalButton = document.getElementById('close-modal');
    const ticketId = document.getElementById('ticket-id');
    
    supportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const issueType = document.getElementById('issue_type').value;
        const message = document.getElementById('message').value;
        const privacyPolicy = document.getElementById('privacy_policy').checked;
        
        if (!name || !email || !phone || !issueType || !message || !privacyPolicy) {
            showToast('Vui lòng điền đầy đủ thông tin bắt buộc');
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
        
        // Generate random ticket ID
        const randomTicketId = 'SUP-' + Math.floor(10000 + Math.random() * 90000);
        ticketId.textContent = randomTicketId;
        
        // Show success modal
        successModal.classList.remove('hidden');
        
        // Reset form
        supportForm.reset();
        fileName.textContent = 'Chưa có tệp nào được chọn';
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
    
    // Search functionality
    const supportSearch = document.getElementById('support-search');
    
    supportSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const searchTerm = this.value.trim();
            
            if (searchTerm) {
                window.location.href = '/faq?search=' + encodeURIComponent(searchTerm);
            }
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
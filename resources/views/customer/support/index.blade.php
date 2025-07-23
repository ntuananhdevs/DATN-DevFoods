@extends('layouts.customer.fullLayoutMaster')
@section('title', 'FastFood - Trung Tâm Hỗ Trợ')

@section('content')
    @php
        $supportBanner = app('App\\Http\\Controllers\\Customer\\BannerController')->getBannersByPosition('supports');
    @endphp
    @include('components.banner', ['banners' => $supportBanner])
    <x-customer-container>

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
                <button id="startChatBtn" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors">
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
                        <option value="payment_issue">Vấn đề về thanh toán</option>
                        <option value="product_issue">Vấn đề về sản phẩm</option>
                        <option value="account_issue">Vấn đề về tài khoản</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium mb-2">Mô tả vấn đề <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" placeholder="Mô tả chi tiết vấn đề bạn gặp phải..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg transition-colors">
                        Gửi Yêu Cầu Hỗ Trợ
                    </button>
                </div>
            </form>
        </div>
    </div>
    </x-customer-container>

<!-- Include Chat Widget -->
@include('partials.customer.chat-widget')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút "Bắt đầu chat"
    const startChatBtn = document.getElementById('startChatBtn');
    if (startChatBtn) {
        startChatBtn.addEventListener('click', function() {
            // Mở chat widget
            const chatToggleBtn = document.getElementById('chatToggleBtn');
            if (chatToggleBtn) {
                chatToggleBtn.click();
            }
        });
    }
});
</script>
@endsection
@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Khuyến Mãi')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
    }
</style>
{{-- <div class="bg-gradient-to-r from-orange-500 to-red-500 py-12 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Khuyến Mãi</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Khám phá các ưu đãi hấp dẫn và tiết kiệm hơn khi thưởng thức món ăn tại FastFood
        </p>
    </div>
</div> --}}


    @php
        $promotionsBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('promotions');
    @endphp
    @include('components.banner', ['banners' => $promotionsBanner])

<div class="container mx-auto px-4 py-12">
    <!-- Khuyến mãi nổi bật -->
    <div class="mb-16">
        <div class="relative rounded-xl overflow-hidden">
            <img src="https://jollibee.com.vn/media/mageplaza/bannerslider/banner/image/z/4/z4535877031401_5788cf9ffb23f108da3cf6ea90e7e80a.jpg?height=500&width=1200" alt="Khuyến mãi đặc biệt" class="w-full h-[400px] object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/30 flex items-center">
                <div class="text-white p-8 md:p-12 max-w-2xl">
                    <span class="inline-block bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium mb-4">Ưu đãi đặc biệt</span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Giảm 50% cho đơn hàng đầu tiên</h2>
                    <p class="text-lg mb-6">
                        Đặt hàng lần đầu qua ứng dụng FastFood và nhận ngay ưu đãi giảm 50% (tối đa 100.000₫). Áp dụng cho tất cả các món ăn trong thực đơn.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/menu" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-utensils mr-2"></i>
                            Đặt hàng ngay
                        </a>
                        <button class="bg-white hover:bg-gray-100 text-gray-800 font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center justify-center copy-code" data-code="WELCOME50">
                            <i class="fas fa-copy mr-2"></i>
                            <span>WELCOME50</span>
                        </button>
                    </div>
                    <p class="text-sm mt-4">
                        * Áp dụng đến 31/12/2025. Chỉ áp dụng cho khách hàng mới.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Danh sách khuyến mãi -->
    <div class="mb-12">
        <h2 class="text-3xl font-bold mb-8 text-center">Ưu Đãi Hiện Hành</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Khuyến mãi 1 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="/placeholder.svg?height=300&width=500" alt="Combo tiết kiệm" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-orange-500 text-white px-3 py-1 text-sm font-medium">
                        Còn 7 ngày
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Combo tiết kiệm - Giảm 25%</h3>
                    <p class="text-gray-600 mb-4">
                        Tiết kiệm 25% khi đặt combo 2 burger + 1 khoai tây chiên + 2 nước ngọt. Áp dụng cho tất cả các loại burger.
                    </p>
                    <div class="flex items-center justify-between">
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center copy-code" data-code="COMBO25">
                            <i class="fas fa-copy mr-2"></i>
                            <span>COMBO25</span>
                        </button>
                        <a href="/menu" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center">
                            Đặt ngay
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Khuyến mãi 2 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="/placeholder.svg?height=300&width=500" alt="Miễn phí giao hàng" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-green-500 text-white px-3 py-1 text-sm font-medium">
                        Không giới hạn
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Miễn phí giao hàng</h3>
                    <p class="text-gray-600 mb-4">
                        Miễn phí giao hàng cho đơn hàng từ 200.000₫ trong bán kính 5km. Áp dụng mọi ngày trong tuần.
                    </p>
                    <div class="flex items-center justify-between">
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center copy-code" data-code="FREESHIP">
                            <i class="fas fa-copy mr-2"></i>
                            <span>FREESHIP</span>
                        </button>
                        <a href="/menu" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center">
                            Đặt ngay
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Khuyến mãi 3 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="/placeholder.svg?height=300&width=500" alt="Giảm giá cuối tuần" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-orange-500 text-white px-3 py-1 text-sm font-medium">
                        Chỉ cuối tuần
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Giảm giá 20% cuối tuần</h3>
                    <p class="text-gray-600 mb-4">
                        Giảm 20% cho tất cả các đơn hàng vào Thứ 7 và Chủ Nhật. Áp dụng cho đơn hàng từ 150.000₫.
                    </p>
                    <div class="flex items-center justify-between">
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center copy-code" data-code="WEEKEND20">
                            <i class="fas fa-copy mr-2"></i>
                            <span>WEEKEND20</span>
                        </button>
                        <a href="/menu" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center">
                            Đặt ngay
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Khuyến mãi 4 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="/placeholder.svg?height=300&width=500" alt="Sinh nhật vui vẻ" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-purple-500 text-white px-3 py-1 text-sm font-medium">
                        Đặc biệt
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Sinh nhật vui vẻ</h3>
                    <p class="text-gray-600 mb-4">
                        Nhận một bánh ngọt miễn phí trong ngày sinh nhật của bạn khi đặt bất kỳ combo nào. Yêu cầu xác minh ngày sinh.
                    </p>
                    <div class="flex items-center justify-between">
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center copy-code" data-code="BIRTHDAY">
                            <i class="fas fa-copy mr-2"></i>
                            <span>BIRTHDAY</span>
                        </button>
                        <a href="/menu" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center">
                            Đặt ngay
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Khuyến mãi 5 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="/placeholder.svg?height=300&width=500" alt="Ưu đãi học sinh, sinh viên" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 text-sm font-medium">
                        Hàng ngày
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Ưu đãi học sinh, sinh viên</h3>
                    <p class="text-gray-600 mb-4">
                        Giảm 15% cho học sinh, sinh viên từ 10:00 - 14:00 các ngày trong tuần. Yêu cầu xuất trình thẻ học sinh, sinh viên.
                    </p>
                    <div class="flex items-center justify-between">
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center copy-code" data-code="STUDENT15">
                            <i class="fas fa-copy mr-2"></i>
                            <span>STUDENT15</span>
                        </button>
                        <a href="/menu" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center">
                            Đặt ngay
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Khuyến mãi 6 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="/placeholder.svg?height=300&width=500" alt="Khuyến mãi app" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-green-500 text-white px-3 py-1 text-sm font-medium">
                        Không giới hạn
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Khuyến mãi đặt qua app</h3>
                    <p class="text-gray-600 mb-4">
                        Giảm thêm 10% cho tất cả các đơn hàng đặt qua ứng dụng di động FastFood. Không áp dụng cùng các khuyến mãi khác.
                    </p>
                    <div class="flex items-center justify-between">
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center copy-code" data-code="APP10">
                            <i class="fas fa-copy mr-2"></i>
                            <span>APP10</span>
                        </button>
                        <a href="#" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center">
                            Tải app
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Đăng ký nhận thông báo khuyến mãi -->
    <div class="bg-orange-50 rounded-xl p-8 text-center">
        <h2 class="text-2xl font-bold mb-4">Đăng Ký Nhận Thông Báo Khuyến Mãi</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-6">
            Đừng bỏ lỡ các ưu đãi hấp dẫn! Đăng ký ngay để nhận thông báo về các chương trình khuyến mãi mới nhất từ FastFood.
        </p>
        
        <form class="max-w-md mx-auto">
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="email" placeholder="Nhập email của bạn" class="flex-grow px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Đăng ký
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-3">
                Bằng cách đăng ký, bạn đồng ý với <a href="/privacy" class="text-orange-500 hover:underline">Chính sách bảo mật</a> của chúng tôi.
            </p>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút sao chép mã khuyến mãi
    const copyButtons = document.querySelectorAll('.copy-code');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            
            // Sao chép mã vào clipboard
            navigator.clipboard.writeText(code).then(() => {
                // Cập nhật giao diện nút
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check mr-2"></i><span>Đã sao chép</span>';
                
                // Hiển thị thông báo
                showToast(`Đã sao chép mã "${code}" vào clipboard`);
                
                // Khôi phục giao diện nút sau 2 giây
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Không thể sao chép: ', err);
                showToast('Không thể sao chép mã. Vui lòng thử lại.');
            });
        });
    });
    
    // Hàm hiển thị thông báo
    function showToast(message) {
        // Tạo element thông báo
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        
        // Thêm vào DOM
        document.body.appendChild(toast);
        
        // Hiển thị thông báo
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Ẩn và xóa thông báo sau 3 giây
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // Xử lý form đăng ký
    const subscribeForm = document.querySelector('form');
    
    if (subscribeForm) {
        subscribeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (!email) {
                showToast('Vui lòng nhập email của bạn');
                return;
            }
            
            // Kiểm tra định dạng email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Vui lòng nhập email hợp lệ');
                return;
            }
            
            // Giả lập gửi form
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
            
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                emailInput.value = '';
                
                showToast('Đăng ký thành công! Cảm ơn bạn đã đăng ký nhận thông báo khuyến mãi.');
            }, 1500);
        });
    }
});
</script>
@endsection
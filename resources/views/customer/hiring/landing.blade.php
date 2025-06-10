@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Tuyển dụng tài xế - DevFoods')

@section('content')
<!-- Custom CSS for hiring page -->
<style>
    .hiring-landing-container {
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        color: #333;
    }
    
    .hero-banner {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .hero-title {
        font-weight: 700;
        color: #1a202c;
    }
    
    .hero-title span {
        color: #3182ce;
    }
    
    .benefit-card {
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    
    .benefit-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .benefit-icon {
        color: #3182ce;
    }
    
    .timeline-number {
        background-color: #3182ce;
    }
    
    .timeline-content {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
    }
    
    .requirements-item {
        background-color: #f7fafc;
        border-left: 3px solid #48bb78;
    }
    
    .documents-required {
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
    }
    
    .btn-primary {
        background-color: #3182ce;
        color: white;
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s ease;
        border: 1px solid #3182ce;
    }
    
    .btn-primary:hover {
        background-color: #2c5282;
        border-color: #2c5282;
    }
    
    .btn-outline {
        background-color: transparent;
        border: 1px solid #3182ce;
        color: #3182ce;
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s ease;
    }
    
    .btn-outline:hover {
        background-color: #3182ce;
        color: white;
    }
    
    .cta-section {
        background-color: #3182ce;
    }
    
    .btn-success {
        background-color: #48bb78;
        border-color: #48bb78;
        color: white;
        padding: 12px 30px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s ease;
    }
    
    .btn-success:hover {
        background-color: #38a169;
        border-color: #38a169;
    }

    /* === Primary Color Overrides === */
    :root {
        --primary-color: #f97316;
        --primary-dark: #c2410c;
        --primary-light: #ffedd5;
    }
    .hero-title span { color: var(--primary-color); }
    .benefit-icon { color: var(--primary-color); }
    .timeline-number { background-color: var(--primary-color); }
    .btn-primary {
        background-color: var(--primary-color);
        border: 1px solid var(--primary-color);
    }
    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }
    .btn-outline {
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }
    .btn-outline:hover {
        background-color: var(--primary-color);
        color: white;
    }
    .cta-section { background-color: var(--primary-color); }
    .text-blue-600 { color: var(--primary-color) !important; }
    .bg-blue-600 { background-color: var(--primary-color) !important; }
    .border-blue-600 { border-color: var(--primary-color) !important; }
</style>

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<main class="hiring-landing-container">
    <!-- Hero Section -->
    <section class="hero-banner py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="flex-1 text-center lg:text-left">
                    <h1 class="hero-title text-4xl lg:text-5xl mb-6">
                        Trở thành <br><span>Đối tác tài xế</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Gia nhập đội ngũ tài xế giao hàng của chúng tôi ngay hôm nay để có cơ hội kiếm thêm thu nhập và làm chủ thời gian của bạn.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('driver.application.form') }}" class="btn-primary font-medium">ĐĂNG KÝ NGAY</a>
                        <a href="#requirements" class="btn-outline font-medium">TÌM HIỂU THÊM</a>
                    </div>
                </div>
                <div class="flex-1 text-center">
                    <img src="{{ asset('images/delivery-driver.jpg') }}" alt="Tài xế giao hàng" class="w-full h-auto rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">LỢI ÍCH KHI TRỞ THÀNH ĐỐI TÁC TÀI XẾ</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">KHÁM PHÁ NHỮNG GIÁ TRỊ KHI TRỞ THÀNH TÀI XẾ CỦA DEVFOODS</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="benefit-card p-8 rounded-lg text-center">
                    <div class="benefit-icon text-4xl mb-6">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Thời gian linh hoạt</h3>
                    <p class="text-gray-600">
                        Bạn hoàn toàn có thể chủ động sắp xếp thời gian làm việc theo lịch trình cá nhân.
                    </p>
                </div>
                <div class="benefit-card p-8 rounded-lg text-center">
                    <div class="benefit-icon text-4xl mb-6">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Thu nhập hấp dẫn</h3>
                    <p class="text-gray-600">
                        Kiếm thêm thu nhập với mức phí giao hàng cạnh tranh và nhiều chương trình thưởng.
                    </p>
                </div>
                <div class="benefit-card p-8 rounded-lg text-center">
                    <div class="benefit-icon text-4xl mb-6">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">An toàn & Bảo đảm</h3>
                    <p class="text-gray-600">
                        Hệ thống hỗ trợ 24/7 và các chính sách bảo hiểm cho đối tác tài xế.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">QUY TRÌNH ĐĂNG KÝ</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">CÁC BƯỚC ĐƠN GIẢN ĐỂ TRỞ THÀNH TÀI XẾ DEVFOODS</p>
            </div>
            <div class="max-w-4xl mx-auto space-y-8">
                <div class="flex flex-col md:flex-row items-start gap-6">
                    <div class="timeline-number w-12 h-12 rounded-full text-white flex items-center justify-center font-bold text-lg flex-shrink-0 mx-auto md:mx-0">1</div>
                    <div class="timeline-content flex-1 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900">Đăng ký trực tuyến</h3>
                        <p class="text-gray-600">Hoàn thành mẫu đơn đăng ký trực tuyến với đầy đủ thông tin cá nhân, phương tiện.</p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-start gap-6">
                    <div class="timeline-number w-12 h-12 rounded-full text-white flex items-center justify-center font-bold text-lg flex-shrink-0 mx-auto md:mx-0">2</div>
                    <div class="timeline-content flex-1 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900">Xác thực hồ sơ</h3>
                        <p class="text-gray-600">Đội ngũ của chúng tôi sẽ kiểm tra và xác minh thông tin của bạn trong vòng 1-3 ngày làm việc.</p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-start gap-6">
                    <div class="timeline-number w-12 h-12 rounded-full text-white flex items-center justify-center font-bold text-lg flex-shrink-0 mx-auto md:mx-0">3</div>
                    <div class="timeline-content flex-1 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900">Tham gia chương trình đào tạo</h3>
                        <p class="text-gray-600">Học cách sử dụng ứng dụng và các quy định, chính sách dành cho tài xế.</p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-start gap-6">
                    <div class="timeline-number w-12 h-12 rounded-full text-white flex items-center justify-center font-bold text-lg flex-shrink-0 mx-auto md:mx-0">4</div>
                    <div class="timeline-content flex-1 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900">Bắt đầu công việc</h3>
                        <p class="text-gray-600">Nhận đơn hàng và bắt đầu công việc giao hàng với thu nhập hấp dẫn.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('driver.application.form') }}" class="btn-primary font-medium">Đăng ký ngay</a>
            </div>
        </div>
    </section>

    <!-- Requirements Section -->
    <section class="py-20 bg-white" id="requirements">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">ĐIỀU KIỆN ĐĂNG KÝ</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">YÊU CẦU TRỞ THÀNH ĐỐI TÁC TÀI XẾ DEVFOODS</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <div class="space-y-4">
                        <div class="requirements-item p-4 rounded-lg">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            Từ 18 tuổi trở lên
                        </div>
                        <div class="requirements-item p-4 rounded-lg">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            Có CMND/CCCD hợp lệ
                        </div>
                        <div class="requirements-item p-4 rounded-lg">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            Có giấy phép lái xe phù hợp với phương tiện
                        </div>
                        <div class="requirements-item p-4 rounded-lg">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            Có smartphone hỗ trợ cài đặt ứng dụng
                        </div>
                        <div class="requirements-item p-4 rounded-lg">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            Có phương tiện đạt tiêu chuẩn vận hành
                        </div>
                    </div>
                </div>
                <div>
                    <div class="documents-required p-6 rounded-lg">
                        <h3 class="text-xl font-semibold mb-6 text-gray-900 border-b border-gray-200 pb-3">Giấy tờ cần chuẩn bị:</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-id-card text-blue-600 mr-3 w-5"></i>
                                CMND/CCCD (mặt trước và sau)
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-id-badge text-blue-600 mr-3 w-5"></i>
                                Giấy phép lái xe
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-blue-600 mr-3 w-5"></i>
                                Đăng ký xe
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-image text-blue-600 mr-3 w-5"></i>
                                Ảnh chân dung
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-file-invoice text-blue-600 mr-3 w-5"></i>
                                Thông tin tài khoản ngân hàng
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">CÂU HỎI THƯỜNG GẶP</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">NHỮNG THẮC MẮC PHỔ BIẾN NHẤT VỀ CHƯƠNG TRÌNH TÀI XẾ DEVFOODS</p>
            </div>
            <div class="max-w-4xl mx-auto space-y-4">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-6">
                        <button class="w-full text-left font-medium text-gray-900 focus:outline-none" onclick="toggleFaq(1)">
                            <div class="flex justify-between items-center">
                                <span>Tôi cần những điều kiện gì để trở thành tài xế?</span>
                                <i id="faq-icon-1" class="fas fa-chevron-down text-gray-500"></i>
                            </div>
                        </button>
                        <div id="faq-content-1" class="mt-4 text-gray-600">
                            Bạn cần đảm bảo đủ 18 tuổi trở lên, có CMND/CCCD hợp lệ, giấy phép lái xe phù hợp, phương tiện đạt tiêu chuẩn và smartphone để cài đặt ứng dụng.
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-6">
                        <button class="w-full text-left font-medium text-gray-900 focus:outline-none" onclick="toggleFaq(2)">
                            <div class="flex justify-between items-center">
                                <span>Thu nhập của tài xế được tính như thế nào?</span>
                                <i id="faq-icon-2" class="fas fa-chevron-down text-gray-500"></i>
                            </div>
                        </button>
                        <div id="faq-content-2" class="mt-4 text-gray-600 hidden">
                            Thu nhập của tài xế được tính dựa trên số lượng đơn hàng, quãng đường giao hàng và các chương trình thưởng. Bạn sẽ được thanh toán định kỳ hàng tuần hoặc theo yêu cầu.
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-6">
                        <button class="w-full text-left font-medium text-gray-900 focus:outline-none" onclick="toggleFaq(3)">
                            <div class="flex justify-between items-center">
                                <span>Tôi có thể hoạt động ở những khu vực nào?</span>
                                <i id="faq-icon-3" class="fas fa-chevron-down text-gray-500"></i>
                            </div>
                        </button>
                        <div id="faq-content-3" class="mt-4 text-gray-600 hidden">
                            Hiện tại chúng tôi đang hoạt động tại các thành phố lớn như Hà Nội, TP.HCM, Đà Nẵng và một số tỉnh thành khác. Bạn có thể chọn khu vực hoạt động phù hợp với mình.
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-6">
                        <button class="w-full text-left font-medium text-gray-900 focus:outline-none" onclick="toggleFaq(4)">
                            <div class="flex justify-between items-center">
                                <span>Quy trình xét duyệt hồ sơ mất bao lâu?</span>
                                <i id="faq-icon-4" class="fas fa-chevron-down text-gray-500"></i>
                            </div>
                        </button>
                        <div id="faq-content-4" class="mt-4 text-gray-600 hidden">
                            Thông thường, quy trình xét duyệt hồ sơ sẽ mất từ 1-3 ngày làm việc. Sau khi hồ sơ được duyệt, bạn sẽ nhận được thông báo qua email hoặc điện thoại.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-20 text-white text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-4">Sẵn sàng trở thành đối tác tài xế?</h2>
            <p class="text-xl mb-8 opacity-90">Đăng ký ngay hôm nay để bắt đầu hành trình của bạn với chúng tôi</p>
            <a href="{{ route('driver.application.form') }}" class="btn-success font-medium text-lg">Đăng ký ngay</a>
        </div>
    </section>
</main>

<script>
function toggleFaq(index) {
    const content = document.getElementById(`faq-content-${index}`);
    const icon = document.getElementById(`faq-icon-${index}`);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endsection

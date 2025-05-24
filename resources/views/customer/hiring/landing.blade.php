@extends('layouts.customer.fullLayoutMaster')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/customer/hiring.css') }}">
    <style>
    /* Basic styles until we create a separate CSS file */
    .hiring-landing-container {
        font-family: 'Roboto', sans-serif;
    }
    
    .hiring-hero {
        padding: 80px 0;
        background-color: #f8f9fa;
    }
    
    .hiring-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 16px;
    }
    
    .hiring-subtitle {
        font-size: 1.5rem;
        color: #3498db;
        font-weight: 500;
        margin-bottom: 20px;
    }
    
    .hiring-description {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 30px;
        line-height: 1.6;
    }
    
    .hero-image {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    
    .hiring-benefits, .hiring-process, .hiring-requirements, .hiring-faq {
        padding: 80px 0;
    }
    
    .hiring-benefits {
        background-color: #fff;
    }
    
    .benefit-card {
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
        height: 100%;
        text-align: center;
    }
    
    .benefit-card:hover {
        transform: translateY(-10px);
    }
    
    .benefit-icon {
        font-size: 2.5rem;
        color: #3498db;
        margin-bottom: 20px;
    }
    
    .benefit-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .benefit-description {
        color: #666;
        line-height: 1.5;
    }
    
    .hiring-process {
        background-color: #f8f9fa;
    }
    
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .timeline-item {
        display: flex;
        margin-bottom: 30px;
        position: relative;
    }
    
    .timeline-number {
        width: 40px;
        height: 40px;
        background-color: #3498db;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .timeline-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        flex-grow: 1;
    }
    
    .timeline-content h3 {
        color: #2c3e50;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }
    
    .hiring-requirements {
        background-color: #fff;
    }
    
    .requirements-list li, .documents-required ul li {
        font-size: 1.1rem;
        margin-bottom: 15px;
        color: #333;
        list-style: none;
        padding-left: 10px;
    }
    
    .requirements-list i, .documents-required i {
        color: #27ae60;
        margin-right: 10px;
    }
    
    .documents-required h3 {
        color: #2c3e50;
        font-size: 1.3rem;
        margin-bottom: 20px;
    }
    
    .hiring-faq {
        background-color: #f8f9fa;
    }
    
    .faq-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .card {
        margin-bottom: 15px;
        border: none;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        background-color: #fff;
        border: none;
    }
    
    .btn-link {
        color: #2c3e50;
        font-weight: 600;
        text-decoration: none;
    }
    
    .btn-link:hover, .btn-link:focus {
        text-decoration: none;
        color: #3498db;
    }
    
    .card-body {
        color: #555;
        line-height: 1.6;
    }
    
    .hiring-cta-section {
        padding: 80px 0;
        background-color: #3498db;
        color: #fff;
    }
    
    .cta-box {
        text-align: center;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .cta-box h2 {
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .cta-box p {
        font-size: 1.1rem;
        margin-bottom: 30px;
    }
    
    .btn-primary {
        background-color: #27ae60;
        border-color: #27ae60;
        padding: 10px 25px;
        font-weight: 600;
        border-radius: 5px;
    }
    
    .btn-primary:hover {
        background-color: #229954;
        border-color: #229954;
    }
    
    @media (max-width: 768px) {
        .hiring-hero {
            padding: 50px 0;
            text-align: center;
        }
        
        .hero-image {
            margin-top: 30px;
        }
        
        .hiring-title {
            font-size: 2rem;
        }
        
        .hiring-benefits, .hiring-process, .hiring-requirements, .hiring-faq, .hiring-cta-section {
            padding: 50px 0;
        }
        
        .section-title {
            font-size: 1.8rem;
        }
    }
    </style>
</head>

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<main>
<div class="hiring-landing-container">
    <!-- Hero Section -->
    <section class="hero-banner">
        <div class="hero-background"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Trở thành <br><span>Đối tác tài xế</span></h1>
                    <p class="hero-description">
                        Gia nhập đội ngũ tài xế giao hàng của chúng tôi ngay hôm nay để có cơ hội kiếm thêm thu nhập và làm chủ thời gian của bạn.
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ route('driver.application.form') }}" class="btn btn-primary">ĐĂNG KÝ NGAY</a>
                        <a href="#requirements" class="btn btn-outline">TÌM HIỂU THÊM</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="{{ asset('images/delivery-driver.jpg') }}" alt="Tài xế giao hàng" class="img-fluid rounded">
                </div>
            </div>
        </div>
        <div class="hero-gradient"></div>
    </section>

    <!-- Benefits Section -->
    <section class="hiring-benefits">
        <div class="container">
            <div class="section-title">
                <h2>LỢI ÍCH KHI TRỞ THÀNH ĐỐI TÁC TÀI XẾ</h2>
                <p>KHÁM PHÁ NHỮNG GIÁ TRỊ KHI TRỞ THÀNH TÀI XẾ CỦA DEVFOODS</p>
            </div>
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="benefit-title">Thời gian linh hoạt</h3>
                        <p class="benefit-description">
                            Bạn hoàn toàn có thể chủ động sắp xếp thời gian làm việc theo lịch trình cá nhân.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3 class="benefit-title">Thu nhập hấp dẫn</h3>
                        <p class="benefit-description">
                            Kiếm thêm thu nhập với mức phí giao hàng cạnh tranh và nhiều chương trình thưởng.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="benefit-title">An toàn & Bảo đảm</h3>
                        <p class="benefit-description">
                            Hệ thống hỗ trợ 24/7 và các chính sách bảo hiểm cho đối tác tài xế.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="hiring-process">
        <div class="container">
            <div class="section-title">
                <h2>QUY TRÌNH ĐĂNG KÝ</h2>
                <p>CÁC BƯỚC ĐƠN GIẢN ĐỂ TRỞ THÀNH TÀI XẾ DEVFOODS</p>
            </div>
            <div class="timeline mt-5">
                <div class="timeline-item">
                    <div class="timeline-number">1</div>
                    <div class="timeline-content">
                        <h3>Đăng ký trực tuyến</h3>
                        <p>Hoàn thành mẫu đơn đăng ký trực tuyến với đầy đủ thông tin cá nhân, phương tiện.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">2</div>
                    <div class="timeline-content">
                        <h3>Xác thực hồ sơ</h3>
                        <p>Đội ngũ của chúng tôi sẽ kiểm tra và xác minh thông tin của bạn trong vòng 1-3 ngày làm việc.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">3</div>
                    <div class="timeline-content">
                        <h3>Tham gia chương trình đào tạo</h3>
                        <p>Học cách sử dụng ứng dụng và các quy định, chính sách dành cho tài xế.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">4</div>
                    <div class="timeline-content">
                        <h3>Bắt đầu công việc</h3>
                        <p>Nhận đơn hàng và bắt đầu công việc giao hàng với thu nhập hấp dẫn.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('driver.application.form') }}" class="btn btn-primary">Đăng ký ngay</a>
            </div>
        </div>
    </section>

    <!-- Requirements Section -->
    <section class="hiring-requirements" id="requirements">
        <div class="container">
            <div class="section-title">
                <h2>ĐIỀU KIỆN ĐĂNG KÝ</h2>
                <p>YÊU CẦU TRỞ THÀNH ĐỐI TÁC TÀI XẾ DEVFOODS</p>
            </div>
            <div class="row mt-5">
                <div class="col-lg-6">
                    <ul class="requirements-list">
                        <li><i class="fas fa-check-circle"></i> Từ 18 tuổi trở lên</li>
                        <li><i class="fas fa-check-circle"></i> Có CMND/CCCD hợp lệ</li>
                        <li><i class="fas fa-check-circle"></i> Có giấy phép lái xe phù hợp với phương tiện</li>
                        <li><i class="fas fa-check-circle"></i> Có smartphone hỗ trợ cài đặt ứng dụng</li>
                        <li><i class="fas fa-check-circle"></i> Có phương tiện đạt tiêu chuẩn vận hành</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="documents-required">
                        <h3>Giấy tờ cần chuẩn bị:</h3>
                        <ul>
                            <li><i class="fas fa-id-card"></i> CMND/CCCD (mặt trước và sau)</li>
                            <li><i class="fas fa-id-badge"></i> Giấy phép lái xe</li>
                            <li><i class="fas fa-file-alt"></i> Đăng ký xe</li>
                            <li><i class="fas fa-image"></i> Ảnh chân dung</li>
                            <li><i class="fas fa-file-invoice"></i> Thông tin tài khoản ngân hàng</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="hiring-faq">
        <div class="container">
            <div class="section-title">
                <h2>CÂU HỎI THƯỜNG GẶP</h2>
                <p>NHỮNG THẮC MẮC PHỔ BIẾN NHẤT VỀ CHƯƠNG TRÌNH TÀI XẾ DEVFOODS</p>
            </div>
            <div class="faq-container mt-5">
                <div class="accordion" id="hiringFaqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="card">
                        <div class="card-header" id="faqHeading1">
                            <h3 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    Tôi cần những điều kiện gì để trở thành tài xế?
                                </button>
                            </h3>
                        </div>
                        <div id="faqCollapse1" class="collapse show" aria-labelledby="faqHeading1" data-parent="#hiringFaqAccordion">
                            <div class="card-body">
                                Bạn cần đảm bảo đủ 18 tuổi trở lên, có CMND/CCCD hợp lệ, giấy phép lái xe phù hợp, phương tiện đạt tiêu chuẩn và smartphone để cài đặt ứng dụng.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="card">
                        <div class="card-header" id="faqHeading2">
                            <h3 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    Thu nhập của tài xế được tính như thế nào?
                                </button>
                            </h3>
                        </div>
                        <div id="faqCollapse2" class="collapse" aria-labelledby="faqHeading2" data-parent="#hiringFaqAccordion">
                            <div class="card-body">
                                Thu nhập của tài xế được tính dựa trên số lượng đơn hàng, quãng đường giao hàng và các chương trình thưởng. Bạn sẽ được thanh toán định kỳ hàng tuần hoặc theo yêu cầu.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="card">
                        <div class="card-header" id="faqHeading3">
                            <h3 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    Tôi có thể hoạt động ở những khu vực nào?
                                </button>
                            </h3>
                        </div>
                        <div id="faqCollapse3" class="collapse" aria-labelledby="faqHeading3" data-parent="#hiringFaqAccordion">
                            <div class="card-body">
                                Hiện tại chúng tôi đang hoạt động tại các thành phố lớn như Hà Nội, TP.HCM, Đà Nẵng và một số tỉnh thành khác. Bạn có thể chọn khu vực hoạt động phù hợp với mình.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="card">
                        <div class="card-header" id="faqHeading4">
                            <h3 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    Quy trình xét duyệt hồ sơ mất bao lâu?
                                </button>
                            </h3>
                        </div>
                        <div id="faqCollapse4" class="collapse" aria-labelledby="faqHeading4" data-parent="#hiringFaqAccordion">
                            <div class="card-body">
                                Thông thường, quy trình xét duyệt hồ sơ sẽ mất từ 1-3 ngày làm việc. Sau khi hồ sơ được duyệt, bạn sẽ nhận được thông báo qua email hoặc điện thoại.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="hiring-cta-section">
        <div class="container">
            <div class="cta-box">
                <h2>Sẵn sàng trở thành đối tác tài xế?</h2>
                <p>Đăng ký ngay hôm nay để bắt đầu hành trình của bạn với chúng tôi</p>
                <a href="{{ route('driver.application.form') }}" class="btn btn-primary btn-lg">Đăng ký ngay</a>
            </div>
        </div>
    </section>
</div>
</main>
@endsection

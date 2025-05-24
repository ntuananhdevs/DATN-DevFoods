@extends('layouts.customer.fullLayoutMaster')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/customer/hiring.css') }}">
    <style>
    /* Basic styles until we create a separate CSS file */
    .success-container {
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 50px 0;
    }
    
    .success-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
        padding: 50px;
        text-align: center;
    }
    
    .success-icon {
        font-size: 5rem;
        color: #27ae60;
        margin-bottom: 20px;
    }
    
    .success-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 30px;
    }
    
    .success-message {
        font-size: 1.1rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    
    .next-steps {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 30px;
        text-align: left;
    }
    
    .next-steps h3 {
        color: #2c3e50;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .next-steps ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .next-steps li {
        margin-bottom: 15px;
        padding-left: 10px;
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .next-steps li i {
        color: #3498db;
        margin-right: 10px;
        font-size: 1.2rem;
    }
    
    .success-actions {
        margin-bottom: 30px;
    }
    
    .btn {
        padding: 10px 25px;
        font-weight: 500;
        margin: 0 10px;
    }
    
    .btn-outline-primary {
        color: #27ae60;
        border-color: #27ae60;
    }
    
    .btn-outline-primary:hover {
        background-color: #27ae60;
        border-color: #27ae60;
    }
    
    .btn-outline-secondary {
        color: #3498db;
        border-color: #3498db;
    }
    
    .btn-outline-secondary:hover {
        background-color: #3498db;
        border-color: #3498db;
    }
    
    .additional-info {
        font-size: 0.9rem;
        color: #7f8c8d;
    }
    
    .additional-info a {
        color: #3498db;
        text-decoration: none;
    }
    
    @media (max-width: 768px) {
        .success-card {
            padding: 30px 20px;
        }
        
        .success-icon {
            font-size: 4rem;
        }
        
        .success-title {
            font-size: 1.8rem;
        }
        
        .success-actions .btn {
            display: block;
            width: 100%;
            margin: 10px 0;
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
<div class="success-container">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    
                    <h1 class="success-title">Đăng ký thành công!</h1>
                    
                    <div class="success-message">
                        <p>Cảm ơn bạn đã đăng ký trở thành đối tác tài xế của DevFoods. Chúng tôi đã nhận được đơn đăng ký của bạn.</p>
                        <p>Đội ngũ của chúng tôi sẽ xem xét hồ sơ của bạn và liên hệ với bạn trong vòng 1-3 ngày làm việc qua email hoặc số điện thoại đã đăng ký.</p>
                    </div>
                    
                    <div class="next-steps">
                        <h3>Các bước tiếp theo</h3>
                        <ul>
                            <li><i class="fas fa-clipboard-check"></i> Chúng tôi sẽ kiểm tra thông tin và giấy tờ của bạn</li>
                            <li><i class="fas fa-phone-alt"></i> Một nhân viên của chúng tôi sẽ liên hệ với bạn để xác nhận thông tin</li>
                            <li><i class="fas fa-book"></i> Nếu hồ sơ được chấp thuận, bạn sẽ được mời tham gia khóa đào tạo ngắn</li>
                            <li><i class="fas fa-route"></i> Sau khi hoàn thành đào tạo, bạn có thể bắt đầu nhận đơn hàng</li>
                        </ul>
                    </div>
                    
                    <div class="success-actions">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">Trở về trang chủ</a>
                        <a href="mailto:support@devfoods.com" class="btn btn-outline-secondary">Liên hệ hỗ trợ</a>
                    </div>
                    
                    <div class="additional-info">
                        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email <a href="mailto:support@devfoods.com">support@devfoods.com</a> hoặc số điện thoại <a href="tel:+84123456789">0123 456 789</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
@endsection 
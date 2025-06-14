@extends('layouts.customer.fullLayoutMaster')

@section('content')
<style>
    .success-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
        padding: 40px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .success-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
        padding: 50px 40px;
        text-align: center;
        max-width: 600px;
        width: 100%;
        border: 1px solid #e5e7eb;
    }
    
    .success-icon {
        font-size: 4rem;
        color: #10b981;
        margin-bottom: 25px;
    }
    
    .success-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 12px;
    }
    
    .success-subtitle {
        font-size: 1.1rem;
        color: #6b7280;
        margin-bottom: 30px;
    }
    
    .success-message {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        text-align: left;
    }
    
    .success-message p {
        margin-bottom: 12px;
        color: #374151;
        line-height: 1.6;
    }
    
    .success-message p:last-child {
        margin-bottom: 0;
    }
    
    .email-info {
        background: var(--primary-color);
        color: white;
        padding: 10px 16px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        margin: 10px 0;
    }
    
    .next-steps {
        background: #fffbeb;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 30px;
        text-align: left;
    }
    
    .next-steps h3 {
        color: #92400e;
        font-size: 1.25rem;
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
        margin-bottom: 12px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: white;
        border-radius: 6px;
        border: 1px solid #f3f4f6;
    }
    
    .next-steps li i {
        color: #f59e0b;
        font-size: 1.1rem;
        margin-top: 2px;
        flex-shrink: 0;
    }
    
    .next-steps li span {
        color: #374151;
        line-height: 1.5;
    }
    
    .success-actions {
        margin-bottom: 25px;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        font-weight: 500;
        font-size: 16px;
        border-radius: 8px;
        text-decoration: none;
        margin: 0 8px;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
        border: 1px solid var(--primary-color);
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }
    
    .btn-secondary {
        background: white;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
    }
    
    .btn-secondary:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .additional-info {
        background: #f9fafb;
        border-radius: 8px;
        padding: 20px;
        font-size: 0.9rem;
        color: #6b7280;
        line-height: 1.6;
    }
    
    .additional-info a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }
    
    .additional-info a:hover {
        text-decoration: underline;
    }
    
    /* === Primary Color Overrides === */
    :root {
        --primary-color: #f97316;
        --primary-dark: #c2410c;
        --primary-light: #ffedd5;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .success-container {
            padding: 20px 15px;
        }
        
        .success-card {
            padding: 40px 25px;
        }
        
        .success-title {
            font-size: 1.875rem;
        }
        
        .success-icon {
            font-size: 3.5rem;
        }
        
        .btn {
            display: block;
            width: 100%;
            margin: 8px 0;
            text-align: center;
            justify-content: center;
        }
        
        .next-steps {
            padding: 20px;
        }
    }
</style>

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
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="success-title">Đăng ký thành công!</h1>
        <p class="success-subtitle">Cảm ơn bạn đã đăng ký trở thành đối tác tài xế của DevFoods</p>
        
        <div class="success-message">
            @if(isset($applicationData))
            <p><strong>Chúc mừng {{ $applicationData['name'] }}!</strong> Đơn đăng ký của bạn đã được gửi thành công.</p>
            
            <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin: 15px 0; text-align: left;">
                <p style="margin: 5px 0; color: #374151;"><strong>Mã đơn:</strong> #{{ str_pad($applicationData['id'], 6, '0', STR_PAD_LEFT) }}</p>
                <p style="margin: 5px 0; color: #374151;"><strong>Email:</strong> {{ $applicationData['email'] }}</p>
                <p style="margin: 5px 0; color: #374151;"><strong>Thời gian gửi:</strong> {{ $applicationData['submitted_at'] }}</p>
            </div>
            @else
            <p><strong>Chúc mừng!</strong> Đơn đăng ký của bạn đã được gửi thành công.</p>
            @endif
            
            <div class="email-info">
                <i class="fas fa-envelope"></i>
                <span>Email xác nhận đã được gửi</span>
            </div>
            
            <p>Vui lòng kiểm tra hộp thư email để xem thông tin chi tiết về đơn ứng tuyển và các bước tiếp theo.</p>
        </div>
        
        <div class="next-steps">
            <h3>Các bước tiếp theo</h3>
            <ul>
                <li>
                    <i class="fas fa-clipboard-check"></i>
                    <span>Đội ngũ HR sẽ xem xét hồ sơ của bạn trong vòng <strong>1-3 ngày làm việc</strong></span>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i>
                    <span>Nhân viên sẽ liên hệ qua điện thoại để xác nhận thông tin</span>
                </li>
                <li>
                    <i class="fas fa-graduation-cap"></i>
                    <span>Tham gia khóa đào tạo ngắn về quy trình giao hàng</span>
                </li>
                <li>
                    <i class="fas fa-rocket"></i>
                    <span>Bắt đầu nhận đơn hàng và trở thành đối tác chính thức</span>
                </li>
            </ul>
        </div>
        
        <div class="success-actions">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                <span>Về trang chủ</span>
            </a>
            <a href="mailto:support@devfoods.com" class="btn btn-secondary">
                <i class="fas fa-headset"></i>
                <span>Liên hệ hỗ trợ</span>
            </a>
        </div>
        
        <div class="additional-info">
            <p>
                <strong>Lưu ý:</strong> Vui lòng đảm bảo điện thoại luôn liên lạc được và kiểm tra email thường xuyên.
            </p>
            <p>
                Cần hỗ trợ? Liên hệ: 
                <a href="mailto:support@devfoods.com">support@devfoods.com</a> | 
                <a href="tel:+84123456789">0123 456 789</a>
            </p>
        </div>
    </div>
</div>
</main>
@endsection 
@extends('emails.layouts.app')

@section('content')
<div style="background-color: #f8f9fa; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #FF6B35; padding: 20px; text-align: center;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="DevFoods Logo" style="max-height: 60px; margin-bottom: 10px;">
            <h1 style="color: #fff; margin: 0; font-size: 24px; font-weight: 600;">Chào mừng bạn đến với DevFoods!</h1>
        </div>

        <!-- Body -->
        <div style="padding: 30px;">
            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">Xin chào <strong>{{ $notifiable->full_name }}</strong>,</p>

            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                Chúng tôi rất vui mừng chào đón bạn đến với cộng đồng DevFoods. Tài khoản của bạn đã được tạo thành công và bạn có thể bắt đầu sử dụng dịch vụ của chúng tôi ngay từ bây giờ.
            </p>

            <div style="background-color: #f8f9fa; border-left: 4px solid #FF6B35; padding: 15px; margin-bottom: 20px;">
                <h3 style="color: #333; margin-top: 0; font-size: 18px;">Thông tin tài khoản của bạn:</h3>
                <p style="margin: 8px 0; color: #555;">
                    <strong>Tên đăng nhập:</strong> {{ $notifiable->user_name }}
                </p>
                <p style="margin: 8px 0; color: #555;">
                    <strong>Email:</strong> {{ $notifiable->email }}
                </p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login') }}" style="background-color: #FF6B35; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 4px; font-weight: 600; display: inline-block;">
                    Đăng nhập ngay
                </a>
            </div>

            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ, đừng ngần ngại liên hệ với đội ngũ hỗ trợ của chúng tôi qua email <a href="mailto:support@devfoods.com" style="color: #FF6B35; text-decoration: none;">support@devfoods.com</a>.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eeeeee;">
            <p style="color: #777; font-size: 14px; margin-bottom: 5px;">
                © {{ date('Y') }} DevFoods. Tất cả các quyền được bảo lưu.
            </p>
            <div style="margin-top: 10px;">
                <a href="{{ url('/terms') }}" style="color: #777; text-decoration: none; margin: 0 10px; font-size: 14px;">Điều khoản sử dụng</a>
                <a href="{{ url('/privacy') }}" style="color: #777; text-decoration: none; margin: 0 10px; font-size: 14px;">Chính sách bảo mật</a>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('emails.layouts.app')

@section('content')
<div style="background-color: #f8f9fa; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #f97316; padding: 20px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 24px; font-weight: 600;">FastFood</h1>
            <p style="color: #fed7aa; margin: 4px 0 0;">Xác thực tài khoản của bạn</p>
        </div>

        <!-- Body -->
        <div style="padding: 30px;">
            <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 16px; text-align: center;">Mã OTP của bạn</h2>
            <p style="font-size: 16px; color: #4b5563; margin-bottom: 16px;">Vui lòng sử dụng mã OTP dưới đây để xác thực tài khoản của bạn. Mã này có hiệu lực trong <strong>10 phút</strong>.</p>
            
            <div style="background-color: #f3f4f6; padding: 16px; border-radius: 8px; text-align: center; margin: 16px 0;">
                <span style="letter-spacing: 0.5rem; font-size: 28px; font-family: monospace; font-weight: bold; color: #f97316;">{{ $otp }}</span>
            </div>
            
            <p style="font-size: 16px; color: #4b5563; margin-top: 16px;">Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này hoặc liên hệ với chúng tôi qua <a href="mailto:support@fastfood.com" style="color: #f97316; text-decoration: underline;">support@fastfood.com</a>.</p>
            
            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ route('customer.verify.otp.show') }}" style="background-color: #f97316; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: 600; display: inline-block;">
                    Xác thực ngay
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eeeeee;">
            <p style="color: #777; font-size: 14px; margin-bottom: 5px;">
                © {{ date('Y') }} FastFood. Tất cả các quyền được bảo lưu.
            </p>
            <div style="margin-top: 10px;">
                <a href="{{ url('/terms') }}" style="color: #777; text-decoration: none; margin: 0 10px; font-size: 14px;">Điều khoản sử dụng</a>
                <a href="{{ url('/privacy') }}" style="color: #777; text-decoration: none; margin: 0 10px; font-size: 14px;">Chính sách bảo mật</a>
            </div>
        </div>
    </div>
</div>
@endsection

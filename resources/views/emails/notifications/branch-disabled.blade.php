@extends('emails.layouts.app')

@section('content')
<div style="background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #dc2626; padding: 20px; text-align: center;">
            <h1 style="color: #ffffff; font-size: 24px; margin: 0;">Thông Báo Chi Nhánh Bị Vô Hiệu Hóa</h1>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <h2 style="font-size: 18px; color: #1f2937; margin-top: 0;">Xin chào {{ $manager->full_name }},</h2>

            <p style="color: #4b5563; font-size: 16px; line-height: 1.6;">
                Chúng tôi xin thông báo rằng chi nhánh mà bạn đang quản lý
                <strong style="color: #dc2626;">{{ $branch->name }}</strong> đã bị vô hiệu hóa.
            </p>

            <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0;">
                <h3 style="color: #dc2626; font-size: 16px; margin-top: 0; margin-bottom: 10px;">⚠️ Thông tin chi nhánh bị vô hiệu hóa:</h3>
                <table style="width: 100%; color: #4b5563; font-size: 15px;">
                    <tr>
                        <td style="padding: 8px 0; width: 120px;"><strong>Tên chi nhánh:</strong></td>
                        <td style="padding: 8px 0;">{{ $branch->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Địa chỉ:</strong></td>
                        <td style="padding: 8px 0;">{{ $branch->address }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Số điện thoại:</strong></td>
                        <td style="padding: 8px 0;">{{ $branch->phone }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Thời gian vô hiệu hóa:</strong></td>
                        <td style="padding: 8px 0;">{{ now()->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>

            <div style="background-color: #f0f9ff; border-left: 4px solid: #0ea5e9; padding: 15px; margin: 20px 0;">
                <h3 style="color: #0ea5e9; font-size: 16px; margin-top: 0; margin-bottom: 10px;">ℹ️ Lưu ý quan trọng:</h3>
                <ul style="color: #4b5563; font-size: 15px; margin: 0; padding-left: 20px;">
                    <li style="margin-bottom: 8px;">Tài khoản quản lý của bạn vẫn hoạt động bình thường</li>
                    <li style="margin-bottom: 8px;">Chi nhánh tạm thời không thể nhận đơn hàng mới</li>
                    <li style="margin-bottom: 8px;">Các đơn hàng đang xử lý sẽ được hoàn thành</li>
                    <li style="margin-bottom: 8px;">Vui lòng liên hệ quản trị viên để biết thêm chi tiết</li>
                </ul>
            </div>

            <p style="color: #4b5563; font-size: 16px; line-height: 1.6;">
                Nếu bạn có bất kỳ câu hỏi nào về việc vô hiệu hóa chi nhánh này, vui lòng liên hệ với bộ phận quản trị để được hỗ trợ.
            </p>

            <div style="margin: 30px 0; text-align: center;">
                <a href="{{ url('/admin/login') }}" style="display: inline-block; background-color: #4361ee; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 16px; margin-right: 10px;">
                    Đăng Nhập Hệ Thống
                </a>
                <a href="{{ url('/admin/contact') }}" style="display: inline-block; background-color: #6b7280; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 16px;">
                    Liên Hệ Hỗ Trợ
                </a>
            </div>

            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-top: 30px;">
                Trân trọng,<br>
                {{ config('app.name') }} Team
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f3f4f6; padding: 15px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 14px; margin: 0;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Bảo lưu mọi quyền.
            </p>
            <p style="color: #6b7280; font-size: 12px; margin: 5px 0 0 0;">
                Email này được gửi tự động, vui lòng không trả lời trực tiếp.
            </p>
        </div>
    </div>
</div>
@endsection
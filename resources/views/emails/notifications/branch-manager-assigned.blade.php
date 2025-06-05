@extends('emails.layouts.app')

@section('content')
<div style="background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #4361ee; padding: 20px; text-align: center;">
            <h1 style="color: #ffffff; font-size: 24px; margin: 0;">Phân Công Quản Lý Chi Nhánh</h1>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <h2 style="font-size: 18px; color: #1f2937; margin-top: 0;">Xin chào {{ $manager->full_name }},</h2>

            <p style="color: #4b5563; font-size: 16px; line-height: 1.6;">
                Chúng tôi xin thông báo rằng bạn đã được phân công làm quản lý cho chi nhánh
                <strong style="color: #4361ee;">{{ $branch->name }}</strong>.
            </p>

            <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <h3 style="color: #1f2937; font-size: 16px; margin-top: 0;">Thông tin chi nhánh:</h3>
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
                        <td style="padding: 8px 0;"><strong>Email:</strong></td>
                        <td style="padding: 8px 0;">{{ $branch->email ?? 'Không có' }}</td>
                    </tr>
                </table>
            </div>

            <p style="color: #4b5563; font-size: 16px; line-height: 1.6;">
                Vui lòng đăng nhập vào hệ thống để quản lý chi nhánh của bạn.
            </p>

            <div style="margin: 30px 0; text-align: center;">
                <a href="{{ url('/admin/login') }}" style="display: inline-block; background-color: #4361ee; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 16px;">
                    Đăng Nhập Vào Hệ Thống
                </a>
            </div>

            <p style="color: #4b5563; font-size: 16px; line-height: 1.6;">
                Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với quản trị viên.
            </p>

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
        </div>
    </div>
</div>
@endsection

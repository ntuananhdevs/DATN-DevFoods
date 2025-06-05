@extends('emails.layouts.app')

@section('content')
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; color: #333333;">
  <!-- Main Container -->
  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #f5f5f5; padding: 20px;">
    <tr>
      <td align="center" style="padding: 20px 0;">
        <!-- Email Content -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
          <!-- Header -->
          <tr>
            <td align="center" bgcolor="#f97316" style="padding: 30px 20px;">
              <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">Poly Crispy Wings</h1>
              <div style="width: 50px; height: 3px; background-color: #ffffff; margin: 12px auto 8px;"></div>
              <p style="color: #fff9f5; margin: 0; font-size: 16px; font-weight: 500;">Xác thực tài khoản</p>
            </td>
          </tr>
          
          <!-- OTP icon -->
          <tr>
            <td align="center" style="padding: 0;">
              <div style="margin-top: -25px; display: inline-block;">
                <table border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td align="center" bgcolor="#ffffff" style="border-radius: 50%; padding: 15px; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);">
                      <img src="https://cdn-icons-png.flaticon.com/512/2343/2343701.png" alt="OTP Verification" width="50" height="50" style="display: block;">
                    </td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
          
          <!-- Content -->
          <tr>
            <td style="padding: 30px 30px 20px;">
              <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin: 0 0 20px 0; text-align: center;">Mã OTP của bạn</h2>
              
              <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
                Vui lòng sử dụng mã OTP dưới đây để xác thực tài khoản của bạn. Mã này có hiệu lực trong <strong>10 phút</strong>.
              </p>
              
              <!-- OTP Code Box -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                <tr>
                  <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; border: 2px dashed #f97316; border-radius: 12px; padding: 20px;">
                      <tr>
                        <td align="center">
                          <p style="margin: 0 0 10px 0; font-size: 14px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Mã xác thực</p>
                          <span style="letter-spacing: 8px; font-size: 32px; font-family: 'Courier New', monospace; font-weight: bold; color: #f97316; display: block; margin: 10px 0;">{{ $otp }}</span>
                          <p style="margin: 10px 0 0 0; font-size: 12px; color: #999;">Nhập mã này để xác thực</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              
              <!-- Button -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 35px 0;">
                <tr>
                  <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" bgcolor="#f97316" style="border-radius: 8px; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.25);">
                          <a href="{{ route('customer.verify.otp.show') }}" target="_blank" style="display: inline-block; padding: 16px 36px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px;">Xác thực ngay</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              
              <!-- Security notice -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fff9f5; border-left: 4px solid #f97316; padding: 15px; margin: 25px 0; border-radius: 4px;">
                <tr>
                  <td>
                    <p style="margin: 0; font-size: 14px; color: #666;">
                      <strong>Lưu ý bảo mật:</strong> Mã OTP này chỉ có hiệu lực trong 10 phút. Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này hoặc <a href="mailto:support@fastfood.com" style="color: #f97316; text-decoration: none;">liên hệ với chúng tôi</a> ngay lập tức.
                    </p>
                  </td>
                </tr>
              </table>
              
              <!-- Tips -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0f9ff; border: 1px solid #bfdbfe; padding: 15px; margin: 25px 0; border-radius: 4px;">
                <tr>
                  <td>
                    <p style="margin: 0 0 10px 0; font-size: 14px; color: #1e40af; font-weight: 600;">💡 Mẹo bảo mật:</p>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #1e40af;">
                      <li>Không chia sẻ mã OTP với bất kỳ ai</li>
                      <li>Chỉ nhập mã trên trang web chính thức</li>
                      <li>Kiểm tra URL trước khi nhập mã</li>
                    </ul>
                  </td>
                </tr>
              </table>
              
              <p style="font-size: 16px; margin-top: 25px; margin-bottom: 5px;">Trân trọng,</p>
              <p style="font-size: 16px; font-weight: 500; color: #f97316; margin-top: 0;">Đội ngũ Poly Crispy Wings</p>
            </td>
          </tr>
          
          <!-- Help section -->
          <tr>
            <td bgcolor="#fafafa" style="padding: 20px 30px; border-top: 1px solid #eeeeee;">
              <p style="font-size: 14px; color: #666; margin: 0 0 10px 0; text-align: center;">Cần hỗ trợ? Liên hệ với chúng tôi qua:</p>
              <p style="text-align: center; margin: 0;">
                <a href="mailto:support@fastfood.com" style="color: #f97316; text-decoration: none; margin: 0 10px; font-size: 14px;">support@fastfood.com</a>
                <span style="color: #ddd;">|</span>
                <a href="tel:+84123456789" style="color: #f97316; text-decoration: none; margin: 0 10px; font-size: 14px;">0123 456 789</a>
              </p>
            </td>
          </tr>
          
          <!-- Footer -->
          <tr>
            <td align="center" style="padding: 20px; border-top: 1px solid #eeeeee; color: #999; font-size: 12px;">
              <p style="margin: 0 0 10px 0;">© {{ date('Y') }} Poly Crispy Wings. Tất cả quyền được bảo lưu.</p>
              <p style="margin: 0 0 10px 0;">
                <a href="{{ url('/terms') }}" style="color: #999; text-decoration: none; margin: 0 8px;">Điều khoản sử dụng</a>
                <span style="color: #ddd;">•</span>
                <a href="{{ url('/privacy') }}" style="color: #999; text-decoration: none; margin: 0 8px;">Chính sách bảo mật</a>
              </p>
              <p style="margin: 15px 0 0 0;">
                <a href="#" style="display: inline-block; margin: 0 5px;">
                  <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="24" height="24" alt="Facebook" style="display: block;">
                </a>
                <a href="#" style="display: inline-block; margin: 0 5px;">
                  <img src="https://cdn-icons-png.flaticon.com/512/3955/3955024.png" width="24" height="24" alt="Instagram" style="display: block;">
                </a>
                <a href="#" style="display: inline-block; margin: 0 5px;">
                  <img src="https://cdn-icons-png.flaticon.com/512/3670/3670151.png" width="24" height="24" alt="Twitter" style="display: block;">
                </a>
              </p>
            </td>
          </tr>
        </table>
        
        <!-- App promotion -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 20px auto 0;">
          <tr>
            <td align="center">
              <p style="color: #999; font-size: 13px; margin-bottom: 10px;">Tải ứng dụng Poly Crispy Wings để đặt món dễ dàng hơn</p>
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding: 0 5px;">
                    <a href="#" style="display: inline-block;">
                      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/2560px-Google_Play_Store_badge_EN.svg.png" alt="Google Play" width="120" style="display: block;">
                    </a>
                  </td>
                  <td style="padding: 0 5px;">
                    <a href="#" style="display: inline-block;">
                      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Download_on_the_App_Store_Badge.svg/2560px-Download_on_the_App_Store_Badge.svg.png" alt="App Store" width="120" style="display: block;">
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
@endsection

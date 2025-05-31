<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Mật khẩu Tài khoản Tài xế</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .password-box {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .password-text {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            word-break: break-all;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table th, .info-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .info-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .security-notice {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .reason-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Reset Mật khẩu Tài khoản</h1>
            <p>{{ $data['companyName'] ?? 'DevFoods' }} - Hệ thống Quản lý Tài xế</p>
        </div>
        
        <div class="content">
            <h2>Xin chào {{ $data['driver']['full_name'] }}</h2>
            
            <div class="alert">
                <strong>⚠️ Thông báo quan trọng:</strong> Mật khẩu tài khoản tài xế của bạn đã được quản trị viên reset vào lúc {{ $data['resetDate'] }}.
            </div>

            @if(!empty($data['reason']))
            <div class="reason-box">
                <strong>📝 Lý do reset mật khẩu:</strong><br>
                {{ $data['reason'] }}
            </div>
            @endif

            <h3>Thông tin tài khoản của bạn:</h3>
            <table class="info-table">
                <tr>
                    <th>Tên tài xế:</th>
                    <td>{{ $data['driver']['full_name'] }}</td>
                </tr>
                <tr>
                    <th>Email đăng nhập:</th>
                    <td>{{ $data['driver']['email'] }}</td>
                </tr>
                <tr>
                    <th>ID tài xế:</th>
                    <td>#{{ $data['driver']['id'] }}</td>
                </tr>
                <tr>
                    <th>Thời gian reset:</th>
                    <td>{{ $data['resetDate'] }}</td>
                </tr>
            </table>

            <h3>🔑 Mật khẩu mới của bạn:</h3>
            <div class="password-box">
                <div class="password-text">{{ $data['newPassword'] }}</div>
                <p style="margin-top: 10px; font-size: 14px; color: #6c757d;">
                    <em>Vui lòng sao chép chính xác mật khẩu trên</em>
                </p>
            </div>

            <div class="security-notice">
                <h4>🛡️ Hướng dẫn bảo mật:</h4>
                <ul>
                    <li><strong>Đăng nhập ngay lập tức</strong> và đổi mật khẩu thành mật khẩu cá nhân của bạn</li>
                    <li><strong>Không chia sẻ</strong> mật khẩu này với bất kỳ ai</li>
                    <li><strong>Xóa email này</strong> sau khi đã đổi mật khẩu thành công</li>
                    <li>Sử dụng mật khẩu mạnh có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ $data['loginUrl'] }}" class="login-button">
                    🚀 Đăng nhập ngay
                </a>
            </div>

            <p><strong>Lưu ý quan trọng:</strong></p>
            <ul>
                <li>Bạn sẽ được yêu cầu đổi mật khẩu ngay khi đăng nhập lần đầu</li>
                <li>Nếu bạn không thực hiện việc reset này, vui lòng liên hệ với quản trị viên ngay lập tức</li>
                <li>Mật khẩu tạm thời này sẽ hết hiệu lực sau 24 giờ nếu không được sử dụng</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>{{ $data['companyName'] ?? 'DevFoods' }}</strong></p>
            <p>Nếu bạn cần hỗ trợ, vui lòng liên hệ: 
                <a href="mailto:{{ $data['supportEmail'] }}">{{ $data['supportEmail'] }}</a>
            </p>
            <p style="font-size: 12px; color: #adb5bd;">
                Email này được gửi tự động từ hệ thống. Vui lòng không trả lời email này.
            </p>
        </div>
    </div>
</body>
</html> 
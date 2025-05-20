<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Tài Xế</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .notification {
            position: fixed;
            bottom: 20px;
            right: -400px;
            background: #ff4444;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .notification.show {
            right: 20px;
        }
        
        .notification.success {
            background: #00C851;
        }
        
        .notification.error {
            background: #ff4444;
        }
        
        body {
            font-family: 'Nunito', 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: url('/placeholder.svg?height=1080&width=1920');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }
        
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(45deg, #FF5722, #FF9800);
            padding: 30px 20px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background-color: white;
            border-radius: 50% 50% 0 0;
        }
        
        .login-header img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid white;
            padding: 5px;
            background-color: white;
            margin-bottom: 10px;
        }
        
        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-form {
            padding: 30px 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #f9f9f9;
        }
        
        .input-with-icon input:focus {
            border-color: #FF7043;
            box-shadow: 0 0 0 3px rgba(255, 112, 67, 0.2);
            outline: none;
            background-color: white;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #FF7043;
            font-size: 18px;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember {
            display: flex;
            align-items: center;
        }
        
        .remember input {
            margin-right: 8px;
            accent-color: #FF7043;
        }
        
        .remember label {
            font-size: 14px;
            color: #666;
        }
        
        .forgot {
            font-size: 14px;
            color: #FF7043;
            text-decoration: none;
            font-weight: 600;
        }
        
        .forgot:hover {
            text-decoration: underline;
        }
        
        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(45deg, #FF5722, #FF9800);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.3);
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 87, 34, 0.4);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        /* Phần khuyến nghị đổi mật khẩu */
        .password-reminder {
            margin: 25px 0;
            padding: 15px;
            background-color: #FFF3E0;
            border-left: 4px solid #FF9800;
            border-radius: 6px;
            display: flex;
            align-items: flex-start;
        }
        
        .password-reminder i {
            color: #FF9800;
            font-size: 20px;
            margin-right: 12px;
            margin-top: 2px;
        }
        
        .password-reminder-content h4 {
            color: #E65100;
            font-size: 15px;
            margin-bottom: 5px;
        }
        
        .password-reminder-content p {
            color: #666;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 8px;
        }
        
        .change-password-btn {
            display: inline-block;
            padding: 6px 12px;
            background-color: #FF9800;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .change-password-btn:hover {
            background-color: #F57C00;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #ddd;
        }
        
        .divider span {
            padding: 0 15px;
            color: #777;
            font-size: 13px;
        }
        
        .quick-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .quick-login-btn {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eee;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .quick-login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .quick-login-btn i {
            font-size: 20px;
        }
        
        .google i {
            color: #DB4437;
        }
        
        .facebook i {
            color: #4267B2;
        }
        
        .qrcode i {
            color: #333;
        }
        
        .fingerprint i {
            color: #009688;
        }
        
        .help-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
        
        .help-text a {
            color: #FF7043;
            text-decoration: none;
            font-weight: 600;
        }
        
        .help-text a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-container {
                border-radius: 12px;
            }
            
            .login-header {
                padding: 25px 15px;
            }
            
            .login-form {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            {{-- <img src="/placeholder.svg?height=70&width=70" alt="Logo Công Ty"> --}}
            <h1>Đăng Nhập Tài Xế</h1>
            <p>Đăng nhập để bắt đầu nhận đơn hàng</p>
        </div>
        
        <div class="login-form">
            <form action="{{ route('login.submit') }}" method="post">
                @csrf
                @if($errors->any())
                    <div class="notification error">
                        {{ $errors->first() }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="notification success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" placeholder="Nhập số điện thoại hoặc email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" placeholder="Nhập mật khẩu">
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember">
                        <input type="checkbox" id="remember">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    
                    <a href="#" class="forgot">Quên mật khẩu?</a>
                </div>
                
                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i> ĐĂNG NHẬP
                </button>
            </form>
            
            <!-- Phần khuyến nghị đổi mật khẩu -->
            <div class="password-reminder">
                <i class="fas fa-shield-alt"></i>
                <div class="password-reminder-content">
                    <h4>Khuyến nghị đổi mật khẩu</h4>
                    <p>Để đảm bảo an toàn cho tài khoản, bạn nên đổi mật khẩu định kỳ 3 tháng một lần. Mật khẩu mạnh sẽ giúp bảo vệ thông tin cá nhân của bạn.</p>
                    <a href="#" class="change-password-btn">Đổi mật khẩu ngay</a>
                </div>
            </div>
            
          
            
            {{-- <div class="quick-login">
                <div class="quick-login-btn google">
                    <i class="fab fa-google"></i>
                </div>
                <div class="quick-login-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                </div>
                <div class="quick-login-btn qrcode">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="quick-login-btn fingerprint">
                    <i class="fas fa-fingerprint"></i>
                </div>
            </div> --}}
            
            <div class="help-text">
                Bạn chưa có tài khoản? <a href="#">Đăng ký ngay</a>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifications = document.querySelectorAll('.notification');
        
        notifications.forEach(notification => {
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 500);
            }, 5000);
        });
    });
</script>
</body>
</html>
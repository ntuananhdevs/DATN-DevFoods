<!DOCTYPE html>
<html>
<head>
    <title>Đơn đăng ký tài xế được chấp nhận</title>
</head>
<body>
    <h2>Xin chào {{ $data['driver']['full_name'] }},</h2>
    
    <p>Chúng tôi rất vui mừng thông báo rằng đơn đăng ký tài xế của bạn đã được chấp nhận!</p>

    <p>Thông tin đăng nhập của bạn:</p>
    <ul>
        <li>Tên đăng nhập: {{ $data['driver']['phone_number'] }}</li>
        <li>Mật khẩu: {{ $data['password'] }}</li>
    </ul>

    <p>Vui lòng đăng nhập và đổi mật khẩu của bạn tại: <a href="{{ url('/driver/login') }}">Đăng nhập</a></p>

    <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>

    <p>Trân trọng,<br>DevFoods Team</p>
</body>
</html> 
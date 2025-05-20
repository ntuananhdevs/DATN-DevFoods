<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài xế - DevFoods</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
        }
        .login-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .login-box {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .login-header {
            background-color: #4f46e5;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .login-form {
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out;
        }
        .form-input:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .btn-primary {
            background-color: #4f46e5;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: background-color 0.15s ease-in-out;
            width: 100%;
            text-align: center;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-warning {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fef3c7;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="flex justify-center items-center min-h-screen">
            <div class="login-box w-full max-w-md">
                <div class="login-header">
                    <h1 class="text-2xl font-bold">Đăng nhập tài xế</h1>
                    <p class="mt-2">Nhập thông tin đăng nhập để tiếp tục</p>
                </div>
                
                <div class="login-form">
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                    @endif

                    <form action="{{ route('driver.login.submit') }}" method="post">
                        @csrf
                        
                        <div class="form-group">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="text" id="phone_number" name="phone_number" class="form-input" value="{{ old('phone_number') }}" placeholder="Nhập số điện thoại đã đăng ký">
                            @error('phone_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" id="password" name="password" class="form-input" placeholder="Nhập mật khẩu">
                            @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4 text-center text-sm text-gray-600">
                        <p>Bạn chưa có tài khoản? <a href="{{ route('driver.apply') }}" class="text-indigo-600 hover:text-indigo-800">Đăng ký làm tài xế</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Đăng nhập Chi nhánh</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    body {
      background-color: #f9f9f9;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .login-container {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      width: 120%;
      max-width: 750px;
      padding: 32px;  
    }

    .login-title {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #111;
    }

    .login-subtitle {
      font-size: 14px;
      color: #666;
      margin-bottom: 24px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 8px;
      color: #111;
    }

    .password-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .forgot-password {
      font-size: 14px;
      color: #111;
      text-decoration: none;
    }

    .forgot-password:hover {
      text-decoration: underline;
    }

    .form-input {
      width: 100%;
      padding: 10px 12px;
      font-size: 14px;
      border: 1px solid #ddd;
      border-radius: 4px;
      transition: border-color 0.2s;
    }

    .form-input:focus {
      outline: none;
      border-color: #000;
    }

    .remember-me {
      display: flex;
      align-items: center;
      margin-bottom: 24px;
    }

    .remember-me input {
      margin-right: 8px;
    }

    .remember-me label {
      font-size: 14px;
      color: #111;
    }

    .login-button {
      width: 100%;
      padding: 12px;
      background-color: #111;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.2s;
    }

    .login-button:hover {
      background-color: #000;
    }

    .login-button svg {
      margin-left: 8px;
    }
  </style>
</head>
<body>
    <main>
        @yield('content')
    </main>

    {{-- Bootstrap JS (optional) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
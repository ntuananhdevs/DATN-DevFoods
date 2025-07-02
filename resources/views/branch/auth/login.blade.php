@extends('layouts.branch.auth')

@section('content')
<div class="login-container">
    <h1 class="login-title">Đăng nhập Chi nhánh</h1>
    <p class="login-subtitle">Nhập thông tin đăng nhập của bạn để tiếp tục</p>
    
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('branch.login.submit') }}">                                                             
        @csrf
      <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input type="text" id="email" name="email" class="form-input @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}">
        @error('email')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
        </div>
      
      <div class="form-group">
        <div class="password-header">
          <label for="password" class="form-label">Mật khẩu</label>
          <a href="#" class="forgot-password">Quên mật khẩu?</a>
        </div>
        <input type="password" id="password" name="password" class="form-input" >
        @error('password')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror

        @if(session('error'))
        <div class="text-danger mt-1">{{ session('error') }}</div>
        @endif

        @if(session('cooldown'))
            <div class="text-danger mt-1" id="countdownBox">
                <strong>Thông báo:</strong> Bạn đã nhập sai quá nhiều lần.
                Vui lòng thử lại sau <span id="countdown">{{ session('cooldown') }}</span> giây.
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let seconds = parseInt('{{ session('cooldown') }}');
                    const countdown = document.getElementById('countdown');
                    const countdownBox = document.getElementById('countdownBox');

                    const interval = setInterval(function () {
                        seconds--;
                        if (seconds <= 0) {
                            clearInterval(interval);
                            countdownBox.style.display = 'none';
                        } else {
                            countdown.textContent = seconds;
                        }
                    }, 1000);
                });
            </script>
        @endif
      
      </div>
      
      <div class="remember-me">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Ghi nhớ đăng nhập</label>
      </div>
      
      <button type="submit" class="login-button">
        Đăng nhập
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14"></path>
          <path d="M12 5l7 7-7 7"></path>
        </svg>
      </button>
    </form>
  </div>
@endsection 
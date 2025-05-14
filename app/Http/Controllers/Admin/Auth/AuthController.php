<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request): RedirectResponse
{
    $credentials = $request->validate([
        'email' => ['required', 'email:dns'],
        'password' => ['required'],
    ], [
        'email.required' => 'Vui lòng nhập địa chỉ email.',
        'email.email' => 'Địa chỉ email không hợp lệ.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
    ]);

    // Khóa (key) rate limit dựa trên IP và email
    $key = Str::lower($request->input('email')) . '|' . $request->ip();

    if (RateLimiter::tooManyAttempts($key, 5)) {
        $seconds = RateLimiter::availableIn($key);
        return back()
            ->withInput()
            ->with('cooldown', $seconds)
            ->with('ratelimit', "Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.");
    }

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        RateLimiter::clear($key); // reset lại khi đăng nhập thành công
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    RateLimiter::hit($key, 60); // mỗi lần sai tăng đếm, key tồn tại trong 60 giây

    return back()->with('error', 'Email hoặc mật khẩu không chính xác')->withInput();
}

    // Xử lý đăng xuất
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'Đăng xuất thành công.');

        return redirect()->route('admin.login');
    }
}

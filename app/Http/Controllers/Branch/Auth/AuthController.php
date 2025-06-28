<?php

namespace App\Http\Controllers\Branch\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('branch.auth.login');
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

        $key = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput()
                ->with('cooldown', $seconds)
                ->with('ratelimit', "Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.");
        }

        $user = \App\Models\User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, 60);
            return back()->with('error', 'Email hoặc mật khẩu không chính xác')->withInput();
        }

        // Kiểm tra user có role manager không
        if (!$user->hasRole('manager')) {
            RateLimiter::hit($key, 60);
            return back()->with('error', 'Bạn không có quyền truy cập vào hệ thống chi nhánh')->withInput();
        }

        // Kiểm tra user có được gán branch không
        if (!$user->branch) {
            RateLimiter::hit($key, 60);
            return back()->with('error', 'Tài khoản chưa được gán chi nhánh. Vui lòng liên hệ quản trị viên.')->withInput();
        }

        // Kiểm tra branch có active không
        if (!$user->branch->active) {
            RateLimiter::hit($key, 60);
            return back()->with('error', 'Chi nhánh của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.')->withInput();
        }

        // Đăng nhập thành công
        Auth::guard('manager')->login($user, $request->boolean('remember'));
        RateLimiter::clear($key);
        $request->session()->regenerate();
        
        return redirect()->route('branch.dashboard');
    }

    // Xử lý đăng xuất
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('manager')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'Đăng xuất thành công.');

        return redirect()->route('branch.login');
    }
} 
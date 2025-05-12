<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Customer\RegisterRequest;
use Exception;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        try {
            return view('customer.auth.login');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
            }

            return back()->withErrors([
                'email' => 'Thông tin đăng nhập không chính xác.',
            ])->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đăng nhập thất bại: ' . $e->getMessage());
        }
    }

    public function showRegisterForm()
    {
        try {
            return view('customer.auth.register');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_name' => $data['user_name'],
                'full_name' => $data['full_name'],
                'phone' => '',
                'avatar' => '',
                'balance' => 0,
                'role_id' => 2,
            ]);
            Auth::login($user);
            return redirect('/login')->with('success', 'Đăng ký thành công!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đăng ký thất bại: ' . $e->getMessage())->withInput();
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Đăng xuất thành công!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đăng xuất thất bại: ' . $e->getMessage());
        }
    }
}

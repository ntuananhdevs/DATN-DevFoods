<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Mail\EmailFactory;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showForgotPasswordForm()
    {
        try {
            return view('driver.auth.forgot_password');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:drivers,email'
        ]);

        $otp = rand(100000, 999999);
        $email = $request->email;

        // Lưu OTP vào session hoặc cache
        Cache::put('otp_'.$email, $otp, now()->addMinutes(15));

        // Gửi email OTP
        EmailFactory::sendNotification(
            'Mã OTP đặt lại mật khẩu',
            'Mã OTP của bạn là: ' . $otp . '. Mã có hiệu lực trong 15 phút.',
            [],
            $email
        );

        return response()->json([
            'success' => true,
            'message' => 'Mã OTP đã được gửi đến email của bạn.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        if ($request->otp != Session::get('otp') || now()->gt(Session::get('otp_expires'))) {
            return back()->with('error', 'Mã OTP không hợp lệ hoặc đã hết hạn');
        }

        return redirect()->route('driver.reset_password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8'
        ]);

        $driver = Driver::where('email', Session::get('reset_email'))->first();
        $driver->password = Hash::make($request->password);
        $driver->save();

        Session::forget(['otp', 'otp_expires', 'reset_email']);

        return redirect()->route('driver.login')->with('success', 'Đặt lại mật khẩu thành công');
    }

    public function showLoginForm()
    {
        try {
            return view('driver.auth.login');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: '. $e->getMessage());
        }
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string',
                'password' => 'required|string',
            ], [
                'phone_number.required' => 'Vui lòng nhập số điện thoại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $driver = Driver::where('phone_number', $request->phone_number)->first();

            if (!$driver) {
                return back()->withErrors([
                    'phone_number' => 'Số điện thoại không tồn tại trong hệ thống',
                ])->withInput();
            }

            // Debug password
            // logger()->debug('Password input: ' . $request->password);
            // logger()->debug('Password in DB: ' . $driver->password);
            // logger()->debug('Hash check result: ' . (Hash::check($request->password, $driver->password) ? 'true' : 'false'));
            
            if (!Hash::check($request->password, $driver->password)) {
                return back()->withErrors([
                    'password' => 'Mật khẩu không chính xác',
                ])->withInput();
            }
            
            session(['driver_id' => $driver->id]);
            session(['driver_name' => $driver->full_name]);
            session(['driver_phone' => $driver->phone_number]);
            session(['driver_logged_in' => true]);
            
            
            return redirect()->route('driver.home')->with('success', 'Đăng nhập thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        try {
            session()->forget(['driver_id', 'driver_name', 'driver_phone', 'driver_logged_in', 'first_login']);
            
            return redirect()->route('driver.login')->with('success', 'Đăng xuất thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
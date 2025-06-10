<?php

namespace App\Http\Controllers\Driver\Auth;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Mail\EmailFactory;
use Tzsk\Otp\Facades\Otp;
use App\Jobs\SendOTPJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function showResetPasswordForm($driver_id)
    {
        return view('driver.auth.reset_password', ['driver_id' => $driver_id]);
    }
    public function showLoginForm()
    {
        try {
            return view('driver.auth.login');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        $phone = Str::lower($request->input('phone_number'));
        $key = 'login_attempts:' . $phone . '|' . $request->ip();

        // Kiểm tra vượt quá số lần thử
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $message = "Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.";

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 429);
            }

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Đã bị khóa tạm thời',
                'message' => $message
            ]);

            return back()->with('error', $message);
        }

        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'password' => 'required',
            ], [
                'phone_number.required' => 'Vui lòng nhập số điện thoại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'errors' => $validator->errors()->toArray()
                    ], 422);
                }

                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => $message
                ]);

                return back()->withErrors($validator)->withInput();
            }

            // Tìm tài xế
            $driver = Driver::where('phone_number', $phone)->first();

            if (!$driver) {
                RateLimiter::hit($key, 60); // chỉ hit khi sai
                $message = 'Số điện thoại không tồn tại trong hệ thống';

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => $message
                ]);

                return back()->withErrors([
                    'phone_number' => $message,
                ])->withInput();
            }

            // Kiểm tra mật khẩu
            if (!Hash::check($request->password, $driver->password)) {
                RateLimiter::hit($key, 60); // chỉ hit khi sai
                $message = 'Mật khẩu không chính xác';

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => $message
                ]);

                return back()->withErrors([
                    'password' => $message,
                ])->withInput();
            }

            // Đăng nhập thành công
            RateLimiter::clear($key); // reset khi đúng

            // Sử dụng Laravel Auth guard để đăng nhập
            Auth::guard('driver')->login($driver);
            
            // Vẫn giữ session cho backward compatibility
            session([
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'driver_phone' => $driver->phone_number,
                'driver_logged_in' => true,
            ]);

            $firstLogin = $driver->created_at->eq($driver->updated_at);
            if ($firstLogin) {
                session(['first_login' => true]);
            }

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'first_login' => $firstLogin
                ]);
            }

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đăng nhập thành công!'
            ]);

            return redirect()->route('driver.dashboard')->with('success', 'Đăng nhập thành công!');
        } catch (Exception $e) {
            $message = 'Đã xảy ra lỗi: ' . $e->getMessage();

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi hệ thống',
                'message' => $message
            ]);

            return back()->with('error', $message)->withInput();
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

    public function showForgotPasswordForm()
    {
        return view('driver.auth.forgot_password');
    }

    public function SendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng'
        ]);

        $driver = Driver::where('email', $request->email)->first();

        if (!$driver) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống']);
        }

        $otp = Otp::generate($driver->email);
        $driver->otp = $otp;
        $driver->expires_at = now()->addMinutes(30);
        $driver->save();

        SendOTPJob::dispatch($driver->email, $otp);

        return redirect()->route('driver.verify_otp', ['driver_id' => $driver->id])
            ->with('success', 'Mã OTP đã được gửi đến email của bạn')
            ->with('toast', ['type' => 'success', 'title' => 'Thành công', 'message' => 'Mã OTP đã được gửi!']);
    }


    public function showVerifyOTPForm($driver_id)
    {
        return view('driver.auth.verify_otp', ['driver_id' => $driver_id]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'driver_id' => 'required|integer',
        ]);

        $driver = Driver::find($request->driver_id);

        if (!$driver || !$driver->otp || !$driver->expires_at) {
            return back()->withErrors(['otp' => 'Không thể xác thực OTP.']);
        }

        if (now()->gt($driver->expires_at)) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn.']);
        }

        if ($request->otp != $driver->otp) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác.']);
        }

        // Xóa OTP sau khi xác thực thành công
        $driver->otp = null;
        $driver->expires_at = null;
        $driver->save();

        return redirect()->route('driver.reset_password', ['driver_id' => $driver->id])
            ->with('success', 'Mã OTP hợp lệ. Vui lòng đặt lại mật khẩu.');
    }


    public function processResetPassword(Request $request, $driver_id)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8'
            ], [
                'password.required' => 'Mật khẩu không được để trống',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
                'password_confirmation.required' => 'Xác nhận mật khẩu không được để trống',
                'password_confirmation.confirmed' => 'Xác nhận mật khẩu không khớp với mật khẩu'
            ]);

            $driver = Driver::findOrFail($driver_id);
            $driver->password = Hash::make($request->password); // Fix: Add hashing
            $driver->save();

            // Clear OTP cache
            Cache::forget('password_reset_otp_' . $driver_id);

            return redirect()->route('driver.login')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Mật khẩu đã được đặt lại thành công!'
                ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý đổi mật khẩu khi đăng nhập lần đầu
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|same:password'
            ], [
                'password.required' => 'Mật khẩu không được để trống',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
                'password_confirmation.required' => 'Xác nhận mật khẩu không được để trống',
                'password_confirmation.same' => 'Xác nhận mật khẩu không khớp với mật khẩu'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $driverId = session('driver_id');
            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.'
                ], 401);
            }

            $driver = Driver::find($driverId);
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài xế không tồn tại.'
                ], 404);
            }

            // Gán password, model của bạn tự hash
            $driver->password = Hash::make($request->password);
            $driver->save();

            // Cập nhật lại session nếu cần
            session(['driver_id' => $driver->id]);
            session(['driver_name' => $driver->full_name]);
            session(['driver_phone' => $driver->phone_number]);
            session(['driver_logged_in' => true]);

            // Trả về kết quả thành công
            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }


    public function resendOTP(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|integer|exists:drivers,id',
        ]);

        $driverId = $request->input('driver_id');
        $driver = Driver::find($driverId);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Tài xế không tồn tại.'
            ], 404);
        }

        // === BẮT ĐẦU: Thêm Rate Limit ===
        $key = 'resend_otp:' . $driver->email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Bạn đã gửi OTP quá nhiều lần. Vui lòng thử lại sau {$seconds} giây."
            ], 429);
        }

        RateLimiter::hit($key, 60);
        // === KẾT THÚC: Rate Limit ===

        $otp = Otp::generate($driver->email);
        $driver->otp = $otp;
        $driver->expires_at = now()->addMinutes(30);
        $driver->save();
        SendOTPJob::dispatch($driver->email, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi lại mã OTP thành công.'
        ]);
    }
}

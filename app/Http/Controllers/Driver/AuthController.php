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
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'password' => 'required',
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
            
            if (!Hash::check($request->password, $driver->password)) {
                return back()->withErrors([
                    'password' => 'Mật khẩu không chính xác',
                ])->withInput();
            }
            session([
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'driver_phone' => $driver->phone_number,
                'driver_logged_in' => true
            ]);
    
            // Kiểm tra: nếu chưa đổi mật khẩu (created_at == updated_at)
            $firstLogin = $driver->created_at->eq($driver->updated_at);
            if ($firstLogin) {
                session(['first_login' => true]);
            }
    
            return response()->json([
                'success' => true,
                'first_login' => $firstLogin
            ]);

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

    public function showForgotPasswordForm()
    {
        return view('driver.auth.forgot_password');
    }

    public function SendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $driver = Driver::where('email', $request->email)->first();

        if (!$driver) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống']);
        }

        $otp = rand(100000, 999999);

        Cache::put('password_reset_otp_' . $driver->id, $otp, now()->addMinutes(30));
        $content = '<div style="padding: 20px; background-color: #f2f2f2; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold;">
    Mã OTP của bạn là: ' . $otp . '
</div>';

        EmailFactory::sendNotification(
            'generic', // <- type
            ['content' => $content], // <- data
            'Mã OTP đặt lại mật khẩu', // <- subject
            $driver->email
        );

        return redirect()->route('driver.verify_otp', ['driver_id' => $driver->id])
            ->with('success', 'Mã OTP đã được gửi đến email của bạn');
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

        $driverId = $request->input('driver_id');
        $enteredOtp = $request->input('otp');
        $cachedOtp = Cache::get('password_reset_otp_' . $driverId);

        if (!$cachedOtp) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn hoặc không tồn tại.']);
        }

        if ($enteredOtp != $cachedOtp) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác.']);
        }

        return redirect()->route('driver.reset_password', ['driver_id' => $driverId])
            ->with('success', 'Mã OTP hợp lệ. Vui lòng đặt lại mật khẩu.');
    }


    public function processResetPassword(Request $request, $driver_id)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8'
            ]);

            $driver = Driver::findOrFail($driver_id);
            $driver->password = $request->password; // Fix: Add hashing
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
        $driver->password = $request->password;
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
}

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
            // Kiểm tra nếu request là AJAX
            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

            // Định nghĩa quy tắc validation và thông báo lỗi
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'password' => 'required',
            ], [
                'phone_number.required' => 'Vui lòng nhập số điện thoại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);

            // Xử lý lỗi validation
            if ($validator->fails()) {
                if ($isAjax) {
                    // Ensure consistent error message format
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->first(),
                        'errors' => $validator->errors()->toArray()
                    ], 422);
                }

                // Thêm toast thông báo cho non-AJAX request
                $errorMessage = $validator->errors()->first();
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => $errorMessage
                ]);

                return back()->withErrors($validator)->withInput();
            }

            // Kiểm tra tài khoản tồn tại
            $driver = Driver::where('phone_number', $request->phone_number)->first();

            if (!$driver) {
                $errorMessage = 'Số điện thoại không tồn tại trong hệ thống';

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }

                // Thêm toast thông báo
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => $errorMessage
                ]);

                return back()->withErrors([
                    'phone_number' => $errorMessage,
                ])->withInput();
            }

            // Kiểm tra mật khẩu
            if (!Hash::check($request->password, $driver->password)) {
                $errorMessage = 'Mật khẩu không chính xác';

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }

                // Thêm toast thông báo
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => $errorMessage
                ]);

                return back()->withErrors([
                    'password' => $errorMessage,
                ])->withInput();
            }

            // Đăng nhập thành công, thiết lập session
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

            // Trả về kết quả thành công
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'first_login' => $firstLogin
                ]);
            }

            // Thêm toast thông báo thành công
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đăng nhập thành công!'
            ]);

            return redirect()->route('driver.home')->with('success', 'Đăng nhập thành công!');
        } catch (Exception $e) {
            // Xử lý lỗi không mong muốn
            $errorMessage = 'Đã xảy ra lỗi: ' . $e->getMessage();

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            // Thêm toast thông báo lỗi
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi hệ thống',
                'message' => $errorMessage
            ]);

            return back()->with('error', $errorMessage)->withInput();
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
        ],[
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng'
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
            ->with('success', 'Mã OTP đã được gửi đến email của bạn')->with('toast', ['type' => 'success', 'title' => 'Thành công', 'message' => 'Mã OTP đã được gửi!']);
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
            return back()->withErrors(['otp' => 'OTP không chính xác. Vui lòng nhập lại.']);
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
            ],[
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
        $otp = random_int(100000, 999999);
        $cacheKey = 'password_reset_otp_' . $driverId;
        Cache::put($cacheKey, $otp, now()->addMinutes(2));

        Cache::put('password_reset_otp_' . $driver->id, $otp, now()->addMinutes(30));
        $content = '<div style="padding: 20px; background-color: #f2f2f2; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold;">
        Mã OTP của bạn là: ' . $otp . '
        </div>';
        EmailFactory::sendNotification(
            'generic',
            ['content' => $content],
            'Mã OTP đặt lại mật khẩu',
            $driver->email
        );
        return response()->json([
            'success' => true,
            'message' => 'Đã gửi lại mã OTP thành công.'
        ]);
    }
}
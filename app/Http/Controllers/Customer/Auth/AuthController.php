<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Rules\TurnstileRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\SendOTPJob;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Kiểm tra xem tài khoản có đang bị khóa tạm thời không
        $lockoutKey = 'login_lockout_' . $request->email;
        $failedAttemptsKey = 'login_attempts_' . $request->email;
        
        if (Cache::has($lockoutKey)) {
            $remainingSeconds = Cache::get($lockoutKey) - now()->timestamp;
            throw ValidationException::withMessages([
                'email' => ['Tài khoản đã bị khóa tạm thời. Vui lòng thử lại sau ' . $remainingSeconds . ' giây.'],
            ]);
        }

        // Kiểm tra người dùng tồn tại và mật khẩu đúng
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Tăng số lần đăng nhập thất bại
            $attempts = Cache::get($failedAttemptsKey, 0) + 1;
            Cache::put($failedAttemptsKey, $attempts, now()->addMinutes(5));

            // Nếu số lần thất bại vượt quá 5, khóa tài khoản trong 60 giây
            if ($attempts >= 5) {
                Cache::put($lockoutKey, now()->addSeconds(60)->timestamp, now()->addSeconds(60));
                Cache::forget($failedAttemptsKey);
                
                throw ValidationException::withMessages([
                    'email' => ['Bạn đã nhập sai mật khẩu quá 5 lần. Tài khoản đã bị khóa tạm thời trong 60 giây.'],
                ]);
            }

            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác. Còn ' . (5 - $attempts) . ' lần thử.'],
            ]);
        }

        // Xóa các key theo dõi khi đăng nhập thành công
        Cache::forget($failedAttemptsKey);
        Cache::forget($lockoutKey);

        // Kiểm tra tài khoản có bị khóa không
        if (!$user->active) {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'],
            ]);
        }

        // Đăng nhập
        Auth::login($user, $request->remember);

        // Kiểm tra vai trò và chuyển hướng phù hợp
        if ($this->hasRole($user, 'admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Chuyển hướng đến trang chủ của khách hàng
        return redirect()->intended(route('home'));
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        // Lấy người dùng hiện tại trước khi đăng xuất
        $user = Auth::user();
        
        // Đăng xuất người dùng
        Auth::logout();
        
        // Xóa remember token của người dùng
        if ($user) {
            $user->setRememberToken(null);
            $user->save();
        }
        
        // Vô hiệu hóa phiên hiện tại
        $request->session()->invalidate();
        
        // Tạo mới token CSRF
        $request->session()->regenerateToken();
        
        // Chuyển hướng về trang chủ
        return redirect()->route('customer.login');
    }

    /**
     * Kiểm tra người dùng có vai trò cụ thể không
     */
    private function hasRole(User $user, string $roleName): bool
    {
        return $user->roles()->where('name', $roleName)->exists();
    }

    /**
     * Hiển thị form đăng ký
     */
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cf-turnstile-response' => 'required', new TurnstileRule(),
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Địa chỉ email đã được sử dụng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'cf-turnstile-response.required' => 'Vui lòng hoàn thành xác minh bảo mật.',
        ]);

        try{
            // Tạo người dùng mới nhưng chưa active
            $user = User::create([
                'user_name' => explode('@', $request->email)[0],
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'active' => true,
            ]);

            // Gán vai trò khách hàng
            $customerRole = Role::where('name', 'customer')->first();
            if ($customerRole) {
                $user->roles()->attach($customerRole->id);
            }

            // Tạo và gửi OTP qua queue
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            SendOTPJob::dispatch($user->email, $otp)->onQueue('default');

            // Lưu thông tin user vào session để sử dụng sau khi xác thực OTP
            $request->session()->put('pending_user_id', $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Mã OTP đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Lỗi đăng ký: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['email' => ['Đã xảy ra lỗi. Vui lòng thử lại.']]
            ], 500);
        }
    }

    /**
     * Tạo và gửi mã OTP
     */
    private function sendOTP($email)
    {
        // Tạo mã OTP ngẫu nhiên 6 chữ số
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Gửi OTP qua queue
        SendOTPJob::dispatch($email, $otp)->onQueue('default');
        
        return $otp;
    }

    /**
     * Hiển thị form nhập OTP
     */
    public function showOTPForm(Request $request)
    {
        if (!$request->session()->has('pending_user_id')) {
            return redirect()->route('customer.register');
        }
        
        return view('customer.auth.verify-otp');
    }

    /**
     * Xác thực OTP
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('pending_user_id');
        if (!$userId) {
            return redirect()->route('customer.register');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('customer.register');
        }

        // Lấy OTP từ cache
        $cachedOTP = Cache::get('otp_' . $user->email);
        
        // Kiểm tra OTP
        if (!$cachedOTP || $cachedOTP !== $request->otp) {
            return back()->withErrors([
                'otp' => 'Mã OTP không chính xác hoặc đã hết hạn.',
            ]);
        }

        // Xác thực thành công, cập nhật trạng thái email đã xác thực
        $user->email_verified_at = now();
        $user->save();
        
        // Xóa OTP khỏi cache
        Cache::forget('otp_' . $user->email);
        
        // Xóa session
        $request->session()->forget('pending_user_id');
        
        // Đăng nhập người dùng
        Auth::login($user);
        
        return redirect()->route('home')->with('success', 'Xác thực email thành công!');
    }

    /**
     * Gửi lại mã OTP
     */
    public function resendOTP(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');
        if (!$userId) {
            return response()->json(['error' => 'Không tìm thấy thông tin người dùng.'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Không tìm thấy thông tin người dùng.'], 400);
        }

        // Gửi lại OTP qua queue
        $this->sendOTP($user->email);
        
        return response()->json(['success' => true, 'message' => 'Mã OTP mới đã được gửi.']);
    }

    /**
     * Hiển thị form quên mật khẩu
     */
    public function showForgotPasswordForm()
    {
        return view('customer.auth.forgot-password');
    }

    /**
     * Xử lý yêu cầu đặt lại mật khẩu
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.exists' => 'Không tìm thấy tài khoản với địa chỉ email này.',
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Tạo token đặt lại mật khẩu
        $token = Str::random(64);
        
        // Lưu token vào cache với thời hạn 1 giờ
        Cache::put('password_reset_' . $token, $user->email, now()->addHour());
        
        // Tạo nội dung email
        $resetLink = url('/reset-password/' . $token);
        $emailContent = '<!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Đặt lại mật khẩu - FastFood</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background-color: #f4f4f4;">
            <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <div style="text-align: center; padding: 20px;">
                    <h1 style="color: #f97316; margin: 0;">FastFood</h1>
                    <p style="color: #666;">Đặt lại mật khẩu</p>
                </div>
                <div style="padding: 20px; color: #444;">
                    <p>Xin chào,</p>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Vui lòng click vào nút bên dưới để đặt lại mật khẩu:</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $resetLink . '" style="background-color: #f97316; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Đặt lại mật khẩu</a>
                    </div>
                    <p>Liên kết này sẽ hết hạn sau 60 phút. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                    <p>Trân trọng,<br>Đội ngũ FastFood</p>
                </div>
                <div style="text-align: center; padding: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px;">
                    <p>© ' . date('Y') . ' FastFood. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </body>
        </html>';
    
        // Gửi email
        try {
            Mail::html($emailContent, function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Đặt lại mật khẩu - FastFood');
            });
    
            // Thay thế JSON response bằng redirect với thông báo thành công
            $status = 'Chúng tôi đã gửi email hướng dẫn đặt lại mật khẩu đến địa chỉ email của bạn.';
            return back()->with('status', $status)->withInput();
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email đặt lại mật khẩu: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Hiển thị form đặt lại mật khẩu
     */
    public function showResetPasswordForm($token)
    {
        $email = Cache::get('password_reset_' . $token);
        
        if (!$email) {
            return redirect()->route('customer.login')
                ->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }
    
        return view('customer.auth.reset-password', ['token' => $token]);
    }

    /**
     * Xử lý đặt lại mật khẩu
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);
    
        $email = Cache::get('password_reset_' . $request->token);
        
        if (!$email) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }
    
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản với địa chỉ email này.']);
        }
    
        // Cập nhật mật khẩu
        $user->password = Hash::make($request->password);
        $user->save();
    
        // Xóa token
        Cache::forget('password_reset_' . $request->token);
    
        return redirect()->route('customer.login')
            ->with('success', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập với mật khẩu mới.');
    }
}
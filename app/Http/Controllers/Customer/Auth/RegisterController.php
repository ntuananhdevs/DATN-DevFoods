<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Rules\TurnstileRule;
use App\Services\AvatarUploadService;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendOTPJob;
use Illuminate\Support\Facades\Mail;
use App\Jobs\UploadGoogleAvatarJob;
use App\Mail\SendWelcomeEmail;

class RegisterController extends Controller
{
    /**
     * Xử lý đăng ký tạm thời: lưu cache và gửi OTP
     */
    public function registerTemp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Kiểm tra email đã tồn tại trong DB
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Email đã tồn tại trong hệ thống.',
            ], 409);
        }

        // Sinh OTP 6 số
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        // Hash mật khẩu
        $hashedPassword = Hash::make($request->password);
        // Lưu vào cache
        $cacheKey = 'register_' . strtolower($request->email);
        $data = [
            'full_name' => $request->full_name,
            'email' => strtolower($request->email),
            'phone' => $request->phone,
            'password' => $hashedPassword,
            'otp' => $otp,
        ];
        Cache::put($cacheKey, $data, now()->addMinutes(10));

        // Gửi OTP (mô phỏng bằng log)
        Log::info("OTP for {$request->email}: $otp");

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi mã OTP đến email. Vui lòng kiểm tra email để xác thực.',
        ]);
    }

    /**
     * Xác thực OTP và ghi vào DB
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.digits' => 'Mã OTP phải gồm 6 số.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cacheKey = 'register_' . strtolower($request->email);
        $data = Cache::get($cacheKey);
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng ký đã hết hạn hoặc không tồn tại.',
            ], 410);
        }

        if ($data['otp'] !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Mã OTP không đúng.',
            ], 401);
        }

        // Kiểm tra lại email chưa tồn tại (tránh race condition)
        if (User::where('email', $data['email'])->exists()) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'Email đã tồn tại trong hệ thống.',
            ], 409);
        }

        // Ghi vào DB
        $user = User::create([
            'user_name' => $this->generateUniqueUserName($data['email']),
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'active' => true,
        ]);

        // Xóa cache
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công! Bạn có thể đăng nhập.',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Sinh user_name duy nhất từ email
     */
    private function generateUniqueUserName($email)
    {
        $base = Str::slug(explode('@', $email)[0]);
        $candidate = $base;
        $suffix = 0;
        while (User::where('user_name', $candidate . ($suffix ? "_{$suffix}" : ""))->exists()) {
            $suffix++;
        }
        return $candidate . ($suffix ? "_{$suffix}" : "");
    }

    /**
     * Hiển thị form đăng ký
     */
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Hiển thị form xác thực OTP
     */
    public function showOTPForm(Request $request)
    {
        // Có thể truyền email qua query hoặc yêu cầu nhập lại
        $email = $request->query('email');
        return view('customer.auth.verify-otp', compact('email'));
    }

    

    /**
     * Gửi lại OTP (nếu còn cache)
     */
    public function resendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cacheKey = 'register_' . strtolower($request->email);
        $data = Cache::get($cacheKey);
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng ký đã hết hạn hoặc không tồn tại.',
            ], 410);
        }

        $otp = $data['otp'];
        // Gửi lại OTP (log)
        Log::info("[RESEND] OTP for {$request->email}: $otp");

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi lại mã OTP đến email. Vui lòng kiểm tra email để xác thực.',
        ]);
    }
} 
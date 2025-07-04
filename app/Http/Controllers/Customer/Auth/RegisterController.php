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
use App\Services\CartTransferService;
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
    protected $cartTransferService;

    public function __construct(CartTransferService $cartTransferService)
    {
        $this->cartTransferService = $cartTransferService;
    }

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
        Cache::put($cacheKey, $data, now()->addMinutes(20));

        // Gửi OTP qua job queue
        SendOTPJob::dispatch($request->email, $otp)->onQueue('default');

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
            return back()->withErrors($validator)->withInput();
        }

        $email = strtolower($request->email);
        $cacheKey = 'register_' . $email;
        $failKey = 'otp_fail_' . $email;
        $lockKey = 'otp_lock_' . $email;

        // Kiểm tra đang bị khóa
        if (Cache::has($lockKey)) {
            $lockInfo = Cache::get($lockKey);
            return back()->withErrors(['otp' => $lockInfo['message']]);
        }

        $data = Cache::get($cacheKey);
        if (!$data) {
            return back()->withErrors(['otp' => 'Thông tin đăng ký đã hết hạn hoặc không tồn tại.'])->withInput();
        }

        if ($data['otp'] !== $request->otp) {
            // Đếm số lần sai, không reset khi hết thời gian khóa
            $failCount = Cache::get($failKey, 0) + 1;
            Cache::put($failKey, $failCount, now()->addMinutes(20));

            // Xử lý logic khóa
            if ($failCount == 3) {
                $lockTime = 60; // 1 phút
                $message = 'Bạn đã nhập sai OTP quá 3 lần, Vui lòng thử lại sau 1 phút';
                Cache::put($lockKey, ['type' => 'lock', 'message' => $message], $lockTime);
                return back()->withErrors(['otp' => $message])->with(['lockInfo' => ['type' => 'lock', 'seconds' => $lockTime]]);
            } elseif ($failCount == 5) {
                $lockTime = 180; // 3 phút
                $message = 'Bạn đã nhập sai OTP quá 5 lần, Vui lòng thử lại sau 3 phút';
                Cache::put($lockKey, ['type' => 'lock', 'message' => $message], $lockTime);
                return back()->withErrors(['otp' => $message])->with(['lockInfo' => ['type' => 'lock', 'seconds' => $lockTime]]);
            } elseif ($failCount > 5) {
                // Tăng dần thời gian khóa
                $lockTime = 180 * ($failCount - 4); // 3 phút * số lần vượt quá 5
                $message = "Bạn đã nhập sai OTP quá nhiều lần, Vui lòng thử lại sau " . ($lockTime / 60) . " phút";
                Cache::put($lockKey, ['type' => 'lock', 'message' => $message], $lockTime);
                return back()->withErrors(['otp' => $message])->with(['lockInfo' => ['type' => 'lock', 'seconds' => $lockTime]]);
            } else {
                if ($failCount < 3) {
                    $message = "Bạn đã nhập sai OTP $failCount lần, còn " . (3 - $failCount) . " lần nhập OTP";
                } elseif ($failCount < 5) {
                    $message = "Bạn đã nhập sai OTP $failCount lần, còn " . (5 - $failCount) . " lần nhập OTP";
                } else {
                    $message = "Bạn đã nhập sai OTP $failCount lần.";
                }
                return back()->withErrors(['otp' => $message])->withInput();
            }
        }

        // Đúng OTP, xóa đếm sai và khóa
        Cache::forget($failKey);
        Cache::forget($lockKey);

        // Kiểm tra lại email chưa tồn tại (tránh race condition)
        if (User::where('email', $data['email'])->exists()) {
            Cache::forget($cacheKey);
            return back()->withErrors(['otp' => 'Email đã tồn tại trong hệ thống.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Lưu session ID trước khi tạo user để chuyển giỏ hàng
            $sessionId = session()->getId();

            // Ghi vào DB
            $user = User::create([
                'user_name' => $this->generateUniqueUserName($data['email']),
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'active' => true,
            ]);

            // Gán role customer
            $customerRole = Role::firstOrCreate(
                ['name' => 'customer'],
                ['display_name' => 'Khách hàng']
            );
            $user->roles()->attach($customerRole->id);

            // Xóa cache
            Cache::forget($cacheKey);

            DB::commit();

            // Tự động đăng nhập user
            Auth::login($user);

            // Chuyển giỏ hàng từ session sang user
            try {
                $this->cartTransferService->transferCartFromSessionToUser($user->id, $sessionId);
                Log::info('Cart transferred successfully after registration', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to transfer cart after registration', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'error' => $e->getMessage()
                ]);
                // Không throw exception để không ảnh hưởng đến quá trình đăng ký
            }

            return redirect()->route('home')->with('status', 'Đăng ký thành công! Chào mừng bạn đến với FastFood.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during registration', [
                'error' => $e->getMessage(),
                'email' => $data['email']
            ]);
            return back()->withErrors(['otp' => 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.'])->withInput();
        }
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
        // Gửi lại OTP qua job queue
        SendOTPJob::dispatch($request->email, $otp)->onQueue('default');

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi lại mã OTP đến email. Vui lòng kiểm tra email để xác thực.',
        ]);
    }

    public function checkOtpLock(Request $request)
    {
        $email = strtolower($request->email);
        $lockKey = 'otp_lock_' . $email;
        if (Cache::has($lockKey)) {
            $lockInfo = Cache::get($lockKey);
            return response()->json([
                'locked' => true,
                'message' => $lockInfo['message']
            ]);
        }
        return response()->json(['locked' => false]);
    }
} 
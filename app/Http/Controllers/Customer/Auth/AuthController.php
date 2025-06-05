<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Rules\TurnstileRule;
use App\Services\AvatarUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\SendOTPJob;
use Illuminate\Support\Facades\Mail;
use App\Jobs\UploadGoogleAvatarJob;
use App\Mail\SendWelcomeEmail;
use App\Mail\ForgotPasswordMail;

class AuthController extends Controller
{
    protected $avatarUploadService;

    public function __construct(AvatarUploadService $avatarUploadService)
    {
        $this->avatarUploadService = $avatarUploadService;
    }

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
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Đăng xuất người dùng
        Auth::logout();
        
        // Xóa remember token của người dùng
        if ($user) {
            User::where('id', $user->id)->update(['remember_token' => null]);
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
     * Tạo user_name duy nhất từ email với 3 số ngẫu nhiên
     */
    private function generateUniqueUserName(string $email): string
    {
        // Lấy phần trước dấu '@' làm base
        $base = Str::slug(explode('@', $email)[0]);
        $candidate = $base;
        $suffix = 0;

        // Kiểm tra lặp cho đến khi có user_name chưa tồn tại
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
                'user_name' => $this->generateUniqueUserName($request->email),
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

            // Tạo mã OTP ngẫu nhiên 6 chữ số
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Lưu OTP vào cache trước khi gửi email để đảm bảo có thể xác thực ngay cả khi email chưa đến
            Cache::put('otp_' . $user->email, $otp, now()->addMinutes(10));
            
            // Log để debug
            Log::info('OTP generated for ' . $user->email . ': ' . $otp);
            
            // Thử gửi OTP qua email trực tiếp trước khi dùng queue
            try {
                $emailContent = $this->getOTPEmailContent($otp);
                Mail::html($emailContent, function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Xác thực tài khoản - FastFood');
                });
                Log::info('OTP sent directly to ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Error sending OTP directly: ' . $e->getMessage());
                // Vẫn tiếp tục và thử gửi qua queue
            }
            
            // Gửi OTP qua queue như cũ
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
     * Tạo nội dung email OTP
     */
    private function getOTPEmailContent($otp)
    {
        // Phương thức này không còn cần thiết vì chúng ta đã sử dụng SendOTPMail
        // Nhưng giữ lại để tránh lỗi nếu có code khác gọi đến
        Log::warning('Deprecated method getOTPEmailContent called. Use SendOTPMail instead.');
        
        // Render view thành string và trả về
        return view('emails.sendOTP', ['otp' => $otp])->render();
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
        User::where('id', $user->id)->update(['email_verified_at' => now()]);
        $user->refresh(); // Refresh user model with updated data
        
        // Xóa OTP khỏi cache
        Cache::forget('otp_' . $user->email);
        
        // Xóa session
        $request->session()->forget('pending_user_id');
        
        // Gửi email chào mừng
        try {
            Mail::to($user->email)->queue(new SendWelcomeEmail($user));
            Log::info('Welcome email queued for ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email chào mừng: ' . $e->getMessage());
        }

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
        
        // Gửi email đặt lại mật khẩu qua queue
        try {
            $resetLink = url('/reset-password/' . $token);
            Mail::to($user->email)->queue(new ForgotPasswordMail($user->email, $resetLink));
            Log::info('Forgot password email queued for ' . $user->email);
            
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

    /**
     * Xử lý đăng nhập Google thông qua Firebase
     */
    public function handleGoogleAuth(Request $request)
    {
        // 1. Validate request cơ bản
        $request->validate([
            'firebase_token'                  => 'required|string',
            'google_user_data'                => 'required|array',
            'google_user_data.uid'            => 'required|string',
            'google_user_data.email'          => 'required|email|max:255',
            'google_user_data.displayName'    => 'required|string|max:255',
            'google_user_data.photoURL'       => 'nullable|string|max:2048',
        ], [
            'google_user_data.email.email'       => 'Email không đúng định dạng.',
            'google_user_data.displayName.required' => 'Google không trả về tên người dùng.',
            'google_user_data.displayName.max'      => 'Tên người dùng quá dài (vượt 255 ký tự).',
            'google_user_data.photoURL.max'         => 'URL avatar quá dài.',
        ]);

        // Lấy dữ liệu từ request
        $googleUserData = $request->google_user_data;
        $emailFromGoogle = strtolower($googleUserData['email']);

       

        try {
            // 3. Bắt đầu transaction để tránh race condition khi tạo user
            DB::beginTransaction();

            // 4. Tìm user theo google_id hoặc email, kèm lock để tránh đồng thời quá nhiều luồng
            $user = User::where('google_id', $googleUserData['uid'])
                        ->orWhere('email', $emailFromGoogle)
                        ->lockForUpdate()
                        ->first();

            if (!$user) {
                // 4a. Nếu chưa có user, tạo mới
                $userName  = $this->generateUniqueUserName($emailFromGoogle);
                $fullName  = Str::limit(strip_tags($googleUserData['displayName']), 255);

                $user = User::create([
                    'user_name'         => $userName,
                    'full_name'         => $fullName,
                    'email'             => $emailFromGoogle,
                    'google_id'         => $googleUserData['uid'],
                    'avatar'            => null,
                    'user_rank_id'      => 1,
                    'rank_updated_at'   => now(),
                    'email_verified_at' => now(),
                    'active'            => true,
                    'password'          => Hash::make(Str::random(24)), // mật khẩu ngẫu nhiên
                ]);

                // Gán role customer (nếu chưa có, tạo mới)
                $customerRole = Role::firstOrCreate(
                    ['name' => 'customer'],
                    ['display_name' => 'Khách hàng']
                );
                $user->roles()->attach($customerRole->id);

                // Dispatch job upload avatar nếu có ảnh từ Google
                if (!empty($googleUserData['photoURL'])) {
                    UploadGoogleAvatarJob::dispatch(
                        $user->id,
                        $googleUserData['photoURL'],
                        $emailFromGoogle
                    )->onQueue('default');
                }
            } else {
                // 4b. Nếu user đã tồn tại
                $needsUpdate = false;

                // Trường hợp user bị soft-deleted (nếu dùng SoftDeletes) hoặc inactive
                if (method_exists($user, 'trashed') && $user->trashed()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản đã bị xóa.'
                    ], 403);
                }
                if (!$user->active) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản của bạn đã bị khóa.'
                    ], 403);
                }

                // Nếu user chưa có google_id (trường hợp đăng ký bằng email/password trước đó)
                if (!$user->google_id) {
                    $user->google_id = $googleUserData['uid'];
                    $user->email_verified_at = now();
                    $needsUpdate = true;
                } elseif ($user->google_id !== $googleUserData['uid']) {
                    // Nếu google_id không khớp với dữ liệu từ request, khả năng cao có hành vi bất thường
                    DB::rollBack();
                    Log::warning('Google ID mismatch', [
                        'expected' => $user->google_id,
                        'actual'   => $googleUserData['uid'],
                        'user_id'  => $user->id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản đã tồn tại, vui lòng đăng nhập bằng email/mật khẩu.'
                    ], 403);
                }

                // Cập nhật full_name nếu đang rỗng và Google truyền về displayName
                if (empty($user->full_name) && !empty($googleUserData['displayName'])) {
                    $user->full_name = Str::limit(strip_tags($googleUserData['displayName']), 255);
                    $needsUpdate = true;
                }

                // Dispatch job upload avatar nếu có ảnh mới từ Google
                if (!empty($googleUserData['photoURL'])) {
                    UploadGoogleAvatarJob::dispatch(
                        $user->id,
                        $googleUserData['photoURL'],
                        $emailFromGoogle
                    )->onQueue('default');
                }

                if ($needsUpdate) {
                    $user->save();
                }
            }

            // 5. Commit transaction
            DB::commit();

            // 6. Đăng nhập user và regenerate session để tránh session fixation
            Auth::login($user, true);
            $request->session()->regenerate();

            // 7. Xác định redirect URL theo role
            if ($this->hasRole($user, 'admin')) {
                $redirectUrl = route('admin.dashboard');
            } else {
                $redirectUrl = route('home');
            }

            // 8. Kiểm tra số điện thoại và redirect
            if (empty($user->phone)) {
                // Nếu chưa có số điện thoại, redirect đến trang nhập số
                $redirectUrl = route('customer.phone-required');
            } elseif ($this->hasRole($user, 'admin')) {
                $redirectUrl = route('admin.dashboard');
            } else {
                $redirectUrl = route('home');
            }
            
            return response()->json([
                'success'      => true,
                'message'      => 'Đăng nhập Google thành công!',
                'redirect_url' => $redirectUrl,
                'user'         => [
                    'id'     => $user->id,
                    'name'   => $user->full_name,
                    'email'  => $user->email,
                    'avatar' => $user->avatar,
                    'phone'  => $user->phone,
                ]
            ]);
        }
        // Nếu có lỗi database (ví dụ duplicate key)
        catch (\Illuminate\Database\QueryException $qe) {
            DB::rollBack();

            // Nếu duplicate entry (mã lỗi MySQL 1062)
            if ($qe->errorInfo[1] == 1062) {
                Log::warning('Duplicate entry when creating user via Google', [
                    'email' => $emailFromGoogle,
                    'error' => $qe->getMessage()
                ]);

                                 // Lấy lại user vừa bị create bởi luồng khác
                 $user = User::where('email', $emailFromGoogle)->first();
                 if ($user) {
                     Auth::login($user, true);
                     $request->session()->regenerate();
                     
                     if (empty($user->phone)) {
                         $redirectUrl = route('customer.phone-required');
                     } elseif ($this->hasRole($user, 'admin')) {
                         $redirectUrl = route('admin.dashboard');
                     } else {
                         $redirectUrl = route('home');
                     }
                     
                     return response()->json([
                         'success'      => true,
                         'message'      => 'Đăng nhập Google thành công!',
                         'redirect_url' => $redirectUrl,
                         'user'         => [
                             'id'     => $user->id,
                             'name'   => $user->full_name,
                             'email'  => $user->email,
                             'avatar' => $user->avatar,
                             'phone'  => $user->phone,
                         ]
                     ]);
                 }
            }

            // Nếu lỗi database khác
            Log::error('Database error during Google login', [
                'error' => $qe->getMessage(),
                'stack' => $qe->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cơ sở dữ liệu. Vui lòng thử lại sau.'
            ], 500);
        }
        // Bắt tất cả các exception còn lại
        catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Unhandled exception in handleGoogleAuth', [
                'error'        => $e->getMessage(),
                'stack'        => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Hiển thị trang yêu cầu nhập số điện thoại
     */
    public function showPhoneRequired()
    {
        // Kiểm tra user đã đăng nhập
        if (!Auth::check()) {
            return redirect()->route('customer.login');
        }

        $user = Auth::user();
        
        // Nếu đã có số điện thoại, chuyển về trang chủ
        if (!empty($user->phone)) {
            return redirect()->route('home');
        }

        return view('customer.auth.phone-required');
    }

    /**
     * Cập nhật số điện thoại cho user sau khi đăng nhập Google
     */
    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^0\d{9}$/', // <-- This is the magic
                'unique:users,phone,' . Auth::id(),
            ],
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại đã được sử dụng bởi tài khoản khác.',
            'phone.regex' => 'Số điện thoại phải là 10 số và bắt đầu bằng số 0.',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->update(['phone' => $request->phone]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số điện thoại thành công!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật số điện thoại: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi. Vui lòng thử lại.'
            ], 500);
        }
    }


    /**
     * Kiểm tra trạng thái đăng nhập Firebase
     */
    public function checkAuthStatus(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url,
                ]
            ]);
        }
        
        return response()->json([
            'authenticated' => false
        ]);
    }
}
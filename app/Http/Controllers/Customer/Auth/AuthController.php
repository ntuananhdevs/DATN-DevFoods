<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Rules\TurnstileRule;
use App\Services\AvatarUploadService;
use App\Services\CartTransferService;
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
    protected $cartTransferService;

    public function __construct(AvatarUploadService $avatarUploadService, CartTransferService $cartTransferService)
    {
        $this->avatarUploadService = $avatarUploadService;
        $this->cartTransferService = $cartTransferService;
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

        // Lưu session ID trước khi đăng nhập để chuyển giỏ hàng
        $sessionId = session()->getId();

        // Đăng nhập
        Auth::login($user, $request->remember);

        // Chuyển giỏ hàng từ session sang user
        try {
            $this->cartTransferService->transferCartFromSessionToUser($user->id, $sessionId);
            Log::info('Cart transferred successfully after login', [
                'user_id' => $user->id,
                'session_id' => $sessionId
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to transfer cart after login', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            // Không throw exception để không ảnh hưởng đến quá trình đăng nhập
        }

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
        
        // Xóa thông tin giỏ hàng khỏi session
        $request->session()->forget('cart');
        $request->session()->forget('cart_count');
        
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

            // Lưu session ID trước khi đăng nhập để chuyển giỏ hàng
            $sessionId = session()->getId();

            // 6. Đăng nhập user và regenerate session để tránh session fixation
            Auth::login($user, true);
            $request->session()->regenerate();

            // Chuyển giỏ hàng từ session sang user
            try {
                $this->cartTransferService->transferCartFromSessionToUser($user->id, $sessionId);
                Log::info('Cart transferred successfully after Google login', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to transfer cart after Google login', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'error' => $e->getMessage()
                ]);
                // Không throw exception để không ảnh hưởng đến quá trình đăng nhập
            }

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
                     // Lưu session ID trước khi đăng nhập để chuyển giỏ hàng
                     $sessionId = session()->getId();
                     
                     Auth::login($user, true);
                     $request->session()->regenerate();
                     
                     // Chuyển giỏ hàng từ session sang user
                     try {
                         $this->cartTransferService->transferCartFromSessionToUser($user->id, $sessionId);
                         Log::info('Cart transferred successfully after Google login (duplicate case)', [
                             'user_id' => $user->id,
                             'session_id' => $sessionId
                         ]);
                     } catch (\Exception $e) {
                         Log::error('Failed to transfer cart after Google login (duplicate case)', [
                             'user_id' => $user->id,
                             'session_id' => $sessionId,
                             'error' => $e->getMessage()
                         ]);
                         // Không throw exception để không ảnh hưởng đến quá trình đăng nhập
                     }
                     
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
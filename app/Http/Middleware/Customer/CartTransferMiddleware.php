<?php

namespace App\Http\Middleware\Customer;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\CartTransferService;

class CartTransferMiddleware
{
    protected $cartTransferService;

    public function __construct(CartTransferService $cartTransferService)
    {
        $this->cartTransferService = $cartTransferService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Chỉ xử lý khi user đã đăng nhập
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = session()->getId();
            
            // Validate user still exists in database
            $userExists = \App\Models\User::where('id', $user->id)->exists();
            if (!$userExists) {
                // User doesn't exist, clear authentication
                Auth::logout();
                \Log::warning('User ID ' . $user->id . ' does not exist in CartTransferMiddleware, clearing authentication');
                return $next($request);
            }

            // Kiểm tra xem đã chuyển giỏ hàng cho session này chưa
            $transferKey = 'cart_transferred_' . $user->id . '_' . $sessionId;
            
            if (!session()->has($transferKey)) {
                try {
                    // Chuyển giỏ hàng từ session sang user
                    $this->cartTransferService->transferCartFromSessionToUser($user->id, $sessionId);
                    
                    // Đánh dấu đã chuyển để tránh chuyển lại
                    session([$transferKey => true]);
                    
                    Log::info('Cart transferred via middleware', [
                        'user_id' => $user->id,
                        'session_id' => $sessionId
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to transfer cart via middleware', [
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                        'error' => $e->getMessage()
                    ]);
                    // Không throw exception để không ảnh hưởng đến request
                }
            }
        }

        return $next($request);
    }
} 
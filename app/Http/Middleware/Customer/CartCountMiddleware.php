<?php

namespace App\Http\Middleware\Customer;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Cart;
use App\Models\CartItem;

class CartCountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for API requests
        if ($request->is('api/*')) {
            return $next($request);
        }
        
        // Find cart by user ID or session ID
        $userId = auth()->id();
        $sessionId = session()->getId();
        
        $cartQuery = Cart::query()->where('status', 'active');
        
        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }
        
        $cart = $cartQuery->first();
        
        // Calculate cart count
        $cartCount = 0;
        if ($cart) {
            $cartCount = CartItem::where('cart_id', $cart->id)->count();
        }
        
        // Always update the session with the correct count
        session(['cart_count' => $cartCount]);
        
        return $next($request);
    }
}

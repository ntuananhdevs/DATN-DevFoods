<?php

namespace App\Http\Middleware\Driver;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Driver;

class DriverAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if driver is authenticated using Laravel's auth guard
        if (!Auth::guard('driver')->check()) {
            // If request expects JSON (API), return JSON response
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            // For web requests, redirect to driver login
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        // Get the authenticated driver
        $driver = Auth::guard('driver')->user();
        
        // Check if driver account is active/not locked
        if ($driver && isset($driver->is_locked) && $driver->is_locked) {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }
        return $next($request);
    }
}
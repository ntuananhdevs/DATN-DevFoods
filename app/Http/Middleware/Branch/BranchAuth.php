<?php

namespace App\Http\Middleware\Branch;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BranchAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('manager')->check()) {
            return redirect()->route('branch.login');
        }

        $user = Auth::guard('manager')->user();
        // Eager load branch relationship
        $user->loadMissing('branch');
        
        if (!$user->hasRole('manager')) {
            Auth::guard('manager')->logout();
            return redirect()->route('branch.login')->with('error', 'Bạn không có quyền truy cập vào hệ thống chi nhánh');
        }
        
        if (!$user->branch) {
            Auth::guard('manager')->logout();
            return redirect()->route('branch.login')->with('error', 'Không tìm thấy chi nhánh được gán cho tài khoản này. Vui lòng liên hệ quản trị viên.');
        }

        return $next($request);
    }
} 
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Chỉ áp dụng cho user đã đăng nhập
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Kiểm tra user có vai trò admin không (admin không cần số điện thoại)
            if ($user->roles()->where('name', 'admin')->exists()) {
                return $next($request);
            }

            // Nếu user chưa có số điện thoại
            if (empty($user->phone)) {
                // Danh sách route được phép access khi chưa có số điện thoại
                $allowedRoutes = [
                    'customer.phone-required',
                    'customer.phone-required.post',
                    'customer.logout',
                    'api.auth.google',
                    'api.auth.status'
                ];

                $currentRoute = $request->route() ? $request->route()->getName() : null;

                // Nếu không phải route được phép, redirect đến trang nhập số điện thoại
                if (!in_array($currentRoute, $allowedRoutes)) {
                    // Nếu là AJAX request, trả về JSON
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Vui lòng cập nhật số điện thoại để tiếp tục.',
                            'redirect_url' => route('customer.phone-required')
                        ], 403);
                    }

                    // Nếu là request thường, redirect
                    return redirect()->route('customer.phone-required')
                        ->with('warning', 'Vui lòng cập nhật số điện thoại để tiếp tục sử dụng dịch vụ.');
                }
            }
        }

        return $next($request);
    }
}

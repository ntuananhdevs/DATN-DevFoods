<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // Thử với guard admin trước, nếu không thì thử manager
        $user = Auth::guard('admin')->user() ?? Auth::guard('manager')->user();

        if (!$user || !$user->hasRole($role)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}

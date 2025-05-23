<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (!\Auth::check() || !\Auth::user()->roles()->where('name', 'admin')->exists()) {
            return redirect()->route('admin.login')->with('error', 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}

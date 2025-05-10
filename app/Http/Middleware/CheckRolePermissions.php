<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRolePermissions
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!$request->user()) {
            return response()->json(['error' => 'Bạn chưa đăng nhập.'], Response::HTTP_UNAUTHORIZED);
        }

        // Kiểm tra vai trò hoặc quyền của người dùng
        if (!$request->user()->hasRole('required_role') && !$request->user()->hasPermission('required_permission')) {
            return response()->json(['error' => 'Bạn không có quyền truy cập.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

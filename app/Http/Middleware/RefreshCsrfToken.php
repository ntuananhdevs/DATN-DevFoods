<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RefreshCsrfToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Refresh CSRF token if session is about to expire
        if (Session::has('_token') && time() - Session::get('_token_time', 0) > 3600) {
            Session::regenerateToken();
        }
        
        // Store token generation time
        Session::put('_token_time', time());
        
        return $next($request);
    }
} 
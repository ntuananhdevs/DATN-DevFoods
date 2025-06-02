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



        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!session()->has('driver_id')) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        $driver = Driver::find(session('driver_id'));
        
        return $next($request);
    }
}
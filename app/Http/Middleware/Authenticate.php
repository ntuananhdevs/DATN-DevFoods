<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // Check the current guard and redirect accordingly
            $guard = $request->route() ? $request->route()->getAction('middleware') : null;
            
            if (is_array($guard)) {
                foreach ($guard as $middleware) {
                    if (str_contains($middleware, 'auth:driver')) {
                        return route('driver.login');
                    }
                }
            }

            // Check URL path as fallback
            if (str_contains($request->path(), 'driver')) {
                return route('driver.login');
            }
            
            // Default to admin login
            return route('admin.login');
        }
        return null;
    }
}

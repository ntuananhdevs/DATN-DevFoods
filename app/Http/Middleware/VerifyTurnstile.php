<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyTurnstile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip verification for GET requests
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Skip verification if Turnstile is not configured
        if (!config('turnstile.secret_key')) {
            return $next($request);
        }

        $turnstileResponse = $request->input('cf-turnstile-response');

        if (!$turnstileResponse) {
            return back()->withErrors([
                'cf-turnstile-response' => 'Vui lòng hoàn thành xác minh bảo mật.'
            ])->withInput();
        }

        $response = Http::asForm()->post(config('turnstile.verify_url'), [
            'secret' => config('turnstile.secret_key'),
            'response' => $turnstileResponse,
            'remoteip' => $request->ip(),
        ]);

        if (!$response->successful()) {
            return back()->withErrors([
                'cf-turnstile-response' => 'Lỗi khi xác minh CAPTCHA. Vui lòng thử lại.'
            ])->withInput();
        }

        $body = $response->json();

        if (!$body['success']) {
            return back()->withErrors([
                'cf-turnstile-response' => 'Xác minh CAPTCHA thất bại. Vui lòng thử lại.'
            ])->withInput();
        }

        return $next($request);
    }
}

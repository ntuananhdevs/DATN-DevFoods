<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class TurnstileHelper
{
    /**
     * Verify Turnstile response
     */
    public static function verify(string $response, string $remoteIp = null): bool
    {
        if (!config('turnstile.secret_key')) {
            return true; // Skip verification if not configured
        }

        $httpResponse = Http::asForm()->post(config('turnstile.verify_url'), [
            'secret' => config('turnstile.secret_key'),
            'response' => $response,
            'remoteip' => $remoteIp ?: request()->ip(),
        ]);

        if (!$httpResponse->successful()) {
            return false;
        }

        $body = $httpResponse->json();
        return $body['success'] ?? false;
    }

    /**
     * Check if Turnstile is enabled
     */
    public static function isEnabled(): bool
    {
        return !empty(config('turnstile.site_key')) && !empty(config('turnstile.secret_key'));
    }

    /**
     * Get Turnstile site key
     */
    public static function getSiteKey(): string
    {
        return config('turnstile.site_key', '');
    }

    /**
     * Get Turnstile theme
     */
    public static function getTheme(): string
    {
        return config('turnstile.theme', 'light');
    }

    /**
     * Get Turnstile size
     */
    public static function getSize(): string
    {
        return config('turnstile.size', 'normal');
    }
} 
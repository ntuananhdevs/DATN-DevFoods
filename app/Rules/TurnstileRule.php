<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::withoutVerifying()->asForm()->post(config('turnstile.verify_url'), [
            'secret' => config('turnstile.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (!$response->successful()) {
            $fail('Lỗi khi xác minh CAPTCHA. Vui lòng thử lại.');
            return;
        }

        $body = $response->json();

        if (!$body['success']) {
            $fail('Xác minh CAPTCHA thất bại. Vui lòng thử lại.');
        }
    }
}

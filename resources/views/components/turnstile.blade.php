@props([
    'theme' => config('turnstile.theme', 'light'),
    'size' => config('turnstile.size', 'normal'),
    'callback' => 'onTurnstileCallback',
    'errorCallback' => 'onTurnstileError',
    'expiredCallback' => 'onTurnstileExpired'
])

<div class="turnstile-container">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Xác minh bảo mật <span class="text-red-500">*</span>
    </label>
    <div class="cf-turnstile" 
         data-sitekey="{{ config('turnstile.site_key') }}"
         data-theme="{{ $theme }}"
         data-size="{{ $size }}"
         data-callback="{{ $callback }}"
         data-error-callback="{{ $errorCallback }}"
         data-expired-callback="{{ $expiredCallback }}">
    </div>
    @error('cf-turnstile-response')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>

@once
    @push('scripts')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        let turnstileToken = null;

        // Default Turnstile callbacks
        window.onTurnstileCallback = function(token) {
            turnstileToken = token;
            // Enable submit buttons
            document.querySelectorAll('button[type="submit"][data-turnstile-required]').forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        };

        window.onTurnstileError = function() {
            turnstileToken = null;
            // Disable submit buttons
            document.querySelectorAll('button[type="submit"][data-turnstile-required]').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
            alert('Xác minh bảo mật thất bại. Vui lòng thử lại.');
        };

        window.onTurnstileExpired = function() {
            turnstileToken = null;
            // Disable submit buttons
            document.querySelectorAll('button[type="submit"][data-turnstile-required]').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
            alert('Xác minh bảo mật đã hết hạn. Vui lòng thực hiện lại.');
        };

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"][data-turnstile-required]');
                    if (submitBtn && !turnstileToken) {
                        e.preventDefault();
                        alert('Vui lòng hoàn thành xác minh bảo mật');
                        return false;
                    }
                });
            });
        });
    </script>
    @endpush
@endonce 
<?php

namespace App\Providers;

use App\Helpers\TurnstileHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class TurnstileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('turnstile', function () {
            return new TurnstileHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directive for Turnstile
        Blade::directive('turnstile', function ($expression) {
            return "<?php echo view('components.turnstile', $expression ?: [])->render(); ?>";
        });

        // Register Blade if directive for Turnstile enabled check
        Blade::if('turnstile', function () {
            return TurnstileHelper::isEnabled();
        });
    }
}

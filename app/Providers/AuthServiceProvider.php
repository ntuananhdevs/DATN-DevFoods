<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Ví dụ:
        // \App\Models\Post::class => \App\Policies\PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::ignoreRoutes();

        // Tuỳ chọn: Cấu hình thời gian sống của access token, refresh token, scopes,...
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}


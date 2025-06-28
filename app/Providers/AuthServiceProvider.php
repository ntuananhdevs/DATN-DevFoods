<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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

        // Gate cho quản lý Roles

        Gate::define('access-admin', function ($user) {
            return $user->roles()->whereIn('name', ['admin'])->exists();
        });

        Gate::define('access-branch', function ($user) {
            return $user->roles()->whereIn('name', ['manager'])->exists();
        });

        Passport::ignoreRoutes();

        // Tuỳ chọn: Cấu hình thời gian sống của access token, refresh token, scopes,...
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}

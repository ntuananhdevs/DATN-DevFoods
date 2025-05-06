<?php

namespace App\Providers;

<<<<<<< HEAD
use App\Models\User;

use Illuminate\Support\Facades\Gate;

=======
>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
<<<<<<< HEAD


=======
>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6
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
<<<<<<< HEAD
        // Gate cho quản lý Roles
        Gate::define('manage-roles', function (User $user) {
            return in_array($user->role->name, ['admin', 'manager']);
        });

=======
>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6

        Passport::ignoreRoutes();

        // Tuỳ chọn: Cấu hình thời gian sống của access token, refresh token, scopes,...
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
<<<<<<< HEAD
=======

>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6

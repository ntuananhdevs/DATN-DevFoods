<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use App\Models\BranchStock;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Passport::ignoreRoutes();

        // Nếu bạn cần tuỳ chỉnh token expiration, scopes... thì thêm ở đây
        // Passport::tokensExpireIn(now()->addDays(15));
    }
}

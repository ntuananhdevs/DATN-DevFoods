<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use App\Models\BranchStock;
use App\Models\ToppingStock;
use App\Observers\BranchStockObserver;
use App\Observers\ToppingStockObserver;
use App\Models\Product;
use App\Observers\ProductPriceObserver;
use App\Models\VariantValue;
use App\Observers\VariantPriceObserver;
use App\Models\Topping;
use App\Observers\ToppingPriceObserver;
use App\Models\ProductVariant;
use App\Observers\ProductVariantObserver;

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

        // Register BranchStock Observer
        BranchStock::observe(BranchStockObserver::class);
        ToppingStock::observe(ToppingStockObserver::class);

        // Register ProductPriceObserver
        Product::observe(ProductPriceObserver::class);

        // Register VariantPriceObserver
        VariantValue::observe(VariantPriceObserver::class);

        // Register ToppingPriceObserver
        Topping::observe(ToppingPriceObserver::class);

        // Register ProductVariantObserver
        ProductVariant::observe(ProductVariantObserver::class);

        // Nếu bạn cần tuỳ chỉnh token expiration, scopes... thì thêm ở đây
        // Passport::tokensExpireIn(now()->addDays(15));
    }
}

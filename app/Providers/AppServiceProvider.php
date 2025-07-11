<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use App\Models\BranchStock;
use App\Models\ToppingStock;
use App\Observers\BranchStockObserver;
use App\Observers\ToppingStockObserver;
use App\Observers\ProductPriceObserver;
use App\Models\Product;
use App\Observers\VariantPriceObserver;
use App\Models\VariantValue;
use App\Observers\ToppingPriceObserver;
use App\Models\Topping;
use App\Observers\ProductVariantObserver;
use App\Models\ProductVariant;
use App\Observers\ComboObserver;
use App\Models\Combo;
use App\Observers\OrderObserver;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\ReviewReply;
use App\Observers\ProductReviewObserver;
use App\Observers\ReviewReplyObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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

        // Register ComboObserver
        Combo::observe(ComboObserver::class);

        // Register OrderObserver
        Order::observe(OrderObserver::class);

        // Register ReviewReplyObserver
        ReviewReply::observe(ReviewReplyObserver::class);

        // Register ProductReviewObserver
        ProductReview::observe(ProductReviewObserver::class);

        // Nếu bạn cần tuỳ chỉnh token expiration, scopes... thì thêm ở đây
        // Passport::tokensExpireIn(now()->addDays(15));

        // View Composer cho layout/partials profile
        View::composer([
            'layouts.profile.fullLayoutProfile',
            'partials.profile.header',
            'partials.profile.sidebar',
        ], function ($view) {
            $user = Auth::user();
            $currentRank = $user?->currentRank ?? null;
            $currentPoints = $user?->points ?? 0;
            $view->with([
                'user' => $user,
                'currentRank' => $currentRank,
                'currentPoints' => $currentPoints,
            ]);
        });
    }
}

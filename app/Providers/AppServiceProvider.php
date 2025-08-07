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
use App\Models\ReviewReport;
use App\Observers\ReviewReportObserver;
use App\Observers\ReviewReplyObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ComboBranchStock;
use App\Observers\ComboBranchStockObserver;

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
        
        // Helper function for safe date formatting
        View::share('formatDate', function($date, $format = 'd/m/Y H:i') {
            if (!$date) return '';
            
            if (is_string($date)) {
                try {
                    return \Carbon\Carbon::parse($date)->format($format);
                } catch (\Exception $e) {
                    return $date;
                }
            }
            
            if ($date instanceof \Carbon\Carbon || $date instanceof \DateTime) {
                return $date->format($format);
            }
            
            return $date;
        });
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

        // Register ComboBranchStockObserver
        ComboBranchStock::observe(ComboBranchStockObserver::class);

        // Register OrderObserver
        // Order::observe(OrderObserver::class);

        // Register ReviewReplyObserver
        ReviewReply::observe(ReviewReplyObserver::class);

        // Register ProductReviewObserver
        ProductReview::observe(ProductReviewObserver::class);

        // Register ReviewReportObserver
        ReviewReport::observe(ReviewReportObserver::class);

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

        // View Composer cho branch notification
        View::composer([
            'partials.branch.header',
            'partials.branch.sidebar',
        ], function ($view) {
            $user = Auth::guard('manager')->user();
            $branch = $user && $user->branch ? $user->branch : null;
            $branchNotifications = $branch ? $branch->notifications()->latest()->limit(10)->get() : collect();
            $branchUnreadCount = $branch ? $branch->unreadNotifications()->count() : 0;
            $view->with([
                'branchNotifications' => $branchNotifications,
                'branchUnreadCount' => $branchUnreadCount,
            ]);
        });

        // View composer for admin notifications
        View::composer(['partials.admin.header', 'partials.admin.sidebar'], function ($view) {
            $admin = Auth::guard('admin')->user() ?? Auth::user();
            if ($admin instanceof \App\Models\User && method_exists($admin, 'hasRole') && $admin->hasRole('admin')) {
                $adminNotifications = $admin->notifications()->latest()->limit(10)->get();
                $adminUnreadCount = $admin->unreadNotifications()->count();

                $view->with([
                    'adminNotifications' => $adminNotifications,
                    'adminUnreadCount' => $adminUnreadCount
                ]);
            } else {
                $view->with([
                    'adminNotifications' => collect(),
                    'adminUnreadCount' => 0
                ]);
            }
        });

        // View Composer cho customer notification
        View::composer('partials.customer.header', function ($view) {
            $user = Auth::user();
            if ($user instanceof \App\Models\User) {
                $customerNotifications = $user->notifications()->latest()->limit(10)->get();
                $customerUnreadCount = $user->unreadNotifications()->count();
            } else {
                $customerNotifications = collect();
                $customerUnreadCount = 0;
            }
            $view->with(compact('customerNotifications', 'customerUnreadCount'));
        });
    }
}

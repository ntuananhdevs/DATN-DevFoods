<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagerController; // This one is not used in the provided web.php but is listed in the original uses
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ToppingController;
use App\Http\Controllers\Admin\ComboController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PromotionProgramController;
use App\Http\Controllers\Admin\NotificationController;

use App\Http\Controllers\Admin\User\UserController as UserUserController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\UserRankController;
use App\Http\Controllers\Admin\BranchStockController;
use App\Http\Controllers\Admin\ToppingStockController;
use App\Http\Controllers\Admin\HiringController;
use App\Http\Controllers\Admin\DriverApplicationController;
use App\Http\Controllers\Admin\DiscountCodeController;
use App\Http\Controllers\Admin\UserRankHistoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\OrderController;
// Driver Auth Controller (if it's considered part of admin management or hiring process)
use App\Http\Controllers\Driver\Auth\AuthController as DriverAuthController;


// Route Auth (login / logout) for Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Đăng nhập
    Route::controller(AuthController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login')->name('login.submit')->middleware('throttle:5,1');
    });

    // Đăng xuất (chỉ cho Admin đã đăng nhập)
    Route::middleware(['auth:admin'])->post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Route chỉ dành cho admin sau khi đăng nhập và có role:admin
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/ecommerce', [DashboardController::class, 'ecommerce'])->name('ecommerce');
    Route::get('/store_analytics', [DashboardController::class, 'store_analytics'])->name('store_analytics');

    // Categories Management
    Route::resource('categories', CategoryController::class)->except(['destroy']);
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::delete('{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('categories/bulk-status-update', [CategoryController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
    });

    // Roles Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('update');
        Route::get('/show/{id}', [RoleController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/show/{id}', [UserController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('trash', [UserController::class, 'trash'])->name('trash');
        Route::post('{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/bulk-status-update', [UserController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
        Route::prefix('managers')->name('managers.')->group(function () {
            Route::get('/', [UserController::class, 'manager'])->name('index');
            Route::get('/create', [UserController::class, 'createManager'])->name('create');
            Route::post('/store', [UserController::class, 'storeManager'])->name('store');
        });
    });

    // User Rank History Management
    Route::prefix('user_rank_history')->name('user_rank_history.')->group(function () {
        Route::get('/', [UserRankHistoryController::class, 'index'])->name('index');
    });

    // Branch Management
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::get('/create', [BranchController::class, 'create'])->name('create');
        Route::post('/store', [BranchController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BranchController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [BranchController::class, 'update'])->name('update');
        Route::get('/show/{id}', [BranchController::class, 'show'])->name('show');
        Route::get('/export', [BranchController::class, 'export'])->name('export');
        Route::patch('/{id}/toggle-status', [BranchController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/bulk-status-update', [BranchController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
        Route::get('/{id}/assign-manager', [BranchController::class, 'assignManager'])->name('assign-manager');
        Route::post('/{id}/update-manager', [BranchController::class, 'updateManager'])->name('update-manager');
        Route::post('/{id}/remove-manager', [BranchController::class, 'removeManager'])->name('remove-manager');
        Route::post('/{branch}/upload-image', [BranchController::class, 'uploadImage'])->name('upload-image');
        Route::post('/{id}/set-featured', [BranchController::class, 'setFeatured'])->name('set-featured');
        Route::delete('/{branch}/images/{image}', [BranchController::class, 'deleteImage'])->name('delete-image');
        Route::post('/bulk-update', [BranchController::class, 'bulkStatusUpdate'])->name('bulk-update');
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'index'])->name('search'); // For AJAX search
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
        Route::get('/show/{id}', [ProductController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [ProductController::class, 'trashed'])->name('trashed');
        Route::patch('/restore/{id}', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [ProductController::class, 'forceDelete'])->name('forceDelete');
        Route::get('/export', [ProductController::class, 'export'])->name('export');
        Route::get('/price-range', [ProductController::class, 'getPriceRange'])->name('price-range');

        // Stock management
        Route::get('{product}/stock', [ProductController::class, 'stock'])->name('stock');
        Route::post('{product}/update-stocks', [ProductController::class, 'updateProductStocks'])->name('update-stocks');
        Route::get('{product}/stock-summary', [BranchStockController::class, 'summary'])->name('stock-summary');
        Route::get('low-stock-alerts', [BranchStockController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('out-of-stock', [BranchStockController::class, 'outOfStock'])->name('out-of-stock');

        // Variant management
        Route::post('{product}/variants', [ProductVariantController::class, 'generate'])->name('generate-variants');
        Route::patch('variants/{variant}/status', [ProductVariantController::class, 'updateStatus'])->name('update-variant-status');
        Route::get('variants/{variant}', [ProductVariantController::class, 'show'])->name('show-variant');

        // Topping management for products
        Route::get('get-toppings', [ToppingController::class, 'getToppings'])->name('get-toppings');
    });

    // Toppings Management
    Route::prefix('toppings')->name('toppings.')->group(function () {
        Route::get('/', [ToppingController::class, 'index'])->name('index');
        Route::get('/create', [ToppingController::class, 'create'])->name('create');
        Route::post('/store', [ToppingController::class, 'store'])->name('store');
        Route::get('/edit/{topping}', [ToppingController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ToppingController::class, 'update'])->name('update');
        Route::get('/show/{topping}', [ToppingController::class, 'show'])->name('show');
        Route::delete('/delete/{topping}', [ToppingController::class, 'destroy'])->name('destroy');

        // Status management
        Route::patch('/{topping}/toggle-status', [ToppingController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/bulk-update-status', [ToppingController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

        // Stock management for toppings
        Route::get('/{topping}/stock', [ToppingStockController::class, 'show'])->name('stock');
        Route::post('/{topping}/update-stock', [ToppingStockController::class, 'update'])->name('update-stock');

        // Advanced stock management
        Route::get('/stock-management', [ToppingStockController::class, 'index'])->name('stock-management');
        Route::get('/stock/{topping}', [ToppingStockController::class, 'show'])->name('stock.show');
        Route::post('/stock/{id}/update', [ToppingStockController::class, 'update'])->name('stock.update');
        Route::post('/stock/bulk-update', [ToppingStockController::class, 'bulkUpdate'])->name('stock.bulk-update');
        Route::get('/stock/low-stock-alerts', [ToppingStockController::class, 'lowStockAlerts'])->name('stock.low-stock-alerts');
        Route::get('/stock/out-of-stock', [ToppingStockController::class, 'outOfStock'])->name('stock.out-of-stock');
        Route::get('/stock/summary', [ToppingStockController::class, 'summary'])->name('stock.summary');
        Route::get('/stock/export', [ToppingStockController::class, 'export'])->name('stock.export');
    });

    // Combos Management
    Route::prefix('combos')->name('combos.')->group(function () {
        Route::get('/', [ComboController::class, 'index'])->name('index');
        Route::get('/create', [ComboController::class, 'create'])->name('create');
        Route::post('/store', [ComboController::class, 'store'])->name('store');
        Route::get('/edit/{combo}', [ComboController::class, 'edit'])->name('edit');
        Route::put('/update/{combo}', [ComboController::class, 'update'])->name('update');
        Route::get('/show/{combo}', [ComboController::class, 'show'])->name('show');
        Route::delete('/delete/{combo}', [ComboController::class, 'destroy'])->name('destroy');

        // Status management
        Route::patch('/{combo}/toggle-status', [ComboController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/bulk-update-status', [ComboController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
        Route::patch('/bulk-update-featured', [ComboController::class, 'bulkUpdateFeatured'])->name('bulk-update-featured');
        Route::patch('/{combo}/quick-update-quantity', [ComboController::class, 'quickUpdateQuantity'])->name('admin.combos.quickUpdateQuantity');
    });

    // Driver Application Management
    Route::prefix('drivers')->name('drivers.')->group(function () {
        Route::get('/', [DriverController::class, 'index'])->name('index');
        Route::get('/create', [DriverController::class, 'create'])->name('create');
        Route::post('/store', [DriverController::class, 'store'])->name('store');
        Route::get('/show/{driver}', [DriverController::class, 'show'])->name('show');
        Route::get('/edit/{driver}', [DriverController::class, 'edit'])->name('edit');
        Route::put('/update/{driver}', [DriverController::class, 'update'])->name('update');
        Route::delete('/destroy/{driver}', [DriverController::class, 'destroy'])->name('destroy');
        Route::post('/reset-password/{driver}', [DriverController::class, 'resetPassword'])->name('reset-password');

        // New driver management routes
        Route::post('/{driver}/toggle-status', [DriverController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{driver}/lock-account', [DriverController::class, 'lockAccount'])->name('lock-account');
        Route::post('/{driver}/unlock-account', [DriverController::class, 'unlockAccount'])->name('unlock-account');
        Route::post('/{driver}/add-violation', [DriverController::class, 'addViolation'])->name('add-violation');

        Route::get('/export', [DriverController::class, 'export'])->name('export');
        Route::get('/applications', [DriverController::class, 'listApplications'])->name('applications.index');
        Route::get('/applications/export', [DriverController::class, 'exportApplications'])->name('applications.export');
        Route::get('/applications/{application}', [DriverController::class, 'viewApplicationDetails'])->name('applications.show');
        Route::post('/applications/{application}/approve', [DriverController::class, 'approveApplication'])->name('applications.approve');
        Route::post('/applications/{application}/reject', [DriverController::class, 'rejectApplication'])->name('applications.reject');
    });

    // Banner Management
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [BannerController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BannerController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/bulk-status-update', [BannerController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
        Route::get('/search-product', [BannerController::class, 'searchProducts'])->name('search.product');
    });

    // User Rank Management
    Route::prefix('user_ranks')->name('user_ranks.')->group(function () {
        Route::get('/', [UserRankController::class, 'index'])->name('index');
        Route::get('/create', [UserRankController::class, 'create'])->name('create');
        Route::post('/store', [UserRankController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserRankController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserRankController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserRankController::class, 'destroy'])->name('destroy');
        Route::post('/search', [UserRankController::class, 'search'])->name('search');
        Route::post('/update-status', [UserRankController::class, 'updateStatus'])->name('updateStatus');
    });

    // Promotion Management
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [PromotionProgramController::class, 'index'])->name('index');
        Route::get('/create', [PromotionProgramController::class, 'create'])->name('create');
        Route::post('/', [PromotionProgramController::class, 'store'])->name('store');
        Route::get('/{program}/edit', [PromotionProgramController::class, 'edit'])->name('edit');
        Route::put('/{program}', [PromotionProgramController::class, 'update'])->name('update');
        Route::delete('/{program}', [PromotionProgramController::class, 'destroy'])->name('destroy');
        Route::get('/{program}', [PromotionProgramController::class, 'show'])->name('show');
        Route::post('/{program}/discount-codes', [PromotionProgramController::class, 'linkDiscountCode'])->name('link-discount');
        Route::delete('/{program}/discount-codes/{discountCode}', [PromotionProgramController::class, 'unlinkDiscountCode'])->name('unlink-discount');
        Route::post('/{program}/branches', [PromotionProgramController::class, 'linkBranch'])->name('link-branch');
        Route::delete('/{program}/branches/{branch}', [PromotionProgramController::class, 'unlinkBranch'])->name('unlink-branch');
        Route::post('/bulk-status-update', [PromotionProgramController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
        Route::post('/search', [PromotionProgramController::class, 'search'])->name('search');
    });

    // Discount Codes Management
    Route::prefix('discount_codes')->name('discount_codes.')->group(function () {
        Route::get('/', [DiscountCodeController::class, 'index'])->name('index');
        Route::get('/create', [DiscountCodeController::class, 'create'])->name('create');
        Route::post('/store', [DiscountCodeController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DiscountCodeController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DiscountCodeController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [DiscountCodeController::class, 'destroy'])->name('destroy');
        Route::get('/show/{id}', [DiscountCodeController::class, 'show'])->name('show');
        Route::post('/search', [DiscountCodeController::class, 'search'])->name('search');
        Route::patch('/{id}/toggle-status', [DiscountCodeController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk-status-update', [DiscountCodeController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
        Route::get('/export', [DiscountCodeController::class, 'export'])->name('export');
        Route::post('/{id}/branches', [DiscountCodeController::class, 'linkBranch'])->name('link-branch');
        Route::delete('/{id}/branches/{branch}', [DiscountCodeController::class, 'unlinkBranch'])->name('unlink-branch');
        Route::post('/{id}/products', [DiscountCodeController::class, 'linkProduct'])->name('link-product');
        Route::delete('/{id}/products/{product}', [DiscountCodeController::class, 'unlinkProduct'])->name('unlink-product');
        Route::post('/{id}/combos', [DiscountCodeController::class, 'linkCombo'])->name('link-combo');
        Route::delete('/{id}/combos/{combo}', [DiscountCodeController::class, 'unlinkCombo'])->name('unlink-combo');
        Route::post('/{id}/product-variants', [DiscountCodeController::class, 'linkProductVariant'])->name('link-product-variant');
        Route::delete('/{id}/product-variants/{variant}', [DiscountCodeController::class, 'unlinkProductVariant'])->name('unlink-product-variant');
        Route::post('/{id}/assign-users', [DiscountCodeController::class, 'assignUsers'])->name('assign-users');
        Route::delete('/{id}/users/{user}', [DiscountCodeController::class, 'unassignUser'])->name('unassign-user');
        Route::get('/{id}/usage-history', [DiscountCodeController::class, 'usageHistory'])->name('usage-history');
        Route::match(['post', 'get'], '/get-users-by-rank', [DiscountCodeController::class, 'getUsersByRank'])->name('users-by-rank');
        Route::get('/products-by-branch', [DiscountCodeController::class, 'getProductsByBranch'])->name('products-by-branch');
        Route::get('/variants-by-branch', [DiscountCodeController::class, 'getVariantsByBranch'])->name('variants-by-branch');
        Route::post('/get-items-by-type', [DiscountCodeController::class, 'getItemsByType'])->name('get-items-by-type');
    });

    // Product Stock Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('{product}/stock', [BranchStockController::class, 'index'])->name('stock');
        Route::post('{product}/stocks', [BranchStockController::class, 'update'])->name('update-branch-stocks');
        Route::get('{product}/stock-summary', [BranchStockController::class, 'summary'])->name('stock-summary');
        Route::get('low-stock-alerts', [BranchStockController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('out-of-stock', [BranchStockController::class, 'outOfStock'])->name('out-of-stock');

    });

    // General Settings Management
    Route::prefix('general-settings')->name('general_settings.')->group(function () {
        Route::get('/', [GeneralSettingController::class, 'index'])->name('index');
        Route::post('/', [GeneralSettingController::class, 'store'])->name('store');
        Route::put('/{id}', [GeneralSettingController::class, 'update'])->name('update');
        Route::delete('/{id}', [GeneralSettingController::class, 'destroy'])->name('destroy');
    });

    // Hiring driver routes (these are publicly accessible for applications but relate to driver management)
    Route::prefix('hiring-driver')->name('driver.')->group(function () {
        Route::get('/', [HiringController::class, 'landing'])->name('landing');
        Route::get('/apply', [HiringController::class, 'applicationForm'])->name('application.form');
        Route::post('/apply', [HiringController::class, 'submitApplication'])->name('application.submit');
        Route::get('/success', [HiringController::class, 'applicationSuccess'])->name('application.success');
    });

    // Admin routes for driver applications (these are protected and belong in admin context)
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:admin']], function () {
        Route::get('/drivers/applications', [DriverApplicationController::class, 'index'])->name('drivers.applications.index');
        Route::get('/drivers/applications/{application}', [DriverApplicationController::class, 'show'])->name('drivers.applications.show');
        Route::patch('/drivers/applications/{application}/status', [DriverApplicationController::class, 'updateStatus'])->name('drivers.applications.update-status');
        Route::delete('/drivers/applications/{application}', [DriverApplicationController::class, 'destroy'])->name('drivers.applications.destroy');
        Route::get('/drivers/applications/export/{type}', [DriverApplicationController::class, 'export'])->name('drivers.applications.export');
        Route::get('/drivers/applications/stats', [DriverApplicationController::class, 'getStats'])->name('drivers.applications.stats');
        Route::get('/drivers/applications/image/{path}', [DriverApplicationController::class, 'streamImage'])->name('drivers.applications.image');
    });

    // Chat Admin
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::get('/messages/{conversation}', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/distribute', [ChatController::class, 'distributeConversation'])->name('distribute');
    });

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('notifications/item/{orderId}', [OrderController::class, 'notificationItem'])->name('admin.notifications.item');
    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        // Thêm route show chi tiết đơn hàng
        Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');
        Route::get('/export', [OrderController::class, 'export'])->name('export');
        // Route để lấy HTML partial cho order row (cho realtime)
        Route::get('/{id}/row', [OrderController::class, 'getOrderRow'])->name('row');
    });
});



// Add public broadcast routes for discount updates
Route::post('/broadcasting/auth', function () {
    return Broadcast::auth(request());
})->middleware('web');
// Thêm vào group combos:


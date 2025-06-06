<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagerController; // This one is not used in the provided web.php but is listed in the original uses
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PromotionProgramController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\User\UserController as UserUserController; // This one is not used directly in the provided web.php but is listed in the original uses
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\UserRankController;
use App\Http\Controllers\Admin\BranchStockController;
use App\Http\Controllers\Admin\HiringController;
use App\Http\Controllers\Admin\DriverApplicationController;
use App\Http\Controllers\Admin\DiscountCodeController;

// Driver Auth Controller (if it's considered part of admin management or hiring process)
use App\Http\Controllers\Driver\Auth\AuthController as DriverAuthController;


// Route Auth (login / logout) for Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Đăng nhập
    Route::controller(AuthController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login')->name('login.submit');
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

        // Stock management
        Route::get('{product}/stock', [ProductController::class, 'stock'])->name('stock');
        Route::post('{product}/update-stocks', [ProductController::class, 'updateStocks'])->name('update-stocks');
        Route::post('/update-topping-stocks', [ProductController::class, 'updateToppingStocks'])->name('update-topping-stocks');
        Route::get('{product}/stock-summary', [BranchStockController::class, 'summary'])->name('stock-summary');
        Route::get('low-stock-alerts', [BranchStockController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('out-of-stock', [BranchStockController::class, 'outOfStock'])->name('out-of-stock');

        // Variant management
        Route::post('{product}/variants', [ProductVariantController::class, 'generate'])->name('generate-variants');
        Route::patch('variants/{variant}/status', [ProductVariantController::class, 'updateStatus'])->name('update-variant-status');
        Route::get('variants/{variant}', [ProductVariantController::class, 'show'])->name('show-variant');
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
        // Liên kết chi nhánh
        Route::post('/{id}/branches', [DiscountCodeController::class, 'linkBranch'])->name('link-branch');
        Route::delete('/{id}/branches/{branch}', [DiscountCodeController::class, 'unlinkBranch'])->name('unlink-branch');
        // Liên kết sản phẩm/danh mục/combo
        Route::post('/{id}/products', [DiscountCodeController::class, 'linkProduct'])->name('link-product');
        Route::delete('/{id}/products/{product}', [DiscountCodeController::class, 'unlinkProduct'])->name('unlink-product');
        // Gán mã cho người dùng
        Route::post('/{id}/assign-users', [DiscountCodeController::class, 'assignUsers'])->name('assign-users');
        Route::delete('/{id}/users/{user}', [DiscountCodeController::class, 'unassignUser'])->name('unassign-user');
        // Lịch sử sử dụng
        Route::get('/{id}/usage-history', [DiscountCodeController::class, 'usageHistory'])->name('usage-history');
    });

    // Product Stock Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('{product}/stock', [BranchStockController::class, 'index'])->name('stock');
        Route::post('{product}/stocks', [BranchStockController::class, 'update'])->name('update-branch-stocks');
        Route::get('{product}/stock-summary', [BranchStockController::class, 'summary'])->name('stock-summary');
        Route::get('low-stock-alerts', [BranchStockController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('out-of-stock', [BranchStockController::class, 'outOfStock'])->name('out-of-stock');
    });

    // Admin Chat Routes
    Route::get('/chat', [AdminChatController::class, 'index'])->name('chat');
    Route::prefix('api/chat')->group(function () {
        Route::get('/chats', [AdminChatController::class, 'getChats'])->name('chat.list');
        Route::get('/chats/{chatId}/messages', [AdminChatController::class, 'getChatMessages'])->name('chat.messages');
        Route::post('/send', [AdminChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/status', [AdminChatController::class, 'updateStatus'])->name('chat.status');
        Route::post('/chats/{chatId}/close', [AdminChatController::class, 'closeChat'])->name('chat.close');
        Route::get('/statistics', [AdminChatController::class, 'getStatistics'])->name('chat.stats');
    });
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

// Driver Authentication Routes
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/login', [DriverAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DriverAuthController::class, 'login'])->name('login.submit');
    Route::post('/change-password', [DriverAuthController::class, 'changePassword'])->name('change_password');
    // Forgot Password
    Route::get('/forgot-password', [DriverAuthController::class, 'showForgotPasswordForm'])->name('forgot_password');
    Route::post('/forgot-password', [DriverAuthController::class, 'SendOTP'])->name('send_otp');
    // Verify OTP
    Route::get('/verify-otp/{driver_id}', [DriverAuthController::class, 'showVerifyOtpForm'])->name('verify_otp');
    Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp'])->name('verify_otp.submit');
    Route::post('/resend-otp', [DriverAuthController::class, 'resendOTP'])->name('resend_otp');
    // Reset Password
    Route::get('/reset-password/{driver_id}', [DriverAuthController::class, 'showResetPasswordForm'])->name('reset_password');
    Route::post('/reset-password/{driver_id}', [DriverAuthController::class, 'processResetPassword'])->name('reset_password.submit');
});

// Routes for logged-in drivers
Route::middleware(['driver.auth'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/', function () {
        return view('driver.home');
    })->name('home');
    Route::post('/logout', [DriverAuthController::class, 'logout'])->name('logout');
});
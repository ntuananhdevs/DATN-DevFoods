<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

//Admin

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\User\UserController as UserUserController;
use App\Http\Controllers\TestController;
//Customer
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Customer\Auth\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\CheckoutController as CustomerCheckoutController;
use App\Http\Controllers\Customer\PromotionController as CustomerPromotionController;
use App\Http\Controllers\Customer\SupportController as CustomerSupportController;
use App\Http\Controllers\Customer\BranchController as CustomerBranchController;
use App\Http\Controllers\Customer\AboutController as CustomerAboutController;
use App\Http\Controllers\Customer\ContactController as CustomerContactController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\WishlistController as CustomerWishlistController;
use Illuminate\Database\Capsule\Manager;
use App\Http\Middleware\Customer\CartCountMiddleware;

//Driver 
use App\Http\Controllers\Driver\Auth\AuthController as DriverAuthController;

// Product Stock Management Routes
use App\Http\Controllers\Admin\BranchStockController;

// Product Variant Routes
use App\Http\Controllers\Admin\ProductVariantController;

Route::prefix('/')->group(function () {
    // Apply the cart count middleware to all customer-facing routes
    Route::middleware([CartCountMiddleware::class])->group(function () {
        // Home
        Route::get('/', [CustomerHomeController::class, 'index'])->name('home');

        // Products
        Route::get('/shop/products', [CustomerProductController::class, 'index'])->name('products.index');
        Route::get('/shop/products/{id}', [CustomerProductController::class, 'show'])->name('products.show');

        Route::get('/wishlist', [CustomerWishlistController::class,'index'])->name('wishlist.index');
        Route::post('/wishlist', [CustomerWishlistController::class, 'store'])->name('wishlist.store');
        Route::delete('/wishlist/{id}', [CustomerWishlistController::class, 'destroy'])->name('wishlist.destroy');
        // // Store Locations
        // Route::get('/store', [StoreController::class, 'index'])->name('store.index');

        // // Blog
        // Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
        // Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        // Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category');
        // Route::get('/blog/tag/{tag}', [BlogController::class, 'tag'])->name('blog.tag');
        // Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search');

        // Cart
        Route::get('/cart', [CustomerCartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', 'App\Http\Controllers\Api\Customer\CartController@add')->name('cart.add');
        Route::post('/cart/update', 'App\Http\Controllers\Api\Customer\CartController@update')->name('cart.update');
        Route::post('/cart/remove', 'App\Http\Controllers\Api\Customer\CartController@remove')->name('cart.remove');
        Route::post('/coupon/apply', 'App\Http\Controllers\Api\Customer\CartController@applyCoupon')->name('coupon.apply');
        // Checkout
        Route::get('/checkout', [CustomerCheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/process', [CustomerCheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/checkout/success', [CustomerCheckoutController::class, 'success'])->name('checkout.success');

        // // User Profile
        // Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        // Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
        // Route::get('/profile/orders/{id}', [ProfileController::class, 'orderDetail'])->name('profile.order.detail');

        // About
        Route::get('/about', [CustomerAboutController::class, 'index'])->name('about.index');

        // Contact
        Route::get('/contact', [CustomerContactController::class, 'index'])->name('contact.index');
        // Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

        // Promotions
        Route::get('/promotions', [CustomerPromotionController::class, 'promotions'])->name('promotions.index');

        // Promotions
        Route::get('/branchs', [CustomerBranchController::class, 'branchs'])->name('branchs.index');

        // // Newsletter Subscription
        // Route::post('/subscribe', [HomeController::class, 'subscribe'])->name('subscribe');

        // Support
        Route::get('/support', [CustomerSupportController::class, 'support'])->name('support.index');
    });

    // Chat routes
    Route::prefix('api/chat')->group(function () {
        Route::post('/send-message', [CustomerChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/rating', [CustomerChatController::class, 'submitRating'])->name('chat.rating');
        Route::get('/history', [CustomerChatController::class, 'getChatHistory'])->name('chat.history');
    });

    // Route Customer (login / logout / register) - Thêm middleware guest
    Route::middleware('guest')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.post');
        Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.post');

        // Thêm route xác thực OTP
        Route::get('/verify-otp', [CustomerAuthController::class, 'showOTPForm'])->name('customer.verify.otp.show');
        Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOTP'])->name('customer.verify.otp.post');
        Route::post('/resend-otp', [CustomerAuthController::class, 'resendOTP'])->name('customer.resend.otp');

        // Thêm routes cho quên mật khẩu
        Route::get('/forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])
            ->name('customer.password.request');
        Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword'])
            ->name('customer.password.email');
        Route::get('/reset-password/{token}', [CustomerAuthController::class, 'showResetPasswordForm'])
            ->name('customer.password.reset');
        Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])
            ->name('customer.password.update');
    });

    // Đăng xuất không cần middleware guest
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

    // Route Customer (profile) - Cần đăng nhập để truy cập
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [CustomerProfileController::class, 'profile'])->name('customer.profile');
        Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
        Route::get('/profile/setting', [CustomerProfileController::class, 'setting'])->name('customer.profile.setting');
    });
});

// Route Auth (login / logout)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
});

// Route chỉ dành cho admin sau khi đăng nhập và có role:admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/ecommerce', [DashboardController::class, 'ecommerce'])->name('ecommerce');
    Route::get('/store_analytics', [DashboardController::class, 'store_analytics'])->name('store_analytics');

    // Đăng xuất
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

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
        Route::get('{product}/stock', [BranchStockController::class, 'index'])->name('stock');
        Route::post('{product}/update-stocks', [ProductController::class, 'updateStocks'])->name('update-stocks');
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

    // Banner Management
    Route::prefix('discount')->name('discount.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [BannerController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BannerController::class, 'destroy'])->name('destroy');
    });

    Route::get('/chat', [AdminChatController::class, 'index'])->name('chat');
    // Chat API routes
    Route::prefix('api/chat')->group(function () {
        Route::get('/chats', [AdminChatController::class, 'getChats'])->name('chat.list');
        Route::get('/chats/{chatId}/messages', [AdminChatController::class, 'getChatMessages'])->name('chat.messages');
        Route::post('/send', [AdminChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/status', [AdminChatController::class, 'updateStatus'])->name('chat.status');
        Route::post('/chats/{chatId}/close', [AdminChatController::class, 'closeChat'])->name('chat.close');
        Route::get('/statistics', [AdminChatController::class, 'getStatistics'])->name('chat.stats');
    });
});

// Customer Cart Routes
// Route::prefix('cart')->name('customer.cart.')->group(function () {
//     Route::get('/', [CustomerCartController::class, 'index'])->name('index');
//     Route::post('/add', [CustomerCartController::class, 'add'])->name('add');
//     Route::post('/update', [CustomerCartController::class, 'update'])->name('update');
//     Route::post('/update-batch', [CustomerCartController::class, 'updateBatch'])->name('update-batch');
//     Route::post('/remove', [CustomerCartController::class, 'remove'])->name('remove');
//     Route::post('/clear', [CustomerCartController::class, 'clear'])->name('clear');
// });
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/login', [DriverAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DriverAuthController::class, 'login'])->name('login.submit');
    Route::post('/change-password', [DriverAuthController::class, 'changePassword'])->name('change_password');
    // Quên mật khẩu
    Route::get('/forgot-password', [DriverAuthController::class, 'showForgotPasswordForm'])->name('forgot_password');
    Route::post('/forgot-password', [DriverAuthController::class, 'SendOTP'])->name('send_otp');
    // Xác minh OTP
    Route::get('/verify-otp/{driver_id}', [DriverAuthController::class, 'showVerifyOtpForm'])->name('verify_otp');
    Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp'])->name('verify_otp.submit');
    Route::post('/resend-otp', [DriverAuthController::class, 'resendOTP'])->name('resend_otp');
    // Đặt lại mật khẩu
    Route::get('/reset-password/{driver_id}', [DriverAuthController::class, 'showResetPasswordForm'])->name('reset_password');
    Route::post('/reset-password/{driver_id}', [DriverAuthController::class, 'processResetPassword'])->name('reset_password.submit');
});

// Route dành cho tài xế đã đăng nhập
Route::middleware(['driver.auth'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/', function () {
        return view('driver.home');
    })->name('home');
    Route::post('/logout', [DriverAuthController::class, 'logout'])->name('logout');
});

//hiring driver
Route::prefix('hiring-driver')->name('driver.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\HiringController::class, 'landing'])->name('landing');
    Route::get('/apply', [App\Http\Controllers\Admin\HiringController::class, 'applicationForm'])->name('application.form');
    Route::post('/apply', [App\Http\Controllers\Admin\HiringController::class, 'submitApplication'])->name('application.submit');
    Route::get('/success', [App\Http\Controllers\Admin\HiringController::class, 'applicationSuccess'])->name('application.success');
});

// Test routes for AWS S3 uploadd
Route::prefix('test')->name('test.')->group(function () {
    Route::get('/upload', [TestController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/upload', [TestController::class, 'uploadImage'])->name('upload.image');
    Route::get('/images', [TestController::class, 'listImages'])->name('images.list');
    Route::delete('/images', [TestController::class, 'deleteImage'])->name('images.delete');
    Route::get('/connection', [TestController::class, 'testConnection'])->name('connection');
});

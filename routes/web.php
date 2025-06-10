<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\User\UserController as UserUserController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Admin\DriverController;
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
use App\Http\Controllers\Driver\Auth\AuthController as DriverAuthController;
use App\Http\Controllers\Admin\BranchStockController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\BranchChatController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\HiringController;
use App\Http\Controllers\TestController;

Route::prefix('/')->group(function () {
    // Home
    Route::get('/', [CustomerHomeController::class, 'index'])->name('home');

    // Products
    Route::get('/shop/products', [CustomerProductController::class, 'index'])->name('products.index');
    Route::get('/shop/products/show', [CustomerProductController::class, 'show'])->name('products.show');

    // Cart
    Route::get('/cart', [CustomerCartController::class, 'index'])->name('cart.index');

    // Checkout
    Route::get('/checkout', [CustomerCheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/process', [CustomerCheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CustomerCheckoutController::class, 'success'])->name('checkout.success');

    // About
    Route::get('/about', [CustomerAboutController::class, 'index'])->name('about.index');

    // Contact
    Route::get('/contact', [CustomerContactController::class, 'index'])->name('contact.index');

    // Promotions
    Route::get('/promotions', [CustomerPromotionController::class, 'promotions'])->name('promotions.index');

    // Branches
    Route::get('/branchs', [CustomerBranchController::class, 'branchs'])->name('branchs.index');

    // Support
    Route::get('/support', [CustomerSupportController::class, 'support'])->name('support.index');

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
    });

    // Driver Application Management
    Route::prefix('drivers')->name('drivers.')->group(function () {
        Route::get('/', [DriverController::class, 'index'])->name('index');
        Route::get('/export', [DriverController::class, 'export'])->name('export');
        Route::get('/applications', [DriverController::class, 'listApplications'])->name('applications.index');
        Route::get('/applications/export', [DriverController::class, 'exportApplications'])->name('applications.export');
        Route::get('/applications/{application}', [DriverController::class, 'viewApplicationDetails'])->name('applications.show');
        Route::post('/applications/{application}/approve', [DriverController::class, 'approve'])->name('applications.approve');
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

    // Product Stock Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('{product}/stock', [BranchStockController::class, 'index'])->name('stock');
        Route::post('{product}/stocks', [BranchStockController::class, 'update'])->name('update-stocks');
        Route::get('{product}/stock-summary', [BranchStockController::class, 'summary'])->name('stock-summary');
        Route::get('low-stock-alerts', [BranchStockController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('out-of-stock', [BranchStockController::class, 'outOfStock'])->name('out-of-stock');
    });

    // Chat routes
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [AdminChatController::class, 'index'])->name('index');
        Route::post('/send-message', [AdminChatController::class, 'sendMessage'])->name('send');
        Route::post('/distribute', [AdminChatController::class, 'distributeConversation'])->name('distribute');
        Route::get('/messages/{conversationId}', [AdminChatController::class, 'getMessages'])->name('messages');
        Route::post('/typing', [AdminChatController::class, 'handleTyping'])->name('typing');
    });
});

// Customer Cart Routes
Route::prefix('cart')->name('customer.cart.')->group(function () {
    Route::get('/', [CustomerCartController::class, 'index'])->name('index');
    Route::post('/add', [CustomerCartController::class, 'add'])->name('add');
    Route::post('/update', [CustomerCartController::class, 'update'])->name('update');
    Route::post('/update-batch', [CustomerCartController::class, 'updateBatch'])->name('update-batch');
    Route::post('/remove', [CustomerCartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CustomerCartController::class, 'clear'])->name('clear');
});

// Driver Auth Routes
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/login', [DriverAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DriverAuthController::class, 'login'])->name('login.submit');
    Route::post('/change-password', [DriverAuthController::class, 'changePassword'])->name('change_password');
    Route::get('/forgot-password', [DriverAuthController::class, 'showForgotPasswordForm'])->name('forgot_password');
    Route::post('/forgot-password', [DriverAuthController::class, 'SendOTP'])->name('send_otp');
    Route::get('/verify-otp/{driver_id}', [DriverAuthController::class, 'showVerifyOtpForm'])->name('verify_otp');
    Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp'])->name('verify_otp.submit');
    Route::post('/resend-otp', [DriverAuthController::class, 'resendOTP'])->name('resend_otp');
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

// Hiring Driver Routes
Route::prefix('hiring-driver')->name('driver.')->group(function () {
    Route::get('/', [HiringController::class, 'landing'])->name('landing');
    Route::get('/apply', [HiringController::class, 'applicationForm'])->name('application.form');
    Route::post('/apply', [HiringController::class, 'submitApplication'])->name('application.submit');
    Route::get('/success', [HiringController::class, 'applicationSuccess'])->name('application.success');
});

// Test routes for AWS S3 upload
Route::prefix('test')->name('test.')->group(function () {
    Route::get('/upload', [TestController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/upload', [TestController::class, 'uploadImage'])->name('upload.image');
    Route::get('/images', [TestController::class, 'listImages'])->name('images.list');
    Route::delete('/images', [TestController::class, 'deleteImage'])->name('images.delete');
    Route::get('/connection', [TestController::class, 'testConnection'])->name('connection');
});


Route::prefix('admin')->group(function () {
    Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/chat/send-message', [AdminChatController::class, 'sendMessage'])->name('admin.chat.send');
    Route::post('/chat/distribute', [AdminChatController::class, 'distributeConversation'])->name('admin.chat.distribute');
});

Route::prefix('branch')->group(function () {
    Route::get('/chat', [BranchChatController::class, 'index'])->name('branch.chat.index');
    Route::get('/chat/api/conversation/{id}', [BranchChatController::class, 'apiGetConversation'])->name('branch.chat.conversation');
    Route::post('/chat/send-message', [BranchChatController::class, 'sendMessage'])->name('branch.chat.send');
    Route::post('/chat/update-status', [BranchChatController::class, 'updateStatus'])->name('branch.chat.status');
    Route::post('/chat/typing', [BranchChatController::class, 'typing'])->name('branch.chat.typing');
});


Route::prefix('customer')->group(function () {
    Route::get('/chat', function () {
        $conversations = \App\Models\Conversation::where('customer_id', auth()->id())
            ->with(['branch', 'messages.sender'])
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('customer.chat', compact('conversations'));
    })->name('customer.chat.index');

    Route::post('/chat/create', [App\Http\Controllers\Customer\ChatController::class, 'createConversation'])->name('customer.chat.create');
    Route::post('/chat/send', [App\Http\Controllers\Customer\ChatController::class, 'sendMessage'])->name('customer.chat.send');
    Route::get('/chat/conversations', [App\Http\Controllers\Customer\ChatController::class, 'getConversations'])->name('customer.chat.conversations');
    Route::get('/chat/messages', [App\Http\Controllers\Customer\ChatController::class, 'getMessages'])->name('customer.chat.messages');
    Route::post('/chat/typing', [App\Http\Controllers\Customer\ChatController::class, 'typing'])->name('customer.chat.typing');
});

Route::prefix('api')->group(function () {
    Route::get('/conversations/{id}', [CustomerChatController::class, 'getMessages']);
    Route::post('/customer/send-message', [CustomerChatController::class, 'sendMessage']);
    Route::post('/customer/typing', [CustomerChatController::class, 'typing']);
});

// Chat routes
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::post('/customer/chat/send', [App\Http\Controllers\Customer\ChatController::class, 'sendMessage'])->name('customer.chat.send');
Route::post('/branch/chat/send', [BranchChatController::class, 'sendMessage'])->name('branch.chat.send');

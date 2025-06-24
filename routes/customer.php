<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Customer Controllers
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
use App\Http\Controllers\Customer\ChatController;
use App\Http\Controllers\Customer\WishlistController as CustomerWishlistController;
use App\Http\Middleware\Customer\CartCountMiddleware;
use App\Http\Controllers\FirebaseConfigController;
use App\Http\Controllers\Admin\HiringController;

// API Controllers for Customer
// use App\Http\Controllers\Api\Customer\ProductController as ApiCustomerProductController;
// use App\Http\Controllers\Api\Customer\FavoriteController as ApiCustomerFavoriteController;
// use App\Http\Controllers\Api\Customer\ProductVariantController as ApiCustomerProductVariantController;
// use App\Http\Controllers\Api\Customer\CartController as ApiCustomerCartController;

// ===== WEB ROUTES (giao diện web, view) =====
Route::middleware([CartCountMiddleware::class, 'phone.required'])->group(function () {
    Route::get('/', [CustomerHomeController::class, 'index'])->name('home');

    // Product
    Route::get('/shop/products', [CustomerProductController::class, 'index'])->name('products.index');
    Route::get('/shop/products/{id}', [CustomerProductController::class, 'show'])->name('products.show');
    Route::post('/products/get-applicable-discounts', [CustomerProductController::class, 'getApplicableDiscounts'])->name('products.get-applicable-discounts');

    // Debug routes for discount codes
    Route::get('/debug/discount-codes', function () {
        $now = \Carbon\Carbon::now();
        $publicCodes = \App\Models\DiscountCode::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('usage_type', 'public')
            ->get();

        return response()->json([
            'count' => $publicCodes->count(),
            'codes' => $publicCodes
        ]);
    });

    Route::get('/debug/product/{id}/discount-codes', [CustomerProductController::class, 'showProductDiscounts']);

    // Wishlist
    Route::get('/wishlist', [CustomerWishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [CustomerWishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [CustomerWishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Cart
    Route::get('/cart', [CustomerCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CustomerCartController::class, 'addToCart'])->name('cart.add');

    // Checkout
    Route::get('/checkout', [CustomerCheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CustomerCheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CustomerCheckoutController::class, 'success'])->name('checkout.success');

    // About, Contact, Promotion, Branches, Support
    Route::get('/about', [CustomerAboutController::class, 'index'])->name('about.index');
    Route::get('/contact', [CustomerContactController::class, 'index'])->name('contact.index');
    Route::get('/promotions', [CustomerPromotionController::class, 'promotions'])->name('promotions.index');
    Route::get('/branches', [CustomerBranchController::class, 'branchs'])->name('branches.index');
    Route::get('/support', [CustomerSupportController::class, 'support'])->name('support.index');
});



// Authentication (login / logout / register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.post');
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.post');

    // OTP
    Route::get('/verify-otp', [CustomerAuthController::class, 'showOTPForm'])->name('customer.verify.otp.show');
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOTP'])->name('customer.verify.otp.post');
    Route::post('/resend-otp', [CustomerAuthController::class, 'resendOTP'])->name('customer.resend.otp');

    // Forgot password
    Route::get('/forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])->name('customer.password.request');
    Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword'])->name('customer.password.email');
    Route::get('/reset-password/{token}', [CustomerAuthController::class, 'showResetPasswordForm'])->name('customer.password.reset');
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('customer.password.update');
});

// Logout
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Customer Profile (cần đăng nhập)
Route::middleware(['auth', 'phone.required'])->group(function () {
    Route::get('/profile', [CustomerProfileController::class, 'profile'])->name('customer.profile');
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::get('/profile/setting', [CustomerProfileController::class, 'setting'])->name('customer.profile.setting');
    Route::put('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('customer.password.update');
    Route::patch('/profile/update', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
});

// Phone Required routes (không cần phone.required middleware)
Route::middleware('auth')->group(function () {
    Route::get('/phone-required', [CustomerAuthController::class, 'showPhoneRequired'])->name('customer.phone-required');
    Route::post('/phone-required', [CustomerAuthController::class, 'updatePhone'])->name('customer.phone-required.post');
});

Route::prefix('api')->group(function () {
    // Route::get('/products', [ApiCustomerProductController::class, 'getProducts']);
    // Route::post('/favorites/toggle', [ApiCustomerFavoriteController::class, 'toggle']);
    // Route::post('/cart/add', [ApiCustomerCartController::class, 'add'])->name('cart.add');
    // Route::post('/cart/update', [ApiCustomerCartController::class, 'update'])->name('cart.update');
    // Route::post('/cart/remove', [ApiCustomerCartController::class, 'remove'])->name('cart.remove');
    // Route::post('/coupon/apply', [ApiCustomerCartController::class, 'applyCoupon'])->name('coupon.apply');

    // Firebase Auth (Google)
    Route::prefix('auth')->group(function () {
        Route::post('/google', [CustomerAuthController::class, 'handleGoogleAuth'])->name('api.auth.google');
        Route::get('/status', [CustomerAuthController::class, 'checkAuthStatus'])->name('api.auth.status');
    });

    // Firebase Config
    Route::get('/firebase/config', [FirebaseConfigController::class, 'getConfig'])->name('api.firebase.config');
});

Route::prefix('branches')->group(function () {
    Route::post('/set-selected', [CustomerBranchController::class, 'setSelectedBranch'])->name('branches.set-selected');
    Route::get('/nearest', [CustomerBranchController::class, 'findNearestBranch'])->name('branches.nearest');
});
// Hiring driver routes (these are publicly accessible for applications but relate to driver management)
Route::prefix('hiring-driver')->name('driver.')->group(function () {
    Route::get('/', [HiringController::class, 'landing'])->name('landing');
    Route::get('/apply', [HiringController::class, 'applicationForm'])->name('application.form');
    Route::post('/apply', [HiringController::class, 'submitApplication'])->name('application.submit');
    Route::get('/success', [HiringController::class, 'applicationSuccess'])->name('application.success');
});

Route::prefix('customer')->middleware(['auth'])->group(function () {
    Route::get('/chat', function () {
        return view('customer.chat');
    })->name('customer.chat.index');

    Route::post('/chat/create', [ChatController::class, 'createConversation'])->name('customer.chat.create');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('customer.chat.send');
    Route::get('/chat/conversations', [ChatController::class, 'getConversations'])->name('customer.chat.conversations');
    Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('customer.chat.messages');
});

// Add new route for discount badge partial
Route::post('/partial/discount-badge', [CustomerProductController::class, 'getDiscountBadges'])->name('products.discount-badges');

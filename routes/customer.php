<?php

use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\WishlistController as CustomerWishlistController;
use App\Http\Middleware\Customer\CartCountMiddleware;

// API Controllers for Customer
use App\Http\Controllers\Api\Customer\ProductController as ApiCustomerProductController;
use App\Http\Controllers\Api\Customer\FavoriteController as ApiCustomerFavoriteController;
use App\Http\Controllers\Api\Customer\ProductVariantController as ApiCustomerProductVariantController;
use App\Http\Controllers\Api\Customer\BranchController as ApiCustomerBranchController;
use App\Http\Controllers\Api\Customer\CartController as ApiCustomerCartController;


Route::prefix('/')->group(function () {
    // Apply the cart count middleware to all customer-facing routes
    Route::middleware([CartCountMiddleware::class])->group(function () {
        // Home
        Route::get('/', [CustomerHomeController::class, 'index'])->name('home');

        // Products
        Route::get('/shop/products', [CustomerProductController::class, 'index'])->name('products.index');
        Route::get('/shop/products/{id}', [CustomerProductController::class, 'show'])->name('products.show');

        // Wishlist
        Route::get('/wishlist', [CustomerWishlistController::class,'index'])->name('wishlist.index');
        Route::post('/wishlist', [CustomerWishlistController::class, 'store'])->name('wishlist.store');
        Route::delete('/wishlist/{id}', [CustomerWishlistController::class, 'destroy'])->name('wishlist.destroy');

        // Cart
        Route::get('/cart', [CustomerCartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [ApiCustomerCartController::class, 'add'])->name('cart.add'); // Updated to use the correct class name
        Route::post('/cart/update', [ApiCustomerCartController::class, 'update'])->name('cart.update'); // Updated to use the correct class name
        Route::post('/cart/remove', [ApiCustomerCartController::class, 'remove'])->name('cart.remove'); // Updated to use the correct class name
        Route::post('/coupon/apply', [ApiCustomerCartController::class, 'applyCoupon'])->name('coupon.apply'); // Updated to use the correct class name

        // Checkout
        Route::get('/checkout', [CustomerCheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/process', [CustomerCheckoutController::class, 'process'])->name('checkout.process');
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
    });

    // Chat routes
    Route::prefix('api/chat')->group(function () {
        Route::post('/send-message', [CustomerChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/rating', [CustomerChatController::class, 'submitRating'])->name('chat.rating');
        Route::get('/history', [CustomerChatController::class, 'getChatHistory'])->name('chat.history');
    });

    // Customer Authentication (login / logout / register) - With guest middleware
    Route::middleware('guest')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.post');
        Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.post');

        // OTP authentication routes
        Route::get('/verify-otp', [CustomerAuthController::class, 'showOTPForm'])->name('customer.verify.otp.show');
        Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOTP'])->name('customer.verify.otp.post');
        Route::post('/resend-otp', [CustomerAuthController::class, 'resendOTP'])->name('customer.resend.otp');

        // Forgot password routes
        Route::get('/forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])
            ->name('customer.password.request');
        Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword'])
            ->name('customer.password.email');
        Route::get('/reset-password/{token}', [CustomerAuthController::class, 'showResetPasswordForm'])
            ->name('customer.password.reset');
        Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])
            ->name('customer.password.update');
    });

    // Logout does not require guest middleware
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

    // Customer Profile - Requires authentication
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [CustomerProfileController::class, 'profile'])->name('customer.profile');
        Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
        Route::get('/profile/setting', [CustomerProfileController::class, 'setting'])->name('customer.profile.setting');
    });
});


// API Routes for products, cart and favorites (Customer-facing API)
Route::prefix('api')->group(function () {
    // Product listing for AJAX filtering
    Route::get('/products', [ApiCustomerProductController::class, 'getProducts']);

    // Favorites
    Route::post('/favorites/toggle', [ApiCustomerFavoriteController::class, 'toggle']);

    // Customer API routes
    Route::prefix('customer')->group(function () {
        Route::post('/products/get-variant', [ApiCustomerProductVariantController::class, 'getVariant'])->name('api.products.get-variant');

        // Branch routes
        Route::post('/branches/set-selected', [ApiCustomerBranchController::class, 'setSelectedBranch'])->name('api.branches.set-selected');
        Route::get('/branches/nearest', [ApiCustomerBranchController::class, 'findNearestBranch'])->name('api.branches.nearest');
    });
});
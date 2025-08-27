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
use App\Http\Controllers\Customer\CouponController as CustomerCouponController;
use App\Http\Middleware\Customer\CartCountMiddleware;
use App\Http\Controllers\FirebaseConfigController;
use App\Http\Controllers\Admin\HiringController;
use App\Http\Controllers\Customer\Auth\RegisterController;
use App\Http\Controllers\Customer\NotificationController;
use Illuminate\Support\Str;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Customer\AddressController as CustomerAddressController;
use App\Http\Controllers\Customer\DriverRatingController;
use App\Http\Controllers\Customer\ReviewReplyController;
use App\Http\Controllers\Customer\WalletController;

// API Controllers for Customer
// use App\Http\Controllers\Api\Customer\ProductController as ApiCustomerProductController;
// use App\Http\Controllers\Api\Customer\FavoriteController as ApiCustomerFavoriteController;
// use App\Http\Controllers\Api\Customer\ProductVariantController as ApiCustomerProductVariantController;
// use App\Http\Controllers\Api\Customer\CartController as ApiCustomerCartController;

// ===== WEB ROUTES (giao diện web, view) =====
Route::middleware([CartCountMiddleware::class, 'phone.required'])->group(function () {
    Route::get('/', [CustomerHomeController::class, 'index'])->name('home');

    // Search
    Route::get('/search', [CustomerHomeController::class, 'search'])->name('customer.search');
    Route::post('/search/ajax', [CustomerHomeController::class, 'searchAjax'])->name('customer.search.ajax');

    // Product
    Route::get('/shop/products', [CustomerProductController::class, 'index'])->name('products.index');
    Route::get('/shop/products/{slug}', [CustomerProductController::class, 'show'])->name('products.show');
    Route::get('/shop/combos/{slug}', [CustomerProductController::class, 'showComboDetail'])->name('combos.show');
    Route::post('/products/get-applicable-discounts', [CustomerProductController::class, 'getApplicableDiscounts'])->name('products.get-applicable-discounts');

    // Wishlist
    Route::get('/wishlist', [CustomerWishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [CustomerWishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist', [CustomerWishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Cart
    Route::get('/cart', [CustomerCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CustomerCartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/add-combo', [CustomerCartController::class, 'addComboToCart'])->name('cart.addCombo');
    Route::post('/cart/update', [CustomerCartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CustomerCartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CustomerCartController::class, 'clear'])->name('cart.clear');

    // Coupon
    Route::post('/coupon/apply', [CustomerCouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [CustomerCouponController::class, 'remove'])->name('coupon.remove');

    // Checkout
    Route::get('/checkout', [CustomerCheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CustomerCheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CustomerCheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/continue-payment/{order}', [CustomerCheckoutController::class, 'continuePayment'])->name('checkout.continuePayment');
    // --- Thêm route cho Mua ngay ---
    Route::post('/checkout/combo-buy-now', [CustomerCheckoutController::class, 'comboBuyNow'])->name('checkout.comboBuyNow');
    Route::post('/checkout/product-buy-now', [CustomerCheckoutController::class, 'productBuyNow'])->name('checkout.productBuyNow');

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
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('customer.register');
    Route::post('/register-temp', [RegisterController::class, 'registerTemp'])->name('customer.register.post');;
    Route::get('/verify-otp', [RegisterController::class, 'showOTPForm'])->name('customer.verify.otp.show');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('customer.verify.otp.post');
    Route::post('/resend-otp', [RegisterController::class, 'resendOTP'])->name('customer.resend.otp');
    Route::post('/check-otp-lock', [RegisterController::class, 'checkOtpLock'])->name('customer.check.otp.lock');

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
    Route::post('/products/{id}/review', [CustomerProductController::class, 'submitReview'])->name('products.review');
    Route::delete('/reviews/{id}', [CustomerProductController::class, 'destroyReview'])->name('reviews.destroy');
    Route::post('/reviews/{review}/reply', [ReviewReplyController::class, 'store'])->name('reviews.reply');
    Route::delete('/review-replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('review-replies.destroy');
    Route::post('/reviews/{id}/helpful', [CustomerProductController::class, 'markHelpful'])->name('reviews.helpful');
    Route::delete('/reviews/{id}/helpful', [CustomerProductController::class, 'unmarkHelpful'])->name('reviews.unhelpful');
    Route::post('/reviews/{id}/report', [CustomerProductController::class, 'reportReview'])->name('reviews.report');
    
    // Driver Rating routes
    Route::get('/orders/{order}/rate-driver', [DriverRatingController::class, 'showRatingForm'])->name('driver.rating.show');
    Route::post('/orders/{order}/rate-driver', [DriverRatingController::class, 'submitRating'])->name('driver.rating.submit');
    
    // Route để hiển thị trang "Tất cả đơn hàng"
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');

    // Route để hiển thị trang "Chi tiết đơn hàng"
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/orders/{order}/partial', [CustomerOrderController::class, 'partial'])->name('customer.orders.partial');
    Route::post('/orders/{order}/status', [CustomerOrderController::class, 'updateStatus'])->name('customer.orders.updateStatus');
    Route::get('/orders/list', [CustomerOrderController::class, 'listPartial'])->name('customer.orders.listPartial');
    Route::get('/profile/addresses', [CustomerProfileController::class, 'getAddresses'])->name('customer.profile.addresses.index');
    Route::post('/profile/addresses', [CustomerProfileController::class, 'storeAddress'])->name('customer.profile.addresses.store');
    Route::put('/profile/addresses/{id}', [CustomerProfileController::class, 'updateAddress'])->name('customer.profile.addresses.update');
    Route::delete('/profile/addresses/{id}', [CustomerProfileController::class, 'deleteAddress'])->name('customer.profile.addresses.delete');
    
    // Address Controller routes (alternative endpoints)
    Route::post('/addresses', [CustomerAddressController::class, 'store'])->name('customer.addresses.store');
    Route::put('/addresses/{id}', [CustomerAddressController::class, 'update'])->name('customer.addresses.update');
    
    // Wallet routes
    Route::prefix('wallet')->name('customer.wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/retry-payment/{transactionId}', [WalletController::class, 'retryPayment'])->name('retry-payment');
        Route::post('/continue-payment/{transactionId}', [WalletController::class, 'continuePayment'])->name('continue-payment');
        Route::post('/cancel-transaction/{transactionId}', [WalletController::class, 'cancelTransaction'])->name('cancel-transaction');
        Route::get('/check-status/{transactionId}', [WalletController::class, 'checkTransactionStatus'])->name('check-status');
        Route::get('/pending-transactions', [WalletController::class, 'getPendingTransactions'])->name('pending-transactions');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        Route::post('/update-expired', [WalletController::class, 'updateExpiredTransactions'])->name('update-expired');
        Route::post('/expire-transactions', [WalletController::class, 'expireTransactions'])->name('expire-transactions');
        Route::get('/transaction/{transactionId}/countdown', [WalletController::class, 'getTransactionWithCountdown'])->name('transaction.countdown');
        

        
        // VNPay routes
        Route::get('/vnpay/return', [WalletController::class, 'vnpayReturn'])->name('vnpay.return');
        Route::post('/vnpay/ipn', [WalletController::class, 'vnpayIpn'])->name('vnpay.ipn');
    });
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
    Route::post('/chat/typing', [ChatController::class, 'typingIndicator'])->name('customer.chat.typing');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');


    Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
        return Broadcast::auth($request);
    })->middleware(['web']);
});

// Route for guest to track order
Route::get('/track', [CustomerOrderController::class, 'showTrackingForm'])->name('customer.order.track.form');
Route::post('/track', [CustomerOrderController::class, 'orderTrackingForGuest'])->name('customer.order.track.submit');
Route::get('/track/{order_code}', [CustomerOrderController::class, 'orderTrackingForGuest'])->name('customer.order.track');

// Route test để debug products
Route::get('/debug-products', function() {
    $products = \App\Models\Product::select('id', 'name', 'slug')->get();
    return response()->json([
        'total' => $products->count(),
        'products' => $products->toArray()
    ]);
});

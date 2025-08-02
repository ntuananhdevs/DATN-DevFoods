<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\ProfileController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/checkout/success', [App\Http\Controllers\Customer\CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/checkout/process', [App\Http\Controllers\Customer\CheckoutController::class, 'process'])->name('checkout.process');

//VNPAY Routes
Route::get('/checkout/vnpay-return', [App\Http\Controllers\Customer\CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay_return');
Route::get('/checkout/vnpay-ipn', [App\Http\Controllers\Customer\CheckoutController::class, 'vnpayIpn'])->name('checkout.vnpay_ipn');

Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::get('/customer/profile/branches-map', [ProfileController::class, 'getBranchesForMap']);

Route::get('/test-notification-debug', function () {
    return view('test-notification-debug');
})->middleware('auth');

Route::post('/api/test-notification', function () {
    $order = \App\Models\Order::where('customer_id', auth()->id())->latest()->first();
    if (!$order) {
        return response()->json(['error' => 'No order found'], 404);
    }
    
    // Trigger the event
    event(new \App\Events\Order\OrderStatusUpdated($order, false, $order->status, $order->status));
    
    return response()->json([
        'success' => true,
        'order_id' => $order->id,
        'status' => $order->status,
        'customer_id' => $order->customer_id
    ]);
})->middleware('auth');

Route::post('/update-product-quantity', function (Request $request) {
    try {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'branch_id' => 'required|exists:branches,id',
            'variant_id' => 'required|exists:product_variants,id'
        ]);

        // Tìm sản phẩm có slug burger-ga-gion
        $product = \App\Models\Product::where('slug', 'burger-ga-gion')->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm burger-ga-gion'
            ], 404);
        }

        // Cập nhật số lượng trong BranchStock
        \App\Models\BranchStock::updateOrCreate(
            [
                'branch_id' => $request->branch_id,
                'product_variant_id' => $request->variant_id
            ],
            [
                'stock_quantity' => $request->quantity
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật số lượng thành công',
            'product' => $product->name,
            'quantity' => $request->quantity
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ], 500);
    }
})->name('update.product.quantity');

// Route test đơn giản - cập nhật số lượng burger-ga-gion
Route::get('/test-update-burger-quantity/{quantity}', function ($quantity) {
    try {
        // Tìm sản phẩm burger-ga-gion
        $product = \App\Models\Product::where('slug', 'burger-ga-gion')->first();

        if (!$product) {
            return "Không tìm thấy sản phẩm burger-ga-gion";
        }

        // Lấy variant đầu tiên của sản phẩm
        $variant = $product->variants()->first();
        if (!$variant) {
            return "Không tìm thấy variant cho sản phẩm này";
        }

        // Lấy branch đầu tiên
        $branch = \App\Models\Branch::first();
        if (!$branch) {
            return "Không tìm thấy branch nào";
        }

        // Cập nhật số lượng
        \App\Models\BranchStock::updateOrCreate(
            [
                'branch_id' => $branch->id,
                'product_variant_id' => $variant->id
            ],
            [
                'stock_quantity' => $quantity
            ]
        );

        return "✅ Cập nhật thành công!<br>" .
               "Sản phẩm: {$product->name}<br>" .
               "Branch: {$branch->name}<br>" .
               "Số lượng: {$quantity}<br>" .
               "Variant ID: {$variant->id}<br>" .
               "Branch ID: {$branch->id}";

    } catch (\Exception $e) {
        return "❌ Lỗi: " . $e->getMessage();
    }
})->name('test.update.burger.quantity');

// Route test cập nhật tất cả variant của burger-ga-gion về cùng số lượng cho tất cả branch và topping liên quan
Route::get('/test-update-burger-all-variants/{quantity}', function ($quantity) {
    try {
        $product = \App\Models\Product::where('slug', 'burger-ga-gion')->first();
        if (!$product) {
            return "Không tìm thấy sản phẩm burger-ga-gion";
        }
        $variants = $product->variants()->get();
        if ($variants->isEmpty()) {
            return "Không tìm thấy variant nào cho sản phẩm này";
        }
        $branches = \App\Models\Branch::all();
        if ($branches->isEmpty()) {
            return "Không tìm thấy branch nào";
        }
        $count = 0;
        foreach ($variants as $variant) {
            foreach ($branches as $branch) {
                \App\Models\BranchStock::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'product_variant_id' => $variant->id
                    ],
                    [
                        'stock_quantity' => $quantity
                    ]
                );
                $count++;
            }
        }
        // Cập nhật topping liên quan
        $toppings = $product->toppings()->get();
        $toppingCount = 0;
        foreach ($toppings as $topping) {
            foreach ($branches as $branch) {
                \App\Models\ToppingStock::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'topping_id' => $topping->id
                    ],
                    [
                        'stock_quantity' => $quantity
                    ]
                );
                $toppingCount++;
            }
        }
        return "✅ Đã cập nhật thành công cho {$count} biến thể/chi nhánh và {$toppingCount} topping/chi nhánh!<br>" .
               "Sản phẩm: {$product->name}<br>" .
               "Số lượng mỗi variant/topping/branch: {$quantity}";
    } catch (\Exception $e) {
        return "❌ Lỗi: " . $e->getMessage();
    }
})->name('test.update.burger.all.variants');

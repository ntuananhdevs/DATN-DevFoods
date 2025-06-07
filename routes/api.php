<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\TestController;
use App\Http\Controllers\Api\Customer\ProductController;


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Product listing API (for AJAX) - Add web middleware for session support
Route::middleware('web')->get('/products', [ProductController::class, 'getProducts']);

Route::group([
  'prefix' => 'auth'
], function () {
  // Route::post('login', 'AuthController@login');
  // Route::post('register', 'AuthController@register');

  // Route::group([
  //   'middleware' => 'auth:api'
  // ], function() {
  //     Route::get('logout', 'AuthController@logout');
  //     Route::get('user', 'AuthController@user');
  // });
});

// Test S3 API Routes - Commented out due to missing TestController
/*
Route::prefix('test')->name('api.test.')->group(function () {
    Route::post('/upload', [TestController::class, 'uploadImage'])->name('upload.image');
    Route::get('/images', [TestController::class, 'listImages'])->name('images.list');
    Route::delete('/images/{filename}', [TestController::class, 'deleteImage'])->name('images.delete');
    Route::get('/connection', [TestController::class, 'testConnection'])->name('connection');
});
*/


// Rutas que requieren autenticación
Route::middleware('auth:api')->group(function () {
    // Otras rutas autenticadas aquí
});

// Customer API routes
Route::prefix('customer')->name('api.')->group(function () {
    Route::post('/products/get-variant', [\App\Http\Controllers\Api\Customer\ProductVariantController::class, 'getVariant'])->name('products.get-variant');
    
    // Branch routes - Add web middleware for session support
    Route::middleware('web')->group(function () {
        Route::post('/branches/set-selected', [\App\Http\Controllers\Api\Customer\BranchController::class, 'setSelectedBranch'])->name('branches.set-selected');
        Route::get('/branches/nearest', [\App\Http\Controllers\Api\Customer\BranchController::class, 'findNearestBranch'])->name('branches.nearest');
    });
});

// Cart and favorites routes
Route::middleware('web')->group(function () {
    Route::post('/cart/add', [\App\Http\Controllers\Api\Customer\CartController::class, 'add']);
    Route::post('/favorites/toggle', [\App\Http\Controllers\Api\Customer\FavoriteController::class, 'toggle']);
});

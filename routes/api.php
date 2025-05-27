<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
  'prefix' => 'auth'
], function () {
  Route::post('login', 'AuthController@login');
  Route::post('register', 'AuthController@register');

  Route::group([
    'middleware' => 'auth:api'
  ], function() {
      Route::get('logout', 'AuthController@logout');
      Route::get('user', 'AuthController@user');
  });
});

// Test S3 API Routes
Route::prefix('test')->name('api.test.')->group(function () {
    Route::post('/upload', [TestController::class, 'uploadImage'])->name('upload.image');
    Route::get('/images', [TestController::class, 'listImages'])->name('images.list');
    Route::delete('/images/{filename}', [TestController::class, 'deleteImage'])->name('images.delete');
    Route::get('/connection', [TestController::class, 'testConnection'])->name('connection');
});

// Rutas para funcionalidad en tiempo real

// Favoritos y carrito - accesibles sin autenticación pero con sesión
Route::middleware('web')->group(function () {
    Route::post('/favorites/toggle', 'App\Http\Controllers\Api\Customer\FavoriteController@toggle');
    
    // Carrito
    Route::post('/cart/add', 'App\Http\Controllers\Api\Customer\CartController@add');
    Route::post('/cart/update', 'App\Http\Controllers\Api\Customer\CartController@update');
    Route::post('/cart/remove', 'App\Http\Controllers\Api\Customer\CartController@remove');
    Route::post('/coupon/apply', 'App\Http\Controllers\Api\Customer\CartController@applyCoupon');
    
    // Products
    Route::get('/products', 'App\Http\Controllers\Api\Customer\ProductController@getProducts');
});

// Rutas que requieren autenticación
Route::middleware('auth:api')->group(function () {
    // Otras rutas autenticadas aquí
});

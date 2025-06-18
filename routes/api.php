<?php

use App\Http\Controllers\Api\ChatController; // Correct namespace for the API ChatController
use App\Http\Controllers\Api\ConversationController;
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
  ], function () {
    Route::get('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
  });
});

// // Test S3 API Routes
// Route::prefix('test')->name('api.test.')->group(function () {
//   Route::post('/upload', [TestController::class, 'uploadImage'])->name('upload.image');
//   Route::get('/images', [TestController::class, 'listImages'])->name('images.list');
//   Route::delete('/images/{filename}', [TestController::class, 'deleteImage'])->name('images.delete');
//   Route::get('/connection', [TestController::class, 'testConnection'])->name('connection');
// });

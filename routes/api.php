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

use App\Http\Controllers\Api\ChatController;
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



// API Routes for Chat - Bỏ middleware auth để test
Route::group(['prefix' => 'conversations'], function () {
  Route::get('/{id}/messages', [ConversationController::class, 'getMessages']);
  Route::get('/{id}', [ConversationController::class, 'getConversation']);
});

// Chat routes
Route::middleware('auth:sanctum')->group(function () {
  // Customer routes
  Route::post('/conversations', [ConversationController::class, 'store']);
  Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);

  // Super admin routes
  Route::middleware('role:super_admin')->group(function () {
    Route::get('/conversations/new', [ConversationController::class, 'getNewConversations']);
    Route::patch('/conversations/{conversation}/distribute', [ConversationController::class, 'distribute']);
  });

  // Branch admin routes
  Route::middleware('role:branch_admin')->group(function () {
    Route::get('/conversations/branch', [ConversationController::class, 'getBranchConversations']);
  });
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
Route::prefix('conversations')->group(function () {
  Route::post('/', [ChatController::class, 'createConversation'])->name('conversations.create'); // Tạo cuộc trò chuyện mới
  Route::post('/{conversationId}/messages', [ChatController::class, 'sendMessage'])->name('conversations.messages.send'); // Gửi tin nhắn
  Route::get('/{conversationId}/messages', [ChatController::class, 'getMessages'])->name('conversations.messages.get'); // Lấy tin nhắn
  Route::patch('/{conversationId}', [ChatController::class, 'distributeConversation'])->name('conversations.distribute'); // Phân phối chat
});

// Gửi tin nhắn trực tiếp (không theo conversation)
Route::post('/send-direct', [ChatController::class, 'sendDirectMessage'])->name('chat.direct');

Route::patch('/conversations/{id}/distribute', [ChatController::class, 'distributeConversation'])->name('conversations.distribute.branch'); // Phân phối cuộc trò chuyện

Route::get('/chat/customer/{conversationId}', [ChatController::class, 'customerChat'])->name('chat.customer'); // Khách hàng chat

Route::get('/chat/branch/{conversationId}', [ChatController::class, 'branchChat'])->name('chat.branch'); // Chi nhánh chat
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

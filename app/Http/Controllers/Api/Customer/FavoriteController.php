<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Events\Customer\FavoriteUpdated;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle the favorite status of a product
     */
    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'is_favorite' => 'required|boolean'
            ]);

            $productId = $request->product_id;
            $isFavorite = $request->is_favorite;
            $message = '';

            if (Auth::check()) {
                $userId = Auth::id();
                \Log::info('Processing favorite toggle', [
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'is_favorite' => $isFavorite
                ]);
                
                $favorite = Favorite::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first();

                if ($isFavorite) {
                    if (!$favorite) {
                        Favorite::create([
                            'user_id' => $userId,
                            'product_id' => $productId
                        ]);
                    }
                    $message = 'Đã thêm vào danh sách yêu thích';
                } else {
                    if ($favorite) {
                        $favorite->delete();
                    }
                    $message = 'Đã xóa khỏi danh sách yêu thích';
                }

                // Get current wishlist count
                $wishlistCount = Favorite::where('user_id', $userId)->count();
                \Log::info('Current wishlist count', ['count' => $wishlistCount]);
                
                // Broadcast event with count
                try {
                    $event = new FavoriteUpdated($userId, $productId, $isFavorite, $wishlistCount);
                    event($event);
                    \Log::info('FavoriteUpdated event broadcasted', [
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'is_favorite' => $isFavorite,
                        'count' => $wishlistCount
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error broadcasting event: ' . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_favorite' => $isFavorite,
                    'count' => $wishlistCount
                ]);
            } else {
                // Handle session-based favorites for guests
                $sessionFavorites = $request->session()->get('wishlist_items', []);
                
                if ($isFavorite) {
                    if (!in_array($productId, $sessionFavorites)) {
                        $sessionFavorites[] = $productId;
                    }
                    $message = 'Đã thêm vào danh sách yêu thích';
                } else {
                    $sessionFavorites = array_filter($sessionFavorites, function($id) use ($productId) {
                        return $id != $productId;
                    });
                    $message = 'Đã xóa khỏi danh sách yêu thích';
                }
                
                $request->session()->put('wishlist_items', $sessionFavorites);
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_favorite' => $isFavorite,
                    'count' => count($sessionFavorites)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in toggle favorite: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
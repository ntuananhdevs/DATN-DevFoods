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

            // Verificar si el usuario está autenticado
            if (Auth::check()) {
                $userId = Auth::id();
                
                // Verificar si ya existe el favorito
                $favorite = Favorite::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first();

                if ($isFavorite) {
                    // Añadir a favoritos
                    if (!$favorite) {
                        $favorite = Favorite::create([
                            'user_id' => $userId,
                            'product_id' => $productId
                        ]);
                    }
                    $message = 'Đã thêm vào danh sách yêu thích';
                } else {
                    // Eliminar de favoritos
                    if ($favorite) {
                        $favorite->delete();
                    }
                    $message = 'Đã xóa khỏi danh sách yêu thích';
                }

                // Comentar la línea de Pusher temporalmente para evitar errores
                event(new FavoriteUpdated($userId, $productId, $isFavorite));
            } else {
                // Manejar favoritos para usuarios no autenticados usando sesión
                $sessionFavorites = $request->session()->get('wishlist_items', []);
                
                if ($isFavorite) {
                    // Añadir a favoritos en sesión
                    if (!in_array($productId, $sessionFavorites)) {
                        $sessionFavorites[] = $productId;
                    }
                    $message = 'Đã thêm vào danh sách yêu thích';
                } else {
                    // Eliminar de favoritos en sesión
                    $sessionFavorites = array_filter($sessionFavorites, function($id) use ($productId) {
                        return $id != $productId;
                    });
                    $message = 'Đã xóa khỏi danh sách yêu thích';
                }
                
                // Guardar favoritos actualizados en sesión
                $request->session()->put('wishlist_items', $sessionFavorites);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorite' => $isFavorite
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en toggle favoritos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
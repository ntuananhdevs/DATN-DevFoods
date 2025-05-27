<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Favorite;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Get products list with filtering and sorting for AJAX
     */
    public function getProducts(Request $request)
    {
        try {
            // Query cơ bản cho products
            $query = Product::with([
                'category', 
                'images' => function($query) {
                    $query->orderBy('is_primary', 'desc');
                }, 
                'reviews' => function($query) {
                    $query->where('approved', true);
                }
            ])
            ->where('status', 'selling');

            // Filter theo category nếu có
            if ($request->has('category') && $request->category != '') {
                $query->where('category_id', $request->category);
            }

            // Search theo tên sản phẩm
            if ($request->has('search') && $request->search != '') {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Sắp xếp
            $sortBy = $request->get('sort', 'created_at');
            switch ($sortBy) {
                case 'price-asc':
                    $query->orderBy('base_price', 'asc');
                    break;
                case 'price-desc':
                    $query->orderBy('base_price', 'desc');
                    break;
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'popular':
                    $query->withCount('reviews')
                        ->orderBy('reviews_count', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $products = $query->paginate(12);
            
            // Obtener favoritos del usuario
            $favorites = [];
            if (auth()->check()) {
                // Si el usuario está autenticado, obtener de la base de datos
                $favorites = Favorite::where('user_id', auth()->id())
                    ->pluck('product_id')
                    ->toArray();
            } elseif ($request->session()->has('wishlist_items')) {
                // Si no está autenticado pero tiene favoritos en sesión
                $favorites = $request->session()->get('wishlist_items', []);
            }
            
            // Thêm thông tin rating trung bình cho mỗi sản phẩm
            $products->getCollection()->transform(function ($product) use ($favorites) {
                $product->average_rating = $product->reviews->avg('rating') ?? 0;
                $product->reviews_count = $product->reviews->count();
                $product->primary_image = $product->images->where('is_primary', true)->first() 
                                        ?? $product->images->first();
                
                // Transform image URL to S3 URL if using S3
                if ($product->primary_image) {
                    $product->primary_image->s3_url = Storage::disk('s3')->url($product->primary_image->img);
                }
                
                // Marcar como favorito si está en la lista
                $product->is_favorite = in_array($product->id, $favorites);
                
                // Obtener la primera variante del producto que tenga stock disponible en cualquier sucursal
                $product->first_variant = ProductVariant::where('product_id', $product->id)
                                            ->whereHas('branchStocks', function($query) {
                                                $query->where('stock_quantity', '>', 0);
                                            })
                                            ->orderBy('id', 'asc')
                                            ->first();
                
                return $product;
            });
            
            return response()->json([
                'success' => true,
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products: ' . $e->getMessage()
            ], 500);
        }
    }
}

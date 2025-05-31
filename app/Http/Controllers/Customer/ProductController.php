<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Branch;
use App\Models\Favorite;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {        
        // Get selected branch ID from session
        $selectedBranchId = session('selected_branch');
        
        // Lấy tất cả categories để hiển thị filter
        $categories = Category::where('status', true)
            ->withCount(['products' => function($query) {
                $query->where('status', 'selling');
            }])
            ->get();

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
        
        // Filter products by selected branch (only show products available in this branch)
        if ($selectedBranchId) {
            $query->whereHas('variants', function($q) use ($selectedBranchId) {
                $q->whereHas('branchStocks', function($q2) use ($selectedBranchId) {
                    $q2->where('branch_id', $selectedBranchId)
                       ->where('stock_quantity', '>', 0);
                });
            });
        }

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
        $products->getCollection()->transform(function ($product) use ($favorites, $selectedBranchId) {
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
            
            // Obtener la primera variante del producto que tenga stock disponible en la sucursal seleccionada
            $productVariantQuery = ProductVariant::where('product_id', $product->id);
            
            if ($selectedBranchId) {
                $productVariantQuery->whereHas('branchStocks', function($query) use ($selectedBranchId) {
                    $query->where('branch_id', $selectedBranchId)
                          ->where('stock_quantity', '>', 0);
                });
            } else {
                $productVariantQuery->whereHas('branchStocks', function($query) {
                    $query->where('stock_quantity', '>', 0);
                });
            }
            
            $product->first_variant = $productVariantQuery->orderBy('id', 'asc')->first();
            
            return $product;
        });
        
        return view("customer.shop.index", compact('products', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Get selected branch ID from session
        $selectedBranchId = session('selected_branch');
        
        $product = Product::with([
            'category',
            'images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'reviews' => function($query) {
                $query->where('approved', true)
                      ->with('user')
                      ->orderBy('created_at', 'desc');
            },
            'variants.variantValues.attribute',
            'toppings' => function($query) {
                $query->where('active', true);
            }
        ])->findOrFail($id);

        // Tính toán thông tin rating
        $product->average_rating = $product->reviews->avg('rating') ?? 0;
        $product->reviews_count = $product->reviews->count();
        
        // Add S3 URLs to all product images
        foreach ($product->images as $image) {
            $image->s3_url = Storage::disk('s3')->url($image->img);
        }

        // Lấy các variant attributes và values
        $variantAttributes = VariantAttribute::with([
            'values' => function($query) use ($product) {
                $query->whereHas('productVariants', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                });
            }
        ])->get();

        // Lấy các sản phẩm liên quan cùng danh mục
        $relatedProductsQuery = Product::with([
            'category',
            'images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'reviews' => function($query) {
                $query->where('approved', true);
            }
        ])
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('status', 'selling');
        
        // Filter related products by selected branch
        if ($selectedBranchId) {
            $relatedProductsQuery->whereHas('variants', function($q) use ($selectedBranchId) {
                $q->whereHas('branchStocks', function($q2) use ($selectedBranchId) {
                    $q2->where('branch_id', $selectedBranchId)
                       ->where('stock_quantity', '>', 0);
                });
            });
        }
        
        $relatedProducts = $relatedProductsQuery->limit(4)->get();

        // Thêm thông tin rating cho related products
        $relatedProducts->transform(function ($relatedProduct) {
            $relatedProduct->average_rating = $relatedProduct->reviews->avg('rating') ?? 0;
            $relatedProduct->reviews_count = $relatedProduct->reviews->count();
            
            // Add primary image and S3 URL
            $relatedProduct->primary_image = $relatedProduct->images->where('is_primary', true)->first() 
                                    ?? $relatedProduct->images->first();
            
            if ($relatedProduct->primary_image) {
                $relatedProduct->primary_image->s3_url = Storage::disk('s3')->url($relatedProduct->primary_image->img);
            }
            
            return $relatedProduct;
        });

        // If branch is selected, only show that branch, otherwise show all active branches
        if ($selectedBranchId) {
            $branches = Branch::where('id', $selectedBranchId)
                ->where('active', true)
                ->get();
        } else {
            // Lấy danh sách chi nhánh có sản phẩm
            $branches = Branch::whereHas('stocks', function($query) use ($product) {
                $query->whereHas('productVariant', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                });
            })
            ->where('active', true)
            ->get();
        }

        return view('customer.shop.show', compact(
            'product',
            'variantAttributes',
            'relatedProducts',
            'branches'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

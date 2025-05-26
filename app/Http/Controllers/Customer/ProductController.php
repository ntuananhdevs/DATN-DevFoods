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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {        
        // Lấy tất cả categories để hiển thị filter
        $categories = Category::where('status', 'active')
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
        
        // Thêm thông tin rating trung bình cho mỗi sản phẩm
        $products->getCollection()->transform(function ($product) {
            $product->average_rating = $product->reviews->avg('rating') ?? 0;
            $product->reviews_count = $product->reviews->count();
            $product->primary_image = $product->images->where('is_primary', true)->first() 
                                    ?? $product->images->first();
            
            // Transform image URL to S3 URL if using S3
            if ($product->primary_image) {
                $product->primary_image->s3_url = Storage::disk('s3')->url($product->primary_image->img);
            }
            
            return $product;
        });
        
        return view("customer.shop.index", compact('products', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
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

        // Lấy các variant attributes và values
        $variantAttributes = VariantAttribute::with([
            'values' => function($query) use ($product) {
                $query->whereHas('productVariants', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                });
            }
        ])->get();

        // Lấy các sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::with([
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
        ->where('status', 'selling')
        ->limit(4)
        ->get();

        // Thêm thông tin rating cho related products
        $relatedProducts->transform(function ($relatedProduct) {
            $relatedProduct->average_rating = $relatedProduct->reviews->avg('rating') ?? 0;
            $relatedProduct->reviews_count = $relatedProduct->reviews->count();
            return $relatedProduct;
        });

        // Lấy danh sách chi nhánh có sản phẩm
        $branches = Branch::whereHas('stocks', function($query) use ($product) {
            $query->whereHas('productVariant', function($q) use ($product) {
                $q->where('product_id', $product->id);
            });
        })
        ->where('active', true)
        ->get();

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

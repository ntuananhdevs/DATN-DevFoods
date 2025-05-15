<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy danh sách sản phẩm có stock = 1 (còn hàng)
        $products = Product::where('stock', 1)
            ->with('category')
            ->orderBy('created_at', 'desc') // Sort by newest first
            ->paginate(12); // Phân trang, 12 sản phẩm mỗi trang

        return view("customer.shop.product-list", compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tải sản phẩm với các quan hệ cần thiết
        $product = Product::with([
            'category',
            'variants.attributeValues.attribute', // Tải attributeValues và attribute liên quan
            'reviews.user'
        ])->findOrFail($id);

        // Lấy danh sách thuộc tính của sản phẩm
        $productAttributes = $product->attributes()->with(['values' => function($query) use ($product) {
            $query->whereHas('productVariants', function($subQuery) use ($product) {
                $subQuery->where('product_id', $product->id);
            });
        }])->get();

        // Chuẩn bị dữ liệu biến thể cho JavaScript
        $variantsData = [];
        foreach ($product->variants as $variant) {
            $attributeValues = [];
            foreach ($variant->attributeValues as $value) {
                $attributeValues[$value->attribute_id] = $value->id;
            }
            
            $variantsData[$variant->id] = [
                'id' => $variant->id,
                'name' => $variant->name,
                'price' => $variant->price,
                'image' => $variant->image ?? $product->image,
                'stock_quantity' => $variant->stock_quantity ?? 0,
                'sku' => $variant->sku,
                'attributes' => $attributeValues
            ];
        }

        // Lấy sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('stock', 1)
            ->take(4)
            ->get();

        return view('customer.shop.product-detail', compact(
            'product',
            'productAttributes',
            'variantsData',
            'relatedProducts'
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

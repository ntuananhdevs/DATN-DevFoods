<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\VariantAttribute;

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
        $product = Product::with(['category', 'variants.variantValues.variantAttribute'])->findOrFail($id);
        
        // Lấy tất cả thuộc tính biến thể của sản phẩm này
        $variantAttributes = VariantAttribute::whereHas('variantValues', function($query) use ($product) {
            $query->whereHas('productVariant', function($q) use ($product) {
                $q->where('product_id', $product->id);
            });
        })->with(['variantValues' => function($query) use ($product) {
            $query->whereHas('productVariant', function($q) use ($product) {
                $q->where('product_id', $product->id);
            });
        }])->get();
        
        // Chuẩn bị dữ liệu biến thể cho JavaScript
        $variantsData = [];
        foreach ($product->variants as $variant) {
            $attributes = [];
            foreach ($variant->variantValues as $value) {
                $attributes[$value->variantAttribute->id] = $value->id;
            }
            
            $variantsData[$variant->id] = [
                'id' => $variant->id,
                'name' => $variant->name,
                'price' => $variant->price,
                'attributes' => $attributes
            ];
        }
        
        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('stock', 1)
            ->take(4)
            ->get();
        
        return view('customer.shop.product-detail', compact('product', 'variantAttributes', 'variantsData', 'relatedProducts'));
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
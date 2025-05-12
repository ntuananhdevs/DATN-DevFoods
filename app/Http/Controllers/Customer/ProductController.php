<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

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
    public function show(string $id)
    {
        // Lấy sản phẩm theo ID, kèm danh mục và biến thể
        $product = Product::with([
            'category',
            'variants' => function ($query) {
                $query->where('active', 1)->with('variantValues');
            }
        ])->findOrFail($id);

        // Lấy sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('stock', 1)
            ->with('category')
            ->take(4)
            ->get();

        return view('customer.shop.product-detail', compact('product', 'relatedProducts'));
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
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {        
        $products = Product::with(['category', 'images', 'reviews'])
            ->where('status', 'selling')
            ->where('available', true)
            ->orderBy('created_at', 'desc')
            ->paginate(8); // Giảm số lượng sản phẩm mỗi trang để phù hợp với grid 2x4
        
        return view("customer.shop.index", compact('products'));
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
        $product = Product::with([
            'category', 
            'images', 
            'reviews.user',
            'variants.variantValues.attribute',
            'variants.branchStocks.branch',
            'toppings'
        ])->findOrFail($id);
    
        // Lấy danh sách chi nhánh có sản phẩm
        $availableBranches = collect();
        foreach ($product->variants as $variant) {
            $branchesWithStock = $variant->branchStocks()
                ->where('stock_quantity', '>', 0)
                ->with('branch')
                ->get()
                ->pluck('branch');
            $availableBranches = $availableBranches->concat($branchesWithStock);
        }
        $availableBranches = $availableBranches->unique('id');
    
        // Lấy các sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::with(['category', 'images', 'reviews'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'selling')
            ->where('available', true)
            ->limit(4)
            ->get();
        
        return view("customer.shop.show", compact('product', 'relatedProducts', 'availableBranches'));
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

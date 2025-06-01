<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantValue;
use Illuminate\Support\Facades\Log;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    /**
     * Get the product variant ID based on selected variant values.
     */
    public function getVariant(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_values' => 'required|array',
                'variant_values.*' => 'exists:variant_values,id'
            ]);

            Log::info('Getting product variant', [
                'product_id' => $request->product_id,
                'variant_values' => $request->variant_values
            ]);

            // Get the product
            $product = Product::findOrFail($request->product_id);
            
            // Find all variants for this product
            $variants = ProductVariant::where('product_id', $product->id)
                ->with('variantValues')
                ->get();
            
            Log::info('Found variants count: ' . $variants->count());
            
            // Convert variant value IDs to integers for reliable comparison
            $requestedValueIds = array_map('intval', $request->variant_values);
            sort($requestedValueIds);
            
            // Find the variant that contains all the requested values
            foreach ($variants as $variant) {
                // Get variant value IDs for this variant and sort them
                $variantValueIds = $variant->variantValues->pluck('id')->map(function($id) {
                    return intval($id);
                })->toArray();
                sort($variantValueIds);
                
                Log::info('Checking variant ' . $variant->id, [
                    'variant_values' => $variantValueIds,
                    'requested_values' => $requestedValueIds
                ]);
                
                // If the arrays match exactly, we found our variant
                if ($variantValueIds == $requestedValueIds) {
                    Log::info('Found matching variant: ' . $variant->id);
                    return response()->json([
                        'success' => true,
                        'variant_id' => $variant->id,
                        'message' => 'Variant found'
                    ]);
                }
            }
            
            // If we get here, no matching variant was found
            Log::warning('No matching variant found for product ' . $product->id);
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy biến thể phù hợp cho sản phẩm này'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error getting product variant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}

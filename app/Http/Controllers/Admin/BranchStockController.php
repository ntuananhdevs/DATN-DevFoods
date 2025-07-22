<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;
use App\Models\BranchStock;
use Illuminate\Support\Facades\DB;

class BranchStockController extends Controller
{
    /**
     * Display the stock management page for a product
     */
    public function index(Product $product)
    {
        $product->load(['variants.variantValues.attribute', 'variants.branchStocks']);
        $branches = Branch::where('active', true)->get();
        
        return view('admin.menu.product.stock', compact('product', 'branches'));
    }

    /**
     * Update stock quantities for a product's variants
     */
    public function update(Request $request, Product $product)
    {
        try {
            $request->validate([
                'stocks' => 'nullable|array',
                'stocks.*' => 'nullable|array',
                'stocks.*.*' => 'nullable|integer|min:0'
            ]);

            // Check if stocks data is provided
            if (!$request->has('stocks') || empty($request->stocks)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu kho hàng nào được cập nhật'
                ]);
            }

            DB::beginTransaction();

            foreach ($request->stocks as $variantId => $branchStocks) {
                foreach ($branchStocks as $branchId => $quantity) {
                    BranchStock::updateOrCreate(
                        [
                            'branch_id' => $branchId,
                            'product_variant_id' => $variantId
                        ],
                        ['stock_quantity' => $quantity]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tồn kho thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock summary for a product
     */
    public function summary(Product $product)
    {
        $stockSummary = DB::table('branch_stocks')
            ->join('product_variants', 'branch_stocks.product_variant_id', '=', 'product_variants.id')
            ->join('branches', 'branch_stocks.branch_id', '=', 'branches.id')
            ->where('product_variants.product_id', $product->id)
            ->select(
                'branches.name as branch_name',
                DB::raw('SUM(branch_stocks.stock_quantity) as total_stock')
            )
            ->groupBy('branches.id', 'branches.name')
            ->get();

        return response()->json($stockSummary);
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        $lowStockThreshold = 10; // Có thể cấu hình trong config

        $lowStockItems = DB::table('branch_stocks')
            ->join('product_variants', 'branch_stocks.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('branches', 'branch_stocks.branch_id', '=', 'branches.id')
            ->where('branch_stocks.stock_quantity', '<', $lowStockThreshold)
            ->where('branch_stocks.stock_quantity', '>', 0)
            ->select(
                'products.name as product_name',
                'branches.name as branch_name',
                'branch_stocks.stock_quantity'
            )
            ->get();

        return response()->json($lowStockItems);
    }

    /**
     * Get out of stock items
     */
    public function outOfStock()
    {
        $outOfStockItems = DB::table('branch_stocks')
            ->join('product_variants', 'branch_stocks.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('branches', 'branch_stocks.branch_id', '=', 'branches.id')
            ->where('branch_stocks.stock_quantity', 0)
            ->select(
                'products.name as product_name',
                'branches.name as branch_name'
            )
            ->get();

        return response()->json($outOfStockItems);
    }
}
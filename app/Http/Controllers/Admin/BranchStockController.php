<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;
use App\Models\BranchStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            // Log incoming request data for debugging
            Log::info('BranchStockController update called', [
                'product_id' => $product->id,
                'stocks_data' => $request->stocks,
                'has_stocks' => $request->has('stocks'),
                'stocks_empty' => empty($request->stocks)
            ]);

            // Check if stocks data is provided
            if (!$request->has('stocks') || empty($request->stocks)) {
                session()->flash('toast', [
                    'type' => 'warning',
                    'message' => 'Không có dữ liệu kho hàng nào được cập nhật'
                ]);
                
                return redirect()->route('admin.products.index');
            }

            DB::beginTransaction();

            // Handle stocks data format: stocks[branchId][variantId] = quantity
            foreach ($request->stocks as $branchId => $variantStocks) {
                // Verify branch exists and is active
                $branch = \App\Models\Branch::where('id', $branchId)->where('active', true)->first();
                if (!$branch) {
                    continue; // Skip invalid or inactive branch
                }
                
                foreach ($variantStocks as $variantId => $quantity) {
                    // Verify variant exists and belongs to the product
                    $variant = $product->variants()->find($variantId);
                    if (!$variant) {
                        continue; // Skip invalid variant
                    }
                    
                    // Ensure quantity is not null and is numeric
                    $quantity = is_numeric($quantity) ? (int)$quantity : 0;
                    
                    $branchStock = BranchStock::updateOrCreate(
                        [
                            'branch_id' => $branchId,
                            'product_variant_id' => $variantId
                        ],
                        ['stock_quantity' => $quantity]
                    );
                    
                    Log::info('Stock updated in BranchStockController', [
                        'branch_id' => $branchId,
                        'variant_id' => $variantId,
                        'quantity' => $quantity,
                        'stock_id' => $branchStock->id
                    ]);
                }
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'Cập nhật tồn kho thành công'
            ]);

            return redirect()->route('admin.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->route('admin.products.index');
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
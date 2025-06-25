<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topping;
use App\Models\Branch;
use App\Models\ToppingStock;
use Illuminate\Support\Facades\DB;

class ToppingStockController extends Controller
{
    /**
     * Display the stock management page for all toppings
     */
    public function index(Request $request)
    {
        $query = Topping::with(['toppingStocks.branch']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by stock status
        if ($request->has('stock_status') && !empty($request->stock_status)) {
            $stockStatuses = $request->stock_status;
            
            $query->where(function($q) use ($stockStatuses) {
                foreach ($stockStatuses as $status) {
                    if ($status === 'in_stock') {
                        $q->orWhereHas('toppingStocks', function($subQ) {
                            $subQ->where('stock_quantity', '>', 10);
                        });
                    } elseif ($status === 'low_stock') {
                        $q->orWhereHas('toppingStocks', function($subQ) {
                            $subQ->where('stock_quantity', '>', 0)
                                 ->where('stock_quantity', '<=', 10);
                        });
                    } elseif ($status === 'out_of_stock') {
                        $q->orWhereDoesntHave('toppingStocks')
                          ->orWhereHas('toppingStocks', function($subQ) {
                              $subQ->where('stock_quantity', 0);
                          });
                    }
                }
            });
        }
        
        $toppings = $query->latest()->paginate(15);
        $branches = Branch::where('active', true)->get();
        
        // Calculate stock statistics
        $totalToppings = Topping::count();
        $lowStockCount = Topping::whereHas('toppingStocks', function($q) {
            $q->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
        })->count();
        $outOfStockCount = Topping::whereDoesntHave('toppingStocks')
            ->orWhereHas('toppingStocks', function($q) {
                $q->where('stock_quantity', 0);
            })->count();
        
        return view('admin.menu.topping.stock-management', compact(
            'toppings', 
            'branches', 
            'totalToppings', 
            'lowStockCount', 
            'outOfStockCount'
        ));
    }
    
    /**
     * Display the stock management page for a specific topping
     */
    public function show(Topping $topping)
    {
        $topping->load('toppingStocks.branch');
        $branches = Branch::where('active', true)->get();
        
        return view('admin.menu.topping.stock', compact('topping', 'branches'));
    }

    /**
     * Update stock quantities for a topping
     */
    public function update(Request $request, Topping $topping)
    {
        try {
            $request->validate([
                'stocks' => 'nullable|array',
                'stocks.*.branch_id' => 'required_with:stocks|exists:branches,id',
                'stocks.*.quantity' => 'required_with:stocks|integer|min:0'
            ]);

            // Check if stocks data is provided
            if (!$request->has('stocks') || empty($request->stocks)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu kho hàng nào được cập nhật'
                ]);
            }

            DB::beginTransaction();

            foreach ($request->stocks as $stock) {
                ToppingStock::updateOrCreate(
                    [
                        'topping_id' => $topping->id,
                        'branch_id' => $stock['branch_id']
                    ],
                    ['stock_quantity' => $stock['quantity']]
                );
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Cập nhật tồn kho thành công'
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back();
        }
    }

    /**
     * Bulk update stock for multiple toppings
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $request->validate([
                'topping_ids' => 'required|array',
                'topping_ids.*' => 'exists:toppings,id',
                'branch_id' => 'required|exists:branches,id',
                'quantity' => 'required|integer|min:0'
            ]);

            DB::beginTransaction();

            foreach ($request->topping_ids as $toppingId) {
                ToppingStock::updateOrCreate(
                    [
                        'topping_id' => $toppingId,
                        'branch_id' => $request->branch_id
                    ],
                    ['stock_quantity' => $request->quantity]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tồn kho hàng loạt thành công'
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
     * Get low stock alerts for toppings
     */
    public function lowStockAlerts()
    {
        $lowStockThreshold = 10;

        $lowStockItems = DB::table('topping_stocks')
            ->join('toppings', 'topping_stocks.topping_id', '=', 'toppings.id')
            ->join('branches', 'topping_stocks.branch_id', '=', 'branches.id')
            ->where('topping_stocks.stock_quantity', '<', $lowStockThreshold)
            ->where('topping_stocks.stock_quantity', '>', 0)
            ->select(
                'toppings.name as topping_name',
                'toppings.sku',
                'branches.name as branch_name',
                'topping_stocks.stock_quantity'
            )
            ->orderBy('topping_stocks.stock_quantity')
            ->get();

        return response()->json($lowStockItems);
    }

    /**
     * Get out of stock toppings
     */
    public function outOfStock()
    {
        $outOfStockItems = DB::table('topping_stocks')
            ->join('toppings', 'topping_stocks.topping_id', '=', 'toppings.id')
            ->join('branches', 'topping_stocks.branch_id', '=', 'branches.id')
            ->where('topping_stocks.stock_quantity', 0)
            ->select(
                'toppings.name as topping_name',
                'toppings.sku',
                'branches.name as branch_name'
            )
            ->get();

        return response()->json($outOfStockItems);
    }

    /**
     * Get stock summary for all toppings
     */
    public function summary()
    {
        $stockSummary = DB::table('topping_stocks')
            ->join('toppings', 'topping_stocks.topping_id', '=', 'toppings.id')
            ->join('branches', 'topping_stocks.branch_id', '=', 'branches.id')
            ->select(
                'toppings.name as topping_name',
                'toppings.sku',
                'branches.name as branch_name',
                'topping_stocks.stock_quantity'
            )
            ->orderBy('toppings.name')
            ->orderBy('branches.name')
            ->get();

        return response()->json($stockSummary);
    }

    /**
     * Export stock data
     */
    public function export(Request $request)
    {
        // Implementation for exporting stock data to Excel/CSV
        // This can be implemented later if needed
        return response()->json([
            'message' => 'Export functionality will be implemented'
        ]);
    }
}
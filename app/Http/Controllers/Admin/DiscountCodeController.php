<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\DiscountCodeBranch;
use App\Models\DiscountCodeProduct;
use App\Models\UserDiscountCode;
use App\Models\DiscountUsageHistory;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Combo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'discount_type' => 'nullable|string'
        ]);

        $search = $request->input('search', '');
        $status = $request->input('status', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $discountType = $request->input('discount_type');
        
        $now = now();
        $query = DiscountCode::with(['createdBy', 'branches', 'products.product', 'products.category', 'products.combo'])
                ->orderBy('display_order', 'asc')
                ->orderBy('start_date', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            switch ($status) {
                case 'active':
                    $query->where('is_active', true)
                          ->where('start_date', '<=', $now)
                          ->where('end_date', '>=', $now);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('is_active', true)
                          ->where('end_date', '<', $now);
                    break;
                case 'upcoming':
                    $query->where('is_active', true)
                          ->where('start_date', '>', $now);
                    break;
            }
        }

        if ($dateFrom) {
            $query->where('end_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('start_date', '<=', $dateTo);
        }

        if ($discountType) {
            $query->where('discount_type', $discountType);
        }

        $discountCodes = $query->paginate(10);

        // Calculate statistics
        $totalCodes = DiscountCode::count();
        $activeCodes = DiscountCode::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();
        $expiringSoon = DiscountCode::where('is_active', true)
            ->where('end_date', '>', $now)
            ->where('end_date', '<=', $now->copy()->addDays(7))
            ->count();
        $expiredCodes = DiscountCode::where('end_date', '<', $now)->count();

        return view('admin.discount_codes.index', compact('discountCodes', 'totalCodes', 'activeCodes', 'expiringSoon', 'expiredCodes'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $combos = Combo::orderBy('name')->get();
        
        return view('admin.discount_codes.create', compact('branches', 'categories', 'products', 'combos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:discount_codes,code',
            'name' => 'required',
            'discount_type' => 'required|in:percentage,fixed_amount,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'applicable_items' => 'nullable|string',
            'applicable_scope' => 'nullable|string',
            'applicable_ranks' => 'nullable|array',
            'applicable_ranks.*' => 'integer|between:1,5',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'valid_days_of_week' => 'nullable|array',
            'valid_days_of_week.*' => 'integer|between:0,6',
            'valid_from_time' => 'nullable|date_format:H:i',
            'valid_to_time' => 'nullable|date_format:H:i|after_or_equal:valid_from_time',
            'usage_type' => 'required|in:public,personal',
            'max_total_usage' => 'nullable|integer|min:0',
            'max_usage_per_user' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        
        try {
            $discountCode = DiscountCode::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $request->image, 
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_order_amount' => $request->min_order_amount ?? 0,
                'max_discount_amount' => $request->max_discount_amount,
                'applicable_scope' => $request->applicable_scope ?? 'all_branches',
                'applicable_items' => $request->applicable_items ?? 'all_items',
                'applicable_ranks' => $request->applicable_ranks,
                'rank_exclusive' => $request->has('rank_exclusive'),
                'valid_days_of_week' => $request->valid_days_of_week,
                'valid_from_time' => $request->valid_from_time,
                'valid_to_time' => $request->valid_to_time,
                'usage_type' => $request->usage_type ?? 'public',
                'max_total_usage' => $request->max_total_usage,
                'max_usage_per_user' => $request->max_usage_per_user ?? 1,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'display_order' => $request->display_order ?? 0,
                'created_by' => Auth::guard('admin')->id(),
            ]);
            
            // Handle specific branches if applicable
            if ($request->applicable_scope === 'specific_branches' && $request->has('branch_ids')) {
                foreach ($request->branch_ids as $branchId) {
                    DiscountCodeBranch::create([
                        'discount_code_id' => $discountCode->id,
                        'branch_id' => $branchId,
                    ]);
                }
            }
            
            // Handle specific products/categories/combos if applicable
            if ($request->applicable_items !== 'all_items' && $request->has('items')) {
                $type = $request->applicable_items;
                $items = $request->items;
                
                foreach ($items as $itemId) {
                    $data = [
                        'discount_code_id' => $discountCode->id,
                        'product_id' => null,
                        'category_id' => null,
                        'combo_id' => null,
                    ];
                    
                    switch ($type) {
                        case 'specific_products':
                            $data['product_id'] = $itemId;
                            break;
                        case 'specific_categories':
                            $data['category_id'] = $itemId;
                            break;
                        case 'combos_only':
                            $data['combo_id'] = $itemId;
                            break;
                    }
                    
                    DiscountCodeProduct::create($data);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.discount_codes.index')->with('success', 'Tạo mã giảm giá thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $discountCode = DiscountCode::with(['branches', 'products'])->findOrFail($id);
        $branches = Branch::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $combos = Combo::orderBy('name')->get();
        
        $selectedBranches = $discountCode->branches->pluck('id')->toArray();
        $selectedProducts = $discountCode->products->where('product_id', '!=', null)->pluck('product_id')->toArray();
        $selectedCategories = $discountCode->products->where('category_id', '!=', null)->pluck('category_id')->toArray();
        $selectedCombos = $discountCode->products->where('combo_id', '!=', null)->pluck('combo_id')->toArray();
        
        return view('admin.discount_codes.edit', compact(
            'discountCode', 'branches', 'categories', 'products', 'combos',
            'selectedBranches', 'selectedProducts', 'selectedCategories', 'selectedCombos'
        ));
    }

    public function update(Request $request, $id)
    {
        $discountCode = DiscountCode::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:discount_codes,code,' . $id,
            'name' => 'required',
            'discount_type' => 'required|in:percentage,fixed_amount,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'applicable_items' => 'nullable|string',
            'applicable_scope' => 'nullable|string',
            'applicable_ranks' => 'nullable|array',
            'applicable_ranks.*' => 'integer|between:1,5',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'valid_days_of_week' => 'nullable|array',
            'valid_days_of_week.*' => 'integer|between:0,6',
            'valid_from_time' => 'nullable|date_format:H:i',
            'valid_to_time' => 'nullable|date_format:H:i|after_or_equal:valid_from_time',
            'usage_type' => 'required|in:public,personal', 
            'max_total_usage' => 'nullable|integer|min:0',
            'max_usage_per_user' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        
        try {
            $discountCode->update([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $request->image,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_order_amount' => $request->min_order_amount ?? 0,
                'max_discount_amount' => $request->max_discount_amount,
                'applicable_scope' => $request->applicable_scope ?? 'all_branches',
                'applicable_items' => $request->applicable_items ?? 'all_items',
                'applicable_ranks' => $request->applicable_ranks,
                'rank_exclusive' => $request->has('rank_exclusive'),
                'valid_days_of_week' => $request->valid_days_of_week,
                'valid_from_time' => $request->valid_from_time,
                'valid_to_time' => $request->valid_to_time,
                'usage_type' => $request->usage_type,
                'max_total_usage' => $request->max_total_usage,
                'max_usage_per_user' => $request->max_usage_per_user ?? 1,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'display_order' => $request->display_order ?? 0
            ]);
            
            // Handle specific branches update if applicable
            if ($request->applicable_scope === 'specific_branches') {
                // Remove existing branch relationships
                DiscountCodeBranch::where('discount_code_id', $discountCode->id)->delete();
                
                // Add new branch relationships
                if ($request->has('branch_ids')) {
                    foreach ($request->branch_ids as $branchId) {
                        DiscountCodeBranch::create([
                            'discount_code_id' => $discountCode->id,
                            'branch_id' => $branchId,
                        ]);
                    }
                }
            }
            
            // Handle specific products/categories/combos update if applicable
            if ($request->applicable_items !== 'all_items') {
                // Remove existing product relationships
                DiscountCodeProduct::where('discount_code_id', $discountCode->id)->delete();
                
                // Add new product relationships
                if ($request->has('items')) {
                    $type = $request->applicable_items;
                    $items = $request->items;
                    
                    foreach ($items as $itemId) {
                        $data = [
                            'discount_code_id' => $discountCode->id,
                            'product_id' => null,
                            'category_id' => null,
                            'combo_id' => null,
                        ];
                        
                        switch ($type) {
                            case 'specific_products':
                                $data['product_id'] = $itemId;
                                break;
                            case 'specific_categories':
                                $data['category_id'] = $itemId;
                                break;
                            case 'combos_only':
                                $data['combo_id'] = $itemId;
                                break;
                        }
                        
                        DiscountCodeProduct::create($data);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.discount_codes.index')->with('success', 'Cập nhật mã giảm giá thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Delete related records first
            DiscountCodeBranch::where('discount_code_id', $id)->delete();
            DiscountCodeProduct::where('discount_code_id', $id)->delete();
            UserDiscountCode::where('discount_code_id', $id)->delete();
            
            // Then delete the discount code
            DiscountCode::findOrFail($id)->delete();
            
            DB::commit();
            
            return redirect()->route('admin.discount_codes.index')->with('success', 'Xóa mã giảm giá thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.discount_codes.index')->with('error', 'Không thể xóa mã giảm giá: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $discountCode = DiscountCode::with([
            'createdBy', 
            'branches', 
            'products.product', 
            'products.category', 
            'products.combo',
            'users.user'
        ])->findOrFail($id);
        
        $usageCount = DiscountUsageHistory::where('discount_code_id', $id)->count();
        $discountCode->current_usage_count = $usageCount;
        
        return view('admin.discount_codes.show', compact('discountCode'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $discountCode = DiscountCode::findOrFail($id);
        $discountCode->update(['is_active' => !$discountCode->is_active]);
        return redirect()->route('admin.discount_codes.index')->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function bulkStatusUpdate(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'is_active' => 'required|boolean']);
        DiscountCode::whereIn('id', $request->ids)->update(['is_active' => $request->is_active]);
        return redirect()->route('admin.discount_codes.index')->with('success', 'Cập nhật trạng thái hàng loạt thành công.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        
        try {
            DB::beginTransaction();
            
            foreach ($request->ids as $id) {
                DiscountCodeBranch::where('discount_code_id', $id)->delete();
                DiscountCodeProduct::where('discount_code_id', $id)->delete();
                UserDiscountCode::where('discount_code_id', $id)->delete();
            }
            
            DiscountCode::whereIn('id', $request->ids)->delete();
            
            DB::commit();
            
            return redirect()->route('admin.discount_codes.index')->with('success', 'Xóa hàng loạt mã giảm giá thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.discount_codes.index')->with('error', 'Không thể xóa mã giảm giá: ' . $e->getMessage());
        }
    }

    public function export()
    {
        // Logic xuất Excel/CSV sử dụng package như Maatwebsite\Excel
        return redirect()->route('admin.discount_codes.index')->with('success', 'Xuất danh sách thành công.');
    }

    public function linkBranch(Request $request, $id)
    {
        $request->validate(['branch_id' => 'required|exists:branches,id']);
        DiscountCodeBranch::create([
            'discount_code_id' => $id,
            'branch_id' => $request->branch_id,
        ]);
        return redirect()->back()->with('success', 'Liên kết chi nhánh thành công.');
    }

    public function unlinkBranch($id, $branch)
    {
        DiscountCodeBranch::where('discount_code_id', $id)
            ->where('branch_id', $branch)
            ->delete();
        return redirect()->back()->with('success', 'Hủy liên kết chi nhánh thành công.');
    }

    public function linkProduct(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'combo_id' => 'nullable|exists:combos,id',
        ]);

        DiscountCodeProduct::create([
            'discount_code_id' => $id,
            'product_id' => $request->product_id,
            'category_id' => $request->category_id,
            'combo_id' => $request->combo_id,
        ]);

        return redirect()->back()->with('success', 'Liên kết sản phẩm/danh mục/combo thành công.');
    }

    public function unlinkProduct($id, $product)
    {
        DiscountCodeProduct::where('discount_code_id', $id)
            ->where(function ($query) use ($product) {
                $query->where('product_id', $product)
                     ->orWhere('category_id', $product)
                     ->orWhere('combo_id', $product);
            })->delete();
        return redirect()->back()->with('success', 'Hủy liên kết sản phẩm/danh mục/combo thành công.');
    }

    public function assignUsers(Request $request, $id)
    {
        $request->validate(['user_ids' => 'required|array', 'user_ids.*' => 'exists:users,id']);
        foreach ($request->user_ids as $user_id) {
            UserDiscountCode::firstOrCreate([
                'discount_code_id' => $id,
                'user_id' => $user_id,
                'status' => 'available',
            ]);
        }
        return redirect()->back()->with('success', 'Gán mã giảm giá cho người dùng thành công.');
    }

    public function unassignUser($id, $user)
    {
        UserDiscountCode::where('discount_code_id', $id)
            ->where('user_id', $user)
            ->delete();
        return redirect()->back()->with('success', 'Hủy gán mã giảm giá thành công.');
    }

    public function usageHistory($id)
    {
        $discountCode = DiscountCode::findOrFail($id);
        $usageHistory = DiscountUsageHistory::with(['discountCode', 'user', 'branch'])
            ->where('discount_code_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.discount_codes.usage_history', compact('discountCode', 'usageHistory'));
    }
}
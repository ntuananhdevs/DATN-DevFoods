<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\DiscountCodeBranch;
use App\Models\DiscountCodeProduct;
use App\Models\UserDiscountCode;
use App\Models\DiscountUsageHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = DiscountCode::with(['createdBy', 'branches', 'products.product', 'products.category', 'products.combo'])
            ->orderBy('display_order', 'asc')
            ->orderBy('start_date', 'desc');

        if ($request->has('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        $discountCodes = $query->paginate(10);

        return view('admin.discount_codes.index', compact('discountCodes'));
    }

    public function create()
    {
        return view('admin.discount_codes.create');
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $discountCode = DiscountCode::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image, // Xử lý upload ảnh nếu cần
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'applicable_scope' => $request->applicable_scope ?? 'all_branches',
            'applicable_items' => $request->applicable_items ?? 'all_items',
            'applicable_ranks' => $request->applicable_ranks, // JSON
            'valid_days_of_week' => $request->valid_days_of_week, // JSON
            'valid_from_time' => $request->valid_from_time,
            'valid_to_time' => $request->valid_to_time,
            'usage_type' => $request->usage_type ?? 'public',
            'max_total_usage' => $request->max_total_usage,
            'max_usage_per_user' => $request->max_usage_per_user ?? 1,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
            'display_order' => $request->display_order ?? 0,
            'created_by' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.discount_codes.index')->with('success', 'Tạo mã giảm giá thành công.');
    }

    public function edit($id)
    {
        $discountCode = DiscountCode::with(['branches', 'products'])->findOrFail($id);
        return view('admin.discount_codes.edit', compact('discountCode'));
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $discountCode->update($request->only([
            'code', 'name', 'description', 'image', 'discount_type', 'discount_value',
            'min_order_amount', 'max_discount_amount', 'applicable_scope', 'applicable_items',
            'applicable_ranks', 'valid_days_of_week', 'valid_from_time', 'valid_to_time',
            'usage_type', 'max_total_usage', 'max_usage_per_user', 'is_active', 'is_featured',
            'display_order'
        ]));

        return redirect()->route('admin.discount_codes.index')->with('success', 'Cập nhật mã giảm giá thành công.');
    }

    public function destroy($id)
    {
        DiscountCode::findOrFail($id)->delete();
        return redirect()->route('admin.discount_codes.index')->with('success', 'Xóa mã giảm giá thành công.');
    }

    public function show($id)
    {
        $discountCode = DiscountCode::with(['createdBy', 'branches', 'products.product', 'products.category', 'products.combo'])
            ->findOrFail($id);
        return view('admin.discount_codes.show', compact('discountCode'));
    }

    public function search(Request $request)
    {
        return $this->index($request);
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
        $usageHistory = DiscountUsageHistory::with(['discountCode', 'user', 'branch'])
            ->where('discount_code_id', $id)
            ->paginate(10);
        return view('admin.discount_codes.usage_history', compact('usageHistory'));
    }
}
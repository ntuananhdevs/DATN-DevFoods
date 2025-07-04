<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Combo;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ComboItem;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ComboBranchStock;
use App\Models\Branch;

class ComboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Combo::with(['category', 'comboItems.productVariant.product', 'createdBy', 'updatedBy']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
            }

            // Filter by category
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Filter by price range
            if ($request->has('price_from') && $request->price_from !== null && $request->price_from !== '') {
                $query->where('price', '>=', $request->price_from);
            }
            if ($request->has('price_to') && $request->price_to !== null && $request->price_to !== '') {
                $query->where('price', '<=', $request->price_to);
            }

            $combos = $query->latest()->paginate(10);
            $categories = Category::where('status', true)->get();
            $minPrice = Combo::min('price') ?? 0;
            $maxPrice = Combo::max('price') ?? 500000;

            // Handle AJAX requests
            if ($request->ajax() || $request->has('ajax')) {
                $totalCombos = Combo::count();
                $activeCombos = Combo::where('status', 'selling')->count();
                $inactiveCombos = Combo::where('status', '!=', 'selling')->count();

                // Load combo items for each combo with proper relationships
                $combosData = $combos->items();
                foreach ($combosData as $combo) {
                    $combo->load(['comboItems.productVariant.product', 'category']);

                    // Add computed properties for frontend
                    $combo->combo_items_count = $combo->comboItems->count();
                    $combo->image_url = $combo->image ? Storage::disk('s3')->url($combo->image) : null;

                    // Add action URLs
                    $combo->show_url = route('admin.combos.show', $combo->id);
                    $combo->edit_url = route('admin.combos.edit', $combo->id);
                    $combo->toggle_status_url = route('admin.combos.toggle-status', $combo->id);
                    $combo->delete_url = route('admin.combos.destroy', $combo->id);

                    // Transform combo items for easier frontend consumption
                    $combo->combo_items = $combo->comboItems->map(function($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'product_variant' => $item->productVariant ? [
                                'id' => $item->productVariant->id,
                                'sku' => $item->productVariant->sku,
                                'price' => $item->productVariant->price,
                                'variant_attribute_value_1' => $item->productVariant->variant_attribute_value_1,
                                'variant_attribute_value_2' => $item->productVariant->variant_attribute_value_2,
                                'product' => $item->productVariant->product ? [
                                    'id' => $item->productVariant->product->id,
                                    'name' => $item->productVariant->product->name,
                                    'sku' => $item->productVariant->product->sku,
                                    'image' => $item->productVariant->product->image,
                                    'image_url' => $item->productVariant->product->image ? Storage::disk('s3')->url($item->productVariant->product->image) : null,
                                ] : null
                            ] : null
                        ];
                    });
                }

                return response()->json([
                    'success' => true,
                    'combos' => $combosData,
                    'stats' => [
                        'total' => $totalCombos,
                        'active' => $activeCombos,
                        'inactive' => $inactiveCombos
                    ],
                    'pagination' => [
                        'current_page' => $combos->currentPage(),
                        'last_page' => $combos->lastPage(),
                        'total' => $combos->total(),
                        'from' => $combos->firstItem(),
                        'to' => $combos->lastItem(),
                        'per_page' => $combos->perPage(),
                        'has_more_pages' => $combos->hasMorePages(),
                        'prev_page_url' => $combos->previousPageUrl(),
                        'next_page_url' => $combos->nextPageUrl(),
                    ]
                ]);
            }

            return view('admin.menu.combo.index', compact('combos', 'categories', 'minPrice', 'maxPrice'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->has('ajax')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $categories = Category::where('status', true)->get();
            $products = Product::with(['variants', 'category'])
                ->where('status', 'selling')
                ->get();

            return view('admin.menu.combo.create', compact('categories', 'products'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->route('admin.combos.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'original_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_variants' => 'required|array|min:1',
            'product_variants.*.id' => 'required|exists:product_variants,id',
            'product_variants.*.quantity' => 'required|integer|min:1',
            'status' => 'required|in:selling,coming_soon,discontinued',
            'branch_quantities' => 'required|array',
            'branch_quantities.*' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên combo là bắt buộc',
            'category_id.required' => 'Danh mục là bắt buộc',
            'category_id.exists' => 'Danh mục không tồn tại',
            'original_price.required' => 'Giá gốc là bắt buộc',
            'price.required' => 'Giá combo là bắt buộc',
            'product_variants.required' => 'Phải chọn ít nhất 1 sản phẩm',
            'product_variants.min' => 'Phải chọn ít nhất 1 sản phẩm',
            'branch_quantities.required' => 'Phải nhập số lượng cho từng chi nhánh',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Generate SKU
            $category = Category::find($request->category_id);
            $sku = $this->generateSKU($category->short_name ?? 'CB');

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->uploadImage($request->file('image'));
            }

            // Create combo
            $combo = Combo::create([
                'sku' => $sku,
                'name' => $request->name,
                'image' => $imagePath,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'original_price' => $request->original_price,
                'price' => $request->price,
                'status' => $request->input('status', 'selling'),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            // Add combo items
            foreach ($request->product_variants as $variant) {
                ComboItem::create([
                    'combo_id' => $combo->id,
                    'product_variant_id' => $variant['id'],
                    'quantity' => $variant['quantity']
                ]);
            }

            // Thêm số lượng cho từng chi nhánh
            foreach ($request->branch_quantities as $branch_id => $quantity) {
                if ($quantity !== null && $quantity >= 0) {
                    ComboBranchStock::create([
                        'combo_id' => $combo->id,
                        'branch_id' => $branch_id,
                        'quantity' => $quantity
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tạo combo thành công!',
                    'combo' => $combo->load(['category', 'comboItems.productVariant.product'])
                ]);
            }

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Tạo combo thành công!'
            ]);

            return redirect()->route('admin.combos.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating combo: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Combo $combo)
    {
        try {
            $combo->load([
                'productVariants.product',
                'productVariants.variantValues',
                'comboItems.productVariant.product'
            ]);

            return view('admin.menu.combo.show', compact('combo'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->route('admin.combos.index');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $combo = Combo::with([
                'category',
                'comboItems.productVariant.product',
                'comboBranchStocks'
            ])->findOrFail($id);

            $categories = Category::where('status', true)->get();
            $products = Product::with(['variants', 'category'])
                ->where('status', 'selling')
                ->get();

            return view('admin.menu.combo.edit', compact('combo', 'categories', 'products'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Combo không tồn tại hoặc có lỗi xảy ra'
            ]);

            return redirect()->route('admin.combos.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'original_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_variants' => 'required|array|min:1',
            'product_variants.*.id' => 'required|exists:product_variants,id',
            'product_variants.*.quantity' => 'required|integer|min:1',
            'status' => 'required|in:selling,coming_soon,discontinued',
            'branch_quantities' => 'required|array',
            'branch_quantities.*' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên combo là bắt buộc',
            'category_id.required' => 'Danh mục là bắt buộc',
            'category_id.exists' => 'Danh mục không tồn tại',
            'original_price.required' => 'Giá gốc là bắt buộc',
            'price.required' => 'Giá combo là bắt buộc',
            'product_variants.required' => 'Phải chọn ít nhất 1 sản phẩm',
            'product_variants.min' => 'Phải chọn ít nhất 1 sản phẩm',
            'branch_quantities.required' => 'Phải nhập số lượng cho từng chi nhánh',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $combo = Combo::findOrFail($id);

            DB::beginTransaction();

            // Handle image upload
            $imagePath = $combo->image;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($combo->image) {
                    Storage::disk('s3')->delete($combo->image);
                }
                $imagePath = $this->uploadImage($request->file('image'));
            }

            // Update combo
            $combo->update([
                'name' => $request->name,
                'image' => $imagePath,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'original_price' => $request->original_price,
                'price' => $request->price,
                'status' => $request->input('status', $combo->status),
                'updated_by' => Auth::id()
            ]);

            // Delete existing combo items
            ComboItem::where('combo_id', $combo->id)->delete();

            // Add new combo items
            foreach ($request->product_variants as $variant) {
                ComboItem::create([
                    'combo_id' => $combo->id,
                    'product_variant_id' => $variant['id'],
                    'quantity' => $variant['quantity']
                ]);
            }

            // Xóa và cập nhật lại số lượng cho từng chi nhánh
            \App\Models\ComboBranchStock::where('combo_id', $combo->id)->delete();
            foreach ($request->branch_quantities as $branch_id => $quantity) {
                if ($quantity !== null && $quantity >= 0) {
                    ComboBranchStock::create([
                        'combo_id' => $combo->id,
                        'branch_id' => $branch_id,
                        'quantity' => $quantity
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật combo thành công!',
                    'combo' => $combo->load(['category', 'comboItems.productVariant.product'])
                ]);
            }

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Cập nhật combo thành công!'
            ]);

            return redirect()->route('admin.combos.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating combo: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $combo = Combo::findOrFail($id);

            DB::beginTransaction();

            // Delete combo items first
            ComboItem::where('combo_id', $combo->id)->delete();

            // Delete image if exists
            if ($combo->image) {
                Storage::disk('s3')->delete($combo->image);
            }

            // Delete combo
            $combo->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa combo thành công!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting combo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle combo status
     */
    public function toggleStatus(string $id)
    {
        try {
            $combo = Combo::findOrFail($id);
            // Chuyển đổi trạng thái: selling -> discontinued -> coming_soon -> selling
            $statusOrder = ['selling', 'discontinued', 'coming_soon'];
            $currentIndex = array_search($combo->status, $statusOrder);
            $nextIndex = ($currentIndex + 1) % count($statusOrder);
            $combo->status = $statusOrder[$nextIndex];
            $combo->save();
            return response()->json(['status' => $combo->status, 'status_text' => $combo->status_text]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product variants for AJAX
     */
    public function getProductVariants(Request $request)
    {
        try {
            $productId = $request->product_id;
            $variants = ProductVariant::with(['variantValues.variantAttribute', 'product'])
                ->where('product_id', $productId)
                ->where('active', true)
                ->get();

            return response()->json([
                'success' => true,
                'variants' => $variants
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique SKU
     */
    private function generateSKU($prefix = 'CB')
    {
        do {
            $sku = $prefix . '-' . strtoupper(Str::random(6));
        } while (Combo::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Upload image to S3
     */
    private function uploadImage($file)
    {
        $fileName = 'combos/' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = Storage::disk('s3')->put($fileName, file_get_contents($file));

        return $fileName;
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:combos,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $combos = Combo::whereIn('id', $request->ids);
            $count = $combos->count();

            switch ($request->action) {
                case 'activate':
                    $combos->update([
                        'status' => 'selling',
                        'updated_by' => Auth::id()
                    ]);
                    $message = "Đã kích hoạt {$count} combo";
                    break;

                case 'deactivate':
                    $combos->update([
                        'status' => 'discontinued',
                        'updated_by' => Auth::id()
                    ]);
                    $message = "Đã vô hiệu hóa {$count} combo";
                    break;

                case 'delete':
                    // Delete combo items first
                    ComboItem::whereIn('combo_id', $request->ids)->delete();

                    // Delete images
                    $combosToDelete = Combo::whereIn('id', $request->ids)->get();
                    foreach ($combosToDelete as $combo) {
                        if ($combo->image) {
                            Storage::disk('s3')->delete($combo->image);
                        }
                    }

                    // Delete combos
                    $combos->delete();
                    $message = "Đã xóa {$count} combo";
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk action: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update combo status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,coming_soon,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:combos,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $combos = Combo::whereIn('id', $request->ids);
            $count = $combos->count();

            switch ($request->action) {
                case 'activate':
                    $combos->update([
                        'status' => 'selling',
                        'updated_by' => Auth::id()
                    ]);
                    $message = "Đã kích hoạt {$count} combo";
                    break;

                case 'deactivate':
                    $combos->update([
                        'status' => 'discontinued',
                        'updated_by' => Auth::id()
                    ]);
                    $message = "Đã vô hiệu hóa {$count} combo";
                    break;

                case 'coming_soon':
                    $combos->update([
                        'status' => 'coming_soon',
                        'updated_by' => Auth::id()
                    ]);
                    $message = "Đã chuyển {$count} combo sang trạng thái sắp bán";
                    break;

                case 'delete':
                    // Delete combo items first
                    ComboItem::whereIn('combo_id', $request->ids)->delete();

                    // Delete images
                    $combosToDelete = Combo::whereIn('id', $request->ids)->get();
                    foreach ($combosToDelete as $combo) {
                        if ($combo->image) {
                            Storage::disk('s3')->delete($combo->image);
                        }
                    }

                    // Delete combos
                    $combos->delete();
                    $message = "Đã xóa {$count} combo";
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk updating combos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update combo featured status
     */
    public function bulkUpdateFeatured(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_featured' => 'required|in:0,1',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:combos,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $combos = Combo::whereIn('id', $request->ids);
            $count = $combos->count();

            $combos->update(['is_featured' => $request->is_featured]);

            DB::commit();

            $featuredText = $request->is_featured ? 'đặt nổi bật' : 'bỏ nổi bật';

            return response()->json([
                'success' => true,
                'message' => "Đã ${featuredText} thành công ${count} combo",
                'count' => $count
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
     * Quick update combo quantity via AJAX
     */
    public function quickUpdateQuantity(\Illuminate\Http\Request $request, $id)
    {
        $combo = Combo::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:0',
        ]);

        $combo->quantity = $validated['quantity'];
        // Bỏ logic tự động dừng hoạt động khi quantity về 0
        $combo->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật số lượng thành công!',
            'active' => $combo->status === 'selling',
        ]);
    }
}


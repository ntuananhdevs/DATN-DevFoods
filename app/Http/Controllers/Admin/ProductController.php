<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use App\Models\BranchStock;
use App\Models\ProductVariant;
use App\Models\ProductImg;
use App\Models\Topping;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        try {
            $query = Product::withTrashed()->with(['category', 'variants.branchStocks', 'images']);

            // Tìm kiếm theo tên hoặc mã sản phẩm (ưu tiên cao nhất)
            $hasSearch = $request->has('search') && $request->search;
            if ($hasSearch) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('sku', 'like', '%' . $request->search . '%');
                });
            }

            // Chỉ áp dụng bộ lọc danh mục khi KHÔNG có tìm kiếm
            if (!$hasSearch && $request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Lọc theo giá tối thiểu
            if ($request->has('price_min') && $request->price_min) {
                $query->where('base_price', '>=', $request->price_min);
            }

            // Lọc theo giá tối đa
            if ($request->has('price_max') && $request->price_max) {
                $query->where('base_price', '<=', $request->price_max);
            }

            // Lọc theo tình trạng kho
            if ($request->has('stock_status') && !empty($request->stock_status)) {
                $query->where(function ($q) use ($request) {
                    foreach ($request->stock_status as $status) {
                        if ($status === 'in_stock') {
                            $q->orWhereHas('variants.branchStocks', function ($subQuery) {
                                $subQuery->where('stock_quantity', '>', 10);
                            });
                        } elseif ($status === 'out_of_stock') {
                            $q->orWhereDoesntHave('variants.branchStocks')
                              ->orWhereHas('variants.branchStocks', function ($subQuery) {
                                  $subQuery->where('stock_quantity', '=', 0);
                              });
                        } elseif ($status === 'low_stock') {
                            $q->orWhereHas('variants.branchStocks', function ($subQuery) {
                                $subQuery->whereBetween('stock_quantity', [1, 10]);
                            });
                        }
                    }
                });
            }

            // Filter by product status
            if ($request->has('status') && !empty($request->status)) {
                $statuses = $request->status;
                $query->where(function ($q) use ($statuses) {
                    foreach ($statuses as $status) {
                        if ($status === 'available') {
                            $q->orWhere('status', 'selling');
                        } elseif ($status === 'unavailable') {
                            $q->orWhereIn('status', ['coming_soon', 'discontinued']);
                        }
                    }
                });
            }

            // Lọc theo ngày thêm
            if ($request->has('date_added') && $request->date_added) {
                $query->whereDate('created_at', $request->date_added);
            }

            // Lọc theo trạng thái xóa
            if ($request->has('deleted_status') && $request->deleted_status !== '') {
                if ($request->deleted_status === 'deleted') {
                    $query->onlyTrashed();
                } elseif ($request->deleted_status === 'active') {
                    $query->withoutTrashed();
                }
                // Nếu là 'all' thì giữ nguyên withTrashed()
            }

            $products = $query->latest()->paginate(10);
            $categories = Category::all();
            $branches = Branch::where('active', true)->get();
            
            // Tính giá min/max dựa trên filter category hiện tại
            $priceQuery = Product::query();
            if ($request->has('category_id') && $request->category_id) {
                $priceQuery->where('category_id', $request->category_id);
            }
            $minPrice = $priceQuery->min('base_price') ?? 0;
            $maxPrice = $priceQuery->max('base_price') ?? 10000000;

            // Handle AJAX requests (both GET and POST)
            if ($request->ajax()) {
                $html = view('admin.menu.product.partials.product-table', compact('products'))->render();
                
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'total' => $products->total(),
                    'message' => 'Tìm kiếm thành công'
                ]);
            }

            return view('admin.menu.product.index', compact('products', 'categories', 'branches', 'minPrice', 'maxPrice'));

        } catch (\Exception $e) {
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

            return redirect()->route('admin.products.index');
        }
    }

    /**
     * Get price range for a specific category
     */
    public function getPriceRange(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            
            $query = Product::query();
            
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
            
            $minPrice = $query->min('base_price') ?? 0;
            $maxPrice = $query->max('base_price') ?? 10000000;
            
            return response()->json([
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'category_id' => $categoryId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Có lỗi xảy ra khi lấy khoảng giá'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $categories = Category::where('status', true)->get();
        $branches = Branch::where('active', true)->get();
        $toppings = Topping::where('active', true)->get();
        
        return view('admin.menu.product.create', compact('categories', 'branches', 'toppings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request) {
        try {
            $validated = $request->validated();
            DB::beginTransaction();

            // Create product with auto-generated SKU
            $product = $this->createProduct($validated);

            // Handle images
            $this->handleImages($product, $request);

            // Handle variants
            $this->createVariants($product, $request->input('attributes', []));

            // Sync toppings (simplified)
            if ($request->has('selected_toppings')) {
                $selectedToppings = $request->input('selected_toppings');
                \Log::info('Selected toppings from request:', ['selected_toppings' => $selectedToppings]);
                
                if (is_string($selectedToppings)) {
                    $selectedToppings = json_decode($selectedToppings, true) ?: [];
                }
                \Log::info('Parsed selected toppings:', ['toppings' => $selectedToppings]);
                $product->toppings()->sync($selectedToppings);
            } else {
                \Log::info('No selected_toppings found in request');
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm đã được tạo thành công'
            ]);

            return redirect()->route('admin.products.stock', $product->id);
        } catch (\Exception $e) {
            DB::rollBack();
            
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
    public function show($id)
    {
        $product = Product::with([
            'category',
            'images',
            'variants.productVariantDetails.variantValue.attribute',
            'variants.branchStocks.branch',
            'toppings'
        ])->findOrFail($id);

        return view('admin.menu.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::with([
            'category',
            'images',
            'variants.productVariantDetails.variantValue.attribute',
            'toppings'
        ])->findOrFail($id);

        $categories = Category::where('status', true)->get();
        $branches = Branch::where('active', true)->get();
        $toppings = Topping::where('active', true)->get();
        $selectedToppings = $product->toppings->pluck('id')->toArray();
        $branchStocks = $this->getBranchStocks($product);

        return view('admin.menu.product.edit', compact(
            'product',
            'categories',
            'branches',
            'branchStocks',
            'toppings',
            'selectedToppings'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $product = Product::with(['variants.productVariantDetails.variantValue.attribute'])->findOrFail($id);
             
            // Check if attributes changed
            $currentAttributes = $this->getCurrentAttributes($product);
            $newAttributes = $request->input('attributes', []);
            $attributesChanged = $this->attributesChanged($currentAttributes, $newAttributes);

            // Update basic info
            $this->updateBasicInfo($product, $request->validated());

            // Handle new images (no deletion)
            $this->handleImages($product, $request, true);

            // Update toppings
            if ($request->has('selected_toppings')) {
                $selectedToppings = $request->input('selected_toppings');
                if (is_string($selectedToppings)) {
                    $selectedToppings = json_decode($selectedToppings, true) ?: [];
                }
                $product->toppings()->sync($selectedToppings);
            } elseif ($request->has('toppings')) {
                $product->toppings()->sync($request->input('toppings', []));
            }

            // Remove deleted attribute values
            $this->removeDeletedAttributeValues($currentAttributes, $newAttributes);
            
            // Update existing variant values (for renames)
            $this->updateVariantValues($currentAttributes, $newAttributes);
            
            // Add new variants if attributes changed
            if ($attributesChanged) {
                $this->addNewVariants($product, $newAttributes);
            }

            // Update variant stocks if provided
            if ($request->has('variant_stocks')) {
                $this->updateVariantStocks($request->input('variant_stocks'));
            }

            DB::commit();

            // Redirect to stock page if attributes changed
            if ($attributesChanged) {
                session()->flash('toast', [
                    'type' => 'success',
                    'title' => 'Thành công!',
                    'message' => 'Thuộc tính đã được thêm. Vui lòng cập nhật kho hàng.'
                ]);
                return redirect()->route('admin.products.stock', $product->id);
            }

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm đã được cập nhật thành công'
            ]);

            return redirect()->route('admin.products.edit', $product->id);
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back()->withInput();
        }
    }

    /**
     * Soft delete the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Kiểm tra xem sản phẩm có trong đơn hàng không
            if ($product->hasOrders()) {
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Không thể ẩn sản phẩm!',
                    'message' => 'Sản phẩm "' . $product->name . '" đang có trong đơn hàng nên không thể ẩn.'
                ]);
                
                return redirect()->back();
            }
            
            // Thực hiện soft delete
            $product->delete();
            
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm "' . $product->name . '" đã được ẩn thành công. Sản phẩm vẫn có thể được khôi phục.'
            ]);
            
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->back();
        }
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            
            // Kiểm tra xem sản phẩm có thể xóa hoàn toàn không
            if (!$product->canForceDelete()) {
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Không thể xóa!',
                    'message' => 'Không thể xóa sản phẩm "' . $product->name . '" vì đã có đơn hàng sử dụng sản phẩm.'
                ]);
                
                return redirect()->back();
            }
            
            DB::beginTransaction();
            
            $productName = $product->name;
            
            // Delete images from storage
            foreach ($product->images as $image) {
                if ($image->img) {
                    Storage::disk('s3')->delete($image->img);
                }
            }
            
            // Force delete the product
            $product->forceDelete();
            
            DB::commit();
            
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm "' . $productName . '" đã được xóa hoàn toàn khỏi hệ thống.'
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
     * Restore the specified soft deleted resource.
     */
    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            
            if (!$product->trashed()) {
                session()->flash('toast', [
                    'type' => 'warning',
                    'title' => 'Thông báo!',
                    'message' => 'Sản phẩm này chưa bị ẩn.'
                ]);
                
                return redirect()->back();
            }
            
            $product->restore();
            
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm "' . $product->name . '" đã được khôi phục thành công.'
            ]);
            
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->back();
        }
    }

    /**
     * Display the stock management page for a product
     */
    public function stock(Product $product)
    {
        $product->load(['variants.variantValues.attribute', 'variants.branchStocks']);
        $branches = Branch::where('active', true)->get();
        $branchStocks = $this->getBranchStocks($product);
        
        return view('admin.menu.product.stock', compact('product', 'branches', 'branchStocks'));
    }

    /**
     * Update stock quantities for a product's variants
     */
    public function updateProductStocks(Request $request, Product $product)
    {
        try {
            $request->validate([
                'stocks' => 'nullable|array',
                'stocks.*' => 'nullable|array',
                'stocks.*.*' => 'nullable|integer|min:0'
            ]);

            // Check if stocks data is provided
            if (!$request->has('stocks') || empty($request->stocks)) {
                session()->flash('toast', [
                    'type' => 'warning',
                    'title' => 'Cảnh báo!',
                    'message' => 'Không có dữ liệu kho hàng nào được cập nhật'
                ]);
                return redirect()->route('admin.products.index');
            }

            DB::beginTransaction();

            foreach ($request->stocks as $branchId => $variantStocks) {
                foreach ($variantStocks as $variantId => $quantity) {
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

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Cập nhật tồn kho thành công'
            ]);

            return redirect()->route('admin.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->route('admin.products.index');
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Create a new product
     */
    private function createProduct($validated) {
        $category = Category::findOrFail($validated['category_id']);
        $sku = $this->generateSKU($category);
        
        return Product::create([
            'name' => trim($validated['name']),
            'category_id' => $validated['category_id'],
            'sku' => $sku,
            'base_price' => $validated['base_price'],
            'preparation_time' => $validated['preparation_time'],
            'short_description' => trim($validated['short_description'] ?? ''),
            'description' => !empty($validated['description']) ? trim($validated['description']) : null,
            'ingredients' => $this->processIngredients($validated),
            'is_featured' => $validated['is_featured'] ?? false,
            'available' => $validated['available'] ?? true,
            'status' => $validated['status'],
            'release_at' => $validated['release_at'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Generate SKU for product
     */
    private function generateSKU($category) {
        $lastProduct = Product::where('sku', 'like', $category->short_name . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $skuNumber = 1;
        if ($lastProduct) {
            $lastNumber = (int) substr($lastProduct->sku, strrpos($lastProduct->sku, '-') + 1);
            $skuNumber = $lastNumber + 1;
        }

        return $category->short_name . '-' . str_pad($skuNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Process ingredients from request
     */
    private function processIngredients($validated) {
        // Only handle simple ingredients field
        if (isset($validated['ingredients'])) {
            if (is_string($validated['ingredients'])) {
                // Split by newlines and filter out empty items
                $ingredientsText = str_replace(["\r\n", "\r"], "\n", $validated['ingredients']);
                $ingredients = array_map('trim', explode("\n", $ingredientsText));
                
                // Filter out empty items
                $ingredients = array_filter($ingredients, function($item) {
                    return !empty(trim($item));
                });
                
                // Return as array, let Laravel handle the JSON encoding
                return array_values($ingredients);
            }
            
            if (is_array($validated['ingredients'])) {
                return $validated['ingredients'];
            }
        }
        
        return [];
    }

    /**
     * Update product basic information
     */
    private function updateBasicInfo($product, $validated) {
        $product->update([
            'name' => trim($validated['name']),
            'category_id' => $validated['category_id'],
            'base_price' => $validated['base_price'],
            'preparation_time' => $validated['preparation_time'],
            'short_description' => trim($validated['short_description'] ?? ''),
            'description' => !empty($validated['description']) ? trim($validated['description']) : null,
            'ingredients' => $this->processIngredients($validated),
            'is_featured' => $validated['is_featured'] ?? false,
            'available' => $validated['available'] ?? true,
            'status' => $validated['status'],
            'release_at' => $validated['release_at'] ?? null,
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Handle product images
     */
    private function handleImages($product, $request, $isUpdate = false) {
        // Handle deleted images first
        if ($isUpdate && $request->has('deleted_images')) {
            $deletedImageIds = $request->input('deleted_images', []);
            $imagesToDelete = $product->images()->whereIn('id', $deletedImageIds)->get();
            
            foreach ($imagesToDelete as $image) {
                if ($image->img) {
                    Storage::disk('s3')->delete($image->img);
                }
                $image->delete();
            }
        }

        if ($request->hasFile('primary_image')) {
            $this->uploadPrimaryImage($product, $request->file('primary_image'), $isUpdate);
        }

        if ($request->hasFile('images')) {
            $this->uploadAdditionalImages($product, $request->file('images'));
        }
    }

    /**
     * Upload primary image
     */
    private function uploadPrimaryImage($product, $image, $isUpdate = false) {
        if ($isUpdate) {
            $oldPrimaryImage = $product->images()->where('is_primary', true)->first();
            if ($oldPrimaryImage) {
                if ($oldPrimaryImage->img) {
                    Storage::disk('s3')->delete($oldPrimaryImage->img);
                }
                $oldPrimaryImage->delete();
            }
        }

        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));

        if ($path) {
            $product->images()->create([
                'img' => 'products/' . $filename,
                'is_primary' => true,
            ]);
        }
    }

    /**
     * Upload additional images
     */
    private function uploadAdditionalImages($product, $images) {
        foreach ($images as $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));

            if ($path) {
                $product->images()->create([
                    'img' => 'products/' . $filename,
                    'is_primary' => false,
                ]);
            }
        }
    }

    /**
     * Create product variants
     */
    private function createVariants($product, $attributes) {
        if (empty($attributes)) {
            $product->variants()->create(['active' => true]);
            return;
        }

        $combinations = $this->generateCombinations($attributes);
        
        foreach ($combinations as $combination) {
            $variant = $product->variants()->create(['active' => true]);
            
            foreach ($combination as $variantValue) {
                $variant->productVariantDetails()->create([
                    'variant_value_id' => $variantValue->id
                ]);
            }
        }
    }

    /**
     * Add new variants (non-destructive)
     */
    private function addNewVariants($product, $attributes) {
        if (empty($attributes)) {
            return;
        }

        $newCombinations = $this->generateCombinations($attributes);
        $existingVariants = $product->variants()->with('productVariantDetails.variantValue')->get();
        
        // Get existing signatures
        $existingSignatures = [];
        foreach ($existingVariants as $variant) {
            $signature = $this->createVariantSignature($variant);
            $existingSignatures[$signature] = true;
        }
        
        // Add only new combinations
        foreach ($newCombinations as $combination) {
            $signature = $this->createCombinationSignature($combination);
            
            if (!isset($existingSignatures[$signature])) {
                $variant = $product->variants()->create(['active' => true]);
                
                foreach ($combination as $variantValue) {
                    $variant->productVariantDetails()->create([
                        'variant_value_id' => $variantValue->id
                    ]);
                }
            }
        }
    }

    /**
     * Generate variant combinations
     */
    private function generateCombinations($attributes) {
        $attributeGroups = [];

        foreach ($attributes as $attributeData) {
            if (empty($attributeData['name'])) {
                continue;
            }

            $attribute = VariantAttribute::firstOrCreate(['name' => $attributeData['name']]);
            $values = [];
            
            if (isset($attributeData['values']) && is_array($attributeData['values'])) {
                foreach ($attributeData['values'] as $valueData) {
                    if (empty($valueData['value'])) {
                        continue;
                    }
                    
                    $priceAdjustment = $valueData['price_adjustment'] ?? 0;
                    
                    $variantValue = VariantValue::firstOrCreate(
                        [
                            'variant_attribute_id' => $attribute->id,
                            'value' => $valueData['value']
                        ],
                        [
                            'price_adjustment' => $priceAdjustment
                        ]
                    );
                    
                    // Update price_adjustment if it changed
                    if ($variantValue->price_adjustment != $priceAdjustment) {
                        $variantValue->update(['price_adjustment' => $priceAdjustment]);
                    }
                    
                    $values[] = $variantValue;
                }
            }
            
            if (!empty($values)) {
                $attributeGroups[] = $values;
            }
        }

        if (empty($attributeGroups)) {
            return [];
        }

        $combinations = [[]];
        
        foreach ($attributeGroups as $group) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($group as $value) {
                    $newCombinations[] = array_merge($combination, [$value]);
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Create variant signature
     */
    private function createVariantSignature($variant) {
        $values = $variant->productVariantDetails
            ->map(function($detail) {
                return $detail->variantValue->variant_attribute_id . ':' . $detail->variantValue->value;
            })
            ->sort()
            ->values()
            ->toArray();
            
        return implode('|', $values);
    }

    /**
     * Create combination signature
     */
    private function createCombinationSignature($combination) {
        $values = collect($combination)
            ->map(function($variantValue) {
                return $variantValue->variant_attribute_id . ':' . $variantValue->value;
            })
            ->sort()
            ->values()
            ->toArray();
            
        return implode('|', $values);
    }

    /**
     * Get current product attributes
     */
    private function getCurrentAttributes($product) {
        $attributes = [];
        
        foreach ($product->variants as $variant) {
            foreach ($variant->productVariantDetails as $detail) {
                $attrName = $detail->variantValue->attribute->name;
                $value = $detail->variantValue->value;
                $valueId = $detail->variantValue->id;
                
                if (!isset($attributes[$attrName])) {
                    $attributes[$attrName] = [];
                }
                
                // Check if this value already exists to avoid duplicates
                $exists = false;
                foreach ($attributes[$attrName] as $existingValue) {
                    if (is_array($existingValue) && $existingValue['id'] == $valueId) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    $attributes[$attrName][] = [
                        'id' => $valueId,
                        'value' => $value
                    ];
                }
            }
        }
        
        return $attributes;
    }

    /**
     * Check if attributes have changed (only for new attributes/values, not renames)
     */
    private function attributesChanged($currentAttributes, $newAttributes) {
        // Convert current attributes to simple format for comparison
        $currentAttributesFormatted = [];
        foreach ($currentAttributes as $name => $values) {
            $currentAttributesFormatted[$name] = array_map(function($v) { 
                return is_array($v) ? $v['value'] : $v; 
            }, $values);
        }
        
        // Convert new attributes to same format
        $newAttributesFormatted = [];
        foreach ($newAttributes as $attr) {
            if (!empty($attr['name']) && !empty($attr['values'])) {
                $values = array_map(function($v) { return $v['value']; }, $attr['values']);
                $newAttributesFormatted[$attr['name']] = $values;
            }
        }
        
        // Check if new attributes are added
        foreach ($newAttributesFormatted as $name => $values) {
            if (!isset($currentAttributesFormatted[$name])) {
                return true; // New attribute added
            }
            
            // Check if the number of values increased (indicating new values)
            if (count($values) > count($currentAttributesFormatted[$name])) {
                return true; // New values added
            }
        }
        
        // Check if any new attributes exist that weren't in current
        if (count($newAttributesFormatted) > count($currentAttributesFormatted)) {
            return true; // New attributes added
        }
        
        return false;
    }

    /**
     * Get branch stocks for product
     */
    private function getBranchStocks(Product $product) {
        $branchStocks = [];
        $stocksData = BranchStock::whereHas('productVariant', function($query) use ($product) {
            $query->where('product_id', $product->id);
        })->get();
        
        foreach ($stocksData as $stock) {
            if (!isset($branchStocks[$stock->branch_id])) {
                $branchStocks[$stock->branch_id] = [];
            }
            $stockQuantity = is_array($stock->stock_quantity) ? 0 : (int)$stock->stock_quantity;
            $branchStocks[$stock->branch_id][$stock->product_variant_id] = $stockQuantity;
        }
        
        return $branchStocks;
    }

    /**
     * Remove deleted attribute values
     */
    private function removeDeletedAttributeValues($currentAttributes, $newAttributes) {
        // Get all current variant value IDs
        $currentValueIds = [];
        foreach ($currentAttributes as $attributeName => $values) {
            foreach ($values as $value) {
                if (isset($value['id'])) {
                    $currentValueIds[] = $value['id'];
                }
            }
        }
        
        // Get all new variant value IDs from the form
        $newValueIds = [];
        foreach ($newAttributes as $attr) {
            if (empty($attr['name']) || empty($attr['values'])) {
                continue;
            }
            
            foreach ($attr['values'] as $valueData) {
                if (isset($valueData['id'])) {
                    $newValueIds[] = $valueData['id'];
                }
            }
        }
        
        // Find IDs that exist in current but not in new (these should be deleted)
        $idsToDelete = array_diff($currentValueIds, $newValueIds);
        
        if (!empty($idsToDelete)) {
            // Delete variant values and their related data
            \App\Models\VariantValue::whereIn('id', $idsToDelete)->delete();
            
            // Also delete related product variant details
            \App\Models\ProductVariantDetail::whereIn('variant_value_id', $idsToDelete)->delete();
            
            // Delete product variants that no longer have any variant details
            $orphanedVariants = \App\Models\ProductVariant::whereDoesntHave('productVariantDetails')->get();
            foreach ($orphanedVariants as $variant) {
                // Delete related branch stocks first
                \App\Models\BranchStock::where('product_variant_id', $variant->id)->delete();
                // Then delete the variant
                $variant->delete();
            }
        }
    }
    
    /**
     * Update variant values (for renames)
     */
    private function updateVariantValues($currentAttributes, $newAttributes) {
        foreach ($newAttributes as $attr) {
            if (empty($attr['name']) || empty($attr['values'])) {
                continue;
            }
            
            foreach ($attr['values'] as $valueData) {
                // Skip if no ID (new value)
                if (!isset($valueData['id'])) {
                    continue;
                }
                
                $variantValueId = $valueData['id'];
                $newValue = $valueData['value'] ?? '';
                $newPriceAdjustment = $valueData['price_adjustment'] ?? 0;
                
                // Find the variant value by ID
                $variantValue = \App\Models\VariantValue::find($variantValueId);
                
                if ($variantValue) {
                    // Update both value and price_adjustment
                    $updateData = [];
                    if ($variantValue->value !== $newValue) {
                        $updateData['value'] = $newValue;
                    }
                    if ($variantValue->price_adjustment != $newPriceAdjustment) {
                        $updateData['price_adjustment'] = $newPriceAdjustment;
                    }
                    
                    if (!empty($updateData)) {
                        $variantValue->update($updateData);
                    }
                }
            }
        }
    }
    
    /**
     * Update variant stocks
     */
    private function updateVariantStocks($variantStocks) {
        foreach ($variantStocks as $variantId => $branchStocks) {
            foreach ($branchStocks as $branchId => $quantity) {
                BranchStock::updateOrCreate(
                    [
                        'branch_id' => $branchId,
                        'product_variant_id' => $variantId
                    ],
                    ['stock_quantity' => (int)$quantity]
                );
            }
        }
    }
    
    /**
     * Export products data
     */
    public function export(Request $request)
    {
        try {
            $type = $request->get('type', 'excel');
            $query = Product::with(['category', 'branchStocks.branch']);

            // Lọc theo danh mục
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Lọc theo giá tối thiểu
            if ($request->filled('price_min')) {
                $query->where('base_price', '>=', $request->price_min);
            }

            // Lọc theo giá tối đa
            if ($request->filled('price_max')) {
                $query->where('base_price', '<=', $request->price_max);
            }

            // Lọc theo chi nhánh
            if ($request->filled('branch_id')) {
                $query->whereHas('branchStocks', function ($subQuery) use ($request) {
                    $subQuery->where('branch_id', $request->branch_id);
                });
            }

            // Lọc theo tình trạng kho
            if ($request->filled('stock_status')) {
                $stockStatus = $request->stock_status;
                if ($stockStatus === 'in_stock') {
                    $query->whereHas('variants.branchStocks', function ($subQuery) {
                        $subQuery->where('stock_quantity', '>', 0);
                    });
                } elseif ($stockStatus === 'out_of_stock') {
                    $query->whereDoesntHave('variants.branchStocks')
                          ->orWhereHas('variants.branchStocks', function ($subQuery) {
                              $subQuery->where('stock_quantity', '=', 0);
                          });
                } elseif ($stockStatus === 'low_stock') {
                    $query->whereHas('variants.branchStocks', function ($subQuery) {
                        $subQuery->whereBetween('stock_quantity', [1, 10]);
                    });
                }
            }

            $products = $query->latest()->get();

            // Tạo tên file với thông tin bộ lọc
            $filename = 'products';
            if ($request->filled('category_id')) {
                $category = \App\Models\Category::find($request->category_id);
                if ($category) {
                    $filename .= '_' . \Str::slug($category->name);
                }
            }
            if ($request->filled('branch_id')) {
                $branch = \App\Models\Branch::find($request->branch_id);
                if ($branch) {
                    $filename .= '_' . \Str::slug($branch->name);
                }
            }
            $filename .= '_' . date('Y-m-d_H-i-s');

            // Xử lý xuất dữ liệu theo định dạng
            switch ($type) {
                case 'excel':
                    return Excel::download(
                        new \App\Exports\ProductsExport($products, $request->branch_id), 
                        $filename . '.xlsx'
                    );

                case 'pdf':
                    // Format data for PDF export with proper formatting
                    $exportData = collect();
                    $selectedBranch = null;
                    
                    if ($request->filled('branch_id')) {
                        $selectedBranch = \App\Models\Branch::find($request->branch_id);
                    }
                    
                    foreach ($products as $product) {
                        $branchStocks = $product->branchStocks;
                        
                        if ($request->filled('branch_id')) {
                            // Nếu chọn chi nhánh cụ thể, chỉ hiển thị dữ liệu của chi nhánh đó
                            $branchStock = $branchStocks->where('branch_id', $request->branch_id)->first();
                            if ($branchStock) {
                                $exportData->push([
                                    'sku' => $product->sku,
                                    'name' => $product->name,
                                    'category' => $product->category ? $product->category->name : 'N/A',
                                    'base_price' => number_format($product->base_price, 0, ',', '.') . ' VNĐ',
                                    'branch_name' => $branchStock->branch ? $branchStock->branch->name : 'N/A',
                                    'stock_quantity' => $branchStock->stock_quantity,
                                    'status' => $branchStock->stock_quantity > 0 ? 'Còn hàng' : 'Hết hàng',
                                    'created_at' => $product->created_at->format('d/m/Y H:i:s'),
                                    'updated_at' => $product->updated_at->format('d/m/Y H:i:s'),
                                ]);
                            }
                        } else {
                            // Hiển thị tất cả chi nhánh như trước
                            if ($branchStocks->isEmpty()) {
                                $exportData->push([
                                    'sku' => $product->sku,
                                    'name' => $product->name,
                                    'category' => $product->category ? $product->category->name : 'N/A',
                                    'base_price' => number_format($product->base_price, 0, ',', '.') . ' VNĐ',
                                    'branch_name' => 'Chưa phân bổ chi nhánh',
                                    'stock_quantity' => 0,
                                    'status' => 'Chưa phân bổ',
                                    'created_at' => $product->created_at->format('d/m/Y H:i:s'),
                                    'updated_at' => $product->updated_at->format('d/m/Y H:i:s'),
                                ]);
                            } else {
                                foreach ($branchStocks as $branchStock) {
                                    $exportData->push([
                                        'sku' => $product->sku,
                                        'name' => $product->name,
                                        'category' => $product->category ? $product->category->name : 'N/A',
                                        'base_price' => number_format($product->base_price, 0, ',', '.') . ' VNĐ',
                                        'branch_name' => $branchStock->branch ? $branchStock->branch->name : 'N/A',
                                        'stock_quantity' => $branchStock->stock_quantity,
                                        'status' => $branchStock->stock_quantity > 0 ? 'Còn hàng' : 'Hết hàng',
                                        'created_at' => $product->created_at->format('d/m/Y H:i:s'),
                                        'updated_at' => $product->updated_at->format('d/m/Y H:i:s'),
                                    ]);
                                }
                            }
                        }
                    }
                    
                    $pdfData = [
                        'products' => $exportData->toArray(),
                        'selectedBranch' => $selectedBranch
                    ];
                    
                    $pdf = Pdf::loadView('exports.products', $pdfData);
                    $pdf->setPaper('A4', 'landscape'); // Set landscape orientation for better table display
                    return $pdf->download($filename . '.pdf');
                    
                case 'csv':
                    return Excel::download(
                        new \App\Exports\ProductsExport($products, $request->branch_id), 
                        $filename . '.csv', 
                        \Maatwebsite\Excel\Excel::CSV
                    );
                    
                default:
                    return $this->exportJson($products, $filename . '.json');
            }
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xuất dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Temporary JSON export method
     */
    private function exportJson($products, $filename)
    {
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category ? $product->category->name : 'N/A',
                'price' => $product->base_price,
                'stock' => $product->stock,
                'status' => $product->stock > 0 ? 'Còn hàng' : 'Hết hàng',
                'created_at' => $product->created_at->format('d/m/Y H:i:s'),
                'updated_at' => $product->updated_at->format('d/m/Y H:i:s'),
            ];
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = storage_path('products-export.json');
        file_put_contents($file, $json);

        return Response::download($file, $filename);
    }

    /**
     * Kiểm tra xem attribute có thể xóa được không
     */
    public function checkAttributeDeletable(Request $request)
    {
        $attributeId = $request->input('attribute_id');
        $productId = $request->input('product_id');
        
        // Kiểm tra xem attribute có đang được sử dụng trong các variant của sản phẩm này không
        $isUsed = \App\Models\ProductVariant::where('product_id', $productId)
            ->whereHas('variantValues.attribute', function($query) use ($attributeId) {
                $query->where('id', $attributeId);
            })
            ->where(function($query) {
                // Kiểm tra trong OrderItem
                $query->whereHas('orderItems')
                    // Hoặc trong CartItem
                    ->orWhereHas('cartItems');
            })
            ->exists();
            
        return response()->json([
            'can_delete' => !$isUsed,
            'message' => $isUsed ? 'Không thể xóa thuộc tính này vì đã có trong đơn hàng hoặc giỏ hàng.' : 'Có thể xóa thuộc tính này.'
        ]);
    }
    
    /**
     * Kiểm tra xem value có thể xóa được không
     */
    public function checkValueDeletable(Request $request)
    {
        $valueId = $request->input('value_id');
        $productId = $request->input('product_id');
        
        // Kiểm tra xem value có đang được sử dụng trong các variant của sản phẩm này không
        $isUsed = \App\Models\ProductVariant::where('product_id', $productId)
            ->whereHas('variantValues', function($query) use ($valueId) {
                $query->where('variant_value_id', $valueId);
            })
            ->where(function($query) {
                // Kiểm tra trong OrderItem
                $query->whereHas('orderItems')
                    // Hoặc trong CartItem
                    ->orWhereHas('cartItems');
            })
            ->exists();
            
        return response()->json([
            'can_delete' => !$isUsed,
            'message' => $isUsed ? 'Không thể xóa giá trị này vì đã có trong đơn hàng hoặc giỏ hàng.' : 'Có thể xóa giá trị này.'
        ]);
    }

}
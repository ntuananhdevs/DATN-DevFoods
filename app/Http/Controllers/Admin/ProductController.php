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
            $query = Product::with(['category', 'variants.branchStocks', 'images']);

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

            // Phần tìm kiếm đã được di chuyển lên trên để ưu tiên cao hơn bộ lọc danh mục

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

            return redirect()->back();
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
        $categories = Category::all();
        $branches = Branch::where('active', true)->get();
        return view('admin.menu.product.create', compact('categories', 'branches'));
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
            $this->handleProductImages($product, $request);

            // Handle variants (for new product, no need to clear existing)
            $this->createProductVariants($product, $request->input('attributes', []));

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm đã được tạo thành công'
            ]);

            return redirect()->route('admin.products.stock', $product->id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->handleValidationException($e, $request);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e, $request);
        }
    }

    /**
     * Create a new product with auto-generated SKU
     */
    private function createProduct($validated) {
        $category = Category::findOrFail($validated['category_id']);
        $sku = $this->generateSKU($category);

        return Product::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'sku' => $sku,
            'base_price' => $validated['base_price'],
            'preparation_time' => $validated['preparation_time'],
            'short_description' => $validated['short_description'] ?? '',
            'description' => $validated['description'] ?? null,
            'ingredients' => $validated['ingredients_json'] ?? $validated['ingredients'] ?? '[]',
            'is_featured' => $validated['is_featured'] ?? false,
            'available' => $validated['available'] ?? true,
            'status' => $validated['status'],
            'release_at' => $validated['release_at'],
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
     * Handle product images upload
     */
    private function handleProductImages($product, $request, $isUpdate = false) {
        // Handle primary image
        if ($request->hasFile('primary_image')) {
            $this->uploadPrimaryImage($product, $request->file('primary_image'), $isUpdate);
        }

        // Handle additional images
        if ($request->hasFile('images')) {
            $this->uploadAdditionalImages($product, $request->file('images'));
        }
    }

    /**
     * Upload primary image
     */
    private function uploadPrimaryImage($product, $image, $isUpdate = false) {
        Log::info('Uploading primary image', [
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
            'mime' => $image->getMimeType()
        ]);

        // Delete old primary image if exists (only for updates)
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
            Log::info('Uploading additional image', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime' => $image->getMimeType()
            ]);

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
     * Create product variants (for new products)
     */
    private function createProductVariants($product, $attributes) {
        if (empty($attributes)) {
            // Create default variant if no attributes
            $product->variants()->create(['active' => true]);
            return;
        }

        $attributeGroups = $this->processAttributes($attributes);
        
        if (!empty($attributeGroups)) {
            $combinations = $this->generateVariantCombinations($attributeGroups);
            Log::info('Generated combinations count:', ['count' => count($combinations)]);

            foreach ($combinations as $combination) {
                $variant = $product->variants()->create(['active' => true]);
                
                foreach ($combination as $variantValue) {
                    $variant->productVariantDetails()->create([
                        'variant_value_id' => $variantValue->id
                    ]);
                }
            }
        } else {
            $product->variants()->create(['active' => true]);
        }
    }

    /**
     * Update product variants (for existing products)
     */
    private function updateProductVariants($product, $attributes) {
        // Backup existing stock data
        $stockBackup = $this->backupVariantStockData($product);
        
        // Clear existing variants
        $variantsToDelete = $product->variants;
        $product->variants()->each(function ($variant) {
            $variant->productVariantDetails()->delete();
            $variant->delete();
        });

        if (empty($attributes)) {
            $product->variants()->create(['active' => true]);
            return;
        }

        $attributeGroups = $this->processAttributes($attributes);
        
        if (!empty($attributeGroups)) {
            $combinations = $this->generateVariantCombinations($attributeGroups);
            
            foreach ($combinations as $combination) {
                $variant = $product->variants()->create(['active' => true]);
                
                foreach ($combination as $variantValue) {
                    $variant->productVariantDetails()->create([
                        'variant_value_id' => $variantValue->id
                    ]);
                }
                
                // Restore stock data
                $this->restoreStockDataForNewVariant($variant, $combination, $stockBackup, $variantsToDelete);
            }
        } else {
            $product->variants()->create(['active' => true]);
        }
    }

    /**
     * Process attributes data
     */
    private function processAttributes($attributes) {
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

                    $value = VariantValue::create([
                        'variant_attribute_id' => $attribute->id,
                        'value' => $valueData['value'],
                        'price_adjustment' => $valueData['price_adjustment'] ?? 0
                    ]);
                    $values[] = $value;
                }
            }

            if (!empty($values)) {
                $attributeGroups[] = [
                    'attribute' => $attribute,
                    'values' => $values
                ];
            }
        }

        return $attributeGroups;
    }



    /**
     * Backup variant stock data
     */
    private function backupVariantStockData($product) {
        $stockBackup = [];
        
        foreach ($product->variants as $variant) {
            $signature = $this->createVariantSignatureFromVariant($variant);
            $stockBackup[$signature] = [];
            
            foreach ($variant->branchStocks as $stock) {
                $stockBackup[$signature][$stock->branch_id] = $stock->stock_quantity;
            }
        }
        
        return $stockBackup;
    }

    /**
     * Handle validation exceptions
     */
    private function handleValidationException($e, $request) {
        DB::rollBack();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại thông tin nhập vào',
                'errors' => $e->validator->errors()
            ], 422);
        }

        session()->flash('toast', [
            'type' => 'error',
            'title' => 'Lỗi!',
            'message' => 'Vui lòng kiểm tra lại thông tin nhập vào'
        ]);

        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    }

    /**
     * Handle general exceptions
     */
    private function handleGeneralException($e, $request) {
        DB::rollBack();

        if ($request->ajax() || $request->wantsJson()) {
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

        return redirect()->back()
            ->withInput()
            ->with('old_attributes', $request->attributes)
            ->with('old_toppings', $request->toppings);
    }

    /**
     * Generate all possible combinations of variant values
     */
    private function generateVariantCombinations($attributeGroups) {
        if (empty($attributeGroups)) {
            return [];
        }

        $combinations = [[]];

        foreach ($attributeGroups as $group) {
            $newCombinations = [];

            foreach ($combinations as $combination) {
                foreach ($group['values'] as $value) {
                    $newCombinations[] = array_merge($combination, [$value]);
                }
            }

            $combinations = $newCombinations;
        }

        return $combinations;
    }
    
    // Create variant signature from existing variant
    protected function createVariantSignatureFromVariant($variant)
    {
        return $variant->productVariantDetails
            ->sortBy('variant_value_id')
            ->pluck('variant_value_id')
            ->implode(',');
    }
    
    // Create variant signature from combination
    protected function createVariantSignatureFromCombination($combination)
    {
        return collect($combination)
            ->sortBy('id')
            ->pluck('id')
            ->implode(',');
    }
    
    // Restore stock data for new variant from similar variants
    protected function restoreStockDataForNewVariant($newVariant, $combination, $stockBackup, $variantsToDelete)
    {
        $newSignature = $this->createVariantSignatureFromCombination($combination);
        
        // First, try to find exact match in backup
        if (isset($stockBackup[$newSignature])) {
            foreach ($stockBackup[$newSignature] as $branchId => $stockQuantity) {
                $newVariant->branchStocks()->updateOrCreate(
                    ['branch_id' => $branchId],
                    ['stock_quantity' => $stockQuantity]
                );
            }
            Log::info('Restored exact stock data for new variant:', ['variant_id' => $newVariant->id, 'signature' => $newSignature]);
            return;
        }
        
        // If no exact match, try to find similar variant from variants to delete
        $bestMatch = null;
        $bestMatchScore = 0;
        
        foreach ($variantsToDelete as $deletingVariant) {
            $deletingSignature = $this->createVariantSignatureFromVariant($deletingVariant);
            $deletingValues = explode(',', $deletingSignature);
            $newValues = explode(',', $newSignature);
            
            // Calculate similarity score (number of matching values)
            $matchingValues = array_intersect($deletingValues, $newValues);
            $score = count($matchingValues);
            
            if ($score > $bestMatchScore && $score > 0) {
                $bestMatchScore = $score;
                $bestMatch = $deletingVariant;
            }
        }
        
        // If we found a similar variant, transfer its stock data
        if ($bestMatch && isset($stockBackup[$this->createVariantSignatureFromVariant($bestMatch)])) {
            $stockData = $stockBackup[$this->createVariantSignatureFromVariant($bestMatch)];
            foreach ($stockData as $branchId => $stockQuantity) {
                $newVariant->branchStocks()->updateOrCreate(
                    ['branch_id' => $branchId],
                    ['stock_quantity' => $stockQuantity]
                );
            }
            Log::info('Transferred stock data from similar variant:', [
                'new_variant_id' => $newVariant->id,
                'source_variant_id' => $bestMatch->id,
                'similarity_score' => $bestMatchScore
            ]);
        } else {
            Log::info('No similar variant found for stock transfer:', ['variant_id' => $newVariant->id]);
        }
    }

    /**
     * Generate attribute combinations
     */
    private function generateAttributeCombinations($attributeGroups, $currentIndex = 0, $currentCombination = []) {
        if ($currentIndex >= count($attributeGroups)) {
            return !empty($currentCombination) ? [$currentCombination] : [];
        }

        $combinations = [];
        $currentGroup = $attributeGroups[$currentIndex];

        foreach ($currentGroup['values'] as $valueId) {
            $newCombination = array_merge($currentCombination, [$valueId]);

            $nextCombinations = $this->generateAttributeCombinations(
                $attributeGroups,
                $currentIndex + 1,
                $newCombination
            );

            $combinations = array_merge($combinations, $nextCombinations);
        }

        return $combinations;
    }



    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::with([
            'category',
            'images',
            'attributes.values',
            'variants.productVariantDetails.variantValue.attribute',
            'variants.variantValues.attribute'
        ])->findOrFail($id);

        // Get primary image
        $primaryImage = $product->images->first();

        // Get all branches
        $branches = Branch::where('active', true)->get();

        // Get branch stocks for all variants of this product
        $branchStocks = [];
        if ($product->variants && $product->variants->count() > 0) {
            $variantIds = $product->variants->pluck('id')->toArray();
            $stocksData = BranchStock::whereIn('product_variant_id', $variantIds)->get();

            // Organize branch stocks by branch_id and variant_id for easier access in the view
            foreach ($stocksData as $stock) {
                if (!isset($branchStocks[$stock->branch_id])) {
                    $branchStocks[$stock->branch_id] = [];
                }
                $branchStocks[$stock->branch_id][$stock->product_variant_id] = $stock->stock_quantity;
            }
        }



        // Get categories for dropdown
        $categories = Category::all();

        return view('admin.menu.product.edit', compact(
            'product',
            'primaryImage',
            'categories',
            'branches',
            'branchStocks'
        ));
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();

            // Find the existing product
            $product = Product::with([
                'images',
                'attributes.values',
                'variants.productVariantDetails'
            ])->findOrFail($id);

            // Update product basic information
            $product->update([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'base_price' => $validated['base_price'],
                'preparation_time' => $validated['preparation_time'],
                'short_description' => $validated['short_description'] ?? '',
                'description' => $validated['description'] ?? null,
                'ingredients' => $validated['ingredients_json'] ?? $validated['ingredients'] ?? '[]',
                'is_featured' => $validated['is_featured'] ?? false,
                'available' => $validated['available'] ?? true,
                'status' => $validated['status'],
                'release_at' => $validated['release_at'],
                'updated_by' => auth()->id(),
            ]);

            // Handle image deletions
            $imagesToDelete = $request->input('delete_images', []);
            if (!empty($imagesToDelete)) {
                $this->deleteImages($product, $imagesToDelete);
            }

            // Handle images using refactored methods
            $this->handleProductImages($product, $request, true);

            // Handle variants using refactored methods
            $this->updateProductVariants($product, $request->input('attributes', []));

            // Update stock data if provided
            $this->updateProductStocks(
                $request,
                $product, 
                $request->input('variant_stocks', [])
            );

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm đã được cập nhật thành công.'
            ]);

            return redirect()->route('admin.products.edit', $product->id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->handleValidationException($e, $request);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e, $request);
        }
    }

    // Handle deletion of images (Delete selected images)
    protected function deleteImages($product, $imagesToDelete)
    {
        $imagesToDeleteModels = $product->images()->whereIn('id', $imagesToDelete)->get();
        foreach ($imagesToDeleteModels as $imageModel) {
            // Delete from S3 storage
            if ($imageModel->img) {
                Storage::disk('s3')->delete($imageModel->img);
            }
            // Delete from the database
            $imageModel->delete();
        }
    }

    // Handle primary image upload
    protected function handlePrimaryImageUpload($product, $image)
    {
        Log::info('Uploading primary image', [
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
            'mime' => $image->getMimeType()
        ]);

        $oldPrimaryImage = $product->images()->where('is_primary', true)->first();
        if ($oldPrimaryImage) {
            // Delete old primary image if exists
            if ($oldPrimaryImage->img) {
                Storage::disk('s3')->delete($oldPrimaryImage->img);
                Log::info('Deleted old primary image', ['path' => $oldPrimaryImage->img]);
            }
            $oldPrimaryImage->delete();
        }

        // Generate unique filename and upload the new primary image
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));

        Log::info('S3 upload result', ['path' => $path, 'filename' => $filename]);

        if ($path) {
            // Store the new primary image in the database
            $url = Storage::disk('s3')->url('products/' . $filename);
            Log::info('S3 file url', ['url' => $url]);

            $product->images()->create([
                'img' => 'products/' . $filename,
                'is_primary' => true,
            ]);

            Log::info('Primary image saved to database successfully');
        } else {
            Log::error('Failed to upload primary image to S3');
            throw new \Exception('Không thể upload hình ảnh chính lên S3');
        }
    }

    // Handle additional images upload
    protected function handleAdditionalImagesUpload($product, $images)
    {
        foreach ($images as $image) {
            // Generate unique filename and upload the additional image
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));

            if ($path) {
                // Store the additional image in the database
                $url = Storage::disk('s3')->url('products/' . $filename);
                $product->images()->create([
                    'img' => 'products/' . $filename,
                    'is_primary' => false,
                ]);
            }
        }
    }

    // Handle attributes and variants
    protected function handleAttributesAndVariants($product, $request)
    {
        $attributes = $request->input('attributes', []);
        
        if (!empty($attributes)) {
            // Get existing variants with their details and stock data
            $existingVariants = $product->variants()->with([
                'productVariantDetails.variantValue.attribute',
                'branchStocks'
            ])->get();
            
            // Store stock data before processing variants
            $stockBackup = [];
            foreach ($existingVariants as $variant) {
                $variantSignature = $this->createVariantSignatureFromVariant($variant);
                $stockBackup[$variantSignature] = $variant->branchStocks->keyBy('branch_id')->map(function($stock) {
                    return $stock->stock_quantity;
                })->toArray();
            }
            
            // Build attribute groups for new combinations
            $attributeGroups = [];
            $newVariantValues = [];
            
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
                        
                        // Find existing variant value for this product or create new one
                        $existingValue = null;
                        foreach ($existingVariants as $existingVariant) {
                            foreach ($existingVariant->productVariantDetails as $detail) {
                                if ($detail->variantValue->attribute->id == $attribute->id && 
                                    $detail->variantValue->value == $valueData['value']) {
                                    $existingValue = $detail->variantValue;
                                    break 2;
                                }
                            }
                        }
                        
                        if ($existingValue) {
                            // Update existing variant value if price adjustment changed
                            $newPriceAdjustment = $valueData['price_adjustment'] ?? 0;
                            if ($existingValue->price_adjustment != $newPriceAdjustment) {
                                $existingValue->update(['price_adjustment' => $newPriceAdjustment]);
                                Log::info('Updated variant value price:', ['id' => $existingValue->id, 'old_price' => $existingValue->price_adjustment, 'new_price' => $newPriceAdjustment]);
                            }
                            $values[] = $existingValue;
                        } else {
                            // Create new variant value only if it doesn't exist for this product
                            $value = VariantValue::create([
                                'variant_attribute_id' => $attribute->id,
                                'value' => $valueData['value'],
                                'price_adjustment' => $valueData['price_adjustment'] ?? 0
                            ]);
                            Log::info('Created new variant value for updated product:', ['id' => $value->id, 'value' => $value->value, 'price_adjustment' => $value->price_adjustment]);
                            $values[] = $value;
                            $newVariantValues[] = $value;
                        }
                    }
                }
                
                if (!empty($values)) {
                    $attributeGroups[] = [
                        'attribute' => $attribute,
                        'values' => $values
                    ];
                }
            }
            
            if (!empty($attributeGroups)) {
                // Generate new combinations
                $newCombinations = $this->generateVariantCombinations($attributeGroups);
                
                // Create signature for each combination to compare
                $newCombinationSignatures = [];
                foreach ($newCombinations as $combination) {
                    $signature = collect($combination)
                        ->sortBy('id')
                        ->pluck('id')
                        ->implode(',');
                    $newCombinationSignatures[$signature] = $combination;
                }
                
                // Get existing combination signatures
                $existingCombinationSignatures = [];
                foreach ($existingVariants as $variant) {
                    $signature = $variant->productVariantDetails
                        ->sortBy('variant_value_id')
                        ->pluck('variant_value_id')
                        ->implode(',');
                    $existingCombinationSignatures[$signature] = $variant;
                }
                
                // Remove variants that no longer exist in new combinations
                $variantsToDelete = [];
                foreach ($existingCombinationSignatures as $signature => $variant) {
                    if (!isset($newCombinationSignatures[$signature])) {
                        Log::info('Marking variant for removal:', ['id' => $variant->id, 'signature' => $signature]);
                        $variantsToDelete[] = $variant;
                    }
                }
                
                // Create or update variants for new combinations
                foreach ($newCombinationSignatures as $signature => $combination) {
                    if (isset($existingCombinationSignatures[$signature])) {
                        // Variant already exists, keep it (preserves stock data)
                        $variant = $existingCombinationSignatures[$signature];
                        Log::info('Keeping existing variant:', ['id' => $variant->id, 'signature' => $signature]);
                    } else {
                        // Create new variant
                        $variant = $product->variants()->create(['active' => true]);
                        
                        // Create variant details
                        foreach ($combination as $variantValue) {
                            $variant->productVariantDetails()->create([
                                'variant_value_id' => $variantValue->id
                            ]);
                        }
                        
                        // Try to restore stock data from similar variant
                        $this->restoreStockDataForNewVariant($variant, $combination, $stockBackup, $variantsToDelete);
                        
                        Log::info('Created new variant:', ['id' => $variant->id, 'signature' => $signature]);
                    }
                }
                
                // Delete variants that are no longer needed
                foreach ($variantsToDelete as $variant) {
                    $variant->productVariantDetails()->delete();
                    $variant->delete();
                    Log::info('Deleted variant:', ['id' => $variant->id]);
                }
            } else {
                // No attributes provided, ensure default variant exists
                if ($existingVariants->isEmpty()) {
                    $defaultVariant = $product->variants()->create(['active' => true]);
                    Log::info('Created default variant:', ['id' => $defaultVariant->id]);
                } else {
                    // Keep first variant as default, remove others
                    $defaultVariant = $existingVariants->first();
                    $existingVariants->slice(1)->each(function($variant) {
                        $variant->productVariantDetails()->delete();
                        $variant->delete();
                    });
                    Log::info('Kept default variant:', ['id' => $defaultVariant->id]);
                }
            }
        } else {
            // No attributes provided, ensure single default variant
            $existingVariants = $product->variants()->get();
            
            if ($existingVariants->isEmpty()) {
                $defaultVariant = $product->variants()->create(['active' => true]);
                Log::info('Created default variant (no attributes):', ['id' => $defaultVariant->id]);
            } else {
                // Keep first variant, remove others
                $defaultVariant = $existingVariants->first();
                $existingVariants->slice(1)->each(function($variant) {
                    $variant->productVariantDetails()->delete();
                    $variant->delete();
                });
                Log::info('Kept single default variant:', ['id' => $defaultVariant->id]);
            }
        }
    }


    // Handle toppings
    protected function handleToppings($product, $request)
    {
        $toppings = $request->input('toppings', []);
        
        if (!empty($toppings)) {
            // Get current toppings attached to this product
            $currentToppings = $product->toppings()->get();
            $currentToppingNames = $currentToppings->pluck('name')->toArray();
            $newToppingNames = [];
            
            foreach ($toppings as $index => $toppingData) {
                if (empty($toppingData['name'])) continue;
                
                $newToppingNames[] = $toppingData['name'];
                
                // Find existing topping or create new one
                $topping = Topping::where('name', $toppingData['name'])->first();
                
                if ($topping) {
                    // Update existing topping if price or availability changed
                    $newPrice = $toppingData['price'] ?? 0;
                    $newActive = isset($toppingData['available']) ? (bool)$toppingData['available'] : true;
                    
                    if ($topping->price != $newPrice || $topping->active != $newActive) {
                        $topping->update([
                            'price' => $newPrice,
                            'active' => $newActive
                        ]);
                        Log::info('Updated existing topping:', ['id' => $topping->id, 'name' => $topping->name, 'price' => $newPrice, 'active' => $newActive]);
                    }
                } else {
                    // Create new topping
                    $topping = Topping::create([
                        'name' => $toppingData['name'],
                        'price' => $toppingData['price'] ?? 0,
                        'active' => isset($toppingData['available']) ? (bool)$toppingData['available'] : true
                    ]);
                    Log::info('Created new topping:', ['id' => $topping->id, 'name' => $topping->name]);
                }

                // Handle topping image upload if exists
                if ($request->hasFile("toppings.{$index}.image")) {
                    $this->handleToppingImageUpload($topping, $request->file("toppings.{$index}.image"));
                }

                // Attach the topping to the product if not already attached
                if (!$product->toppings()->where('topping_id', $topping->id)->exists()) {
                    $product->toppings()->attach($topping->id);
                    Log::info('Attached topping to product:', ['topping_id' => $topping->id, 'product_id' => $product->id]);
                }
            }
            
            // Remove toppings that are no longer in the new list
            $toppingsToRemove = array_diff($currentToppingNames, $newToppingNames);
            if (!empty($toppingsToRemove)) {
                $toppingIdsToRemove = $currentToppings->whereIn('name', $toppingsToRemove)->pluck('id')->toArray();
                $product->toppings()->detach($toppingIdsToRemove);
                Log::info('Detached toppings from product:', ['topping_ids' => $toppingIdsToRemove, 'product_id' => $product->id]);
            }
        } else {
            // If no toppings provided, detach all current toppings
            $product->toppings()->detach();
            Log::info('Detached all toppings from product:', ['product_id' => $product->id]);
        }
    }

    // Handle topping image upload
    protected function handleToppingImageUpload($topping, $image)
    {
        // Delete old image if exists
        if ($topping->image) {
            Storage::disk('s3')->delete($topping->image);
        }

        // Upload new image
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = Storage::disk('s3')->put('toppings/' . $filename, file_get_contents($image));

        if ($path) {
            $topping->update(['image' => 'toppings/' . $filename]);
        }
    }

    // Update stock quantities for product variants
    public function updateProductStocks(Request $request, Product $product, $variantStocks = [])
    {
        try {
            DB::beginTransaction();

            // Update variant stocks
            if (!empty($variantStocks)) {
                foreach ($variantStocks as $variantId => $branchStocks) {
                    $variantExists = DB::table('product_variants')
                        ->where('id', $variantId)
                        ->where('product_id', $product->id)
                        ->exists();

                    if (!$variantExists) continue;

                    foreach ($branchStocks as $branchId => $quantity) {
                        // Validate that branch exists
                        $branchExists = DB::table('branches')
                            ->where('id', $branchId)
                            ->exists();

                        if (!$branchExists) continue;

                        // Update branch stock
                        $branchStock = DB::table('branch_stocks')
                            ->where('branch_id', $branchId)
                            ->where('product_variant_id', $variantId)
                            ->first();

                        if ($branchStock) {
                            DB::table('branch_stocks')
                                ->where('branch_id', $branchId)
                                ->where('product_variant_id', $variantId)
                                ->update(['stock_quantity' => $quantity]);
                        } else {
                            DB::table('branch_stocks')->insert([
                                'branch_id' => $branchId,
                                'product_variant_id' => $variantId,
                                'stock_quantity' => $quantity,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm và số lượng tồn kho đã được cập nhật thành công'
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
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Xóa thành công!',
                'message' => 'Sản phẩm đã được xóa thành công.'
            ]);

            return redirect()->route('admin.products.index');
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Không thể xóa sản phẩm. ' . $e->getMessage()
            ]);

            return back();
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
                    return \Maatwebsite\Excel\Facades\Excel::download(
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
                    
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.products', $pdfData);
                    $pdf->setPaper('A4', 'landscape'); // Set landscape orientation for better table display
                    return $pdf->download($filename . '.pdf');
                    
                case 'csv':
                    return \Maatwebsite\Excel\Facades\Excel::download(
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
     * Generate custom pagination HTML to match the design
     */
    private function generateCustomPagination($products)
    {
        $html = '<div class="pagination-container flex items-center justify-between px-4 py-4 border-t">';
        
        // Left side - showing items info
        $html .= '<div class="text-sm text-muted-foreground">';
        $html .= 'Hiển thị <span id="paginationStart">' . $products->firstItem() . '</span> đến <span id="paginationEnd">' . $products->lastItem() . '</span> của <span id="paginationTotal">' . $products->total() . '</span> mục';
        $html .= '</div>';
        
        // Right side - pagination controls
        $html .= '<div class="flex items-center justify-end space-x-2 ml-auto" id="paginationControls">';
        
        // Previous button
        if (!$products->onFirstPage()) {
            $html .= '<button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage(' . ($products->currentPage() - 1) . ')">';
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">';
            $html .= '<path d="m15 18-6-6 6-6"></path>';
            $html .= '</svg>';
            $html .= '</button>';
        }
        
        // Page numbers
        foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url) {
            $activeClass = $products->currentPage() == $page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted';
            $html .= '<button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium ' . $activeClass . '" onclick="changePage(' . $page . ')">';
            $html .= $page;
            $html .= '</button>';
        }
        
        // Next button
        if ($products->currentPage() !== $products->lastPage()) {
            $html .= '<button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage(' . ($products->currentPage() + 1) . ')">';
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">';
            $html .= '<path d="m9 18 6-6-6-6"></path>';
            $html .= '</svg>';
            $html .= '</button>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}
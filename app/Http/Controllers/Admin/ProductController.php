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
use App\Models\Topping;
use App\Models\BranchStock;
use App\Models\ProductVariant;
use App\Models\ProductImg;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\ToppingStock;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        try {
            $query = Product::with('category');

            if ($request->has('category_id') && $request->category_id) {
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
                    if (in_array('in_stock', $request->stock_status)) {
                        $q->orWhere('stock', '>', 0);
                    }
                    if (in_array('out_of_stock', $request->stock_status)) {
                        $q->orWhere('stock', '=', 0);
                    }
                    if (in_array('low_stock', $request->stock_status)) {
                        $q->orWhereBetween('stock', [1, 9]);
                    }
                });
            }

            // Lọc theo ngày thêm
            if ($request->has('date_added') && $request->date_added) {
                $query->whereDate('created_at', $request->date_added);
            }

            // Tìm kiếm theo tên hoặc mã sản phẩm
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('id', 'like', '%' . $request->search . '%');
                });
            }

            $products = $query->latest()->paginate(10);
            $categories = Category::all();
            $minPrice = Product::min('base_price') ?? 0;
            $maxPrice = Product::max('base_price') ?? 10000000;

            return view('admin.products.index', compact('products', 'categories', 'minPrice', 'maxPrice'));

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
     * Show the form for creating a new resource.
     */
    public function create() {
        $categories = Category::all();
        $branches = Branch::where('active', true)->get();
        return view('admin.products.create', compact('categories', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request) {
        try {

            $validated = $request->validated();

            DB::beginTransaction();

            $category = Category::findOrFail($validated['category_id']);

            $lastProduct = Product::where('sku', 'like', $category->short_name . '-%')
                ->orderBy('id', 'desc')
                ->first();

            $skuNumber = 1;
            if ($lastProduct) {
                $lastNumber = (int) substr($lastProduct->sku, strrpos($lastProduct->sku, '-') + 1);
                $skuNumber = $lastNumber + 1;
            }

            $sku = $category->short_name . '-' . str_pad($skuNumber, 5, '0', STR_PAD_LEFT);

            // Create product
            $product = Product::create([
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

            // Handle primary image
            if ($request->hasFile('primary_image')) {
                $image = $request->file('primary_image');
                Log::info('Uploading primary image', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType()
                ]);

                // Delete old primary image if exists
                $oldPrimaryImage = $product->images()->where('is_primary', true)->first();
                if ($oldPrimaryImage) {
                    if ($oldPrimaryImage->img) {
                        Storage::disk('s3')->delete($oldPrimaryImage->img);
                    }
                    $oldPrimaryImage->delete();
                }

                // Generate unique filename
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

                // Upload to S3
                $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));
                Log::info('S3 put result', ['path' => $path, 'filename' => $filename]);

                if ($path) {
                    // Get the URL of uploaded file
                    $url = Storage::disk('s3')->url('products/' . $filename);
                    Log::info('S3 file url', ['url' => $url]);

                    $product->images()->create([
                        'img' => 'products/' . $filename,
                        'is_primary' => true,
                    ]);
                }
            }

            // Handle additional images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    Log::info('Uploading additional image', [
                        'original_name' => $image->getClientOriginalName(),
                        'size' => $image->getSize(),
                        'mime' => $image->getMimeType()
                    ]);

                    // Generate unique filename
                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

                    // Upload to S3
                    $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));

                    if ($path) {
                        // Get the URL of uploaded file
                        $url = Storage::disk('s3')->url('products/' . $filename);

                        $product->images()->create([
                            'img' => 'products/' . $filename,
                            'is_primary' => false,
                        ]);
                    }
                }
            }

            // Handle attributes and variant values - Clear existing variants first
            $product->variants()->each(function ($variant) {
                $variant->productVariantDetails()->delete();
                $variant->delete();
            });

            $attributes = $request->input('attributes', []);
            if (!empty($attributes)) {
                $attributeGroups = [];

                foreach ($attributes as $attributeData) {
                    // Skip if attribute name is empty
                    if (empty($attributeData['name'])) {
                        continue;
                    }

                    // Create or get attribute
                    $attribute = VariantAttribute::firstOrCreate(['name' => $attributeData['name']]);

                    $values = [];
                    if (isset($attributeData['values']) && is_array($attributeData['values'])) {
                        foreach ($attributeData['values'] as $valueData) {
                            // Skip if value is empty
                            if (empty($valueData['value'])) {
                                continue;
                            }

                            // Create new variant value for each product (don't share between products)
                            $value = VariantValue::create([
                                'variant_attribute_id' => $attribute->id,
                                'value' => $valueData['value'],
                                'price_adjustment' => $valueData['price_adjustment'] ?? 0
                            ]);
                            Log::info('Created/Updated variant value:', ['id' => $value->id, 'value' => $value->value]);
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

                if (!empty($attributeGroups)) {
                    // Generate all possible combinations
                    $combinations = $this->generateVariantCombinations($attributeGroups);
                    Log::info('Generated combinations count:', ['count' => count($combinations)]);

                    // Create variants for each combination
                    foreach ($combinations as $index => $combination) {
                        // Create product variant
                        $variant = $product->variants()->create([
                            'active' => true
                        ]);
                        Log::info('Created variant:', ['id' => $variant->id]);

                        // Create variant details for each value in the combination
                        foreach ($combination as $variantValue) {
                            $variantDetail = $variant->productVariantDetails()->create([
                                'variant_value_id' => $variantValue->id
                            ]);
                            Log::info('Created variant detail:', ['id' => $variantDetail->id, 'variant_value_id' => $variantValue->id]);
                        }
                    }
                } else {
                    $defaultVariant = $product->variants()->create([
                        'active' => true
                    ]);
                }
            } else {
                $defaultVariant = $product->variants()->create([
                    'active' => true
                ]);
            }

            // Handle toppings - Clear existing toppings first
            $product->toppings()->detach();

            $toppings = $request->input('toppings', []);
            if (!empty($toppings)) {
                Log::info('Processing toppings...');

                foreach ($toppings as $index => $toppingData) {
                    // Skip if topping name is empty
                    if (empty($toppingData['name'])) {
                        continue;
                    }

                    // Create topping if it doesn't exist
                    $topping = Topping::firstOrCreate(
                        ['name' => $toppingData['name']],
                        [
                            'price' => $toppingData['price'] ?? 0,
                            'active' => isset($toppingData['available']) ? (bool)$toppingData['available'] : true
                        ]
                    );
                    Log::info('Created/Found topping:', ['id' => $topping->id, 'name' => $topping->name]);

                    // Handle topping image if uploaded
                    if ($request->hasFile("toppings.{$index}.image")) {
                        $image = $request->file("toppings.{$index}.image");
                        Log::info('Uploading topping image from dot notation', [
                            'original_name' => $image->getClientOriginalName(),
                        ]);

                        // Delete old image if exists
                        if ($topping->image) {
                            Storage::disk('s3')->delete($topping->image);
                        }

                        // Generate unique filename
                        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

                        // Upload to S3
                        $path = Storage::disk('s3')->put('toppings/' . $filename, file_get_contents($image));

                        if ($path) {
                            $topping->update([
                                'image' => 'toppings/' . $filename
                            ]);
                        }
                    }
                    // Also check the alternative array format
                    else if (isset($request->file('toppings')[$index]['image'])) {
                        $image = $request->file('toppings')[$index]['image'];
                        Log::info('Uploading topping image from array', [
                            'original_name' => $image->getClientOriginalName(),
                        ]);

                        // Delete old image if exists
                        if ($topping->image) {
                            Storage::disk('s3')->delete($topping->image);
                        }

                        // Generate unique filename
                        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

                        // Upload to S3
                        $path = Storage::disk('s3')->put('toppings/' . $filename, file_get_contents($image));

                        if ($path) {
                            $topping->update([
                                'image' => 'toppings/' . $filename
                            ]);
                        }
                    }

                    // Attach topping to product
                    $product->toppings()->attach($topping->id);
                }
            }

            DB::commit();

            // Chỉ flash toast sau khi commit thành công
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm đã được cập nhật thành công'
            ]);

            return redirect()->route('admin.products.stock', $product->id);
        } catch (\Illuminate\Validation\ValidationException $e) {
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
        } catch (\Exception $e) {
            DB::rollBack();

            // Check if this is an AJAX request
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
            'variants.variantValues.attribute',
            'toppings'
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

        // Get topping stocks for all toppings of this product
        $toppingStocks = [];
        if ($product->toppings && $product->toppings->count() > 0) {
            $toppingIds = $product->toppings->pluck('id')->toArray();
            $toppingStocksData = ToppingStock::whereIn('topping_id', $toppingIds)->get();

            // Organize topping stocks by branch_id and topping_id for easier access in the view
            foreach ($toppingStocksData as $stock) {
                if (!isset($toppingStocks[$stock->branch_id])) {
                    $toppingStocks[$stock->branch_id] = [];
                }
                $toppingStocks[$stock->branch_id][$stock->topping_id] = $stock->stock_quantity;
            }
        }

        // Get categories for dropdown
        $categories = Category::all();

        return view('admin.products.edit', compact(
            'product',
            'primaryImage',
            'categories',
            'branches',
            'branchStocks',
            'toppingStocks'
        ));
    }

    public function update(ProductRequest $request, $id)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Find the existing product
            $product = Product::with([
                'images',
                'attributes.values',
                'variants.productVariantDetails',
                'toppings'
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

            // Handle image deletions (Delete selected images)
            $imagesToDelete = $request->input('delete_images', []);
            if (!empty($imagesToDelete)) {
                $this->deleteImages($product, $imagesToDelete);
            }

            // Handle primary image upload
            if ($request->hasFile('primary_image')) {
                $this->handlePrimaryImageUpload($product, $request->file('primary_image'));
            }

            // Handle additional images upload
            if ($request->hasFile('images')) {
                $this->handleAdditionalImagesUpload($product, $request->file('images'));
            }

            // Handle attributes and variants
            $this->handleAttributesAndVariants($product, $request);

            // Handle toppings
            $this->handleToppings($product, $request);

            // Update stock data if provided (variant and topping stocks)
            $variantStocks = $request->input('variant_stocks', []);
            $toppingStocks = $request->input('topping_stocks', []);

            // Log the received stock data for debugging
            Log::info('Received variant stocks:', $variantStocks);
            Log::info('Received topping stocks:', $toppingStocks);

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
                        BranchStock::updateOrCreate(
                            [
                                'branch_id' => $branchId,
                                'product_variant_id' => $variantId,
                            ],
                            ['stock_quantity' => $quantity]
                        );
                    }
                }
            }

            // Update topping stocks
            if (!empty($toppingStocks)) {
                foreach ($toppingStocks as $toppingId => $branchStocks) {
                    $toppingExists = DB::table('product_toppings')
                        ->join('toppings', 'product_toppings.topping_id', '=', 'toppings.id')
                        ->where('product_toppings.product_id', $product->id)
                        ->where('toppings.id', $toppingId)
                        ->exists();

                    if (!$toppingExists) continue;

                    foreach ($branchStocks as $branchId => $quantity) {
                        // Validate that branch exists
                        $branchExists = DB::table('branches')
                            ->where('id', $branchId)
                            ->exists();

                        if (!$branchExists) continue;

                        // Update or create topping stock
                        ToppingStock::updateOrCreate(
                            [
                                'branch_id' => $branchId,
                                'topping_id' => $toppingId,
                            ],
                            ['stock_quantity' => $quantity]
                        );
                    }
                }
            }

            DB::commit();

            // Flash success message
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Sản phẩm đã được cập nhật thành công.'
            ]);

            return redirect()->route('admin.products.edit', $product->id);
        } catch (\Exception $e) {
            DB::rollBack();

            // Flash error message
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back()->withInput();
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
        // Clear existing variants first
        $product->variants()->each(function ($variant) {
            $variant->productVariantDetails()->delete();
            $variant->delete();
        });

        $attributes = $request->input('attributes', []);
        if (!empty($attributes)) {
            $attributeGroups = [];

            foreach ($attributes as $attributeData) {
                // Skip if attribute name is empty
                if (empty($attributeData['name'])) {
                    continue;
                }

                // Create or get attribute
                $attribute = VariantAttribute::firstOrCreate(['name' => $attributeData['name']]);

                $values = [];
                if (isset($attributeData['values']) && is_array($attributeData['values'])) {
                    foreach ($attributeData['values'] as $valueData) {
                        // Skip if value is empty
                        if (empty($valueData['value'])) {
                            continue;
                        }

                        // Create new variant value for each product (don't share between products)
                        $value = VariantValue::create([
                            'variant_attribute_id' => $attribute->id,
                            'value' => $valueData['value'],
                            'price_adjustment' => $valueData['price_adjustment'] ?? 0
                        ]);
                        Log::info('Updated variant value:', ['id' => $value->id, 'value' => $value->value]);
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

            if (!empty($attributeGroups)) {
                // Generate all possible combinations
                $combinations = $this->generateVariantCombinations($attributeGroups);
                Log::info('Generated combinations count for update:', ['count' => count($combinations)]);

                // Create variants for each combination
                foreach ($combinations as $index => $combination) {
                    // Create product variant
                    $variant = $product->variants()->create([
                        'active' => true
                    ]);
                    Log::info('Updated/Created variant:', ['id' => $variant->id]);

                    // Create variant details for each value in the combination
                    foreach ($combination as $variantValue) {
                        $variantDetail = $variant->productVariantDetails()->create([
                            'variant_value_id' => $variantValue->id
                        ]);
                        Log::info('Updated/Created variant detail:', ['id' => $variantDetail->id, 'variant_value_id' => $variantValue->id]);
                    }
                }
            } else {
                // Create default variant if no attributes
                $defaultVariant = $product->variants()->create([
                    'active' => true
                ]);
                Log::info('Created default variant for update:', ['id' => $defaultVariant->id]);
            }
        } else {
            // Create default variant if no attributes provided
            $defaultVariant = $product->variants()->create([
                'active' => true
            ]);
            Log::info('Created default variant (no attributes):', ['id' => $defaultVariant->id]);
        }
    }


    // Handle toppings
    protected function handleToppings($product, $request)
    {
        // Detach all previous toppings
        $product->toppings()->detach();

        $toppings = $request->input('toppings', []);
        if (!empty($toppings)) {
            foreach ($toppings as $index => $toppingData) {
                if (empty($toppingData['name'])) continue;

                // Create or find the topping
                $topping = Topping::firstOrCreate(
                    ['name' => $toppingData['name']],
                    [
                        'price' => $toppingData['price'] ?? 0,
                        'active' => isset($toppingData['available']) ? (bool)$toppingData['available'] : true
                    ]
                );

                // Handle topping image upload if exists
                if ($request->hasFile("toppings.{$index}.image")) {
                    $this->handleToppingImageUpload($topping, $request->file("toppings.{$index}.image"));
                }

                // Attach the topping to the product
                $product->toppings()->attach($topping->id);
            }
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

    // Update stock quantities for product variants and toppings
    public function updateProductStocks(Request $request, Product $product, $variantStocks = [], $toppingStocks = [])
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

            // Update topping stocks
            if (!empty($toppingStocks)) {
                foreach ($toppingStocks as $toppingId => $branchStocks) {
                    $toppingExists = DB::table('product_toppings')
                        ->join('toppings', 'product_toppings.topping_id', '=', 'toppings.id')
                        ->where('product_toppings.product_id', $product->id)
                        ->where('toppings.id', $toppingId)
                        ->exists();

                    if (!$toppingExists) continue;

                    foreach ($branchStocks as $branchId => $quantity) {
                        // Validate that branch exists
                        $branchExists = DB::table('branches')
                            ->where('id', $branchId)
                            ->exists();

                        if (!$branchExists) continue;

                        // Update or create topping stock
                        ToppingStock::updateOrCreate(
                            [
                                'branch_id' => $branchId,
                                'topping_id' => $toppingId,
                            ],
                            ['stock_quantity' => $quantity]
                        );
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
            $type = $request->type ?? 'excel';
            $query = Product::with('category');

            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('price_min') && $request->price_min) {
                $query->where('base_price', '>=', $request->price_min);
            }

            if ($request->has('price_max') && $request->price_max) {
                $query->where('base_price', '<=', $request->price_max);
            }

            if ($request->has('stock_status')) {
                if ($request->stock_status == 'in_stock') {
                    $query->where('stock', '>', 0);
                } elseif ($request->stock_status == 'out_of_stock') {
                    $query->where('stock', '<=', 0);
                }
            }

            $products = $query->latest()->get();

            // Xử lý xuất dữ liệu theo định dạng
            switch ($type) {
                case 'excel':
                    return Excel::download(new \App\Exports\ProductsExport($products), 'products.xlsx');

                case 'pdf':
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.products', compact('products'));
                    return $pdf->download('products.pdf');
                case 'csv':
                    return Excel::download(new \App\Exports\ProductsExport($products), 'products.csv', \Maatwebsite\Excel\Excel::CSV);
                default:
                    return $this->exportJson($products, 'products.json');
            }
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xuất dữ liệu: ' . $e->getMessage()
            ]);
            return redirect()->back();
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
}
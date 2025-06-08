<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
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
use Illuminate\Support\Str;
use App\Models\ToppingStock;
use Illuminate\Support\Facades\Log;

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
    public function store(StoreProductRequest $request) {
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
                'ingredients' => $validated['ingredients_json'] ?? '[]',
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
                        'img_url' => $url,
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
                            'img_url' => $url,
                            'is_primary' => false,
                        ]);
                    }
                }
            }

            // Handle attributes and variant values - Clear existing variants first
            $product->variants()->each(function($variant) {
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

                            // Create or update variant value
                            $value = VariantValue::updateOrCreate(
                                [
                                    'variant_attribute_id' => $attribute->id,
                                    'value' => $valueData['value']
                                ],
                                ['price_adjustment' => $valueData['price'] ?? 0]
                            );
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
    private function generateVariantCombinations($attributeGroups)
    {
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
    private function generateAttributeCombinations($attributeGroups, $currentIndex = 0, $currentCombination = [])
    {
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
    public function edit($id) {
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id) {
        try {

            $validated = $request->validated();

            DB::beginTransaction();

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
                'ingredients' => $validated['ingredients_json'] ?? '[]',
                'is_featured' => $validated['is_featured'] ?? false,
                'available' => $validated['available'] ?? true,
                'status' => $validated['status'],
                'release_at' => $validated['release_at'],
                'updated_by' => auth()->id(),
            ]);

            // Handle image deletions
            $imagesToDelete = $request->input('delete_images', []);
            if (!empty($imagesToDelete)) {
                $imagesToDeleteModels = $product->images()->whereIn('id', $imagesToDelete)->get();
                foreach ($imagesToDeleteModels as $imageModel) {
                    // Delete from S3
                    if ($imageModel->img) {
                        Storage::disk('s3')->delete($imageModel->img);
                    }
                    // Delete from database
                    $imageModel->delete();
                }
            }

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
                        'img_url' => $url,
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
                            'img_url' => $url,
                            'is_primary' => false,
                        ]);
                    }
                }
            }

            // Handle attributes and variant values - Clear existing variants first
            $product->variants()->each(function($variant) {
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

                            // Create or update variant value
                            $value = VariantValue::updateOrCreate(
                                [
                                    'variant_attribute_id' => $attribute->id,
                                    'value' => $valueData['value']
                                ],
                                ['price_adjustment' => $valueData['price'] ?? 0]
                            );
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
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product) {
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
    public function export(Request $request) {
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
    private function exportJson($products, $filename) {
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
     * Update stock quantities for product variants across branches
     */
    public function updateStocks(Request $request, Product $product) {
        try {
            DB::beginTransaction();
            
            $stocks = $request->input('stocks', []);
            
            // Update stock for each branch and variant
            foreach ($stocks as $branchId => $variantStocks) {
                foreach ($variantStocks as $variantId => $quantity) {
                    // Get or create the branch stock entry
                    $branchStock = DB::table('branch_stocks')
                        ->where('branch_id', $branchId)
                        ->where('product_variant_id', $variantId)
                        ->first();
                    
                    if ($branchStock) {
                        // Update existing record
                        DB::table('branch_stocks')
                            ->where('branch_id', $branchId)
                            ->where('product_variant_id', $variantId)
                            ->update(['stock_quantity' => $quantity]);
                    } else {
                        // Create new record
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
            
            DB::commit();
            
            // Check if product has toppings
            if ($product->toppings && $product->toppings->count() > 0) {
                // Redirect to stock page with topping tab
                return redirect()->route('admin.products.stock', $product->id)
                    ->with('active_tab', 'toppings');
            } else {
                // Redirect to products index
                return redirect()->route('admin.products.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Set session flash message for the toast notification
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show stock management page for a product
     */
    public function stock($id)
    {
        $product = Product::with([
            'variants.productVariantDetails.variantValue.attribute',
            'toppings'
        ])->findOrFail($id);
        
        // Get all active branches
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
        
        return view('admin.products.stock', compact('product', 'branches', 'branchStocks', 'toppingStocks'));
    }

    /**
     * Show topping stock management page
     */
    public function toppingStocks()
    {
        // Get all active toppings
        $toppings = Topping::where('active', true)->get();
        
        // Get all active branches
        $branches = Branch::where('active', true)->get();
        
        // Get all topping stocks
        $toppingStocks = ToppingStock::all()->groupBy(['branch_id', 'topping_id']);
        
        return view('admin.products.topping-stocks', compact('toppings', 'branches', 'toppingStocks'));
    }
    
    /**
     * Update topping stocks
     */
    public function updateToppingStocks(Request $request) {
        try {
            DB::beginTransaction();
            
            foreach ($request->input('topping_stock', []) as $branchId => $toppings) {
                foreach ($toppings as $toppingId => $quantity) {
                    ToppingStock::updateOrCreate(
                        [
                            'branch_id' => $branchId,
                            'topping_id' => $toppingId,
                        ],
                        [
                            'stock_quantity' => $quantity,
                        ]
                    );
                }
            }
            
            DB::commit();
            
            
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Số lượng topping đã được cập nhật thành công.'
            ]);
            
            return redirect()->route('admin.products.index');
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
}

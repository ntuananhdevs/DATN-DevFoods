<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with('category');

            // Lọc theo danh mục
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
    public function create()
    {
        $categories = Category::all();
        $branches = Branch::where('active', true)->get();
        return view('admin.products.create', compact('categories', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'base_price' => 'required|numeric|min:0',
                'preparation_time' => 'nullable|integer|min:0',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'ingredients_json' => 'nullable|string',
                'is_featured' => 'boolean',
                'available' => 'boolean',
                'status' => 'required|in:coming_soon,selling,discontinued',
                'release_at' => 'nullable|date|required_if:status,coming_soon',
                'primary_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'attributes' => 'nullable|array',
                'attributes.*.name' => 'required_with:attributes|string|max:255',
                'attributes.*.values' => 'required_with:attributes|array',
                'attributes.*.values.*.value' => 'required|string|max:255',
                'attributes.*.values.*.price_adjustment' => 'nullable|numeric',
                'toppings' => 'nullable|array',
                'toppings.*.name' => 'required_with:toppings|string|max:255',
                'toppings.*.price' => 'required_with:toppings|numeric|min:0',
                'toppings.*.available' => 'nullable|boolean',
                'toppings.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            DB::beginTransaction();

            // Get category for SKU generation
            $category = Category::findOrFail($validated['category_id']);
            
            // Generate SKU
            $lastProduct = Product::where('sku', 'like', $category->short_name . '-%')
                ->orderBy('id', 'desc')
                ->first();
            
            $skuNumber = 1;
            if ($lastProduct) {
                $lastNumber = (int) substr($lastProduct->sku, strrpos($lastProduct->sku, '-') + 1);
                $skuNumber = $lastNumber + 1;
            }
            
            // Format SKU with 5 digits
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
                'is_featured' => $request->boolean('is_featured'),
                'available' => $request->boolean('available'),
                'status' => $validated['status'],
                'release_at' => $validated['release_at'],
                'created_by' => auth()->id(),
            ]);

            // Handle primary image
            if ($request->hasFile('primary_image')) {
                $image = $request->file('primary_image');
                \Log::info('Uploading primary image', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType()
                ]);
                
                // Generate unique filename
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                
                // Upload to S3
                $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));
                \Log::info('S3 put result', ['path' => $path, 'filename' => $filename]);
                
                if ($path) {
                    // Get the URL of uploaded file
                    $url = Storage::disk('s3')->url('products/' . $filename);
                    \Log::info('S3 file url', ['url' => $url]);
                    
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
                    \Log::info('Uploading additional image', [
                        'original_name' => $image->getClientOriginalName(),
                        'size' => $image->getSize(),
                        'mime' => $image->getMimeType()
                    ]);
                    
                    // Generate unique filename
                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    
                    // Upload to S3
                    $path = Storage::disk('s3')->put('products/' . $filename, file_get_contents($image));
                    \Log::info('S3 put result', ['path' => $path, 'filename' => $filename]);
                    
                    if ($path) {
                        // Get the URL of uploaded file
                        $url = Storage::disk('s3')->url('products/' . $filename);
                        \Log::info('S3 file url', ['url' => $url]);
                        
                        $product->images()->create([
                            'img' => 'products/' . $filename,
                            'img_url' => $url,
                            'is_primary' => false,
                        ]);
                    }
                }
            }

            // Handle attributes and variant values
            $attributes = $request->input('attributes', []);
            if (!empty($attributes)) {
                \Log::info('Processing attributes...');
                
                $attributeGroups = [];
                
                foreach ($attributes as $attributeData) {
                    // Create or get attribute
                    $attribute = VariantAttribute::firstOrCreate(['name' => $attributeData['name']]);
                    \Log::info('Created/Found attribute:', ['id' => $attribute->id, 'name' => $attribute->name]);
                    
                    $values = [];
                    foreach ($attributeData['values'] as $valueData) {
                        // Create or update variant value
                        $value = VariantValue::updateOrCreate(
                            [
                                'variant_attribute_id' => $attribute->id,
                                'value' => $valueData['value']
                            ],
                            ['price_adjustment' => $valueData['price_adjustment'] ?? 0]
                        );
                        \Log::info('Created/Updated variant value:', ['id' => $value->id, 'value' => $value->value]);
                        $values[] = $value;
                    }
                    
                    $attributeGroups[] = [
                        'attribute' => $attribute,
                        'values' => $values
                    ];
                }
            
                // Generate all possible combinations
                $combinations = $this->generateVariantCombinations($attributeGroups);
                \Log::info('Generated combinations count:', ['count' => count($combinations)]);
            
                // Create variants for each combination
                foreach ($combinations as $index => $combination) {
                    // Create product variant
                    $variant = $product->variants()->create([
                        'active' => true
                    ]);
                    \Log::info('Created variant:', ['id' => $variant->id]);
            
                    // Create variant details for each value in the combination
                    foreach ($combination as $variantValue) {
                        $variantDetail = $variant->productVariantDetails()->create([
                            'variant_value_id' => $variantValue->id
                        ]);
                        \Log::info('Created variant detail:', ['id' => $variantDetail->id, 'variant_value_id' => $variantValue->id]);
                    }
                }
            } else {
                \Log::info('No attributes provided, creating default variant');
                // If no attributes, create a default variant
                $defaultVariant = $product->variants()->create([
                    'active' => true
                ]);
                \Log::info('Created default variant:', ['id' => $defaultVariant->id]);
            }

            // Handle toppings
            $toppings = $request->input('toppings', []);
            if (!empty($toppings)) {
                \Log::info('Processing toppings...');
                
                foreach ($toppings as $index => $toppingData) {
                    // Create topping if it doesn't exist
                    $topping = Topping::firstOrCreate(
                        ['name' => $toppingData['name']],
                        [
                            'price' => $toppingData['price'],
                            'active' => isset($toppingData['available']) ? (bool)$toppingData['available'] : true
                        ]
                    );
                    \Log::info('Created/Found topping:', ['id' => $topping->id, 'name' => $topping->name]);

                    // Handle topping image if uploaded
                    if ($request->hasFile("toppings.{$index}.image")) {
                        $image = $request->file("toppings.{$index}.image");
                        \Log::info('Uploading topping image from dot notation', [
                            'original_name' => $image->getClientOriginalName(),
                        ]);
                        
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
                        \Log::info('Uploading topping image from array', [
                            'original_name' => $image->getClientOriginalName(),
                        ]);
                        
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
                    \Log::info('Attached topping to product:', ['product_id' => $product->id, 'topping_id' => $topping->id]);
                }
            }

            DB::commit();
            \Log::info('Transaction committed successfully');

            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được tạo thành công!',
                    'redirect' => route('admin.products.index')
                ]);
            }
            
            return redirect()->route('admin.products.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Sản phẩm đã được tạo thành công!'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product creation error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tạo sản phẩm: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra khi tạo sản phẩm: ' . $e->getMessage()
            ]);
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            return view('admin.products.show', compact('product'));
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
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::with([
            'category',
            'images',
            'attributes.values',
            'variants.productVariantDetails.variantValue.attribute',
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
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'base_price' => 'required|numeric|min:0',
                'status' => 'required|in:coming_soon,selling,discontinued',
                'release_at' => 'nullable|date',
                'preparation_time' => 'nullable|integer|min:0',
            ]);

            DB::beginTransaction();
            
            $product = Product::findOrFail($id);
            
            // Update basic product information
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->base_price = $request->base_price;
            $product->status = $request->status;
            $product->release_at = $request->release_at;
            $product->preparation_time = $request->preparation_time;
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->ingredients = json_decode($request->ingredients_json ?? '[]');
            $product->is_featured = $request->has('is_featured') ? 1 : 0;
            
            $product->save();
            
            // Handle primary image upload
            if ($request->hasFile('primary_image')) {
                $primaryImage = $request->file('primary_image');
                $path = $primaryImage->store('products', 's3');
                
                // Check if primary image already exists
                $existingPrimaryImage = $product->images()->first();
                if ($existingPrimaryImage) {
                    // Delete old image from storage
                    Storage::disk('s3')->delete($existingPrimaryImage->img);
                    $existingPrimaryImage->img = $path;
                    $existingPrimaryImage->save();
                } else {
                    // Create new primary image
                    $product->images()->create([
                        'img' => $path,
                        'display_order' => 0
                    ]);
                }
            }
            
            // Handle additional images
            if ($request->hasFile('images')) {
                $displayOrder = $product->images()->count();
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 's3');
                    $product->images()->create([
                        'img' => $path,
                        'display_order' => $displayOrder++
                    ]);
                }
            }
            
            // Handle deleted images
            if ($request->has('deleted_images')) {
                $deletedImages = $request->input('deleted_images');
                $productImages = ProductImg::whereIn('id', $deletedImages)->get();
                
                foreach ($productImages as $image) {
                    Storage::disk('s3')->delete($image->img);
                    $image->delete();
                }
            }
            
            // Handle attributes and variant values
            if ($request->has('attributes')) {
                // Log for debugging
                \Log::info('Processing attributes...', ['count' => count($request->input('attributes'))]);
                
                $attributeGroups = [];
                $existingVariantValueIds = [];
                
                foreach ($request->input('attributes') as $attrIndex => $attribute) {
                    // Skip if no name provided
                    if (empty($attribute['name'])) continue;
                    
                    // Create or update attribute
                    $attributeModel = null;
                    
                    if (isset($attribute['id'])) {
                        // Update existing attribute
                        $attributeModel = VariantAttribute::find($attribute['id']);
                        if ($attributeModel) {
                            $attributeModel->name = $attribute['name'];
                            $attributeModel->save();
                        }
                    }
                    
                    if (!$attributeModel) {
                        // Create new attribute
                        $attributeModel = VariantAttribute::firstOrCreate(['name' => $attribute['name']]);
                    }
                    
                    \Log::info('Attribute processed:', [
                        'id' => $attributeModel->id, 
                        'name' => $attributeModel->name
                    ]);
                    
                    $values = [];
                    // Handle attribute values
                    if (!empty($attribute['values'])) {
                        foreach ($attribute['values'] as $valueData) {
                            // Skip if no value provided
                            if (empty($valueData['value'])) continue;
                            
                            $valueModel = null;
                            
                            if (isset($valueData['id'])) {
                                // Update existing value
                                $valueModel = VariantValue::find($valueData['id']);
                                if ($valueModel) {
                                    $valueModel->value = $valueData['value'];
                                    $valueModel->price_adjustment = $valueData['price_adjustment'] ?? 0;
                                    $valueModel->save();
                                    $existingVariantValueIds[] = $valueModel->id;
                                }
                            }
                            
                            if (!$valueModel) {
                                // Create new value
                                $valueModel = VariantValue::updateOrCreate(
                                    [
                                        'variant_attribute_id' => $attributeModel->id,
                                        'value' => $valueData['value']
                                    ],
                                    [
                                        'price_adjustment' => $valueData['price_adjustment'] ?? 0
                                    ]
                                );
                                $existingVariantValueIds[] = $valueModel->id;
                            }
                            
                            \Log::info('Value processed:', [
                                'id' => $valueModel->id, 
                                'value' => $valueModel->value,
                                'price_adjustment' => $valueModel->price_adjustment
                            ]);
                            
                            $values[] = $valueModel;
                        }
                    }
                    
                    $attributeGroups[] = [
                        'attribute' => $attributeModel,
                        'values' => $values
                    ];
                }
                
                // If attributes have changed, regenerate variants
                if (count($attributeGroups) > 0) {
                    \Log::info('Regenerating variants based on attributes');
                    
                    // Save existing variants in case we need to map stock data
                    $existingVariants = $product->variants()->with('productVariantDetails.variantValue')->get();
                    $existingVariantMap = [];
                    
                    foreach ($existingVariants as $variant) {
                        // Create a signature for each variant based on its attribute values
                        $valueIds = $variant->productVariantDetails()
                            ->join('variant_values', 'product_variant_details.variant_value_id', '=', 'variant_values.id')
                            ->orderBy('variant_values.variant_attribute_id')
                            ->pluck('variant_value_id')
                            ->toArray();
                        
                        if (!empty($valueIds)) {
                            $signature = implode('-', $valueIds);
                            $existingVariantMap[$signature] = $variant->id;
                        }
                    }
                    
                    // Delete old variants and their details
                    // But save branch stock data first to reapply after creating new variants
                    $stockData = DB::table('branch_stocks')
                        ->whereIn('product_variant_id', $existingVariants->pluck('id')->toArray())
                        ->get(['branch_id', 'product_variant_id', 'stock_quantity'])
                        ->keyBy(function($item) {
                            return $item->branch_id . '-' . $item->product_variant_id;
                        });
                    
                    // Now delete old data
                    foreach ($existingVariants as $variant) {
                        $variant->productVariantDetails()->delete();
                    }
                    $product->variants()->delete();
                    
                    // Generate all possible combinations
                    $combinations = $this->generateVariantCombinations($attributeGroups);
                    \Log::info('Generated combinations count:', ['count' => count($combinations)]);
                
                    // Create variants for each combination
                    foreach ($combinations as $combination) {
                        // Create product variant
                        $variant = $product->variants()->create([
                            'active' => true
                        ]);
                        \Log::info('Created variant:', ['id' => $variant->id]);
                
                        // Create variant details for each value in the combination
                        $valueIds = [];
                        foreach ($combination as $variantValue) {
                            $variantDetail = $variant->productVariantDetails()->create([
                                'variant_value_id' => $variantValue->id
                            ]);
                            $valueIds[] = $variantValue->id;
                            \Log::info('Created variant detail:', [
                                'id' => $variantDetail->id, 
                                'variant_value_id' => $variantValue->id
                            ]);
                        }
                        
                        // If this combination maps to an old variant, restore its stock data
                        if (!empty($valueIds)) {
                            $signature = implode('-', $valueIds);
                            if (isset($existingVariantMap[$signature])) {
                                $oldVariantId = $existingVariantMap[$signature];
                                \Log::info('Found matching old variant:', [
                                    'old_id' => $oldVariantId,
                                    'new_id' => $variant->id
                                ]);
                                
                                // Restore stock data for this variant
                                foreach ($stockData as $key => $stock) {
                                    if ($stock->product_variant_id == $oldVariantId) {
                                        DB::table('branch_stocks')->updateOrInsert(
                                            [
                                                'branch_id' => $stock->branch_id,
                                                'product_variant_id' => $variant->id
                                            ],
                                            [
                                                'stock_quantity' => $stock->stock_quantity,
                                                'updated_at' => now()
                                            ]
                                        );
                                        \Log::info('Restored stock data:', [
                                            'branch_id' => $stock->branch_id,
                                            'quantity' => $stock->stock_quantity
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Handle toppings
            if ($request->has('toppings')) {
                // Get existing topping IDs
                $existingToppingIds = $product->toppings()->pluck('toppings.id')->toArray();
                $newToppingIds = [];
                
                foreach ($request->input('toppings') as $index => $toppingData) {
                    if (empty($toppingData['name'])) continue;
                    
                    if (isset($toppingData['id'])) {
                        // Update existing topping
                        $topping = Topping::find($toppingData['id']);
                        if ($topping) {
                            $topping->name = $toppingData['name'];
                            $topping->price = $toppingData['price'];
                            $topping->active = isset($toppingData['available']) ? 1 : 0;
                            $topping->save();
                            $newToppingIds[] = $topping->id;
                        }
                    } else {
                        // Create new topping
                        $topping = new Topping();
                        $topping->name = $toppingData['name'];
                        $topping->price = $toppingData['price'];
                        $topping->active = isset($toppingData['available']) ? 1 : 0;
                        $topping->save();
                        
                        // Attach to product
                        $product->toppings()->attach($topping->id);
                        $newToppingIds[] = $topping->id;
                    }
                    
                    // Handle topping image
                    if ($request->hasFile('topping_images') && isset($request->file('topping_images')[$index])) {
                        $toppingImage = $request->file('topping_images')[$index];
                        
                        // Generate unique filename
                        $filename = Str::uuid() . '.' . $toppingImage->getClientOriginalExtension();
                        
                        // Upload to S3
                        $path = Storage::disk('s3')->put('toppings/' . $filename, file_get_contents($toppingImage));
                        
                        // Delete old image if exists
                        if ($topping->image) {
                            Storage::disk('s3')->delete($topping->image);
                        }
                        
                        $topping->image = 'toppings/' . $filename;
                        $topping->save();
                        
                        \Log::info('Updated topping image:', [
                            'topping_id' => $topping->id,
                            'topping_name' => $topping->name,
                            'image_path' => 'toppings/' . $filename
                        ]);
                    }
                }
                
                // Detach toppings that were removed
                $toppingsToDetach = array_diff($existingToppingIds, $newToppingIds);
                if (!empty($toppingsToDetach)) {
                    $product->toppings()->detach($toppingsToDetach);
                }
            }
            
            // Handle branch stocks if the flag is set
            if ($request->has('update_branch_stocks')) {
                foreach ($request->input('branch_stock', []) as $branchId => $variants) {
                    foreach ($variants as $variantId => $quantity) {
                        // Make sure variant exists and belongs to this product
                        $variant = ProductVariant::where('id', $variantId)
                            ->where('product_id', $product->id)
                            ->first();
                            
                        // If variant is valid or we're using the default variant (0)
                        if ($variant || $variantId == 0) {
                            // Find or create the branch stock record
                            BranchStock::updateOrCreate(
                                [
                                    'branch_id' => $branchId,
                                    'product_variant_id' => $variantId,
                                ],
                                [
                                    'stock_quantity' => $quantity,
                                ]
                            );
                        }
                    }
                }
            }
            
            // Handle topping stocks if the flag is set
            if ($request->has('update_topping_stocks')) {
                foreach ($request->input('topping_stock', []) as $branchId => $toppings) {
                    foreach ($toppings as $toppingId => $quantity) {
                        // Make sure topping exists
                        $topping = Topping::find($toppingId);
                        
                        if ($topping) {
                            // Find or create the topping stock record
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
                }
            }
            
            DB::commit();
            
            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được cập nhật thành công!',
                    'redirect' => route('admin.products.index')
                ]);
            }
            
            return redirect()->route('admin.products.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Sản phẩm đã được cập nhật thành công!'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product update error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage()
            ]);
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
                'message' => 'Không thể xóa sản phẩmmm. ' . $e->getMessage()
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

    /**
     * Update stock quantities for product variants across branches
     */
    public function updateStocks(Request $request, Product $product)
    {
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
            
            // Set session flash message for the toast notification
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Cập nhật số lượng tồn kho thành công'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số lượng tồn kho thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Set session flash message for the toast notification
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
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
    public function updateToppingStocks(Request $request)
    {
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
     * Update topping stocks via AJAX
     */
    public function updateToppingStocksAjax(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $toppingStocks = $request->input('topping_stock', []);
            \Log::info('Received topping stocks data', ['data' => $toppingStocks]);
            
            // Count successful updates
            $updateCount = 0;
            
            // Update stock for each branch and topping
            foreach ($toppingStocks as $branchId => $toppings) {
                foreach ($toppings as $toppingId => $quantity) {
                    // Validate the data
                    if (!is_numeric($branchId) || !is_numeric($toppingId) || !is_numeric($quantity)) {
                        \Log::warning('Invalid data in topping stocks update', [
                            'branch_id' => $branchId,
                            'topping_id' => $toppingId,
                            'quantity' => $quantity
                        ]);
                        continue;
                    }
                    
                    // Get or create the topping stock entry
                    $stock = ToppingStock::updateOrCreate(
                        [
                            'branch_id' => $branchId,
                            'topping_id' => $toppingId,
                        ],
                        [
                            'stock_quantity' => $quantity,
                        ]
                    );
                    
                    \Log::info('Updated topping stock', [
                        'branch_id' => $branchId,
                        'topping_id' => $toppingId,
                        'quantity' => $quantity,
                        'record_id' => $stock->id
                    ]);
                    
                    $updateCount++;
                }
            }
            
            DB::commit();
            
            // Set session flash message for the toast notification
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => "Cập nhật $updateCount số lượng topping thành công"
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Cập nhật $updateCount số lượng topping thành công",
                'update_count' => $updateCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating topping stocks', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Set session flash message for the toast notification
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}

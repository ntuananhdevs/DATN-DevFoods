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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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

            // // Debug: Log request data
            // \Log::info('Attributes data:', $request->input('attributes', []));
            // \Log::info('Toppings data:', $request->input('toppings', []));

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

                    // Attach topping to product
                    $product->toppings()->attach($topping->id);
                    \Log::info('Attached topping to product:', ['product_id' => $product->id, 'topping_id' => $topping->id]);
                }
            }

            DB::commit();
            \Log::info('Transaction committed successfully');

            session()->flash('modal', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Sản phẩm và các biến thể đã được tạo thành công'
            ]);
            
            // Redirect to stock management page
            return redirect()->route('admin.products.stock', $product->id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            session()->flash('modal', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Vui lòng kiểm tra lại thông tin nhập vào'
            ]);
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('modal', [
                'type' => 'error',
                'title' => 'Lỗi',
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            return view('admin.products.show', compact('product'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $product = Product::with(['category', 'variants.branchStocks', 'images'])->findOrFail($id);
            $categories = Category::all();
            $branches = Branch::where('active', true)->get();
            return view('admin.products.edit', compact('product', 'categories', 'branches'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
                'ingredients' => 'nullable|string',
                'is_featured' => 'boolean',
                'available' => 'boolean',
                'status' => 'boolean',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'branch_stocks.*' => 'nullable|integer|min:0',
                'attributes.*.name' => 'required|string|max:255',
                'attributes.*.values.*.value' => 'required|string|max:255',
                'attributes.*.values.*.price_adjustment' => 'required|numeric',
            ]);

            DB::beginTransaction();

            // Find product
            $product = Product::findOrFail($id);

            // Update product
            $product->update([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'base_price' => $validated['base_price'],
                'preparation_time' => $validated['preparation_time'],
                'short_description' => $validated['short_description'] ?? '',
                'description' => $validated['description'],
                'ingredients' => $validated['ingredients'] ? json_decode($validated['ingredients']) : null,
                'is_featured' => $request->boolean('is_featured'),
                'available' => $request->boolean('available'),
                'status' => $request->boolean('status'),
                'updated_by' => auth()->id(),
            ]);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'img' => $path,
                        'is_primary' => false,
                    ]);
                }
            }

            // Handle branch stocks
            if ($request->has('branch_stocks')) {
                // Get or create default variant
                $defaultVariant = $product->variants()->first() ?? $product->variants()->create([
                    'active' => true
                ]);

                foreach ($request->branch_stocks as $branchId => $quantity) {
                    $defaultVariant->branchStocks()->updateOrCreate(
                        ['branch_id' => $branchId],
                        ['stock_quantity' => $quantity]
                    );
                }
            }

            // Handle attributes and variant values
            if ($request->has('attributes')) {
                // Delete existing attributes and values
                $product->variants()->delete();

                foreach ($request->attributes as $attributeData) {
                    // Create or get attribute
                    $attribute = VariantAttribute::firstOrCreate(['name' => $attributeData['name']]);

                    // Create variant values
                    foreach ($attributeData['values'] as $valueData) {
                        $attribute->values()->create([
                            'value' => $valueData['value'],
                            'price_adjustment' => $valueData['price_adjustment'],
                        ]);
                    }
                }
            }

            DB::commit();
    
            session()->flash('modal', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Sản phẩm đã được cập nhật thành công'
            ]);
            
            return redirect()->route('admin.products.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            session()->flash('modal', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Vui lòng kiểm tra lại thông tin nhập vào'
            ]);
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('modal', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('old_attributes', $request->attributes)
                ->with('old_branch_stocks', $request->branch_stocks);
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
        return redirect()->back()->with('error', 'Có lỗi xuất dữ liệu: ' . $e->getMessage());
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

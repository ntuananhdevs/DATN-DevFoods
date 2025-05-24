<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel; // Cần cài thêm package maatwebsite/excel
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use App\Models\VariantAttribute;

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
                'ingredients' => 'nullable|string',
                'is_featured' => 'boolean',
                'available' => 'boolean',
                'status' => 'boolean',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'branch_stocks.*' => 'nullable|integer|min:0',
                'attributes.*.name' => 'required|string|max:255',
                'attributes.*.values.*.value' => 'required|string|max:255',
                'attributes.*.values.*.price_adjustment' => 'required|numeric',
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

            // Create product with SKU
            $product = Product::create([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'sku' => $sku,
                'base_price' => $validated['base_price'],
                'preparation_time' => $validated['preparation_time'],
                'short_description' => $validated['short_description'],
                'description' => $validated['description'],
                'ingredients' => $validated['ingredients'] ? json_decode($validated['ingredients']) : null,
                'is_featured' => $request->boolean('is_featured'),
                'available' => $request->boolean('available'),
                'status' => $request->boolean('status'),
                'created_by' => auth()->id(),
            ]);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'img' => $path,
                        'is_primary' => $index === 0, // First image is primary
                    ]);
                }
            }

            // Handle branch stocks
            if ($request->has('branch_stocks')) {
                // Create a default variant for the product
                $defaultVariant = $product->variants()->create([
                    'active' => true
                ]);

                foreach ($request->branch_stocks as $branchId => $quantity) {
                    $defaultVariant->branchStocks()->create([
                        'branch_id' => $branchId,
                        'stock_quantity' => $quantity,
                    ]);
                }
            }

            // Handle attributes and variant values
            if ($request->has('attributes')) {
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
                'message' => 'Sản phẩm đã được tạo thành công'
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
                'short_description' => $validated['short_description'],
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

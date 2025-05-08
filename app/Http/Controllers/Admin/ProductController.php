<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Product;
use App\Models\Admin\Category;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel; // Cần cài thêm package maatwebsite/excel

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
            if ($request->has('stock_status')) {
                if ($request->stock_status == 'in_stock') {
                    $query->where('stock', '>', 0);
                } elseif ($request->stock_status == 'out_of_stock') {
                    $query->where('stock', '<=', 0);
                }
            }
            
            // Tìm kiếm theo tên hoặc mã sản phẩm
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('id', 'like', '%' . $request->search . '%');
                });
            }
            
            $products = $query->latest()->paginate(10);
            $categories = Category::all(); // Lấy danh sách danh mục cho form lọc
                
            return view('admin.products.index', compact('products', 'categories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $categories = Category::all();
            return view('admin.products.create', compact('categories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Xử lý lưu sản phẩm mới
            // Code xử lý lưu sản phẩm sẽ được thêm vào đây
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
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
            $product = Product::findOrFail($id);
            $categories = Category::all();
            return view('admin.products.edit', compact('product', 'categories'));
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
            // Xử lý cập nhật sản phẩm
            // Code xử lý cập nhật sản phẩm sẽ được thêm vào đây
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được cập nhật thành công');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được xóa thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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
            
            // Áp dụng các bộ lọc tương tự như trong index
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
                    // Nếu đã cài đặt barryvdh/laravel-dompdf
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

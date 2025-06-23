<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topping;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Product\ToppingRequest;

class ToppingController extends Controller
{
    // ==================== CRUD OPERATIONS ====================
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Topping::query();

            // Apply filters
            $this->applyFilters($query, $request);

            $toppings = $query->latest()->paginate(10);
            $priceRange = $this->getPriceRange();

            return view('admin.menu.topping.index', array_merge(
                compact('toppings'),
                $priceRange
            ));
        } catch (\Exception $e) {
            return $this->handleError($e, 'Không thể tải danh sách topping');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.menu.topping.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ToppingRequest $request)
    {
        try {
            DB::beginTransaction();

            $topping = $this->createTopping($request);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $this->handleImageUpload($topping, $request->file('image'));
            }

            DB::commit();

            return $this->successResponse(
                'Topping đã được tạo thành công',
                route('admin.toppings.stock', $topping->id),
                'Topping đã được tạo thành công! Vui lòng cập nhật tồn kho cho các chi nhánh.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleError($e, 'Không thể tạo topping', true);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Topping $topping)
    {
        $topping->load(['toppingStocks.branch', 'createdBy', 'updatedBy']);
        return view('admin.menu.topping.show', compact('topping'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topping $topping)
    {
        $topping->load('toppingStocks.branch');
        return view('admin.menu.topping.edit', compact('topping'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ToppingRequest $request, $id)
    {
        try {
            $topping = Topping::findOrFail($id);
            
            DB::beginTransaction();

            $this->updateTopping($topping, $request);

            // Handle image upload
            if ($request->hasFile('image')) {
                $this->handleImageUpload($topping, $request->file('image'), true);
            }

            DB::commit();

            return $this->successResponse(
                'Topping đã được cập nhật thành công',
                route('admin.toppings.index')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleError($e, 'Không thể cập nhật topping', true);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topping $topping)
    {
        try {
            DB::beginTransaction();

            // Check if topping is being used
            if ($this->isToppingInUse($topping)) {
                return $this->errorResponse(
                    'Không thể xóa topping này vì đang được sử dụng bởi một số sản phẩm'
                );
            }

            // Delete image if exists
            $this->deleteImage($topping);
            
            $topping->delete();
            DB::commit();

            return $this->successResponse(
                'Topping đã được xóa thành công',
                route('admin.toppings.index')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleError($e, 'Không thể xóa topping');
        }
    }
    
    // ==================== STATUS MANAGEMENT ====================

    /**
     * Toggle topping status
     */
    public function toggleStatus(Topping $topping)
    {
        try {
            $topping->update(['active' => !$topping->active]);
            
            $status = $topping->active ? 'kích hoạt' : 'vô hiệu hóa';
            
            return $this->successResponse(
                "Topping đã được {$status} thành công"
            );
        } catch (\Exception $e) {
            return $this->handleError($e, 'Không thể thay đổi trạng thái topping');
        }
    }

    /**
     * Bulk update topping status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:toppings,id',
            'status' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            $updatedCount = Topping::whereIn('id', $request->ids)
                                  ->update(['active' => $request->status]);

            DB::commit();

            $statusText = $request->status ? 'kích hoạt' : 'vô hiệu hóa';
            
            return $this->successResponse(
                "Đã {$statusText} {$updatedCount} topping thành công"
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleError($e, 'Không thể cập nhật trạng thái hàng loạt');
        }
    }
    
    // ==================== API METHODS ====================

    /**
     * Get active toppings for AJAX requests
     */
    public function getToppings(Request $request)
    {
        try {
            $query = Topping::where('active', true);

            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $toppings = $query->select('id', 'name', 'price', 'image')
                             ->orderBy('name')
                             ->get()
                             ->map(function ($topping) {
                                 return [
                                     'id' => $topping->id,
                                     'name' => $topping->name,
                                     'price' => $topping->price,
                                     'formatted_price' => number_format($topping->price, 0, ',', '.') . ' VNĐ',
                                     'image_url' => $topping->image ? asset('storage/' . $topping->image) : null
                                 ];
                             });

            return response()->json([
                'success' => true,
                'data' => $toppings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== HELPER METHODS ====================
    
    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('active', $request->status);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
    }
    
    /**
     * Get price range for filters
     */
    private function getPriceRange()
    {
        return [
            'minPrice' => Topping::min('price') ?? 0,
            'maxPrice' => Topping::max('price') ?? 100000
        ];
    }
    
    /**
     * Create new topping
     */
    private function createTopping(ToppingRequest $request)
    {
        $data = $request->validated();
        
        // Convert status to active boolean
        $data['active'] = $data['status'] === 'active';
        unset($data['status']);
        
        // Add SKU
        $data['sku'] = $this->generateToppingSku();
        
        // Add created_by and updated_by
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        
        return Topping::create($data);
    }
    
    /**
     * Update existing topping
     */
    private function updateTopping(Topping $topping, ToppingRequest $request)
    {
        $data = $request->validated();
        
        // Convert status to active boolean
        $data['active'] = $data['status'] === 'active';
        unset($data['status']);
        
        // Add updated_by
        $data['updated_by'] = auth()->id();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($topping->image) {
                $this->deleteImage($topping->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        }
        
        $topping->update($data);
        return $topping;
    }
    
    /**
     * Check if topping is being used by products
     */
    private function isToppingInUse(Topping $topping)
    {
        return $topping->products()->count() > 0;
    }
    
    /**
     * Delete topping image
     */
    private function deleteImage(Topping $topping)
    {
        if ($topping->image) {
            Storage::disk('s3')->delete($topping->image);
        }
    }

    /**
     * Upload image and return the path
     */
    private function uploadImage($image)
    {
        Log::info('Uploading topping image', [
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
            'mime' => $image->getMimeType()
        ]);

        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = 'toppings/' . $filename;
        
        $putResult = Storage::disk('s3')->put($path, file_get_contents($image));
        
        if ($putResult) {
            return $path;
        }
        
        throw new \Exception('Failed to upload image');
    }

    /**
     * Handle image upload for topping
     */
    private function handleImageUpload($topping, $image, $isUpdate = false)
    {
        Log::info('Uploading topping image', [
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
            'mime' => $image->getMimeType()
        ]);

        // Delete old image if exists (only for updates)
        if ($isUpdate && $topping->image) {
            $this->deleteImage($topping);
        }

        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = Storage::disk('s3')->put('toppings/' . $filename, file_get_contents($image));

        if ($path) {
            $topping->update(['image' => 'toppings/' . $filename]);
        }
    }

    /**
     * Generate unique SKU for topping
     */
    private function generateToppingSku()
    {
        do {
            $timestamp = now()->format('ymd');
            $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $sku = 'TP' . $timestamp . $random;
        } while (Topping::where('sku', $sku)->exists());

        return $sku;
    }
    
    // ==================== RESPONSE HELPERS ====================
    
    /**
     * Handle success response
     */
    private function successResponse($message, $redirectTo = null, $flashMessage = null)
    {
        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => $message
        ]);
        
        $redirect = redirect($redirectTo ?: back());
        
        if ($flashMessage) {
            $redirect->with('success', $flashMessage);
        }
        
        return $redirect;
    }
    
    /**
     * Handle error response
     */
    private function errorResponse($message)
    {
        session()->flash('toast', [
            'type' => 'error',
            'title' => 'Lỗi!',
            'message' => $message
        ]);
        
        return redirect()->back();
    }
    
    /**
     * Handle exception and return error response
     */
    private function handleError(\Exception $e, $message, $withInput = false)
    {
        Log::error('ToppingController Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        session()->flash('toast', [
            'type' => 'error',
            'title' => 'Lỗi!',
            'message' => $message . ': ' . $e->getMessage()
        ]);
        
        $redirect = redirect()->back();
        
        if ($withInput) {
            $redirect->withInput();
        }
        
        return $redirect;
    }
}
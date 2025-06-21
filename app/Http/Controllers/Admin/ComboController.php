<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ComboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Combo::with(['products']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('active', $request->status);
            }

            // Filter by price range
            if ($request->has('price_min') && $request->price_min) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->has('price_max') && $request->price_max) {
                $query->where('price', '<=', $request->price_max);
            }

            $combos = $query->latest()->paginate(10);
            $minPrice = Combo::min('price') ?? 0;
            $maxPrice = Combo::max('price') ?? 500000;

            return view('admin.menu.combo.index', compact('combos', 'minPrice', 'maxPrice'));
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
        $products = Product::where('active', true)
                          ->where('type', 'product')
                          ->select('id', 'name', 'price')
                          ->orderBy('name')
                          ->get();

        return view('admin.menu.combo.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:combos,name',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean',
            'products' => 'required|array|min:2',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $combo = Combo::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'active' => $request->has('active')
            ]);

            // Attach products to combo with quantities
            $productData = [];
            foreach ($request->products as $index => $productVariantId) {
                $productData[$productVariantId] = [
                    'quantity' => $request->quantities[$index] ?? 1
                ];
            }
            $combo->productVariants()->attach($productData);

            // Handle image upload
            if ($request->hasFile('image')) {
                $this->handleImageUpload($combo, $request->file('image'));
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Combo đã được tạo thành công'
            ]);

            return redirect()->route('admin.combos.index');
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
    public function show(Combo $combo)
    {
        $combo->load(['products']);
        return view('admin.menu.combo.show', compact('combo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Combo $combo)
    {
        $combo->load(['products']);
        $products = Product::where('active', true)
                          ->where('type', 'product')
                          ->select('id', 'name', 'price')
                          ->orderBy('name')
                          ->get();

        return view('admin.menu.combo.edit', compact('combo', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Combo $combo)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:combos,name,' . $combo->id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean',
            'products' => 'required|array|min:2',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $combo->update([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'active' => $request->has('active')
            ]);

            // Sync products with quantities
            $productData = [];
            foreach ($request->products as $index => $productVariantId) {
                $productData[$productVariantId] = [
                    'quantity' => $request->quantities[$index] ?? 1
                ];
            }
            $combo->productVariants()->sync($productData);

            // Handle image upload
            if ($request->hasFile('image')) {
                $this->handleImageUpload($combo, $request->file('image'), true);
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Combo đã được cập nhật thành công'
            ]);

            return redirect()->route('admin.combos.index');
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
    public function destroy(Combo $combo)
    {
        try {
            DB::beginTransaction();

            // Detach all products
            $combo->productVariants()->detach();

            // Delete image if exists
            if ($combo->image) {
                Storage::disk('s3')->delete($combo->image);
            }

            $combo->delete();

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Combo đã được xóa thành công'
            ]);

            return redirect()->route('admin.combos.index');
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
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        try {
            $combo = Combo::withTrashed()->findOrFail($id);
            $combo->restore();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Combo đã được khôi phục thành công'
            ]);

            return redirect()->route('admin.combos.index');
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
            DB::beginTransaction();

            $combo = Combo::withTrashed()->findOrFail($id);

            // Detach all products
            $combo->productVariants()->detach();

            // Delete image if exists
            if ($combo->image) {
                Storage::disk('s3')->delete($combo->image);
            }

            $combo->forceDelete();

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Combo đã được xóa vĩnh viễn'
            ]);

            return redirect()->route('admin.combos.index');
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
     * Toggle combo status
     */
    public function toggleStatus(Combo $combo)
    {
        try {
            $combo->update([
                'active' => !$combo->active
            ]);

            $status = $combo->active ? 'kích hoạt' : 'vô hiệu hóa';

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => "Combo đã được {$status} thành công"
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
     * Handle image upload for combo
     */
    private function handleImageUpload($combo, $image, $isUpdate = false)
    {
        Log::info('Uploading combo image', [
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
            'mime' => $image->getMimeType()
        ]);

        // Delete old image if exists (only for updates)
        if ($isUpdate && $combo->image) {
            Storage::disk('s3')->delete($combo->image);
        }

        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = Storage::disk('s3')->put('combos/' . $filename, file_get_contents($image));

        if ($path) {
            $combo->update([
                'image' => 'combos/' . $filename
            ]);
        }
    }

    /**
     * Get combos for AJAX requests
     */
    public function getCombos(Request $request)
    {
        try {
            $query = Combo::where('active', true);

            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $combos = $query->with(['products'])
                           ->select('id', 'name', 'price', 'image')
                           ->orderBy('name')
                           ->get();

            return response()->json([
                'success' => true,
                'data' => $combos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
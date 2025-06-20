<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topping;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ToppingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Topping::query();

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

            $toppings = $query->latest()->paginate(10);
            $minPrice = Topping::min('price') ?? 0;
            $maxPrice = Topping::max('price') ?? 100000;

            return view('admin.menu.topping.index', compact('toppings', 'minPrice', 'maxPrice'));
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
        return view('admin.menu.topping.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:toppings,name',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $topping = Topping::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'active' => $request->has('active')
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $this->handleImageUpload($topping, $request->file('image'));
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Topping đã được tạo thành công'
            ]);

            return redirect()->route('admin.toppings.index');
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
    public function show(Topping $topping)
    {
        return view('admin.menu.topping.show', compact('topping'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topping $topping)
    {
        return view('admin.menu.topping.edit', compact('topping'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topping $topping)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:toppings,name,' . $topping->id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $topping->update([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'active' => $request->has('active')
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $this->handleImageUpload($topping, $request->file('image'), true);
            }

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Topping đã được cập nhật thành công'
            ]);

            return redirect()->route('admin.toppings.index');
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
    public function destroy(Topping $topping)
    {
        try {
            DB::beginTransaction();

            // Check if topping is being used by any products
            if ($topping->products()->count() > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi!',
                    'message' => 'Không thể xóa topping này vì đang được sử dụng bởi một số sản phẩm'
                ]);

                return redirect()->back();
            }

            // Delete image if exists
            if ($topping->image) {
                Storage::disk('s3')->delete($topping->image);
            }

            $topping->delete();

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Topping đã được xóa thành công'
            ]);

            return redirect()->route('admin.toppings.index');
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
     * Toggle topping status
     */
    public function toggleStatus(Topping $topping)
    {
        try {
            $topping->update([
                'active' => !$topping->active
            ]);

            $status = $topping->active ? 'kích hoạt' : 'vô hiệu hóa';

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => "Topping đã được {$status} thành công"
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
     * Bulk update topping status
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:toppings,id',
                'status' => 'required|boolean'
            ]);

            DB::beginTransaction();

            $updatedCount = Topping::whereIn('id', $request->ids)
                                  ->update(['active' => $request->status]);

            DB::commit();

            $statusText = $request->status ? 'kích hoạt' : 'vô hiệu hóa';

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => "Đã {$statusText} {$updatedCount} topping thành công"
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
            Storage::disk('s3')->delete($topping->image);
        }

        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = Storage::disk('s3')->put('toppings/' . $filename, file_get_contents($image));

        if ($path) {
            $topping->update([
                'image' => 'toppings/' . $filename
            ]);
        }
    }

    /**
     * Get toppings for AJAX requests
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
                             ->get();

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
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        try {
            Banner::where('end_at', '<', now())->update(['is_active' => false]);
            $search = $request->input('search');
            $query = Banner::orderBy('created_at', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            $banners = $query->paginate(10);
            $banners->appends(['search' => $search]);

            return view('admin.banner.index', compact('banners', 'search'));
        } catch (\Exception $e) {
            Log::error('Error in BannerController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách banner.');
        }
    }


    public function create()
    {
        return view('admin.banner.create');
    }


    public function store(Request $request)
    {
        try {
            $availableOrder = null;
            $orders = [0, 1, 2];
            
            foreach ($orders as $order) {
                $banner = Banner::where('order', $order)->first();
                if (!$banner || !$banner->is_active) {
                    $availableOrder = $order;
                    break;
                }
            }
            
            if ($availableOrder === null) {
                throw new \Exception('Tất cả vị trí đều đã có banner hiển thị');
            }
            
            $validated = $request->validate([
                'image_path' => 'required|image|max:5120',
                'link' => 'nullable|url',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
                'is_active' => 'required|boolean',
                'order' => 'required|integer|min:0|max:2|unique:banners,order,' . ($availableOrder === null ? '' : $availableOrder),
            ], [
                'order.unique' => 'Vị trí này đã được sử dụng bởi banner khác',
                'required' => ':attribute không được để trống.',
                'string' => ':attribute phải là chuỗi.',
                'url' => ':attribute phải là URL hợp lệ.',
                'max' => ':attribute không được vượt quá :max KB.',
                'date' => ':attribute phải là ngày hợp lệ.',
                'after' => ':attribute phải sau ngày bắt đầu.',
                'image' => ':attribute phải là hình ảnh.',
                'regex' => ':attribute phải là đường dẫn đến trang sản phẩm (ví dụ: https://example.com/products/123)',
                'boolean' => ':attribute không hợp lệ.'
            ], [
                'image_path' => 'Hình ảnh',
                'link' => 'Liên kết',
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'start_at' => 'Ngày bắt đầu',
                'end_at' => 'Ngày kết thúc',
                'is_active' => 'Trạng thái',
                'order' => 'Thứ tự hiển thị'
            ]);

            if ($request->hasFile('image_path')) {
                $path = $request->file('image_path')->store('banners', 'public');
                $validated['image_path'] = $path;
            }
            Banner::create($validated);
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Banner đã được tạo thành công'
            ]);
            return redirect()->route('admin.banners.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error in BannerController@store: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            return back()->withInput();
        }
    }
    
    public function show(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);
            return view('admin.banner.show', compact('banner'));
        } catch (\Exception $e) {
            Log::error('Error in BannerController@show: ' . $e->getMessage());
            return redirect()->route('admin.banners.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không tìm thấy banner'
                ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);
            return view('admin.banner.edit', compact('banner'));
        } catch (\Exception $e) {
            Log::error('Error in BannerController@edit: ' . $e->getMessage());
            return redirect()->route('admin.banners.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không tìm thấy banner cần chỉnh sửa'
                ]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            $validated = $request->validate([
                'image_path' => 'nullable|image|max:5120',
                'link' => 'nullable|url',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
                'is_active' => 'required|boolean',
                'order' => 'required|integer|min:0|max:2|unique:banners,order,' . $id,
            ], [
                'order.unique' => 'Vị trí này đã được sử dụng bởi banner khác',
                'required' => ':attribute không được để trống.',
                'string' => ':attribute phải là chuỗi.',
                'url' => ':attribute phải là URL hợp lệ.',
                'max' => ':attribute không được vượt quá :max KB.',
                'date' => ':attribute phải là ngày hợp lệ.',
                'after' => ':attribute phải sau ngày bắt đầu.',
                'image' => ':attribute phải là hình ảnh.',
                'regex' => ':attribute phải là đường dẫn đến trang sản phẩm (ví dụ: https://example.com/products/123)',
                'boolean' => ':attribute không hợp lệ.'
            ], [
                'image_path' => 'Hình ảnh',
                'link' => 'Liên kết',
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'start_at' => 'Ngày bắt đầu',
                'end_at' => 'Ngày kết thúc',
                'is_active' => 'Trạng thái',
                'order' => 'Thứ tự hiển thị'
            ]);

            if ($request->hasFile('image_path')) {
                $path = $request->file('image_path')->store('banners', 'public');
                $validated['image_path'] = $path;
            }
            $banner->update($validated);
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Banner đã được cập nhật thành công'
            ]);
            return redirect()->route('admin.banners.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error in BannerController@update: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra khi cập nhật banner: ' . $e->getMessage()
            ]);
            return back()->withInput();
        }
    }
    
    public function destroy(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $banner->delete();
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Banner đã được tạo thành công'
            ]);
            return redirect()->route('admin.banners.index');
        } catch (\Exception $e) {
            Log::error('Error in BannerController@destroy: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra khi xóa banner: ' . $e->getMessage()
            ]);
            return redirect()->route('admin.banners.index');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->is_active = !$banner->is_active;
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => $banner->is_active ? 'Đã kích hoạt banner thành công' : 'Đã vô hiệu hóa banner thành công',
                'is_active' => $banner->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStatusUpdate(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required',
                'status' => 'required|boolean'
            ]);

            $idsInput = $request->input('ids');

            // Xử lý dữ liệu ids để đảm bảo là mảng
            if (is_string($idsInput)) {
                $ids = json_decode($idsInput, true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($ids)) {
                    return back()->with('toast', [
                        'type' => 'error',
                        'title' => 'Lỗi',
                        'message' => 'Dữ liệu ids không hợp lệ, phải là một mảng'
                    ]);
                }
            } else {
                $ids = $idsInput;
            }
            if (!is_array($ids)) {
                return back()->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Dữ liệu ids không hợp lệ, phải là một mảng'
                ]);
            }

            $status = $request->input('status');

            $updated = Banner::whereIn('id', $ids)->update(['is_active' => $status]);

            if ($updated === 0) {
                return back()->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không có banner nào được cập nhật'
                ]);
            }

            $statusText = $status ? 'kích hoạt' : 'vô hiệu hóa';
            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã ' . $statusText . ' ' . $updated . ' banner thành công'
            ]);
        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Product;
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

            $banners = $query->paginate(5);
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
            $validated = $request->validate([
                'image_path' => 'nullable|image|max:5120',
                'image_link' => 'nullable|url',
                'link' => ['nullable', 'string', 'regex:/^\/shop\/products\/show\/\d+$/'],
                'position' => 'required|string',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
                'is_active' => 'required|boolean',
                'order' => 'required|integer|min:0|unique:banners,order',
            ], [
                'order.unique' => 'Vị trí này đã được sử dụng bởi banner khác',
                'required' => ':attribute không được để trống.',
                'string' => ':attribute phải là chuỗi.',
                'url' => ':attribute phải là URL hợp lệ.',
                'link.regex' => 'Link sản phẩm không hợp lệ.',
                'max' => ':attribute không được vượt quá :max KB.',
                'date' => ':attribute phải là ngày hợp lệ.',
                'after' => ':attribute phải sau ngày bắt đầu.',
                'image' => ':attribute phải là hình ảnh.',
                'boolean' => ':attribute không hợp lệ.'
            ], [
                'image_path' => 'Hình ảnh tải lên',
                'image_link' => 'Đường dẫn ảnh',
                'link' => 'Link sản phẩm',
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'start_at' => 'Ngày bắt đầu',
                'end_at' => 'Ngày kết thúc',
                'is_active' => 'Trạng thái',
                'order' => 'Thứ tự hiển thị'
            ]);

            // CHỈ CHO PHÉP 1 TRONG 2
            $hasFile = $request->hasFile('image_path');
            $hasLink = $request->filled('image_link');

            if ($hasFile && $hasLink) {
                return back()->withErrors([
                    'image_path' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                    'image_link' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                ])->withInput();
            }

            if (!$hasFile && !$hasLink) {
                return back()->withErrors([
                    'image_path' => 'Bạn phải chọn ảnh tải lên hoặc nhập đường dẫn ảnh.',
                ])->withInput();
            }
            if ($hasFile) {
                $path = $request->file('image_path')->store('banners', 'public');
                $validated['image_path'] = $path;
                unset($validated['image_link']);
            } else {
                $validated['image_path'] = $request->input('image_link');
                unset($validated['image_link']);
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
                'image_link' => 'nullable|url',
                'link' => ['nullable', 'string', 'regex:/^\/shop\/products\/show\/\d+$/'],
                'position' => 'required|string',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
                'is_active' => 'required|boolean',
                'order' => 'required|integer|min:0|unique:banners,order,' . $id,
            ], [
                'order.unique' => 'Vị trí này đã được sử dụng bởi banner khác',
                'required' => ':attribute không được để trống.',
                'string' => ':attribute phải là chuỗi.',
                'url' => ':attribute phải là URL hợp lệ.',
                'link.regex' => 'Link sản phẩm không hợp lệ. Định dạng đúng là /products/ID (ví dụ: /products/123).',
                'max' => ':attribute không được vượt quá :max KB.',
                'date' => ':attribute phải là ngày hợp lệ.',
                'after' => ':attribute phải sau ngày bắt đầu.',
                'image' => ':attribute phải là hình ảnh.',
                'boolean' => ':attribute không hợp lệ.'
            ], [
                'image_path' => 'Hình ảnh tải lên',
                'image_link' => 'Đường dẫn ảnh',
                'link' => 'Link sản phẩm',
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'start_at' => 'Ngày bắt đầu',
                'end_at' => 'Ngày kết thúc',
                'is_active' => 'Trạng thái',
                'order' => 'Thứ tự hiển thị'
            ]);

            // CHỈ CHO PHÉP 1 TRONG 2
            $hasFile = $request->hasFile('image_path');
            $hasLink = $request->filled('image_link');

            if ($hasFile && $hasLink) {
                return back()->withErrors([
                    'image_path' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                    'image_link' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                ])->withInput();
            }

            if ($hasFile) {
                // Nếu banner đã có ảnh cũ và không phải URL, xóa ảnh cũ
                if ($banner->image_path && !filter_var($banner->image_path, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($banner->image_path);
                }

                $path = $request->file('image_path')->store('banners', 'public');
                $validated['image_path'] = $path;
                unset($validated['image_link']);
            } else if ($hasLink) {
                // Nếu banner đã có ảnh cũ và không phải URL, xóa ảnh cũ
                if ($banner->image_path && !filter_var($banner->image_path, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($banner->image_path);
                }

                $validated['image_path'] = $request->input('image_link');
                unset($validated['image_link']);
            } else {
                // Nếu không có file mới và không có link mới, giữ nguyên giá trị cũ
                unset($validated['image_path']);
                unset($validated['image_link']);
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
                'message' => 'Banner đã được xóa thành công'
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
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        $id = $request->get('id');
        $products = Product::query();
        if ($id) {
            $products->where('id', $id);
        } elseif ($query) {
            $products->where('name', 'like', '%' . $query . '%');
        } else {
            return response()->json([]);
        }
        return response()->json(
            $products->select('id', 'name')
                ->limit(10)
                ->get()
        );
    }
}

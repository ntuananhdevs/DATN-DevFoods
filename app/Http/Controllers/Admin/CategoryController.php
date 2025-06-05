<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin']);
    }

    public function index(Request $request)
    {
        try {
            // Lấy từ input tìm kiếm
            $search = $request->input('search');

            // Tạo truy vấn tìm kiếm
            $categories = Category::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%');
                });
            })->paginate(10);

            return view('admin.categories.index', compact('categories'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Không thể lấy danh sách danh mục. ' . $e->getMessage()
            ]);
            return back();
        }
    }

    public function create()
    {
        try {
            return view('admin.categories.create');
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Không thể truy cập trang tạo danh mục. ' . $e->getMessage()
            ]);
            return back();
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:191',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'status' => 'required|boolean',
            ]);

            $data = $request->only(['name', 'description', 'status']);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = \Str::uuid() . '.' . $image->getClientOriginalExtension();
                $path = Storage::disk('s3')->put("categories/{$filename}", file_get_contents($image));

                if ($path) {
                    $data['image'] = "categories/{$filename}";
                }
            } else {
                // Gán ảnh mặc định nếu không có ảnh upload
                $data['image'] = 'categories/default-logo.avif';
            }


            Category::create($data);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Danh mục đã được tạo thành công.'
            ]);

            return redirect()->route('admin.categories.index');
        } catch (\Exception $e) {
            session()->flash('modal', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Lỗi khi tạo danh mục: ' . $e->getMessage()
            ]);
            return back()->withInput();
        }
    }

    public function edit(Category $category)
    {
        try {
            return view('admin.categories.edit', compact('category'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Không thể truy cập trang chỉnh sửa. ' . $e->getMessage()
            ]);
            return back();
        }
    }

    public function update(Request $request, Category $category)
{
    try {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        $data = $request->only(['name', 'description', 'status']);

        if ($request->hasFile('image')) {
    // Xóa ảnh cũ nếu có
    if ($category->image && Storage::disk('s3')->exists($category->image)) {
        Storage::disk('s3')->delete($category->image);
    }

    // Upload ảnh mới
    $image = $request->file('image');
    $filename = \Str::uuid() . '.' . $image->getClientOriginalExtension();
    $path = Storage::disk('s3')->put("categories/{$filename}", file_get_contents($image));

    if ($path) {
        $data['image'] = "categories/{$filename}";
    }
}

        $category->update($data);

        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => 'Cập nhật danh mục thành công.'
        ]);

        // Quay lại trang sửa thay vì index
        return redirect()->route('admin.categories.edit', $category->id);

    } catch (\Exception $e) {
        session()->flash('modal', [
            'type' => 'error',
            'title' => 'Lỗi!',
            'message' => 'Lỗi khi cập nhật danh mục: ' . $e->getMessage()
        ]);
        return back()->withInput();
    }
}


    public function show(Category $category)
    {
        try {
            return view('admin.categories.show', compact('category'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi!',
                'message' => 'Không thể truy cập trang chi tiết. ' . $e->getMessage()
            ]);
            return back();
        }
    }

    public function destroy($id)
{
    try {
        $category = Category::find($id);

        if (! $category) {
            // Flash lỗi thông báo (hiển thị bằng modal hoặc alert)
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Không tìm thấy!',
                'message' => 'Danh mục không tồn tại hoặc đã bị xóa.'
            ]);

            return redirect()->route('admin.categories.index');
        }

        // Nếu tồn tại thì tiếp tục xóa
        if (
            $category->image &&
            $category->image !== 'categories/default-logo.avif' &&
            Storage::disk('s3')->exists($category->image)
        ) {
            Storage::disk('s3')->delete($category->image);
        }

        $category->delete();

        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Xóa thành công!',
            'message' => 'Danh mục đã được xóa thành công.'
        ]);

        return redirect()->route('admin.categories.index');
    } catch (\Exception $e) {
        session()->flash('toast', [
            'type' => 'error',
            'title' => 'Lỗi!',
            'message' => 'Không thể xóa danh mục. ' . $e->getMessage()
        ]);

        return back();
    }
}

    public function toggleStatus(Category $category)
    {
        try {
            $category->status = !$category->status;
            $category->save();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Trạng thái danh mục đã được thay đổi thành công.'
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi khi thay đổi trạng thái danh mục: ' . $e->getMessage());
            
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->back();
        }
    }

    public function bulkStatusUpdate(Request $request)
    {
        try {
            $categoryIds = explode(',', $request->category_ids);
            $status = (bool)$request->status;

            Category::whereIn('id', $categoryIds)->update(['status' => $status]);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã cập nhật trạng thái danh mục thành công'
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật trạng thái danh mục hàng loạt: ' . $e->getMessage());

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back();
        }
    }


}

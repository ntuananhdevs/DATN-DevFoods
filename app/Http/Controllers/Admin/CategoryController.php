<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        try {
            // Lấy từ input tìm kiếm
            $keyword = $request->input('keyword');

            // Tạo truy vấn tìm kiếm
            $categories = Category::when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('id', 'like', '%' . $keyword . '%');
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
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            Category::create($data);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Danh mục đã được tạo thành công.'
            ]);

            return redirect()->route('admin.categories.index');
        } catch (\Exception $e) {
            session()->flash('toast', [
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
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $category->update($data);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Cập nhật danh mục thành công.'
            ]);

            return redirect()->route('admin.categories.index');
        } catch (\Exception $e) {
            session()->flash('toast', [
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

    public function destroy(Category $category)
    {
        try {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
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
}

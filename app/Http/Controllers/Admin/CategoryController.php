<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        try {
            $categories = Category::latest()->paginate(5);
            return view('admin.categories.index', compact('categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể lấy danh sách categories. ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.categories.create');
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

            return redirect()->route('admin.categories.index')->with('success', 'Tạo danh mục thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi khi tạo danh mục: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        try {
            return view('admin.categories.edit', compact('category'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể truy cập trang chỉnh sửa. ' . $e->getMessage());
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

            return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi khi cập nhật danh mục: ' . $e->getMessage());
        }
    }

    public function show(Category $category)
    {
        try {
            return view('admin.categories.show', compact('category'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể truy cập trang chi tiết. ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa danh mục. ' . $e->getMessage());
        }
    }
}

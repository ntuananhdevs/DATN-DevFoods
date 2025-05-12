<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-roles');
    }

    public function index(Request $request)
    {
        try {
            $keyword = $request->input('keyword');

            $roles = Role::when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('id', 'like', '%' . $keyword . '%');
                });
            })->paginate(10);

            return view('admin.roles.index', compact('roles'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Không thể tải danh sách roles.'
            ]);
            return redirect()->route('admin.roles.index');
        }
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'permissions' => 'required|array',
                'permissions.*' => 'string|in:create,edit,view,delete',
            ], [
                'name.required' => 'Tên là bắt buộc.',
                'name.string' => 'Tên phải là một chuỗi ký tự.',
                'name.max' => 'Tên không được vượt quá 255 ký tự.',
                'permissions.required' => 'Phân quyền là bắt buộc.',
                'permissions.array' => 'Phân quyền phải là một mảng.',
                'permissions.*.string' => 'Phân quyền phải là một chuỗi ký tự.',
                'permissions.*.in' => 'Phân quyền phải có giá trị trong các giá trị: create, edit, view, delete.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Role::create([
                'name' => $request->name,
                'permissions' => $request->permissions,
            ]);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã tạo vai trò mới thành công.'
            ]);
            return redirect()->route('admin.roles.index');
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi khi tạo',
                'message' => 'Đã xảy ra lỗi khi tạo vai trò.'
            ]);
            return redirect()->route('admin.roles.index');
        }
    }

    public function edit($id)
    {
        try {
            $role = Role::findOrFail($id);
            return view('admin.roles.edit', compact('role'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Không tìm thấy',
                'message' => 'Vai trò không tồn tại.'
            ]);
            return redirect()->route('admin.roles.index');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'permissions' => 'required|array',
                'permissions.*' => 'string|in:create,edit,view,delete',
            ], [
                'name.required' => 'Tên là bắt buộc.',
                'name.string' => 'Tên phải là một chuỗi ký tự.',
                'name.max' => 'Tên không được vượt quá 255 ký tự.',
                'permissions.required' => 'Phân quyền là bắt buộc.',
                'permissions.array' => 'Phân quyền phải là một mảng.',
                'permissions.*.string' => 'Phân quyền phải là một chuỗi ký tự.',
                'permissions.*.in' => 'Phân quyền phải có giá trị trong các giá trị: create, edit, view, delete.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $role->update([
                'name' => $request->name,
                'permissions' => $request->permissions,
            ]);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Cập nhật thành công',
                'message' => 'Vai trò đã được cập nhật.'
            ]);
            return redirect()->route('admin.roles.index');
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi khi cập nhật',
                'message' => 'Không thể cập nhật vai trò.'
            ]);
            return redirect()->route('admin.roles.index');
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Xóa thành công',
                'message' => 'Vai trò đã được xóa.'
            ]);
            return redirect()->route('admin.roles.index');
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi khi xóa',
                'message' => 'Không thể xóa vai trò.'
            ]);
            return redirect()->route('admin.roles.index');
        }
    }

    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            return view('admin.roles.show', compact('role'));
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Không tìm thấy',
                'message' => 'Vai trò không tồn tại.'
            ]);
            return redirect()->route('admin.roles.index');
        }
    }
}

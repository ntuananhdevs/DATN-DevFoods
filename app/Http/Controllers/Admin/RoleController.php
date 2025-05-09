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
            // Lấy từ input tìm kiếm
            $keyword = $request->input('keyword');

            // Tạo truy vấn tìm kiếm
            $roles = Role::when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('id', 'like', '%' . $keyword . '%');
                });
            })->paginate(10);

            return view('admin.roles.index', compact('roles'));
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi lấy danh sách roles.');
        }
    }

    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            return view('admin.roles.show', compact('role'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không tìm thấy vai trò.');
        }
    }

    /**
     * Show the form for creating a new role.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'permissions' => 'required|array', // Bắt buộc nhập trường quyền
                'permissions.*' => 'string|in:create,edit,view,delete', // Xác thực giá trị quyền
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Role::create([
                'name' => $request->name,
                'permissions' => $request->permissions,
            ]);

            return redirect()->route('admin.roles.index')->with('success', 'Tạo role thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể tạo role: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $role = Role::findOrFail($id);
            return view('admin.roles.edit', compact('role'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không tìm thấy vai trò.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'permissions' => 'required|array', // Bắt buộc nhập trường quyền
                'permissions.*' => 'string|in:create,edit,view,delete', // Xác thực giá trị quyền
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $role->update([
                'name' => $request->name,
                'permissions' => $request->permissions,
            ]);

            return redirect()->route('admin.roles.index')->with('success', 'Cập nhật role thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi cập nhật role: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return back()->with('success', 'Xóa role thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xóa role.');
        }
    }
}

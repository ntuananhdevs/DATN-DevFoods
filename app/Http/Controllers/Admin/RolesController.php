<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public function __construct()
    {
        // Middleware kiểm tra quyền quản lý roles (chỉ cho phép admin, manager)
        $this->middleware('can:manage-roles');
    }

    // Lấy danh sách roles
    public function index(Request $request)
    {
        try {
            $roles = Role::paginate(10); // Phân trang 10 role mỗi trang
            return view('admin.roles.index', compact('roles'));
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi lấy danh sách roles: ' . $e->getMessage());
        }
    }

    // Lấy chi tiết role
    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            return view('admin.roles.show', compact('role'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không tìm thấy role này.');
        }
    }

    // Tạo mới role
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'permissions' => 'required|json'
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Dữ liệu không hợp lệ!')->withErrors($validator)->withInput();
        }

        try {
            $role = new Role;
            $role->name = $request->name;
            $role->permissions = json_encode($request->permissions);
            $role->save();

            return redirect()->route('admin.roles.index')->with('success', 'Tạo role thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi tạo role: ' . $e->getMessage());
        }
    }

    // Cập nhật role
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'permissions' => 'required|json'
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Dữ liệu không hợp lệ!')->withErrors($validator)->withInput();
        }

        try {
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->permissions = json_encode($request->permissions);
            $role->save();

            return redirect()->route('admin.roles.index')->with('success', 'Cập nhật role thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi cập nhật role: ' . $e->getMessage());
        }
    }

    // Xóa role
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return redirect()->route('admin.roles.index')->with('success', 'Xóa role thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xóa role: ' . $e->getMessage());
        }
    }
}

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

    public function index()
    {
        try {
            $roles = Role::paginate(10);
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'permissions' => 'required|json',
            ]);

            if ($validator->fails()) {
                return back()->with('error', $validator->errors()->first());
            }

            Role::create($request->only('name', 'permissions'));
            return back()->with('success', 'Tạo role thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể tạo role: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'permissions' => 'required|json',
            ]);

            if ($validator->fails()) {
                return back()->with('error', $validator->errors()->first());
            }

            $role->update($request->only('name', 'permissions'));
            return back()->with('success', 'Cập nhật role thành công.');
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

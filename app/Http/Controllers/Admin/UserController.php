<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Workbench\App\Models\User as ModelsUser;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('role')
                ->whereHas('role', function($query) {
                    $query->where('name', 'customer');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách người dùng');
        }
    }

    public function create()
    {
        try {
            $roles = Role::all();  // Get all roles
            return view('admin.users.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@create: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while loading the create form.');
        }
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_name' => 'required|string|max:255|unique:users',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'active' => 'boolean',
            'balance' => 'nullable|numeric|min:0'
        ], [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute phải là chuỗi.',
            'email' => ':attribute phải là email hợp lệ.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'unique' => ':attribute đã tồn tại trong hệ thống.',
            'confirmed' => ':attribute xác nhận không khớp.',
            'numeric' => ':attribute phải là số.',
            'image' => ':attribute phải là hình ảnh.',
            'mimes' => ':attribute phải có định dạng: :values.',
            'boolean' => ':attribute phải là true hoặc false.'
        ], [
            'role_id' => 'Vai trò',
            'user_name' => 'Tên đăng nhập',
            'full_name' => 'Họ và tên',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'password' => 'Mật khẩu',
            'avatar' => 'Ảnh đại diện',
            'active' => 'Trạng thái',
            'balance' => 'Số dư'
        ]);

        try {

            $validatedData['password'] = Hash::make($validatedData['password']);
            if ($request->hasFile('avatar')) {
                $validatedData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            User::create($validatedData);

            return redirect()->route('admin.users.index')
                ->with('success', 'Tạo người dùng mới thành công.');
        } catch (\Exception $e) {
            // Ghi log lỗi và chuyển hướng trở lại với thông báo lỗi
            Log::error('Lỗi khi tạo người dùng: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi khi tạo người dùng. Vui lòng thử lại.');
        }
    }


    public function show(User $user , $id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@show: ' . $e->getMessage());
            var_dump($e->getMessage());

        }
    }

    public function edit(User $user , $id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@edit: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while loading the edit form.');
        }
    }

    public function update(Request $request, $id)
    {

            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'user_name' => 'required|string|max:255|unique:users,user_name,'.$id,
                'full_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$id,
                'phone' => 'nullable|string|max:20|unique:users,phone,'.$id,
                'password' => 'nullable|string|min:8|confirmed',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'active' => 'boolean',
                'balance' => 'nullable|numeric|min:0'
            ], [
                'required' => ':attribute không được để trống.',
                'string' => ':attribute phải là chuỗi.',
                'email' => ':attribute phải là email hợp lệ.',
                'max' => ':attribute không được vượt quá :max ký tự.',
                'min' => ':attribute phải có ít nhất :min ký tự.',
                'unique' => ':attribute đã tồn tại trong hệ thống.',
                'confirmed' => ':attribute xác nhận không khớp.',
                'numeric' => ':attribute phải là số.',
                'image' => ':attribute phải là hình ảnh.',
                'mimes' => ':attribute phải có định dạng: :values.',
                'boolean' => ':attribute phải là true hoặc false.'
            ], [
                'role_id' => 'Vai trò',
                'user_name' => 'Tên đăng nhập',
                'full_name' => 'Họ và tên',
                'email' => 'Email',
                'phone' => 'Số điện thoại',
                'password' => 'Mật khẩu',
                'avatar' => 'Ảnh đại diện',
                'active' => 'Trạng thái',
                'balance' => 'Số dư'
            ]);
            try {
            // Xử lý mật khẩu nếu được cung cấp
            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            // Xử lý avatar nếu có file mới
            if ($request->hasFile('avatar')) {
                // Xóa avatar cũ nếu có
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $validatedData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($validatedData);

            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật người dùng thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật người dùng: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi khi cập nhật người dùng. Vui lòng thử lại.');
        }
    }

    public function destroy(User $user , $id)
    {
        try {

            $user =User::findOrFail($id)->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Đã xóa người dùng thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa người dùng: ' . $e->getMessage());
                                            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi xóa người dùng. Vui lòng thử lại.');
        }
    }

    public function trash()
    {
        try {
            $users = User::onlyTrashed()
                ->orderBy('deleted_at', 'desc')
                ->paginate(10);

            return view('admin.users.trash', compact('users'));
        } catch (\Exception $e) {
            Log::error('Lỗi khi tải danh sách người dùng đã xóa: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách người dùng đã xóa');
        }
    }

    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id)->restore();


            return redirect()->route('admin.users.trash')
                ->with('success', 'Đã khôi phục người dùng thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi khôi phục người dùng: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi khôi phục người dùng. Vui lòng thử lại.');
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            // Xóa avatar nếu có
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->forceDelete();

            return redirect()->route('admin.users.trash')
                ->with('success', 'Đã xóa vĩnh viễn người dùng thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa vĩnh viễn người dùng: ' . $e->getMessage());
            var_dump($e->getMessage());

        }
    }
}

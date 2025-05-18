<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\UserRole;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Workbench\App\Models\User as ModelsUser;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with(['roles'])
                ->whereHas('roles', function($q) {
                    $q->where('name', 'manager');
                })
                ->when($request->search, function($q) use ($request) {
                    $q->where(function($subQ) use ($request) {
                        $subQ->where('user_name', 'LIKE', "%{$request->search}%")
                            ->orWhere('full_name', 'LIKE', "%{$request->search}%")
                            ->orWhere('email', 'LIKE', "%{$request->search}%")
                            ->orWhere('phone', 'LIKE', "%{$request->search}%");
                    });
                })
                ->orderBy('created_at', 'desc');

            $users = $query->paginate(10)->onEachSide(1);

            return $request->ajax()
                ? response()->json([
                    'success' => true,
                    'users' => $users->items(),
                    'pagination' => [
                        'total' => $users->total(),
                        'per_page' => $users->perPage(),
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage()
                    ]
                ])
                : view('admin.users.manager.index', compact('users'));

        } catch (\Exception $e) {
            Log::error('UserController@index Error: ' . $e->getMessage());
            return $request->ajax()
                ? response()->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ], 500)
                : redirect()->back()->with('error', 'Lỗi tải danh sách: ' . $e->getMessage());
        }
    }
    // Thêm phương thức mới để xử lý thay đổi trạng thái
   
    public function create()
    {
        try {
            $roles = Role::where('name', '!=', 'admin')->get();
            return view('admin.users.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Lỗi form tạo người dùng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không tải được form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_ids' => 'required|array|exists:roles,id',
            'user_name' => 'required|string|max:255|unique:users',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'role_ids.required' => 'Vui lòng chọn ít nhất một vai trò',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'user_name' => $validated['user_name'],
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'avatar' => $request->hasFile('avatar') 
                    ? $request->file('avatar')->store('avatars', 'public')
                    : null
            ]);

            $user->roles()->sync($validated['role_ids']);

            DB::commit();

            return redirect()->route('admin.users.index')->with([
                'toast' => [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Tạo người dùng thành công'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo người dùng: ' . $e->getMessage());
            var_dump($e->getMessage());die;
            return redirect()->back()->withInput()->with([
                'toast' => [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không thể tạo người dùng: ' . $e->getMessage()
                ]
            ]);
        }
    }
  


 
    public function bulkStatusUpdate(Request $request)
    {
        try {
            // Kiểm tra xem dữ liệu đến từ form hay từ AJAX
            $userIds = $request->has('ids') ? $request->ids : explode(',', $request->user_ids);

            // Xác định trạng thái từ action hoặc status
            $status = $request->has('action')
                ? ($request->action === 'activate')
                : (bool)$request->status;

            User::whereIn('id', $userIds)->update(['active' => $status]);

            // Xử lý phản hồi cho AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật trạng thái người dùng thành công'
                ]);
            }

            // Xử lý phản hồi cho form thông thường
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã cập nhật trạng thái người dùng thành công'
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái hàng loạt: ' . $e->getMessage());

            // Xử lý lỗi cho AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            // Xử lý lỗi cho form thông thường
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back();
        }
    }
}

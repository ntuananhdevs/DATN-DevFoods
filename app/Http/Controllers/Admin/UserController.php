<?php

namespace App\Http\Controllers\Admin;

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
use App\Models\Branch;
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
                    $q->where('name', 'customer');
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
                : view('admin.users.customer.index', compact('users'));

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
    public function manager(Request $request)
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
            Log::error('ManagerController@index Error: ' . $e->getMessage());
            return $request->ajax()
                ? response()->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ], 500)
                : redirect()->back()->with('error', 'Lỗi tải danh sách: ' . $e->getMessage());
        }
    }
    public function createManager()
    {
        try {
            $roles = Role::where('name', 'manager')->get();
            return view('admin.users.manager.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Lỗi form tạo người quản lý: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không tải được form: ' . $e->getMessage());
        }
    }

    public function storeManager(Request $request)
    {
        try {
            // Validate dữ liệu trước khi bắt đầu transaction
            $validated = $request->validate([
                'user_name' => 'required|string|max:255|unique:users',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'nullable|string|max:20|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
                'password.confirmed' => 'Xác nhận mật khẩu không khớp'
            ]);

            // Bắt đầu transaction sau khi validate thành công
            DB::beginTransaction();

            // Kiểm tra role trước khi tạo user
            $managerRole = Role::where('name', 'manager')->first();
            if (!$managerRole) {
                throw new \Exception('Không tìm thấy vai trò quản lý');
            }

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

            // Gán role manager cho user
            $user->roles()->attach($managerRole->id);

            // Commit transaction khi tất cả thành công
            DB::commit();

            return redirect()->route('admin.users.managers.index')->with([
                'toast' => [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Tạo người quản lý thành công'
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Xử lý riêng lỗi validation
            return redirect()->back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            // Đảm bảo rollback transaction nếu có lỗi
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error('Lỗi tạo người quản lý: ' . $e->getMessage());
            
            return redirect()->back()->withInput()->with([
                'toast' => [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không thể tạo người quản lý: ' . $e->getMessage()
                ]
            ]);
        }
    }
    // Thêm phương thức mới để xử lý thay đổi trạng thái
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Kiểm tra nếu là quản lý và đang quản lý chi nhánh
            if ($user->roles()->where('name', 'manager')->exists()) {
                $managedActiveBranches = Branch::where('manager_user_id', $user->id)
                    ->where('active', true) // Thêm điều kiện chi nhánh đang hoạt động
                    ->count();
                
                if ($managedActiveBranches > 0) {
                    throw new \Exception('Không thể thay đổi trạng thái quản lý đang quản lý chi nhánh HOẠT ĐỘNG');
                }
            }

            $user->active = !$user->active;
            $user->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Đã thay đổi trạng thái người dùng thành công'
                ]);
            }

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã thay đổi trạng thái người dùng thành công'
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi khi thay đổi trạng thái người dùng: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() // Trả về message lỗi cụ thể
                ], 500);
            }

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);

            return redirect()->back();
        }
    }

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
    public function show(Request $request, $id)
    {
        try {
            $user = User::with(['roles'])
                ->withTrashed()
                ->whereHas('roles')
                ->findOrFail($id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'user_name' => $user->user_name,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'balance' => $user->balance,
                        'avatar_url' => $user->avatar ? Storage::url($user->avatar) : null,
                        'roles' => $user->roles->pluck('name'),
                        'status' => $user->active ? 'Hoạt động' : 'Vô hiệu hóa',
                        'created_at' => $user->created_at->format('d/m/Y H:i'),
                        'deleted_at' => $user->deleted_at?->format('d/m/Y H:i')
                    ],
                    'html' => view('admin.users.partials.user_info', compact('user'))->render(),
                    'message' => 'Tải thông tin người dùng thành công'
                ]);
            }

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            Log::error('UserController@show Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng',
                    'error' => $e->getMessage()
                ], 404);
            }

            return redirect()->back()->with('error', 'Không tìm thấy người dùng: ' . $e->getMessage());
        }
    }


    /**
     * Export users data
     */
    public function export(Request $request)
    {
        try {
            $type = $request->type ?? 'excel';
            $query = User::with('role')->whereHas('role', function ($q) {
                $q->where('name', 'customer');
            });

            // Áp dụng các bộ lọc tương tự như trong index
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('user_name', 'LIKE', "%$request->search%")
                        ->orWhere('full_name', 'LIKE', "%$request->search%")
                        ->orWhere('email', 'LIKE', "%$request->search%")
                        ->orWhere('phone', 'LIKE', "%$request->search%");
                });
            }

            $users = $query->latest()->get();

            switch ($type) {
                case 'excel':
                    return Excel::download(new UsersExport($users), 'users.xlsx');

                case 'pdf':
                    $pdf = Pdf::loadView('admin.exports.users', compact('users'));
                    return $pdf->download('users.pdf');

                case 'csv':
                    return Excel::download(new UsersExport($users), 'users.csv', \Maatwebsite\Excel\Excel::CSV);

                case 'json':
                    return $this->exportJson($users, 'users.json');

                default:
                    return redirect()->back()->with('error', 'Định dạng xuất không hợp lệ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi xuất dữ liệu: ' . $e->getMessage());
        }
    }

    private function exportJson($users, $filename)
    {
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'user_name' => $user->user_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->name,
                'status' => $user->active ? 'Active' : 'Inactive',
                'balance' => $user->balance,
                'created_at' => $user->created_at->format('d/m/Y H:i:s'),
                'updated_at' => $user->updated_at->format('d/m/Y H:i:s')
            ];
        });

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ], JSON_PRETTY_PRINT);
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

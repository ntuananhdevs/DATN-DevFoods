<?php

namespace App\Http\Controllers\Admin\User;

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

class ManagerController extends Controller
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
            Log::error('ManagerController@index Error: ' . $e->getMessage());
            return $request->ajax()
                ? response()->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ], 500)
                : redirect()->back()->with('error', 'Lỗi tải danh sách: ' . $e->getMessage());
        }
    }
    
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->active = !$user->active;
            $user->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Đã thay đổi trạng thái người quản lý thành công'
                ]);
            }

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã thay đổi trạng thái người quản lý thành công'
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi khi thay đổi trạng thái người quản lý: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
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
            $roles = Role::where('name', 'manager')->get();
            return view('admin.users.manager.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Lỗi form tạo người quản lý: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không tải được form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
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

            return redirect()->route('admin.managers.index')->with([
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
    
    public function show(Request $request, $id)
    {
        try {
            $user = User::with(['roles'])
                ->withTrashed()
                ->whereHas('roles', function($q) {
                    $q->where('name', 'manager');
                })
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
                        'avatar_url' => $user->avatar ? Storage::url($user->avatar) : null,
                        'roles' => $user->roles->pluck('name'),
                        'status' => $user->active ? 'Hoạt động' : 'Vô hiệu hóa',
                        'created_at' => $user->created_at->format('d/m/Y H:i'),
                        'deleted_at' => $user->deleted_at?->format('d/m/Y H:i')
                    ],
                    'html' => view('admin.managers.show', compact('user'))->render(),
                    'message' => 'Tải thông tin người quản lý thành công'
                ]);
            }

            return view('admin.users.manager.show', compact('user'));
        } catch (\Exception $e) {
            Log::error('ManagerController@show Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người quản lý',
                    'error' => $e->getMessage()
                ], 404);
            }

            return redirect()->back()->with('error', 'Không tìm thấy người quản lý: ' . $e->getMessage());
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

            User::whereIn('id', $userIds)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'manager');
                })
                ->update(['active' => $status]);

            // Xử lý phản hồi cho AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật trạng thái người quản lý thành công'
                ]);
            }

            // Xử lý phản hồi cho form thông thường
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã cập nhật trạng thái người quản lý thành công'
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

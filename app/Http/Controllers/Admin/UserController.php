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
use Barryvdh\DomPDF\Facade\Pdf;
use Workbench\App\Models\User as ModelsUser;

class UserController extends Controller
{
    public function index()
    {
        try {
            $search = request()->input('search');
            
            $users = User::with('role')
                ->whereHas('role', function($query) {
                    $query->where('name', 'customer');
                })
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('user_name', 'LIKE', "%$search%")
                          ->orWhere('full_name', 'LIKE', "%$search%")
                          ->orWhere('email', 'LIKE', "%$search%")
                          ->orWhere('phone', 'LIKE', "%$search%");
                    });
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

    // Thêm phương thức mới để xử lý thay đổi trạng thái
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->active = !$user->active;
            $user->save();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã thay đổi trạng thái người dùng thành công'
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi khi thay đổi trạng thái người dùng: ' . $e->getMessage());
            
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

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Người dùng đã được tạo thành công'
            ]);
            
            return redirect()->route('admin.users.index');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo người dùng: ' . $e->getMessage());
            
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->back()->withInput();
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

  
    /**
     * Export users data
     */
    public function export(Request $request)
    {
        try {
            $type = $request->type ?? 'excel';
            $query = User::with('role')->whereHas('role', function($q) {
                $q->where('name', 'customer');
            });
            
            // Áp dụng các bộ lọc tương tự như trong index
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
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
        $data = $users->map(function($user) {
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
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ], JSON_PRETTY_PRINT);
    }

    public function bulkStatusUpdate(Request $request)
    {
        try {
            $userIds = explode(',', $request->user_ids);
            $status = (bool)$request->status;
            
            User::whereIn('id', $userIds)->update(['active' => $status]);
            
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã cập nhật trạng thái người dùng thành công'
            ]);
            
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái hàng loạt: ' . $e->getMessage());
            
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            
            return redirect()->back();
        }
    }
}

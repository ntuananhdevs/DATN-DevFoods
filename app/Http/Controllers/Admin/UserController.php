<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Address;
use App\Models\UserRank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\Branch;
use App\Models\UserRole;
use App\Notifications\NewUserWelcomeNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Workbench\App\Models\User as ModelsUser;
use App\Models\UserImage;
use Illuminate\Validation\ValidationException;
use App\Mail\SendWelcomeEmail;
use Illuminate\Support\Facades\Mail;
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
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
            's3_url' => config('filesystems.disks.s3.url'),
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
    public function create()
    {
        try {
            $roles = Role::whereIn('name', ['customer'])->get();


            return view('admin.users.customer.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Lỗi form tạo người dùng: ' . $e->getMessage());
            var_dump($e->getMessage());           return redirect()->back()->with('error', 'Không tải được form: ' . $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            // Validate dữ liệu
            $validated = $request->validate([
                'user_name' => 'required|string|max:255|unique:users',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'nullable|string|max:20|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'role_id' => 'required|exists:roles,id',
                'user_rank_id' => 'nullable|exists:user_ranks,id',
                'balance' => 'nullable|numeric|min:0',
                'active' => 'boolean',
                // Multiple addresses validation
                'addresses' => 'nullable|array',
                'addresses.*.address_line' => 'nullable|string|max:255',
                'addresses.*.city' => 'nullable|string|max:100',
                'addresses.*.district' => 'nullable|string|max:100',
                'addresses.*.ward' => 'nullable|string|max:100',
                'addresses.*.phone' => 'nullable|string|max:20',
                'addresses.*.latitude' => 'nullable|numeric|between:-90,90',
                'addresses.*.longitude' => 'nullable|numeric|between:-180,180',
                'addresses.*.is_default' => 'nullable|boolean'
            ], [
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
                'user_name.unique' => 'Tên đăng nhập đã tồn tại',
                'email.unique' => 'Email đã tồn tại',
                'phone.unique' => 'Số điện thoại đã tồn tại',
                'role_id.required' => 'Vui lòng chọn vai trò',
                'role_id.exists' => 'Vai trò không hợp lệ'
            ]);

            DB::beginTransaction();

            // Kiểm tra role tồn tại
            $role = Role::findOrFail($validated['role_id']);

            // Xử lý upload avatar
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                try {
                    $avatar = $request->file('avatar');
                    $filename = 'users/avatars/' . Str::uuid() . '.' . $avatar->getClientOriginalExtension();
                    $avatarPath = Storage::disk('s3')->put($filename, file_get_contents($avatar));
                    $avatarPath = $filename;
                } catch (\Exception $e) {
                    Log::error('Error uploading avatar to S3: ' . $e->getMessage());
                    throw new \Exception('Không thể tải lên ảnh đại diện: ' . $e->getMessage());
                }
            }

            // Tạo user mới
            $user = User::create([
                'user_name' => $validated['user_name'],
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'avatar' => $avatarPath,
                'user_rank_id' => $validated['user_rank_id'] ?? null,
                'balance' => $validated['balance'] ?? 0,
                'active' => $validated['active'] ?? true
            ]);

            // Gán role cho user
            $user->roles()->attach($role->id);

            // Xử lý multiple addresses
            if (!empty($validated['addresses'])) {
                $hasDefault = false;

                // Kiểm tra xem có địa chỉ nào được đánh dấu là default không
                foreach ($validated['addresses'] as $addressData) {
                    if (!empty($addressData['is_default'])) {
                        $hasDefault = true;
                        break;
                    }
                }

                foreach ($validated['addresses'] as $index => $addressData) {
                    // Bỏ qua địa chỉ trống
                    if (empty($addressData['address_line']) && empty($addressData['city']) &&
                        empty($addressData['district']) && empty($addressData['ward'])) {
                        continue;
                    }

                    // Nếu không có địa chỉ nào được đánh dấu default, đặt địa chỉ đầu tiên làm default
                    $isDefault = !empty($addressData['is_default']) || (!$hasDefault && $index === array_key_first($validated['addresses']));

                    Address::create([
                        'user_id' => $user->id,
                        'address_line' => $addressData['address_line'] ?? '',
                        'city' => $addressData['city'] ?? '',
                        'district' => $addressData['district'] ?? '',
                        'ward' => $addressData['ward'] ?? '',
                        'phone_number' => $addressData['phone'] ?? $validated['phone'],
                        'latitude' => $addressData['latitude'] ?? null,
                        'longitude' => $addressData['longitude'] ?? null,
                        'is_default' => $isDefault
                    ]);

                    // Nếu địa chỉ này được đặt làm default, đảm bảo các địa chỉ khác không phải default
                    if ($isDefault && $hasDefault) {
                        $hasDefault = false; // Chỉ cho phép một địa chỉ default
                    }
                }
            }


    Mail::to($user->email)->send(new SendWelcomeEmail($user));
            DB::commit();

            // Get user's role
            $userRole = $user->roles()->first();

            // Determine redirect path and message based on role
            if ($userRole->name === 'customer') {
                return redirect()->route('admin.users.index')->with([
                    'toast' => [
                        'type' => 'success',
                        'title' => 'Thành công',
                        'message' => 'Tạo khách hàng thành công'
                    ]
                ]);
            } else if ($userRole->name === 'manager') {
                return redirect()->route('admin.users.managers.index')->with([
                    'toast' => [
                        'type' => 'success',
                        'title' => 'Thành công',
                        'message' => 'Tạo quản lý thành công'
                    ]
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Lỗi tạo người dùng: ' . $e->getMessage());

            return redirect()->back()->withInput()->with([
                'toast' => [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không thể tạo người dùng: ' . $e->getMessage()
                ]
            ]);
        }
    }
    public function manager(Request $request)
    {
        try {
            $query = User::with(['roles'])
                ->whereHas('roles', function($q) {
                    $q->where('name', 'manager');
                });

            // Xử lý tìm kiếm
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($subQ) use ($search) {
                    $subQ->where('user_name', 'LIKE', "%{$search}%")
                        ->orWhere('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            // Xử lý sắp xếp
            $sortField = $request->input('sort_field', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Đảm bảo field sắp xếp hợp lệ
            $allowedSortFields = ['id', 'user_name', 'full_name', 'email', 'phone', 'created_at'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            $query->orderBy($sortField, $sortOrder);

            // Phân trang
            $perPage = $request->input('per_page', 10);
            $users = $query->paginate($perPage)->onEachSide(1);

            // Trả về kết quả dựa trên loại request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'users' => $users->items(),
                    'pagination' => [
                        'total' => $users->total(),
                        'per_page' => $users->perPage(),
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'from' => $users->firstItem(),
                        'to' => $users->lastItem()
                    ]
                ]);
            }

            return view('admin.users.manager.index', compact('users'));

        } catch (\Exception $e) {
            Log::error('UserController@manager Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Lỗi tải danh sách: ' . $e->getMessage());
        }
    }
    public function createManager()
    {
        try {
            $roles = Role::whereIn('name', [ 'manager'])->get();
            return view('admin.users.manager.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Lỗi form tạo người quản lý: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không tải được form: ' . $e->getMessage());
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

    public function show(Request $request, $id)
    {
        try {
            $user = User::with([
                'roles',
                'userRank',
                'addresses' => function($query) {
                    $query->orderBy('is_default', 'desc')
                          ->orderBy('created_at', 'desc');
                }
            ])
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
                        'google_id' => $user->google_id,
                        'balance' => number_format($user->balance, 0, ',', '.') . ' VNĐ',
                        'raw_balance' => $user->balance,
                        'total_spending' => number_format($user->total_spending, 0, ',', '.') . ' VNĐ',
                        'raw_total_spending' => $user->total_spending,
                        'total_orders' => $user->total_orders,
                        'user_rank' => [
                            'id' => $user->userRank?->id,
                            'name' => $user->userRank?->name ?? 'Chưa có hạng',
                            'updated_at' => $user->rank_updated_at?->format('d/m/Y H:i')
                        ],
                        'avatar_url' => $user->avatar ? config('filesystems.disks.s3.url') . '/' . $user->avatar : null,
                        'roles' => $user->roles->pluck('name'),
                        'status' => $user->active ? 'Hoạt động' : 'Vô hiệu hóa',
                        'email_verified' => $user->email_verified_at ? 'Đã xác thực' : 'Chưa xác thực',
                        'email_verified_at' => $user->email_verified_at?->format('d/m/Y H:i'),
                        'addresses' => $user->addresses->map(function($address) {
                            return [
                                'id' => $address->id,
                                'address_line' => $address->address_line,
                                'city' => $address->city,
                                'district' => $address->district,
                                'ward' => $address->ward,
                                'phone_number' => $address->phone_number,
                                'is_default' => $address->is_default,
                                'full_address' => $address->address_line . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city,
                                'coordinates' => [
                                    'latitude' => $address->latitude,
                                    'longitude' => $address->longitude
                                ],
                                'created_at' => $address->created_at->format('d/m/Y H:i')
                            ];
                        }),
                        'addresses_count' => $user->addresses->count(),
                        'default_address' => $user->addresses->where('is_default', true)->first(),
                        'created_at' => $user->created_at->format('d/m/Y H:i'),
                        'updated_at' => $user->updated_at->format('d/m/Y H:i'),
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

            // Lọc ra những user có thể thay đổi trạng thái
            $eligibleUserIds = [];
            $skippedUsers = [];

            foreach ($userIds as $userId) {
                $user = User::with('roles')->find($userId);

                if (!$user) {
                    continue;
                }

                // Kiểm tra nếu là quản lý và đang quản lý chi nhánh hoạt động
                if ($user->roles()->where('name', 'manager')->exists()) {
                    $managedActiveBranches = Branch::where('manager_user_id', $user->id)
                        ->where('active', true)
                        ->count();

                    if ($managedActiveBranches > 0) {
                        $skippedUsers[] = $user->full_name;
                        continue;
                    }
                }

                $eligibleUserIds[] = $userId;
            }

            // Cập nhật trạng thái cho những user đủ điều kiện
            if (!empty($eligibleUserIds)) {
                User::whereIn('id', $eligibleUserIds)->update(['active' => $status]);
            }

            // Tạo thông báo kết quả
            $message = 'Đã cập nhật trạng thái người dùng thành công';
            if (!empty($skippedUsers)) {
                $message .= '. Bỏ qua: ' . implode(', ', $skippedUsers) . ' (đang quản lý chi nhánh hoạt động)';
            }

            // Xử lý phản hồi cho AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated_count' => count($eligibleUserIds),
                    'skipped_count' => count($skippedUsers)
                ]);
            }

            // Xử lý phản hồi cho form thông thường
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => $message
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $query = Branch::when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('address', 'LIKE', "%$search%")
                        ->orWhere('phone', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%");
                });
            })
            ->orderBy('id', 'asc');

            $branches = $query->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'branches' => $branches->items(),
                    'pagination' => [
                        'total' => $branches->total(),
                        'per_page' => $branches->perPage(),
                        'current_page' => $branches->currentPage(),
                        'last_page' => $branches->lastPage()
                    ]
                ]);
            }

            return view('admin.branch.index', compact('branches'));
        } catch (\Exception $e) {
            Log::error('Error in BranchController@index: ' . $e->getMessage());
           
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tải danh sách chi nhánh'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách chi nhánh');
        }
    }

    public function create()
    {
        return view('admin.branch.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'opening_hour' => 'required|date_format:H:i',
            'closing_hour' => 'required|date_format:H:i|after:opening_hour',
        ]);

        Branch::create($validated);
        
        return redirect()->route('admin.branches.index')->with('success', 'Thêm chi nhánh thành công');
    }

    public function show($id)
    {
        try {
            // Lấy chi nhánh kèm thông tin quản lý (chỉ khi tài khoản active)
            $branch = Branch::with(['manager' => function($query) {
                $query->where('active', true);
            }])->findOrFail($id);
    
            // Kiểm tra và xóa tham chiếu nếu quản lý không active
            if ($branch->manager_user_id && !$branch->manager) {
                $branch->manager_user_id = null; // Cho phép null
                $branch->save();
            }
    
            $hasActiveManager = $branch->manager_user_id && $branch->manager;
            
            return view('admin.branch.show', compact('branch', 'hasActiveManager'));
        } catch (\Exception $e) {
            Log::error('Error in BranchController@show: ' . $e->getMessage());
            var_dump($e->getMessage());
            die;
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải thông tin chi nhánh');
        }
    }

    public function edit(Branch $branch)
    {
        return view('admin.branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            // ... existing code ...
        ]);

        $branch->update($validated);
        
        return redirect()->route('admin.branches.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->back()->with('success', 'Đã xóa chi nhánh');
    }

    /**
     * Thay đổi trạng thái của một chi nhánh
     */
    public function toggleStatus($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $branch->active = !$branch->active;
            $branch->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thay đổi trạng thái chi nhánh thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in BranchController@toggleStatus: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái chi nhánh: ' . $e->getMessage(),
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái hàng loạt cho nhiều chi nhánh
     */
    public function bulkStatusUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'branch_ids' => 'required|array',
                'branch_ids.*' => 'required|integer|exists:branches,id',
                'action' => 'required|in:activate,deactivate'
            ]);
            
            $active = $validated['action'] === 'activate';
            $count = Branch::whereIn('id', $validated['branch_ids'])
                ->update(['active' => $active]);
            
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái $count chi nhánh thành công",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error in BranchController@bulkStatusUpdate: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái hàng loạt'
            ], 500);
        }
    }

    /**
     * Hiển thị form chọn người quản lý cho chi nhánh
     */
    public function assignManager($id)
    {
        try {
            $branch = Branch::with('manager')->findOrFail($id);
            
            // Lấy danh sách người dùng có vai trò manager (bao gồm cả inactive)
            $managers = User::whereHas('roles', function($query) {
                $query->where('name', 'manager');
            })->orderBy('full_name', 'asc') // Bỏ điều kiện active
              ->get();
            
            // Lấy danh sách chi nhánh ĐANG HOẠT ĐỘNG đã có người quản lý
            $assignedBranches = Branch::whereNotNull('manager_user_id')
                ->where('id', '!=', $id)
                ->where('active', true) // Thêm điều kiện active
                ->pluck('manager_user_id')
                ->toArray();
            
            // Lọc ra những quản lý có thể phân công
            $availableManagers = $managers->filter(function($manager) use ($assignedBranches, $branch) {
                return !in_array($manager->id, $assignedBranches) || 
                       $manager->id == $branch->manager_user_id;
            });
            
            return view('admin.branch.assign_manager', compact('branch', 'availableManagers'));
        } catch (\Exception $e) {
            Log::error('Error in BranchController@assignManager: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải form phân công quản lý.');
        }
    }


    /**
     * Lưu thông tin người quản lý cho chi nhánh
     */
    public function updateManager(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'manager_user_id' => 'required|exists:users,id'
            ]);
            
            $branch = Branch::findOrFail($id);
            $branch->manager_user_id = $validated['manager_user_id'];
            $branch->save();
            
            return redirect()->route('admin.branches.show', $branch->id)
                ->with('success', 'Đã cập nhật người quản lý chi nhánh thành công.');
        } catch (\Exception $e) {
            Log::error('Error in BranchController@updateManager: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật người quản lý chi nhánh.');
        }
    }

    public function removeManager(Branch $branch)
    {
        try {
            $branch->update(['manager_user_id' => null]);
            
            return redirect()->route('admin.branches.show', $branch->id)
                ->with('success', 'Đã gỡ bỏ quản lý thành công');
            
        } catch (\Exception $e) {
            Log::error('Error removing manager: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gỡ bỏ quản lý thất bại: ' . $e->getMessage());
        }
    }

    public function uploadImage(Request $request, Branch $branch)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        try {
            $path = $request->file('image')->store('branch-images', 'public');
            
            $branch->images()->create([
                'image_path' => $path, // Đổi từ 'path' -> 'image_path'
                'caption' => 'Hình ảnh chi nhánh' // Đổi tên trường description -> caption
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Upload ảnh thành công'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error uploading image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload ảnh: ' . $e->getMessage()
            ], 500);
        }
    }
}
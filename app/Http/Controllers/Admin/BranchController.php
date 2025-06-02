<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        try {
            // Lấy danh sách người dùng có vai trò manager và đang active
            $managers = User::whereHas('roles', function($query) {
                $query->where('name', 'manager');
            })
            ->where('active', true)
            ->orderBy('full_name', 'asc')
            ->get();
            
            // Lấy danh sách chi nhánh đang hoạt động đã có người quản lý
            $assignedBranches = Branch::whereNotNull('manager_user_id')
                ->where('active', true)
                ->pluck('manager_user_id')
                ->toArray();
            
            // Lọc ra những quản lý có thể phân công (chưa quản lý chi nhánh nào)
            $availableManagers = $managers->filter(function($manager) use ($assignedBranches) {
                return !in_array($manager->id, $assignedBranches);
            });
            
            return view('admin.branch.create', compact('availableManagers'));
        } catch (\Exception $e) {
            Log::error('Error in BranchController@create: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải form tạo chi nhánh: ' . $e->getMessage());
        }
    }

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:branches',
            'address' => 'required|unique:branches',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:branches',
            'email' => 'nullable|email|unique:branches',
            'opening_hour' => 'required|date_format:H:i',
            'closing_hour' => 'required|date_format:H:i|after:opening_hour',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'manager_user_id' => 'nullable|exists:users,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'nullable|integer|min:0',
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:255',
        ]);
        
        DB::beginTransaction();
        
        // Tạo mã chi nhánh tự động
        $branchCode = 'BR' . Str::padLeft(Branch::count() + 1, 4, '0');
        
        // Tạo chi nhánh mới
        $branch = Branch::create([
            'name' => $validated['name'],
            'address' => $validated['address'], 
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'opening_hour' => $validated['opening_hour'],
            'closing_hour' => $validated['closing_hour'],
            'branch_code' => $branchCode,
            'latitude' => $validated['latitude'] ?? null, 
            'longitude' => $validated['longitude'] ?? null,
            'manager_user_id' => $validated['manager_user_id'] ?? null,
            'active' => true,
            'balance' => 0,
            'rating' => 5.00,
            'reliability_score' => 100
        ]);

        $uploadedImages = [];
        // Xử lý upload ảnh tối ưu
        if ($request->hasFile('images')) {
            try {
                $directory = 'branches/' . $branch->branch_code;
                Storage::disk('public')->makeDirectory($directory);

                $primaryImageIndex = $request->input('primary_image', 0);
                $images = $request->file('images');
                
                // Validate primary image index
                if ($primaryImageIndex >= count($images)) {
                    throw new \Exception("Vị trí ảnh chính không hợp lệ");
                }
                
                foreach ($images as $index => $image) {
                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs($directory, $filename, 'public');
                    
                    $uploadedImages[] = [
                        'image_path' => $path,
                        'caption' => $request->input('captions.'.$index, ''),
                        'is_primary' => ($index === $primaryImageIndex)
                    ];
                }

                // Xử lý ảnh trong transaction riêng
                DB::transaction(function () use ($branch, $uploadedImages) {
                    foreach ($uploadedImages as $imageData) {
                        $branch->images()->create($imageData);
                    }
                    
                    // Đánh dấu ảnh chính sau khi tất cả ảnh đã được tạo
                    if ($primaryImage = $branch->images()->where('is_primary', true)->first()) {
                        $branch->images()
                            ->where('id', '!=', $primaryImage->id)
                            ->update(['is_primary' => false]);
                    }
                });

            } catch (\Exception $e) {
                // Rollback tự động và xóa file đã upload
                if (isset($uploadedImages)) {
                    foreach ($uploadedImages as $image) {
                        Storage::disk('public')->delete($image['image_path']);
                    }
                }
                throw $e;
            }
        }

        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Thêm chi nhánh thành công',
            'data' => [
                'branch' => $branch,
                'images' => $branch->images
            ]
        ], 201);
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        // Xóa toàn bộ thư mục nếu có lỗi
        if (isset($directory) && Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }
        
        // Log detailed error
        Log::error('Error in BranchController@store', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi thêm chi nhánh: ' . $e->getMessage()
        ], 500);
    }
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
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải thông tin chi nhánh');
        }
    }

    public function edit($id)
    {
        try {
            $branch = Branch::findOrFail($id);

            $managers = User::whereHas('roles', function($query) {
                $query->where('name', 'manager');
            })
            ->where('active', true)
            ->orderBy('full_name', 'asc')
            ->get();
    
            $assignedBranches = Branch::whereNotNull('manager_user_id')
                ->where('active', true)
                ->pluck('manager_user_id')
                ->toArray();
    
            $availableManagers = $managers->filter(function($manager) use ($assignedBranches, $branch) {
                return !in_array($manager->id, $assignedBranches) || $manager->id === $branch->manager_user_id;
            });
    
            $branch->load('images');
            return view('admin.branch.edit', compact('branch', 'availableManagers'));
            
        } catch (\Exception $e) {
            Log::error('Error in BranchController@edit: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải form chỉnh sửa: ' . $e->getMessage());
        }
    }
    public function update(Request $request, Branch $branch)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'address' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'email' => 'nullable|email',
                'opening_hour' => 'required|date_format:H:i',
                'closing_hour' => 'required|date_format:H:i|after:opening_hour',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'manager_user_id' => 'nullable|exists:users,id',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'delete_images' => 'nullable|array',
                'delete_images.*' => 'exists:branch_images,id',
                'primary_image' => 'nullable|integer|min:0',
                'captions' => 'nullable|array',
                'captions.*' => 'nullable|string|max:255',
            ]);
    
            DB::beginTransaction();
    
            // Cập nhật thông tin cơ bản
            $branch->update($validated);
    
            // Xử lý xóa ảnh
            if ($request->has('delete_images')) {
                $imagesToDelete = $branch->images()->whereIn('id', $request->delete_images)->get();
                
                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
    
            // Xử lý upload ảnh mới
            if ($request->hasFile('images')) {
                $directory = 'branches/' . $branch->branch_code;
                Storage::disk('public')->makeDirectory($directory);
    
                $primaryImageIndex = $request->input('primary_image', 0);
                
                foreach ($request->file('images') as $index => $image) {
                    $filename = Str::slug($branch->name).'_'.time().'_'.$index.'.'.$image->getClientOriginalExtension();
                    $path = $image->storeAs($directory, $filename, 'public');
                    
                    $isPrimary = ($index == $primaryImageIndex);
                    
                    $branch->images()->create([
                        'image_path' => $path,
                        'caption' => $request->input('captions.'.$index, ''),
                        'is_primary' => $isPrimary
                    ]);
                    
                    if ($isPrimary) {
                        $branch->images()->where('id', '!=', $branch->images()->latest()->first()->id)
                            ->update(['is_primary' => false]);
                    }
                }
            }
    
            // Xử lý ảnh chính từ những ảnh hiện có
            if ($request->has('primary_image') && !$request->hasFile('images')) {
                $primaryImageId = $request->input('primary_image');
                $branch->images()->where('id', $primaryImageId)->first()->setAsPrimary();
            }
    
            DB::commit();
            
            return redirect()->route('admin.branches.index')
                ->with('success', 'Cập nhật chi nhánh thành công');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating branch: '.$e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Branch $branch)
    {
        try {
            DB::beginTransaction();
            
            // Kiểm tra xem chi nhánh có đang được sử dụng không
            // Ví dụ: kiểm tra có đơn hàng nào đang liên kết với chi nhánh này không
            
            $branch->delete();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Đã xóa chi nhánh thành công');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in BranchController@destroy: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa chi nhánh: ' . $e->getMessage());
        }
    }

    /**
     * Thay đổi trạng thái của một chi nhánh
     */
    public function toggleStatus($id)
    {
        try {
            DB::beginTransaction();
            
            $branch = Branch::findOrFail($id);
            $branch->active = !$branch->active;
            $branch->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thay đổi trạng thái chi nhánh thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
            
            DB::beginTransaction();
            
            $active = $validated['action'] === 'activate';
            $count = Branch::whereIn('id', $validated['branch_ids'])
                ->update(['active' => $active]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái $count chi nhánh thành công",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
            
            DB::beginTransaction();
            
            $branch = Branch::findOrFail($id);
            $branch->manager_user_id = $validated['manager_user_id'];
            $branch->save();
            
            DB::commit();
            
            return redirect()->route('admin.branches.show', $branch->id)
                ->with('success', 'Đã cập nhật người quản lý chi nhánh thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in BranchController@updateManager: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật người quản lý chi nhánh.');
        }
    }

    public function removeManager(Branch $branch)
    {
        try {
            DB::beginTransaction();
            
            $branch->update(['manager_user_id' => null]);
            
            DB::commit();
            
            return redirect()->route('admin.branches.show', $branch->id)
                ->with('success', 'Đã gỡ bỏ quản lý thành công');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing manager: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gỡ bỏ quản lý thất bại: ' . $e->getMessage());
        }
    }

    
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmailFactory;
use App\Models\Branch;
use App\Models\BranchImage;
use App\Models\User;
use App\Models\Role;
use App\Notifications\BranchManagerAssigned;
use App\Notifications\BranchManagerRemoved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Mail\NotificationMail;
use App\Jobs\SendEmailJob;
use App\Notifications\BranchDisabled;
use Illuminate\Support\Facades\Mail;

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
                    'toast' => true,
                    'branches' => $branches,
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
                    'toast' => false,
                    'message' => 'Có lỗi xảy ra khi tải danh sách chi nhánh'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách chi nhánh');
        }
    }



    public function show($id)
    {
        try {
            $branch = Branch::with(['manager'])->findOrFail($id);

            // Kiểm tra trạng thái active của quản lý
            $hasActiveManager = $branch->manager_user_id &&
                $branch->manager &&
                $branch->manager->active;

            return view('admin.branch.show', compact('branch', 'hasActiveManager'));
        } catch (\Exception $e) {
            Log::error('Error in BranchController@show: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải thông tin chi nhánh');
        }
    }

    /**
     * Hiển thị form chỉnh sửa chi nhánh
     */
    public function edit($id)
    {
        try {
            $branch = Branch::with(['manager', 'images'])->findOrFail($id);
            
            // Lấy danh sách người dùng có vai trò manager và đang active
            $managers = User::whereHas('roles', function ($query) {
                $query->where('name', 'manager');
            })
                ->where('active', true)
                ->orderBy('full_name', 'asc')
                ->get();

            // Lấy danh sách chi nhánh đang hoạt động đã có người quản lý (trừ chi nhánh hiện tại)
            $assignedBranches = Branch::whereNotNull('manager_user_id')
                ->where('active', true)
                ->where('id', '!=', $id)
                ->pluck('manager_user_id')
                ->toArray();

            // Lọc ra những quản lý có thể phân công (chưa quản lý chi nhánh nào hoặc đang quản lý chi nhánh hiện tại)
            $availableManagers = $managers->filter(function ($manager) use ($assignedBranches, $branch) {
                return !in_array($manager->id, $assignedBranches) || $manager->id == $branch->manager_user_id;
            });

            return view('admin.branch.edit', compact('branch', 'availableManagers'));
        } catch (\Exception $e) {
            Log::error('Error in BranchController@edit: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải form chỉnh sửa chi nhánh: ' . $e->getMessage());
        }
    }
        /**
     * Cập nhật thông tin chi nhánh
     */
    public function update(Request $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);
            
            // Validate dữ liệu đầu vào
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:branches,name,' . $id,
                'address' => 'required|string|max:255|unique:branches,address,' . $id,
                'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:branches,phone,' . $id,
                'email' => 'nullable|email|unique:branches,email,' . $id,
                'opening_hour' => 'required|date_format:H:i',
                'closing_hour' => 'required|date_format:H:i|after:opening_hour',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'manager_user_id' => 'nullable|exists:users,id',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'primary_image' => 'nullable|integer|min:0',
                'captions' => 'nullable|array',
                'captions.*' => 'nullable|string|max:255',
                'delete_images' => 'nullable|array',
                'delete_images.*' => 'integer|exists:branch_images,id',
            ]);

            DB::beginTransaction();

            // Lưu thông tin quản lý cũ để gửi thông báo
            $oldManagerId = $branch->manager_user_id;
            $newManagerId = $validated['manager_user_id'] ?? null;

            // Cập nhật thông tin chi nhánh
            $branch->name = $validated['name'];
            $branch->address = $validated['address'];
            $branch->phone = $validated['phone'];
            $branch->email = $validated['email'] ?? null;
            $branch->opening_hour = $validated['opening_hour'];
            $branch->closing_hour = $validated['closing_hour'];
            $branch->latitude = $validated['latitude'] ?? null;
            $branch->longitude = $validated['longitude'] ?? null;
            $branch->manager_user_id = $newManagerId;
            $branch->active = $request->has('active') ? true : false;
            $branch->save();

            // Xử lý thay đổi quản lý
            if ($oldManagerId != $newManagerId) {
                // Gửi thông báo cho quản lý cũ nếu có
                if ($oldManagerId) {
                    $oldManager = User::find($oldManagerId);
                    if ($oldManager && $oldManager->active) {
                        $oldManager->notify(new BranchManagerRemoved($branch, $oldManager));
                    }
                }

                // Gửi thông báo cho quản lý mới nếu có
                if ($newManagerId) {
                    $newManager = User::find($newManagerId);
                    if ($newManager && $newManager->active) {
                        $newManager->notify(new BranchManagerAssigned($branch, $newManager));
                    }
                }
            }

            // Xóa hình ảnh được chọn
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                $imagesToDelete = BranchImage::where('branch_id', $branch->id)
                    ->whereIn('id', $request->delete_images)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    // Xóa file từ S3
                    Storage::disk('s3')->delete($image->image_path);
                    // Xóa record từ database
                    $image->delete();
                }
            }

            // Upload hình ảnh mới
            if ($request->hasFile('images')) {
                $directory = 'branches/' . $branch->branch_code;
                $primaryImageIndex = $request->input('primary_image', 0);
                $existingImagesCount = $branch->images()->count();

                foreach ($request->file('images') as $index => $image) {
                    $filename = $directory . '/' . \Illuminate\Support\Str::uuid() . '.' . $image->getClientOriginalExtension();
                    // Upload to S3
                    $putResult = Storage::disk('s3')->put($filename, file_get_contents($image));
                    if ($putResult) {
                        $isPrimary = ($index == $primaryImageIndex);
                        
                        // Nếu đây là ảnh chính, cập nhật tất cả ảnh khác thành không phải ảnh chính
                        if ($isPrimary) {
                            BranchImage::where('branch_id', $branch->id)
                                ->update(['is_primary' => false]);
                        }
                        
                        BranchImage::create([
                            'branch_id' => $branch->id,
                            'image_path' => $filename,
                            'caption' => $request->input("captions.$index", ''),
                            'is_primary' => $isPrimary,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.branches.show', $branch->id)->with([
                'toast' => [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Đã cập nhật thông tin chi nhánh thành công'
                ]
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating branch: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật chi nhánh: ' . $e->getMessage() .
                    "\nFile: " . $e->getFile() .
                    "\nLine: " . $e->getLine())
                ->withInput();
        }
    }
    public function toggleStatus($id)
    {
        try {
            $branch = Branch::with('manager')->findOrFail($id);
            $oldStatus = $branch->active;
    
            // Thay đổi trạng thái branch
            $branch->active = !$branch->active;
            
            // Nếu chi nhánh bị vô hiệu hóa, gỡ bỏ quản lý
            if ($oldStatus && !$branch->active) {
                // Gửi email cho người quản lý trước khi gỡ bỏ
                if ($branch->manager_user_id) {
                    $manager = User::find($branch->manager_user_id);
                    if ($manager && $manager->active) {
                        // Gửi thông báo vô hiệu hóa chi nhánh cho manager
                        $manager->notify(new BranchDisabled($branch, $manager));
                    }
                    // Gỡ bỏ quản lý khỏi chi nhánh
                    $branch->manager_user_id = null;
                }
            }
            
            $branch->save();
    
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'branch' => $branch,
                    'message' => 'Đã thay đổi trạng thái chi nhánh thành công',
                    'data' => [
                        'id' => $branch->id,
                        'active' => $branch->active,
                        'status_text' => $branch->active ? 'Hoạt động' : 'Vô hiệu hóa',
                        'status_class' => $branch->active ? 'badge-success' : 'badge-danger'
                    ]
                ]);
            }
    
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã thay đổi trạng thái chi nhánh thành công'
            ]);
    
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Lỗi khi thay đổi trạng thái chi nhánh: ' . $e->getMessage());
    
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
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
    
    /**
     * Cập nhật trạng thái hàng loạt cho nhiều chi nhánh
     */
    public function bulkStatusUpdate(Request $request)
    {
        try {
            // Check if data comes from form or AJAX
            $branchIds = $request->has('ids') ? $request->ids : explode(',', $request->branch_ids);
    
            // Determine status from action or status parameter
            $status = $request->has('action')
                ? ($request->action === 'activate')
                : (bool)$request->status;
    
            DB::beginTransaction();
    
            $branches = Branch::whereIn('id', $branchIds)->get();
            $updatedCount = 0;
    
            foreach ($branches as $branch) {
                $oldStatus = $branch->active;
                $branch->active = $status;
                
                // Nếu chi nhánh bị vô hiệu hóa, gỡ bỏ quản lý
                if ($oldStatus && !$status) {
                    // Gửi email cho người quản lý trước khi gỡ bỏ
                    if ($branch->manager_user_id) {
                        $manager = User::find($branch->manager_user_id);
                        if ($manager && $manager->active) {
                            // Gửi thông báo vô hiệu hóa chi nhánh cho manager
                            $manager->notify(new BranchDisabled($branch, $manager));
                        }
                        // Gỡ bỏ quản lý khỏi chi nhánh
                        $branch->manager_user_id = null;
                    }
                }
                
                $branch->save();
                $updatedCount++;
            }
    
            DB::commit();
    
            $message = "Đã cập nhật trạng thái cho {$updatedCount} chi nhánh";
    
            // Return appropriate response based on request type
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated_count' => $updatedCount
                ]);
            }
    
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => $message
            ]);
    
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật trạng thái hàng loạt: ' . $e->getMessage());
    
            if ($request->ajax()) {
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

    /**
     * Hiển thị form chọn người quản lý cho chi nhánh
     */
    public function assignManager($id)
    {
        try {
            $branch = Branch::with('manager')->findOrFail($id);

            // Get list of active managers
            $managers = User::whereHas('roles', function ($query) {
                $query->where('name', 'manager');
            })
                ->where('active', true)
                ->orderBy('full_name', 'asc')
                ->get();

            // Get list of active branches that already have managers
            $assignedBranches = Branch::whereNotNull('manager_user_id')
                ->where('id', '!=', $id)
                ->where('active', true)
                ->pluck('manager_user_id')
                ->toArray();

            // Filter out managers who are already assigned to other branches
            $availableManagers = $managers->reject(function ($manager) use ($assignedBranches) {
                return in_array($manager->id, $assignedBranches);
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
            
            // Kiểm tra chi nhánh có đang hoạt động không
            if (!$branch->active) {
                DB::rollBack();
                return redirect()->back()
                    ->with([
                        'toast' => [
                            'type' => 'error',
                            'title' => 'Lỗi',
                            'message' => 'Không thể gán quản lý cho chi nhánh đã bị vô hiệu hóa'
                        ]
                    ]);
            }
    
            // Check if there was a previous manager
            if ($branch->manager_user_id && $branch->manager_user_id != $validated['manager_user_id']) {
                // Get the previous manager to send notification
                $previousManager = User::findOrFail($branch->manager_user_id);
    
                // Send removal notification to the previous manager
                $previousManager->notify(new BranchManagerRemoved($branch, $previousManager));
            }
    
            // Update branch with new manager
            $branch->manager_user_id = $validated['manager_user_id'];
            $branch->save();
    
            // Get the new manager to send notification
            $newManager = User::findOrFail($validated['manager_user_id']);
    
            // Send email notification to the new manager
            $newManager->notify(new BranchManagerAssigned($branch, $newManager));
    
            DB::commit();
    
            return redirect()->route('admin.branches.show', $branch->id)
                ->with([
                    'toast' => [
                        'type' => 'success',
                        'title' => 'Thành công',
                        'message' => 'Đã cập nhật người quản lý chi nhánh thành công'
                    ]
                ]);
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

            // Store manager information before removing it
            if ($branch->manager_user_id) {
                $manager = User::findOrFail($branch->manager_user_id);

                // Update branch to remove manager
                $branch->update(['manager_user_id' => null]);

                // Send email notification to the manager about removal
                $manager->notify(new BranchManagerRemoved($branch, $manager));
            } else {
                // No manager to remove
                $branch->update(['manager_user_id' => null]);
            }

            DB::commit();

            return redirect()->route('admin.branches.show', $branch->id)
                ->with([
                    'toast' => [
                        'type' => 'success',
                        'title' => 'Thành công',
                        'message' => 'Đã gỡ bỏ quản lý thành công'
                    ]
                ]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error removing manager: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gỡ bỏ quản lý thất bại: ' . $e->getMessage());
        }
    }

  
  
    public function deleteImage(Request $request, Branch $branch, $imageId)
    {
        try {
            // Find the image associated with the branch
            $image = BranchImage::where('branch_id', $branch->id)->findOrFail($imageId);

            // Delete the image file from S3
            Storage::disk('s3')->delete($image->image_path);

            // Delete the image record from the database
            $image->delete();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Hình ảnh đã được xóa thành công.'
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error deleting branch image', [
                'branch_id' => $branch->id,
                'image_id' => $imageId,
                'error' => $e->getMessage()
            ]);

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa hình ảnh: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function setFeatured(Request $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $imageId = $request->input('imageId'); // Lấy từ request body
            $image = BranchImage::where('branch_id', $branch->id)->findOrFail($imageId);
            
            // Reset all images to not primary
            BranchImage::where('branch_id', $branch->id)->update(['is_primary' => false]);
            
            // Set selected image as primary
            $image->update(['is_primary' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã đặt ảnh làm ảnh chính thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting featured image', [
                'branch_id' => $id,
                'image_id' => $imageId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể đặt ảnh chính: ' . $e->getMessage()
            ], 500);
        }
    }
    public function uploadImage(Request $request, $id)
{
    try {
        $branch = Branch::findOrFail($id);
        
        // Validate request
        $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'set_as_featured' => 'nullable|in:0,1,true,false,on' // Chấp nhận nhiều format
        ]);

        DB::beginTransaction();

        $uploadedImages = [];
        $directory = 'branches/' . $branch->branch_code;
        
        // Xử lý set_as_featured với nhiều format khác nhau
        $setAsFeatured = in_array($request->input('set_as_featured'), ['1', 'true', 'on', true, 1], true);
        
        // Nếu set_as_featured = true, reset tất cả ảnh hiện tại về không phải primary
        if ($setAsFeatured) {
            BranchImage::where('branch_id', $branch->id)->update(['is_primary' => false]);
        }

        foreach ($request->file('images') as $index => $image) {
            try {
                // Tạo tên file unique
                $filename = $directory . '/' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                
                // Upload to S3
                $putResult = Storage::disk('s3')->put($filename, file_get_contents($image));
                
                if ($putResult) {
                    $branchImage = BranchImage::create([
                        'branch_id' => $branch->id,
                        'image_path' => $filename,
                        'caption' => $request->input("captions.{$index}", ''),
                        'is_primary' => ($setAsFeatured && $index === 0), // Chỉ ảnh đầu tiên làm primary nếu được chọn
                    ]);
                    
                    $uploadedImages[] = $branchImage;
                    
                    Log::info('Branch image uploaded successfully', [
                        'branch_id' => $branch->id,
                        'image_id' => $branchImage->id,
                        'filename' => $filename
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error uploading individual image', [
                    'branch_id' => $branch->id,
                    'index' => $index,
                    'error' => $e->getMessage()
                ]);
                // Tiếp tục với ảnh tiếp theo thay vì dừng toàn bộ quá trình
            }
        }

        DB::commit();

        if (empty($uploadedImages)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ảnh nào được tải lên thành công'
            ], 400);
        }

        // Sửa lỗi: Convert array thành Collection trước khi dùng map()
        $imagesData = collect($uploadedImages)->map(function($img) {
            return [
                'id' => $img->id,
                'url' => Storage::disk('s3')->url($img->image_path),
                'is_primary' => $img->is_primary
            ];
        });

        return redirect()->route('admin.branches.show', $branch->id)->with([
            'toast' => [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã tải lên ' . count($uploadedImages) . ' ảnh thành công'
            ]
        ]);
        
    } catch (ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error uploading branch images', [
            'branch_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi tải lên ảnh: ' . $e->getMessage()
        ], 500);
    }
}

// ... existing code ...

/**
 * Hiển thị form tạo chi nhánh mới
 */
public function create()
{
    try {
        // Lấy danh sách người dùng có vai trò manager và đang active
        $managers = User::whereHas('roles', function ($query) {
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
        $availableManagers = $managers->filter(function ($manager) use ($assignedBranches) {
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
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'address' => 'required|string|max:255|unique:branches,address',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:branches,phone',
            'email' => 'nullable|email|unique:branches,email',
            'opening_hour' => 'required|date_format:H:i',
            'closing_hour' => 'required|date_format:H:i|after:opening_hour',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'manager_user_id' => 'nullable|exists:users,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'nullable|integer|min:0',
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:255',
        ], [
            'name.unique' => 'Tên chi nhánh đã tồn tại',
            'address.unique' => 'Địa chỉ chi nhánh đã tồn tại',
            'phone.unique' => 'Số điện thoại đã được sử dụng',
            'email.unique' => 'Email đã được sử dụng',
            'closing_hour.after' => 'Giờ đóng cửa phải sau giờ mở cửa',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
        ]);

        DB::beginTransaction();

        // Tạo mã chi nhánh tự động
        $branchCode = 'BR' . str_pad(Branch::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Đảm bảo mã chi nhánh là duy nhất
        while (Branch::where('branch_code', $branchCode)->exists()) {
            $branchCode = 'BR' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        }

        // Tạo chi nhánh mới
        $branch = Branch::create([
            'branch_code' => $branchCode,
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'opening_hour' => $validated['opening_hour'],
            'closing_hour' => $validated['closing_hour'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'manager_user_id' => $validated['manager_user_id'] ?? null,
            'active' => $request->has('active') ? true : false,
            'balance' => 0,
            'rating' => 5.00,
            'reliability_score' => 100,
        ]);

        // Xử lý upload hình ảnh
        if ($request->hasFile('images')) {
            $directory = 'branches/' . $branch->branch_code;
            $primaryImageIndex = $request->input('primary_image', 0);

            foreach ($request->file('images') as $index => $image) {
                try {
                    $filename = $directory . '/' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                    
                    // Upload to S3
                    $putResult = Storage::disk('s3')->put($filename, file_get_contents($image));
                    
                    if ($putResult) {
                        $isPrimary = ($index == $primaryImageIndex);
                        
                        BranchImage::create([
                            'branch_id' => $branch->id,
                            'image_path' => $filename,
                            'caption' => $request->input("captions.$index", ''),
                            'is_primary' => $isPrimary,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading branch image: ' . $e->getMessage());
                    // Tiếp tục với ảnh tiếp theo
                }
            }
        }

        // Gửi thông báo cho quản lý nếu có
        if ($validated['manager_user_id']) {
            $manager = User::find($validated['manager_user_id']);
            if ($manager && $manager->active) {
                $manager->notify(new BranchManagerAssigned($branch, $manager));
            }
        }

        DB::commit();

        return redirect()->route('admin.branches.show', $branch->id)->with([
            'toast' => [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã tạo chi nhánh mới thành công'
            ]
        ]);

    } catch (ValidationException $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating branch: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Có lỗi xảy ra khi tạo chi nhánh: ' . $e->getMessage())
            ->withInput();
    }
}
}

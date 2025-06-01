<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchImage;
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
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'primary_image' => 'nullable|integer|min:0',
                'captions' => 'nullable|array',
                'captions.*' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            // Tạo mã chi nhánh
            $branchCode = 'BR' . str_pad((Branch::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

            // Tạo chi nhánh mới
            $branch = new Branch();
            $branch->branch_code = $branchCode;
            $branch->name = $validated['name'];
            $branch->address = $validated['address'];
            $branch->phone = $validated['phone'];
            $branch->email = $validated['email'] ?? null;
            $branch->opening_hour = $validated['opening_hour'];
            $branch->closing_hour = $validated['closing_hour'];
            $branch->latitude = $validated['latitude'] ?? null;
            $branch->longitude = $validated['longitude'] ?? null;
            $branch->manager_user_id = $validated['manager_user_id'] ?? null;
            $branch->active = $request->has('active') ? true : false;
            $branch->balance = 0.00; // Mặc định từ migration
            $branch->rating = 5.00; // Mặc định từ migration
            $branch->reliability_score = 100; // Mặc định từ migration
            $branch->save();

            // Upload images to S3
            if ($request->hasFile('images')) {
                $directory = 'branches/' . $branch->branch_code;
                $primaryImageIndex = $request->input('primary_image', 0);

                foreach ($request->file('images') as $index => $image) {
                    $filename = $directory . '/' . \Illuminate\Support\Str::uuid() . '.' . $image->getClientOriginalExtension();
                    // Upload to S3
                    $putResult = Storage::disk('s3')->put($filename, file_get_contents($image));
                    if ($putResult) {
                        BranchImage::create([
                            'branch_id' => $branch->id,
                            'image_path' => $filename, // S3 path
                            'caption' => $request->input("captions.$index", ''),
                            'is_primary' => ($index == $primaryImageIndex),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.branches.index')->with([
                'toast' => [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Đã thêm chi nhánh mới thành công'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating branch: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo chi nhánh: ' . $e->getMessage() .
                    "\nFile: " . $e->getFile() .
                    "\nLine: " . $e->getLine() .
                    "\nTrace: " . $e->getTraceAsString())
                ->withInput();
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

    public function edit($id)
    {
        try {
            $branch = Branch::findOrFail($id);

            $managers = User::whereHas('roles', function ($query) {
                $query->where('name', 'manager');
            })
                ->where('active', true)
                ->orderBy('full_name', 'asc')
                ->get();

            $assignedBranches = Branch::whereNotNull('manager_user_id')
                ->where('active', true)
                ->pluck('manager_user_id')
                ->toArray();

            $availableManagers = $managers->filter(function ($manager) use ($assignedBranches, $branch) {
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
    public function update(Request $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);

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
                'primary_image' => 'nullable|string',
                'captions' => 'nullable|array',
                'captions.*' => 'nullable|string|max:255',
                's3_keys' => 'nullable|array',
                's3_keys.*' => 'string',
                'content_types' => 'nullable|array',
                'content_types.*' => 'string',
                'original_names' => 'nullable|array',
                'original_names.*' => 'string',
                'delete_images' => 'nullable|array',
                'delete_images.*' => 'exists:branch_images,id'
            ]);

            DB::beginTransaction();

            // Update branch details
            $branch->name = $validated['name'];
            $branch->address = $validated['address'];
            $branch->phone = $validated['phone'];
            $branch->email = $validated['email'] ?? null;
            $branch->opening_hour = $validated['opening_hour'];
            $branch->closing_hour = $validated['closing_hour'];
            $branch->latitude = $validated['latitude'] ?? null;
            $branch->longitude = $validated['longitude'] ?? null;
            $branch->manager_user_id = $validated['manager_user_id'] ?? null;
            $branch->active = $request->has('active');
            $branch->save();

            // Handle image deletions
            if ($request->has('delete_images')) {
                foreach ($request->input('delete_images') as $imageId) {
                    $image = BranchImage::find($imageId);
                    if ($image) {

                        if (Storage::disk('s3')->exists($image->image_path)) {
                            Storage::disk('s3')->delete($image->image_path);
                        }
                        $image->delete();
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $directory = 'branches/' . $branch->branch_code;
                $s3Keys = $request->input('s3_keys', []);
                $contentTypes = $request->input('content_types', []);
                $originalNames = $request->input('original_names', []);
                $primaryImage = $request->input('primary_image');

                foreach ($request->file('images') as $index => $image) {
                    $s3Key = $s3Keys[$index] ?? null;
                    if (!$s3Key) continue;

                    // Upload to S3 with content type
                    $options = [
                        'ContentType' => $contentTypes[$index] ?? $image->getMimeType(),
                        'ACL' => 'public-read'
                    ];

                    if (Storage::disk('s3')->put($s3Key, file_get_contents($image), $options)) {
                        $branchImage = new BranchImage([
                            'branch_id' => $branch->id,
                            'image_path' => $s3Key,
                            'caption' => $request->input("captions.$s3Key", ''),
                            'is_primary' => ($s3Key === $primaryImage),
                            'original_name' => $originalNames[$index] ?? $image->getClientOriginalName()
                        ]);
                        $branchImage->save();
                    }
                }
            }

            // Update primary image for existing images
            if ($request->has('primary_image')) {
                $primaryImageId = $request->input('primary_image');
                if (is_numeric($primaryImageId)) {
                    BranchImage::where('branch_id', $branch->id)
                        ->update(['is_primary' => false]);
                    BranchImage::where('id', $primaryImageId)
                        ->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Chi nhánh đã được cập nhật thành công',
                'redirect' => route('admin.branches.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật chi nhánh: ' . $e->getMessage()
            ], 500);
        }
    }

public function toggleStatus($id)
{
    try {
        $branch = Branch::findOrFail($id);

        // Check if branch has active manager
        if ($branch->manager_user_id) {
            $hasActiveManager = User::where('id', $branch->manager_user_id)
                ->where('active', true)
                ->exists();

            if ($hasActiveManager && $branch->active) {
                throw new \Exception('Không thể thay đổi trạng thái chi nhánh đang có quản lý HOẠT ĐỘNG');
            }
        }

        $branch->active = !$branch->active;
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

        $count = Branch::whereIn('id', $branchIds)->update(['active' => $status]);

        DB::commit();

        // Handle AJAX response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully updated status for $count branches",
                'count' => $count
            ]);
        }

        // Handle regular form response
        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Success',
            'message' => "Successfully updated status for $count branches"
        ]);

        return redirect()->back();

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in BranchController@bulkStatusUpdate: ' . $e->getMessage());

        // Handle AJAX error
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred: ' . $e->getMessage()
            ], 500);
        }

        // Handle regular form error
        session()->flash('toast', [
            'type' => 'error',
            'title' => 'Error',
            'message' => 'Error occurred: ' . $e->getMessage()
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
            $branch->manager_user_id = $validated['manager_user_id'];
            $branch->save();

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

            $branch->update(['manager_user_id' => null]);

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

    public function uploadImage(Request $request, $branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
            'set_as_featured' => 'nullable|boolean',
        ]);

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = 'branches/' . $branch->branch_code . '/' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                // Upload to S3
                $path = Storage::disk('s3')->put($filename, file_get_contents($image));
                $branchImage = $branch->images()->create([
                    'image_path' => $filename, // Save S3 path
                    'caption' => null,
                    'is_featured' => false,
                ]);
                $uploadedImages[] = $branchImage;
            }
            // Set the first uploaded image as featured if requested
            if ($request->has('set_as_featured') && $request->boolean('set_as_featured') && count($uploadedImages) > 0) {
                $branch->images()->update(['is_featured' => false]);
                $uploadedImages[0]->is_featured = true;
                $uploadedImages[0]->save();
            }
        }
        return redirect()->route('admin.branches.show', $branch->id)
            ->with([
                'toast' => [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Hình ảnh đã được tải lên thành công'
                ]
            ]);
    }
    public function setFeatured(Request $request, $branchId, $imageId)
    {
        try {
            Log::info('Set featured called', ['branch' => $branchId, 'image' => $imageId]);

            $image = BranchImage::where(['id' => $imageId, 'branch_id' => $branchId])->firstOrFail();
            BranchImage::where('branch_id', $branchId)->update(['is_featured' => false]);
            $image->update(['is_featured' => true]);

            return response()->json([
                'toast' => [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'message' => 'Đã thêm chi nhánh mới thành công'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Set featured failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cài đặt ảnh đại diện: ' . $e->getMessage()
            ], 500);
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
}

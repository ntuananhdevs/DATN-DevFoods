<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Services\BranchService;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }
    public function branchs()
    {
        $branches = Branch::with('images')
            ->where('active', 1)
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('customer.branchs.index', compact('branches'));
    }

    /**
     * Set the selected branch in session and handle cart changes
     */
    public function setSelectedBranch(Request $request)
    {
        try {
            $request->validate([
                'branch_id' => 'required|integer|min:1',
            ]);

            $branchId = $request->branch_id;
            
            // Use BranchService to handle branch selection
            $this->branchService->setSelectedBranch($branchId, true);
            
            // Set cookie as fallback
            $cookie = cookie('selected_branch', $branchId, 60*24*30); // 30 days
            
            return response()->json([
                'success' => true,
                'message' => 'Chi nhánh đã được chọn thành công',
                'branch_id' => $branchId,
                'session_has_branch' => session()->has('selected_branch'),
                'session_branch_id' => session('selected_branch')
            ])->cookie($cookie);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error setting selected branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống'
            ], 500);
        }
    }
    
    /**
     * Find the nearest branch based on coordinates
     */
    public function findNearestBranch(Request $request)
    {
        try {
            $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
            ]);

            $latitude = $request->lat;
            $longitude = $request->lng;
            
            $nearestBranch = $this->branchService->findNearestBranch($latitude, $longitude);
            
            if (!$nearestBranch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy chi nhánh nào gần bạn'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'branch_id' => $nearestBranch->id,
                'branch_name' => $nearestBranch->name,
                'branch_address' => $nearestBranch->address,
                'distance' => round($nearestBranch->distance, 2) // Distance in km
            ]);
        } catch (\Exception $e) {
            Log::error('Error finding nearest branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống'
            ], 500);
        }
    }

    /**
     * Get all active branches
     */
    public function getActiveBranches()
    {
        try {
            $branches = $this->branchService->getActiveBranches();
            
            return response()->json([
                'success' => true,
                'data' => $branches
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting active branches: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống'
            ], 500);
        }
    }

    /**
     * Get current selected branch
     */
    public function getCurrentBranch()
    {
        try {
            $currentBranch = $this->branchService->getCurrentBranch();
            
            if (!$currentBranch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa chọn chi nhánh'
                ]);
            }
            
            $status = $this->branchService->getBranchStatus($currentBranch->id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'branch' => $currentBranch,
                    'status' => $status
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting current branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống'
            ], 500);
        }
    }

    /**
     * Clear selected branch
     */
    public function clearSelectedBranch()
    {
        try {
            $this->branchService->clearSelectedBranch();
            
            // Clear cookie
            $cookie = cookie()->forget('selected_branch');
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa chi nhánh đã chọn'
            ])->cookie($cookie);
        } catch (\Exception $e) {
            Log::error('Error clearing selected branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống'
            ], 500);
        }
    }
}
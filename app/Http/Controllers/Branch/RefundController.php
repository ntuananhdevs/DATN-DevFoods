<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{
    /**
     * Display a listing of refund requests for the branch.
     */
    public function index(Request $request)
    {
        try {
            $status = $request->get('status');
            $search = $request->get('search');
            $branchId = Auth::user()->branch_id; // Assuming branch users have branch_id
            
            $query = RefundRequest::with(['order', 'customer', 'processedBy'])
                ->byBranch($branchId);

            if ($status) {
                $query->byStatus($status);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('refund_code', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($customerQuery) use ($search) {
                          $customerQuery->where('full_name', 'like', "%{$search}%")
                                       ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('order', function($orderQuery) use ($search) {
                          $orderQuery->where('order_code', 'like', "%{$search}%");
                      });
                });
            }

            $refundRequests = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $refundRequests,
                'message' => 'Danh sách yêu cầu hoàn tiền được tải thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching branch refund requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Display the specified refund request.
     */
    public function show($id)
    {
        try {
            $branchId = Auth::user()->branch_id;
            
            $refundRequest = RefundRequest::with([
                'order.orderItems.product',
                'customer',
                'branch',
                'processedBy'
            ])
            ->byBranch($branchId)
            ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $refundRequest,
                'message' => 'Chi tiết yêu cầu hoàn tiền được tải thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching branch refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu hoàn tiền'
            ], 404);
        }
    }

    /**
     * Update refund request status (limited permissions for branch).
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:processing,approved,rejected',
            'admin_note' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $branchId = Auth::user()->branch_id;
            $refundRequest = RefundRequest::with(['order', 'customer'])
                ->byBranch($branchId)
                ->findOrFail($id);
            
            $oldStatus = $refundRequest->status;

            // Branch can only process pending requests
            if ($oldStatus !== RefundRequest::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xử lý các yêu cầu đang chờ duyệt'
                ], 400);
            }

            // Validate status transition for branch
            if (!$this->isValidBranchStatusTransition($oldStatus, $request->status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể chuyển từ trạng thái ' . $oldStatus . ' sang ' . $request->status
                ], 400);
            }

            $refundRequest->update([
                'status' => $request->status,
                'admin_note' => $request->admin_note,
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ]);

            // Send status update message to customer
            $refundRequest->sendStatusUpdateMessage($oldStatus);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $refundRequest->fresh(['order', 'customer', 'branch', 'processedBy']),
                'message' => 'Trạng thái yêu cầu hoàn tiền đã được cập nhật thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating branch refund request status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Approve refund request (branch level).
     */
    public function approve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_note' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $branchId = Auth::user()->branch_id;
            $refundRequest = RefundRequest::with(['order', 'customer'])
                ->byBranch($branchId)
                ->findOrFail($id);

            if ($refundRequest->status !== RefundRequest::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể duyệt các yêu cầu đang chờ xử lý'
                ], 400);
            }

            $refundRequest->update([
                'status' => RefundRequest::STATUS_APPROVED,
                'admin_note' => $request->admin_note,
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ]);

            // Send approval message
            $refundRequest->sendStatusUpdateMessage();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $refundRequest->fresh(['order', 'customer', 'branch', 'processedBy']),
                'message' => 'Yêu cầu hoàn tiền đã được duyệt thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving branch refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi duyệt yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Reject refund request (branch level).
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_note' => 'required|string|min:10|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp lý do từ chối',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $branchId = Auth::user()->branch_id;
            $refundRequest = RefundRequest::with(['order', 'customer'])
                ->byBranch($branchId)
                ->findOrFail($id);

            if ($refundRequest->status !== RefundRequest::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể từ chối các yêu cầu đang chờ xử lý'
                ], 400);
            }

            $refundRequest->update([
                'status' => RefundRequest::STATUS_REJECTED,
                'admin_note' => $request->admin_note,
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ]);

            // Send rejection message
            $refundRequest->sendStatusUpdateMessage();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $refundRequest->fresh(['order', 'customer', 'branch', 'processedBy']),
                'message' => 'Yêu cầu hoàn tiền đã được từ chối'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting branch refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi từ chối yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Get refund statistics for the branch.
     */
    public function statistics(Request $request)
    {
        try {
            $branchId = Auth::user()->branch_id;
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            
            $query = RefundRequest::byBranch($branchId);

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $stats = [
                'total_requests' => $query->count(),
                'pending_requests' => (clone $query)->byStatus(RefundRequest::STATUS_PENDING)->count(),
                'processing_requests' => (clone $query)->byStatus(RefundRequest::STATUS_PROCESSING)->count(),
                'approved_requests' => (clone $query)->byStatus(RefundRequest::STATUS_APPROVED)->count(),
                'completed_requests' => (clone $query)->byStatus(RefundRequest::STATUS_COMPLETED)->count(),
                'rejected_requests' => (clone $query)->byStatus(RefundRequest::STATUS_REJECTED)->count(),
                'total_refunded_amount' => (clone $query)->byStatus(RefundRequest::STATUS_COMPLETED)->sum('refund_amount'),
                'average_refund_amount' => (clone $query)->byStatus(RefundRequest::STATUS_COMPLETED)->avg('refund_amount'),
                'total_pending_amount' => (clone $query)->whereIn('status', [RefundRequest::STATUS_PENDING, RefundRequest::STATUS_PROCESSING, RefundRequest::STATUS_APPROVED])->sum('refund_amount')
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Thống kê hoàn tiền được tải thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching branch refund statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thống kê hoàn tiền'
            ], 500);
        }
    }

    /**
     * Check if status transition is valid for branch users.
     */
    private function isValidBranchStatusTransition($fromStatus, $toStatus): bool
    {
        $validTransitions = [
            RefundRequest::STATUS_PENDING => [RefundRequest::STATUS_PROCESSING, RefundRequest::STATUS_APPROVED, RefundRequest::STATUS_REJECTED]
        ];

        return in_array($toStatus, $validTransitions[$fromStatus] ?? []);
    }
}
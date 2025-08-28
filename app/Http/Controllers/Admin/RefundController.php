<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\User;
use App\Events\Chat\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{
    /**
     * Display a listing of refund requests.
     */
    public function index(Request $request)
    {
        try {
            $status = $request->get('status');
            $branchId = $request->get('branch_id');
            $search = $request->get('search');
            
            $query = RefundRequest::with(['order', 'customer', 'branch', 'processedBy']);

            if ($status) {
                $query->byStatus($status);
            }

            if ($branchId) {
                $query->byBranch($branchId);
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

            // Check if request expects JSON (for AJAX calls)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $refundRequests,
                    'message' => 'Danh sách yêu cầu hoàn tiền được tải thành công'
                ]);
            }

            // Return view for web interface
            return view('admin.refunds.index', compact('refundRequests'));
        } catch (\Exception $e) {
            Log::error('Error fetching refund requests: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tải danh sách yêu cầu hoàn tiền'
                ], 500);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Có lỗi xảy ra khi tải danh sách yêu cầu hoàn tiền');
        }
    }

    /**
     * Display the specified refund request.
     */
    public function show(Request $request, $id)
    {
        try {
            $refundRequest = RefundRequest::with([
                'order.orderItems.product',
                'customer',
                'branch',
                'processedBy'
            ])->findOrFail($id);

            // Check if request expects JSON (for AJAX calls)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $refundRequest,
                    'message' => 'Chi tiết yêu cầu hoàn tiền được tải thành công'
                ]);
            }

            // Return view for web interface
            return view('admin.refunds.show', compact('refundRequest', 'id'));
        } catch (\Exception $e) {
            Log::error('Error fetching refund request: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu hoàn tiền'
                ], 404);
            }
            
            return redirect()->route('admin.refunds.index')
                ->with('error', 'Không tìm thấy yêu cầu hoàn tiền');
        }
    }

    /**
     * Update refund request status.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,approved,rejected,completed',
            'admin_note' => 'nullable|string|max:1000',
            'refund_amount' => 'nullable|numeric|min:0'
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

            $refundRequest = RefundRequest::with(['order', 'customer'])->findOrFail($id);
            $oldStatus = $refundRequest->status;

            // Validate status transition
            if (!$this->isValidStatusTransition($oldStatus, $request->status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể chuyển từ trạng thái ' . $oldStatus . ' sang ' . $request->status
                ], 400);
            }

            // Update refund amount if provided
            $updateData = [
                'status' => $request->status,
                'admin_note' => $request->admin_note,
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ];

            if ($request->has('refund_amount')) {
                $updateData['refund_amount'] = $request->refund_amount;
            }

            if ($request->status === RefundRequest::STATUS_COMPLETED) {
                $updateData['completed_at'] = now();
                
                // Process the actual refund to user balance
                $this->processRefundToBalance($refundRequest);
            }

            $refundRequest->update($updateData);

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
            Log::error('Error updating refund request status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Approve refund request.
     */
    public function approve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_note' => 'nullable|string|max:1000',
            'refund_amount' => 'nullable|numeric|min:0'
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

            $refundRequest = RefundRequest::with(['order', 'customer'])->findOrFail($id);

            if (!$refundRequest->canBeProcessed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu hoàn tiền này không thể được xử lý'
                ], 400);
            }

            $updateData = [
                'status' => RefundRequest::STATUS_APPROVED,
                'admin_note' => $request->admin_note,
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ];

            if ($request->has('refund_amount')) {
                $updateData['refund_amount'] = $request->refund_amount;
            }

            $refundRequest->update($updateData);

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
            Log::error('Error approving refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi duyệt yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Reject refund request.
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

            $refundRequest = RefundRequest::with(['order', 'customer'])->findOrFail($id);

            if (!$refundRequest->canBeProcessed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu hoàn tiền này không thể được xử lý'
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
            Log::error('Error rejecting refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi từ chối yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Complete refund request and process payment.
     */
    public function complete($id)
    {
        try {
            DB::beginTransaction();

            $refundRequest = RefundRequest::with(['order', 'customer'])->findOrFail($id);

            if ($refundRequest->status !== RefundRequest::STATUS_APPROVED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể hoàn thành các yêu cầu đã được duyệt'
                ], 400);
            }

            // Process refund to user balance
            $this->processRefundToBalance($refundRequest);

            $refundRequest->update([
                'status' => RefundRequest::STATUS_COMPLETED,
                'completed_at' => now()
            ]);

            // Send completion message
            $refundRequest->sendStatusUpdateMessage();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $refundRequest->fresh(['order', 'customer', 'branch', 'processedBy']),
                'message' => 'Yêu cầu hoàn tiền đã được hoàn thành thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hoàn thành yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Get refund statistics.
     */
    public function statistics(Request $request)
    {
        try {
            $branchId = $request->get('branch_id');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            
            $query = RefundRequest::query();

            if ($branchId) {
                $query->byBranch($branchId);
            }

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
            Log::error('Error fetching refund statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thống kê hoàn tiền'
            ], 500);
        }
    }

    /**
     * Process refund to user balance.
     */
    private function processRefundToBalance(RefundRequest $refundRequest)
    {
        $customer = $refundRequest->customer;
        $customer->increment('balance', $refundRequest->refund_amount);

        Log::info('Refund processed to user balance', [
            'refund_request_id' => $refundRequest->id,
            'customer_id' => $customer->id,
            'refund_amount' => $refundRequest->refund_amount,
            'new_balance' => $customer->fresh()->balance
        ]);
    }

    /**
     * Check if status transition is valid.
     */
    private function isValidStatusTransition($fromStatus, $toStatus): bool
    {
        $validTransitions = [
            RefundRequest::STATUS_PENDING => [RefundRequest::STATUS_PROCESSING, RefundRequest::STATUS_APPROVED, RefundRequest::STATUS_REJECTED],
            RefundRequest::STATUS_PROCESSING => [RefundRequest::STATUS_APPROVED, RefundRequest::STATUS_REJECTED],
            RefundRequest::STATUS_APPROVED => [RefundRequest::STATUS_COMPLETED, RefundRequest::STATUS_REJECTED],
            RefundRequest::STATUS_REJECTED => [], // No transitions from rejected
            RefundRequest::STATUS_COMPLETED => [] // No transitions from completed
        ];

        return in_array($toStatus, $validTransitions[$fromStatus] ?? []);
    }
}
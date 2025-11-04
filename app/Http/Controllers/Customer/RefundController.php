<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Models\User;
use App\Events\Chat\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    /**
     * Display a listing of customer's refund requests.
     */
    public function index(Request $request)
    {
        try {
            $customerId = Auth::id();
            
            $refundRequests = RefundRequest::with(['order', 'branch'])
                ->byCustomer($customerId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $refundRequests,
                'message' => 'Danh sách yêu cầu hoàn tiền được tải thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching refund requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new refund request.
     */
    public function create(Request $request)
    {
        try {
            $orderId = $request->get('order_id');
            
            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp ID đơn hàng'
                ], 400);
            }

            $order = Order::with(['branch', 'payment'])
                ->where('id', $orderId)
                ->where('customer_id', Auth::id())
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng hoặc bạn không có quyền truy cập'
                ], 404);
            }

            // Check if order is eligible for refund
            if (!$this->isOrderEligibleForRefund($order)) {
                Log::info('Order not eligible for refund', [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'payment_method' => $order->payment?->payment_method ?? 'null',
                    'customer_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng này không đủ điều kiện để hoàn tiền',
                    'order_status' => $order->status,
                    'payment_method' => $order->payment?->payment_method ?? 'null'
                ], 400);
            }

            // Check if refund request already exists
            $existingRefund = RefundRequest::where('order_id', $orderId)
                ->whereIn('status', [RefundRequest::STATUS_PENDING, RefundRequest::STATUS_PROCESSING, RefundRequest::STATUS_APPROVED])
                ->first();

            if ($existingRefund) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã có yêu cầu hoàn tiền cho đơn hàng này',
                    'existing_refund' => $existingRefund
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order,
                    'max_refund_amount' => $order->total_amount,
                    'payment_method' => $order->payment?->payment_method ?? 'unknown'
                ],
                'message' => 'Thông tin đơn hàng được tải thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error preparing refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi chuẩn bị yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Store a newly created refund request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'refund_amount' => 'required|numeric|min:1000',
            'refund_type' => 'required|in:full,partial',
            'reason' => 'required|string',
            'customer_message' => 'required|string|min:10|max:2000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240' // 10MB max
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

            $customerId = Auth::id();
            $order = Order::with(['branch', 'payment'])
                ->where('id', $request->order_id)
                ->where('customer_id', $customerId)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // Validate refund eligibility
            if (!$this->isOrderEligibleForRefund($order)) {
                Log::info('Order not eligible for refund in store method', [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'payment_method' => $order->payment?->payment_method ?? 'null',
                    'customer_id' => Auth::id(),
                    'request_data' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng không đủ điều kiện hoàn tiền',
                    'order_status' => $order->status,
                    'payment_method' => $order->payment?->payment_method ?? 'null'
                ], 400);
            }

            // Validate refund amount
            if ($request->refund_amount > $order->total_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền hoàn không được vượt quá tổng giá trị đơn hàng'
                ], 400);
            }

            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('refund-attachments', 'public');
                    $attachments[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ];
                }
            }

            // Create refund request
            $refundRequest = RefundRequest::create([
                'order_id' => $order->id,
                'customer_id' => $customerId,
                'branch_id' => $order->branch_id,
                'refund_amount' => $request->refund_amount,
                'refund_type' => $request->refund_type,
                'reason' => $request->reason,
                'customer_message' => $request->customer_message,
                'attachments' => $attachments,
                'status' => RefundRequest::STATUS_PENDING
            ]);

            // Send system message to chat
            $refundRequest->sendStatusUpdateMessage();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $refundRequest->load(['order', 'branch']),
                'message' => 'Yêu cầu hoàn tiền đã được tạo thành công'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Display the specified refund request.
     */
    public function show($id)
    {
        try {
            $refundRequest = RefundRequest::with(['order', 'branch', 'processedBy'])
                ->byCustomer(Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $refundRequest,
                'message' => 'Chi tiết yêu cầu hoàn tiền được tải thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu hoàn tiền'
            ], 404);
        }
    }

    /**
     * Cancel a pending refund request.
     */
    public function cancel($id)
    {
        try {
            $refundRequest = RefundRequest::byCustomer(Auth::id())
                ->where('status', RefundRequest::STATUS_PENDING)
                ->findOrFail($id);

            $refundRequest->update([
                'status' => RefundRequest::STATUS_REJECTED,
                'admin_note' => 'Đã hủy bởi khách hàng',
                'processed_at' => now()
            ]);

            // Send cancellation message
            $refundRequest->sendStatusUpdateMessage(RefundRequest::STATUS_PENDING);

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu hoàn tiền đã được hủy thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling refund request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy yêu cầu hoàn tiền'
            ], 500);
        }
    }

    /**
     * Get refund history for customer.
     */
    public function history(Request $request)
    {
        try {
            $customerId = Auth::id();
            $status = $request->get('status');
            
            $query = RefundRequest::with(['order', 'branch'])
                ->byCustomer($customerId);

            if ($status) {
                $query->byStatus($status);
            }

            $refundRequests = $query->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $refundRequests,
                'message' => 'Lịch sử hoàn tiền được tải thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching refund history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lịch sử hoàn tiền'
            ], 500);
        }
    }

    /**
     * Check if order is eligible for refund.
     */
    private function isOrderEligibleForRefund(Order $order): bool
    {
        // Only allow refund for item_received and completed orders with VNPAY or COD payment
        $eligibleStatuses = ['item_received', 'completed'];
        $eligiblePaymentMethods = ['vnpay', 'cod'];
        
        $paymentMethod = $order->payment?->payment_method ?? '';
        
        return in_array($order->status, $eligibleStatuses) && 
               in_array($paymentMethod, $eligiblePaymentMethods);
    }

    /**
     * Get refund statistics for customer.
     */
    public function statistics()
    {
        try {
            $customerId = Auth::id();
            
            $stats = [
                'total_requests' => RefundRequest::byCustomer($customerId)->count(),
                'pending_requests' => RefundRequest::byCustomer($customerId)->byStatus(RefundRequest::STATUS_PENDING)->count(),
                'approved_requests' => RefundRequest::byCustomer($customerId)->byStatus(RefundRequest::STATUS_APPROVED)->count(),
                'completed_requests' => RefundRequest::byCustomer($customerId)->byStatus(RefundRequest::STATUS_COMPLETED)->count(),
                'rejected_requests' => RefundRequest::byCustomer($customerId)->byStatus(RefundRequest::STATUS_REJECTED)->count(),
                'total_refunded_amount' => RefundRequest::byCustomer($customerId)
                    ->byStatus(RefundRequest::STATUS_COMPLETED)
                    ->sum('refund_amount')
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
}
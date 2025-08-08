<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverRating;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverRatingController extends Controller
{
    /**
     * Hiển thị form đánh giá tài xế
     *
     * @param int $orderId
     * @return \Illuminate\Http\Response
     */
    public function showRatingForm(Request $request, $orderId)
    {
        $order = Order::with('driver')->findOrFail($orderId);
        
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->customer_id != Auth::id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền đánh giá đơn hàng này.'
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }
            return redirect()->route('customer.orders.index')
                ->with('error', 'Bạn không có quyền đánh giá đơn hàng này.');
        }
        
        // Kiểm tra xem đơn hàng đã được xác nhận nhận hàng chưa
        if ($order->status != 'item_received') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chỉ có thể đánh giá tài xế sau khi xác nhận đã nhận hàng.'
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Bạn chỉ có thể đánh giá tài xế sau khi xác nhận đã nhận hàng.');
        }
        
        // Kiểm tra xem đơn hàng có tài xế không
        if (!$order->driver_id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng này không có tài xế để đánh giá.'
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Đơn hàng này không có tài xế để đánh giá.');
        }
        
        // Kiểm tra xem đã đánh giá tài xế chưa
        $existingRating = DriverRating::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->first();
        
        return view('customer.orders.rate_driver', [
            'order' => $order,
            'existingRating' => $existingRating
        ]);
    }
    
    /**
     * Lưu đánh giá tài xế
     *
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\Response
     */
    public function submitRating(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'is_anonymous' => 'nullable|boolean'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu đánh giá không hợp lệ.',
                    'errors' => $validator->errors()
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $order = Order::with('driver')->findOrFail($orderId);
        
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->customer_id != Auth::id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền đánh giá đơn hàng này.'
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }
            
            return redirect()->route('customer.orders.index')
                ->with('error', 'Bạn không có quyền đánh giá đơn hàng này.');
        }
        
        // Kiểm tra xem đơn hàng đã được xác nhận nhận hàng chưa
        if ($order->status != 'item_received') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chỉ có thể đánh giá tài xế sau khi xác nhận đã nhận hàng.'
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Bạn chỉ có thể đánh giá tài xế sau khi xác nhận đã nhận hàng.');
        }
        
        // Kiểm tra xem đơn hàng có tài xế không
        if (!$order->driver_id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng này không có tài xế để đánh giá.'
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Đơn hàng này không có tài xế để đánh giá.');
        }
        
        // Tạo hoặc cập nhật đánh giá
        $rating = DriverRating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'driver_id' => $order->driver_id,
                'order_id' => $order->id
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_anonymous' => $request->has('is_anonymous'),
                'rated_at' => Carbon::now()
            ]
        );
        
        // Cập nhật thống kê đánh giá của tài xế
        $order->driver->updateRatingStatistics();
        
        // Kiểm tra nếu là AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã đánh giá tài xế!'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        
        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Cảm ơn bạn đã đánh giá tài xế!');
    }
}
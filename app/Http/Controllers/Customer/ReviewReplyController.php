<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\ReviewReply;
use Illuminate\Support\Facades\Auth;

class ReviewReplyController extends Controller
{
    /**
     * Lưu phản hồi cho review sản phẩm
     */
    public function store(Request $request, $reviewId)
    {
        $request->validate([
            'reply' => 'required|string|max:2000',
        ]);

        $review = ProductReview::findOrFail($reviewId);
        $user = Auth::user();
        $productId = $review->product_id;
        // Kiểm tra user đã mua sản phẩm này chưa
        $order = \App\Models\Order::where('customer_id', $user->id)
            ->where('status', 'delivered')
            ->whereHas('orderItems.productVariant', function($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->first();
        if (!$order) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bạn chỉ có thể phản hồi khi đã mua sản phẩm này!'], 403);
            }
            return back()->with('error', 'Bạn chỉ có thể phản hồi khi đã mua sản phẩm này!');
        }

        $reply = new ReviewReply();
        $reply->review_id = $review->id;
        $reply->user_id = $user->id;
        $reply->reply = $request->input('reply');
        $reply->reply_date = now();
        $reply->is_official = $user->is_admin ?? false;
        $reply->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Phản hồi thành công!']);
        }
        return back()->with('success', 'Phản hồi thành công!');
    }

    /**
     * Xóa phản hồi review
     */
    public function destroy($id)
    {
        \Log::info('GỌI XOÁ REPLY', ['reply_id' => $id, 'user_id' => \Auth::id()]);
        $reply = \App\Models\ReviewReply::findOrFail($id);
        $user = \Auth::user();
        if ($reply->user_id !== $user->id && !($user->is_admin ?? false)) {
            \Log::warning('KHÔNG CÓ QUYỀN XOÁ REPLY', ['reply_id' => $id, 'user_id' => $user->id]);
            return response()->json(['message' => 'Bạn không có quyền xóa phản hồi này!'], 403);
        }
        $reply->delete();
        \Log::info('XOÁ REPLY THÀNH CÔNG', ['reply_id' => $id, 'user_id' => $user->id]);
        return response()->json(['message' => 'Xóa phản hồi thành công!']);
    }
}

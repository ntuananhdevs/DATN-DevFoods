<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\ReviewReply;
use App\Models\ReviewReport;

class ReviewController extends Controller
{
    // Hiển thị danh sách bình luận
    public function index(Request $request)
    {
        $query = ProductReview::with(['user', 'product', 'replies.user'])
            ->when($request->input('status'), function ($q) use ($request) {
                if ($request->status === 'approved') $q->where('approved', true);
                elseif ($request->status === 'pending') $q->where('approved', false);
                elseif ($request->status === 'hidden') $q->where('is_hidden', true);
            })
            ->when($request->input('keyword'), function ($q) use ($request) {
                $keyword = $request->keyword;
                $q->where(function($sub) use ($keyword) {
                    $sub->whereHas('user', function($u) use ($keyword) {
                        $u->where('full_name', 'like', "%$keyword%");
                    })->orWhereHas('product', function($p) use ($keyword) {
                        $p->where('name', 'like', "%$keyword%");
                    })->orWhere('review', 'like', "%$keyword%");
                });
            })
            ->when($request->input('rating'), fn($q) => $q->where('rating', $request->rating))
            ->orderByRaw('approved = 0 DESC') // Chờ duyệt lên đầu
            ->orderByDesc('review_date');

        $reviews = $query->paginate(20)->appends($request->all());
        return view('admin.reviews.index', compact('reviews'));
    }

    // Duyệt bình luận
    public function approve($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->approved = true;
        $review->save();
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Duyệt bình luận thành công!'
            ]);
        }
        return back()->with('success', 'Duyệt bình luận thành công!');
    }

    // Xóa bình luận
    public function destroy($id)
    {
        $review = ProductReview::with('replies', 'reports')->findOrFail($id);
        $review->replies()->delete();
        $review->reports()->delete();
        $review->delete();
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa bình luận thành công!'
            ]);
        }
        return back()->with('success', 'Xóa bình luận thành công!');
    }

    // Ẩn/hiện bình luận
    public function hide($id)
    {
        // Không còn chức năng ẩn/hiện, trả về lỗi hoặc thông báo không hỗ trợ
        return back()->with('error', 'Chức năng ẩn/hiện bình luận đã bị tắt.');
    }

    // Trả lời bình luận
    public function reply(Request $request, $reviewId)
    {
        $request->validate([
            'reply' => 'required|string|max:2000',
        ]);
        $review = ProductReview::findOrFail($reviewId);
        $admin = auth('admin')->user();
        $reply = new ReviewReply();
        $reply->review_id = $review->id;
        $reply->user_id = $admin->id;
        $reply->reply = $request->input('reply');
        $reply->reply_date = now();
        $reply->is_official = true;
        $reply->is_hidden = false;
        $reply->save();
        return back()->with('success', 'Phản hồi thành công!');
    }

    // Danh sách báo cáo vi phạm
    public function reports(Request $request)
    {
        $reports = ReviewReport::with(['review.user', 'review.product'])
            ->when($request->input('reason_type'), fn($q) => $q->where('reason_type', $request->reason_type))
            ->orderByDesc('created_at')
            ->paginate(20)->appends($request->all());
        return view('admin.reviews.reports', compact('reports'));
    }

    // Xem chi tiết bình luận
    public function show($id)
    {
        $review = ProductReview::with(['user', 'product', 'replies.user'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    // Lọc bình luận
    public function filter(Request $request)
    {
        return $this->index($request);
    }
} 
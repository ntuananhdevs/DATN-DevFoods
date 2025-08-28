<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\ReviewReply;
use App\Models\ReviewReport;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    // Hiển thị danh sách bình luận của chi nhánh
    public function index(Request $request)
    {
        $branchId = auth('manager')->user()->branch->id;
        
        $query = ProductReview::with(['user', 'product', 'combo', 'replies.user'])
            ->where('branch_id', $branchId)
            ->when($request->input('keyword'), function ($q) use ($request) {
                $keyword = $request->keyword;
                $q->where(function($sub) use ($keyword) {
                    $sub->whereHas('user', function($u) use ($keyword) {
                        $u->where('full_name', 'like', "%$keyword%");
                    })->orWhereHas('product', function($p) use ($keyword) {
                        $p->where('name', 'like', "%$keyword%");
                    })->orWhereHas('combo', function($c) use ($keyword) {
                        $c->where('name', 'like', "%$keyword%");
                    })->orWhere('review', 'like', "%$keyword%");
                });
            })
            ->when($request->input('rating'), fn($q) => $q->where('rating', $request->rating))
            ->orderByDesc('review_date');

        $reviews = $query->paginate(20)->appends($request->all());
        return view('branch.reviews.index', compact('reviews'));
    }

    // Trả lời bình luận
    public function reply(Request $request, $reviewId)
    {
        try {
            $request->validate([
                'reply' => 'required|string|max:2000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first(),
                    'errors' => $e->validator->errors()
                ], 422);
            }
            throw $e;
        }
        
        try {
            $branchId = auth('manager')->user()->branch->id;
            $review = ProductReview::where('branch_id', $branchId)->findOrFail($reviewId);
            
            $manager = auth('manager')->user();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bình luận hoặc bạn không có quyền truy cập.'
                ], 404);
            }
            return back()->with('error', 'Không tìm thấy bình luận.');
        }
        $reply = new ReviewReply();
        $reply->review_id = $review->id;
        $reply->user_id = $manager->id;
        $reply->reply = $request->input('reply');
        $reply->reply_date = now();
        $reply->is_official = true;
        $reply->is_hidden = false;
        $reply->save();
        
        // Load user relationship for response
        $reply->load('user');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Phản hồi thành công!',
                'reply' => [
                    'id' => $reply->id,
                    'review_id' => $reply->review_id,
                    'reply' => $reply->reply,
                    'reply_date' => $reply->reply_date,
                    'is_official' => $reply->is_official,
                    'user' => [
                        'id' => $reply->user->id,
                        'name' => $reply->user->full_name ?? 'Chi nhánh'
                    ]
                ]
            ]);
        }
        
        return back()->with('success', 'Phản hồi thành công!');
    }

    // Xóa phản hồi
    public function deleteReply($replyId)
    {
        try {
            $branchId = auth('manager')->user()->branch->id;
            
            // Tìm reply và kiểm tra quyền
            $reply = ReviewReply::with(['review', 'user'])->whereHas('review', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->where('is_official', true)->findOrFail($replyId);
            
            // Trigger event before deleting
            event(new \App\Events\Branch\ReplyDeletedNotification($reply));
            
            $reply->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa phản hồi thành công!'
                ]);
            }
            
            return back()->with('success', 'Xóa phản hồi thành công!');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa phản hồi!'
                ], 500);
            }
            return back()->with('error', 'Có lỗi xảy ra khi xóa phản hồi!');
        }
    }

    // Danh sách báo cáo vi phạm của chi nhánh
    public function reports(Request $request)
    {
        $branchId = auth('manager')->user()->branch->id;
        
        $reports = ReviewReport::with(['review.user', 'review.product', 'review.combo'])
            ->whereHas('review', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->when($request->input('reason_type'), fn($q) => $q->where('reason_type', $request->reason_type))
            ->orderByDesc('created_at')
            ->paginate(20)->appends($request->all());
            
        return view('branch.reviews.reports', compact('reports'));
    }

    // Xem chi tiết bình luận
    public function show($id)
    {
        $branchId = auth('manager')->user()->branch->id;
        $review = ProductReview::with(['user', 'product', 'combo', 'replies.user'])
            ->where('branch_id', $branchId)
            ->findOrFail($id);
            
        return view('branch.reviews.show', compact('review'));
    }

    // Xem chi tiết báo cáo bình luận
    public function showReport($id)
    {
        $branchId = auth('manager')->user()->branch->id;
        $review = ProductReview::with(['user', 'product', 'combo', 'reports.user'])
            ->where('branch_id', $branchId)
            ->findOrFail($id);
            
        $reports = $review->reports;
        return view('branch.reviews.show-report', compact('review', 'reports'));
    }

    // Xóa bình luận (chỉ khi có >= 5 báo cáo)
    public function deleteReview($reviewId)
    {
        try {
            $branchId = auth('manager')->user()->branch->id;
            
            $review = ProductReview::with('reports')
                ->where('branch_id', $branchId)
                ->findOrFail($reviewId);
            
            // Kiểm tra số lượng báo cáo
            if ($review->reports->count() < 5) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cần ít nhất 5 báo cáo để có thể xóa bình luận này!'
                    ], 400);
                }
                return back()->with('error', 'Cần ít nhất 5 báo cáo để có thể xóa bình luận này!');
            }
            
            // Observer sẽ tự động trigger event khi delete
            $review->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa bình luận vi phạm thành công!'
                ]);
            }
            
            return redirect()->route('branch.reviews.reports')->with('success', 'Đã xóa bình luận vi phạm thành công!');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting review: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa bình luận!'
                ], 500);
            }
            return back()->with('error', 'Có lỗi xảy ra khi xóa bình luận!');
        }
    }


}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRankHistory;
use App\Models\User;
use App\Models\UserRank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRankHistoryController extends Controller
{
    /**
     * Display a listing of user rank histories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Lấy danh sách user_id có role name là 'customer'
            $customerUserIds = DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->pluck('user_roles.user_id')
                ->toArray();

            // Truy vấn lịch sử thăng hạng của các user có role là customer
            $query = UserRankHistory::with(['user', 'oldRank', 'newRank'])
                ->whereIn('user_id', $customerUserIds)
                ->orderBy('changed_at', 'desc');

            // Filter by user if provided
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by rank if provided
            if ($request->has('rank_id') && $request->rank_id) {
                $query->where('new_rank_id', $request->rank_id);
            }

            // Filter by date range if provided
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('changed_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('changed_at', '<=', $request->date_to);
            }

            // Get the histories with pagination
            $histories = $query->paginate(15);
            
            // Get all ranks for filter dropdown
            $ranks = UserRank::all();
            
            // Tính toán thống kê chỉ cho user có role là customer
            $totalUsers = User::whereIn('id', $customerUserIds)->count();
            $recentUpgrades = UserRankHistory::whereIn('user_id', $customerUserIds)
                ->whereDate('changed_at', '>=', now()->subDays(30))
                ->count();
            $topRank = UserRank::orderBy('min_spending', 'desc')->first();
            $usersInTopRank = User::whereIn('id', $customerUserIds)
                ->where('user_rank_id', $topRank ? $topRank->id : 0)
                ->count();
            
            return view('admin.user_rank_histories.index', compact('histories','ranks','totalUsers','recentUpgrades','topRank','usersInTopRank'));
        } catch (\Exception $e) {
            // Ghi log lỗi nếu cần
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
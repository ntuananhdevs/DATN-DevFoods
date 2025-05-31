<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRank;
use App\Models\User;
use App\Models\DiscountCode;
use App\Models\UserRankHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRankController extends Controller
{
    public function index(Request $request)
    {
        // Existing code remains unchanged
        $search = $request->input('search', '');
        $userMin = $request->input('user_min', 0);
        $userMax = $request->input('user_max', 1500);
        $status = $request->input('status', 'all');

        $query = UserRank::select('user_ranks.*')
            ->leftJoin('users', 'user_ranks.id', '=', 'users.user_rank_id')
            ->groupBy('user_ranks.id')
            ->withCount('users')
            ->orderBy('display_order');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== 'all') {
            $query->where('is_active', $status === 'active');
        }

        $query->havingRaw('COUNT(users.id) >= ?', [$userMin])
            ->havingRaw('COUNT(users.id) <= ?', [$userMax]);

        $ranks = $query->paginate(10);

        $totalUsers = User::count();
        $activeTiersCount = UserRank::where('is_active', true)->count();
        $vipUsersCount = User::whereIn('user_rank_id', UserRank::where('discount_percentage', '>=', 10)->pluck('id'))->count();
        $usersWithUpgrades = UserRankHistory::distinct('user_id')->count('user_id');
        $upgradeRate = $totalUsers > 0 ? round(($usersWithUpgrades / $totalUsers) * 100, 1) : 0;

        $maxUsers = DB::selectOne('
            SELECT MAX(users_count) as max_users
            FROM (
                SELECT COUNT(users.id) as users_count
                FROM user_ranks
                LEFT JOIN users ON user_ranks.id = users.user_rank_id
                GROUP BY user_ranks.id
            ) as subquery
        ')->max_users ?? 1500;
        $minUsers = 0;

        return view('admin.user_ranks.index', compact(
            'ranks',
            'totalUsers',
            'activeTiersCount',
            'vipUsersCount',
            'upgradeRate',
            'minUsers',
            'maxUsers'
        ));
    }

    public function search(Request $request)
    {
        // Validate the request
        $request->validate([
            'search' => 'nullable|string|max:255',
            'user_min' => 'nullable|integer|min:0',
            'user_max' => 'nullable|integer|min:0',
            'status' => 'nullable|in:all,active,inactive'
        ]);

        $search = $request->input('search', '');
        $userMin = $request->input('user_min', 0);
        $userMax = $request->input('user_max', 1500);
        $status = $request->input('status', 'all');

        // Build the query
        $query = UserRank::select('user_ranks.*')
            ->leftJoin('users', 'user_ranks.id', '=', 'users.user_rank_id')
            ->groupBy('user_ranks.id')
            ->withCount('users')
            ->orderBy('display_order');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== 'all') {
            $query->where('is_active', $status === 'active');
        }

        $query->havingRaw('COUNT(users.id) >= ?', [$userMin])
              ->havingRaw('COUNT(users.id) <= ?', [$userMax]);

        // Get the ranks (without pagination for simplicity)
        $ranks = $query->get();

        // Calculate total users for percentage calculations
        $totalUsers = User::count();

        // Format the response
        $response = [
            'ranks' => $ranks->map(function ($rank) {
                // Ensure benefits is an array
                $benefits = $rank->benefits;
                if (is_string($rank->benefits)) {
                    $decoded = json_decode($rank->benefits, true);
                    $benefits = json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : [];
                }
                $benefits = is_array($benefits) ? $benefits : [];

                return [
                    'id' => $rank->id,
                    'name' => $rank->name,
                    'slug' => $rank->slug,
                    'color' => $rank->color,
                    'icon' => $rank->icon,
                    'min_spending' => $rank->min_spending,
                    'min_orders' => $rank->min_orders,
                    'discount_percentage' => $rank->discount_percentage,
                    'benefits' => $benefits,
                    'is_active' => $rank->is_active,
                    'users_count' => $rank->users_count
                ];
            }),
            'total_users' => $totalUsers
        ];

        return response()->json($response);
    }

    public function create()
    {
        return view('admin.user_ranks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:user_ranks',
            'color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'icon' => 'nullable|string|max:255',
            'min_spending' => 'required|numeric|min:0',
            'min_orders' => 'required|integer|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Remove manual JSON encoding; the model cast handles it
        UserRank::create($validated);

        return redirect()->route('admin.user_ranks.index')
            ->with('success', 'Hạng thành viên được tạo thành công.');
    }

    public function show($id)
    {
        $rank = UserRank::withCount('users')->findOrFail($id);
        $users = User::where('user_rank_id', $id)->paginate(10);
        $discountCodes = DiscountCode::whereJsonContains('applicable_ranks', (string)$id)->get();

        return view('admin.user_ranks.show', compact('rank', 'users', 'discountCodes'));
    }

    public function edit($id)
    {
        $userTier = UserRank::findOrFail($id);
        // Optionally add user_count if it's a computed attribute
        $userTier->user_count = $userTier->users()->count(); // Example
        return view('admin.user_ranks.edit', compact('userTier'));
    }

    public function update(Request $request, $id)
    {
        $rank = UserRank::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'icon' => 'nullable|string|max:255',
            'min_spending' => 'required|numeric|min:0',
            'min_orders' => 'required|integer|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Remove manual JSON encoding; the model cast handles it
        $rank->update($validated);

        return redirect()->route('admin.user_ranks.index')
            ->with('success', 'Hạng thành viên được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $rank = UserRank::findOrFail($id);

        if ($rank->users()->count() > 0) {
            return redirect()->route('admin.user_ranks.index')
                ->with('error', 'Không thể xóa hạng vì còn người dùng thuộc hạng này.');
        }

        if ($rank->rankHistoryAsOld()->count() > 0 || $rank->rankHistoryAsNew()->count() > 0) {
            return redirect()->route('admin.user_ranks.index')
                ->with('error', 'Không thể xóa hạng vì có lịch sử thay đổi liên quan.');
        }

        $rank->delete();

        return redirect()->route('admin.user_ranks.index')
            ->with('success', 'Hạng thành viên đã được xóa.');
    }
}

<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchService
{
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get all active branches with caching
     */
    public function getActiveBranches()
    {
        return Cache::remember('active_branches', self::CACHE_TTL, function () {
            return Branch::where('active', true)
                ->select(['id', 'name', 'address', 'phone', 'latitude', 'longitude', 'opening_hour', 'closing_hour'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get branch by ID with validation and caching
     */
    public function getBranchById($branchId)
    {
        if (!$branchId) {
            return null;
        }

        return Cache::remember("branch_{$branchId}", self::CACHE_TTL, function () use ($branchId) {
            return Branch::where('id', $branchId)
                ->where('active', true)
                ->first();
        });
    }

    /**
     * Validate if branch exists and is active
     */
    public function isValidBranch($branchId)
    {
        return $this->getBranchById($branchId) !== null;
    }

    /**
     * Get current selected branch from session
     */
    public function getCurrentBranch()
    {
        $branchId = session('selected_branch');
        
        if (!$branchId) {
            return null;
        }

        $branch = $this->getBranchById($branchId);
        
        // If branch is no longer valid, clear it from session
        if (!$branch) {
            $this->clearSelectedBranch();
            return null;
        }

        return $branch;
    }

    /**
     * Set selected branch with validation and cart handling
     */
    public function setSelectedBranch($branchId, $clearCart = true)
    {
        // Validate branch
        if (!$this->isValidBranch($branchId)) {
            throw new \InvalidArgumentException('Chi nhánh không tồn tại hoặc đã ngừng hoạt động');
        }

        $previousBranchId = session('selected_branch');
        
        // Check if changing branch and clear cart if needed
        if ($clearCart && $previousBranchId && $previousBranchId != $branchId) {
            $this->clearUserCart();
            Log::info("Branch changed from {$previousBranchId} to {$branchId}, cart cleared");
        }

        // Set selected branch in session
        session(['selected_branch' => $branchId]);
        session()->save();
        
        return true;
    }

    /**
     * Clear selected branch from session
     */
    public function clearSelectedBranch()
    {
        session()->forget('selected_branch');
        session()->save();
    }

    /**
     * Clear user cart (both authenticated and guest)
     */
    public function clearUserCart()
    {
        $identifier = auth()->check() ? ['user_id' => auth()->id()] : ['session_id' => session()->getId()];
        
        // Delete cart items first
        CartItem::whereHas('cart', function($query) use ($identifier) {
            $query->where($identifier)->where('status', 'active');
        })->delete();
        
        // Delete the cart
        Cart::where($identifier)->where('status', 'active')->delete();
        
        // Reset cart count in session
        session(['cart_count' => 0]);
        
        Log::info('Cart cleared for ' . (auth()->check() ? 'user: ' . auth()->id() : 'session: ' . session()->getId()));
    }

    /**
     * Find nearest branch based on coordinates
     */
    public function findNearestBranch($latitude, $longitude, $limit = 1)
    {
        $branches = Branch::select([
            'id', 'name', 'address', 'latitude', 'longitude',
            DB::raw("(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance")
        ])
        ->where('active', true)
        ->orderBy('distance', 'asc')
        ->limit($limit)
        ->get();
        
        return $limit === 1 ? $branches->first() : $branches;
    }

    /**
     * Get branch operating hours status
     */
    public function getBranchStatus($branchId)
    {
        $branch = $this->getBranchById($branchId);
        
        if (!$branch) {
            return ['status' => 'closed', 'message' => 'Chi nhánh không tồn tại'];
        }

        $currentTime = now()->format('H:i');
        $openingTime = $branch->opening_hour ? $branch->opening_hour->format('H:i') : '00:00';
        $closingTime = $branch->closing_hour ? $branch->closing_hour->format('H:i') : '23:59';

        $isOpen = $currentTime >= $openingTime && $currentTime <= $closingTime;
        
        return [
            'status' => $isOpen ? 'open' : 'closed',
            'message' => $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa'
        ];
    }

    /**
     * Clear all branch-related cache
     */
    public function clearBranchCache()
    {
        Cache::forget('active_branches');
        
        // Clear individual branch caches
        Branch::pluck('id')->each(function($branchId) {
            Cache::forget("branch_{$branchId}");
        });
    }

    /**
     * Handle branch status change (when admin activates/deactivates)
     */
    public function handleBranchStatusChange($branchId, $isActive)
    {
        $this->clearBranchCache();
        
        // If branch is deactivated and it's currently selected, clear selection
        if (!$isActive && session('selected_branch') == $branchId) {
            $this->clearSelectedBranch();
            $this->clearUserCart();
            Log::info("Branch {$branchId} deactivated, cleared from session and cart");
        }
    }
}
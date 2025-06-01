<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    /**
     * Set the selected branch in session and handle cart changes
     */
    public function setSelectedBranch(Request $request)
    {
        try {
            $request->validate([
                'branch_id' => 'required|exists:branches,id',
            ]);

            $branchId = $request->branch_id;
            $previousBranchId = session('selected_branch');
            
            // Check if changing branch
            if ($previousBranchId && $previousBranchId != $branchId) {
                Log::info('Changing branch from ' . $previousBranchId . ' to ' . $branchId);
                
                // Clear the cart if changing branch
                if (auth()->check()) {
                    $userId = auth()->id();
                    Log::info('Clearing cart for authenticated user: ' . $userId);
                    
                    // Delete cart items
                    $cartItemsDeleted = CartItem::whereHas('cart', function($query) use ($userId) {
                        $query->where('user_id', $userId)
                              ->where('status', 'active');
                    })->delete();
                    
                    // Delete the cart itself
                    $cartDeleted = Cart::where('user_id', $userId)
                        ->where('status', 'active')
                        ->delete();
                    
                    Log::info('Cart cleanup: ' . $cartItemsDeleted . ' items deleted, cart delete result: ' . $cartDeleted);
                } else {
                    // Handle non-logged-in users with session-based carts
                    $sessionId = session()->getId();
                    Log::info('Clearing cart for session: ' . $sessionId);
                    
                    // Delete cart items
                    $cartItemsDeleted = CartItem::whereHas('cart', function($query) use ($sessionId) {
                        $query->where('session_id', $sessionId)
                              ->where('status', 'active');
                    })->delete();
                    
                    // Delete the cart itself
                    $cartDeleted = Cart::where('session_id', $sessionId)
                        ->where('status', 'active')
                        ->delete();
                    
                    Log::info('Session cart cleanup: ' . $cartItemsDeleted . ' items deleted, cart delete result: ' . $cartDeleted);
                }
                
                // Reset cart count in session
                session(['cart_count' => 0]);
            }
            
            // Set selected branch in session
            session(['selected_branch' => $branchId]);
            
            // Make sure session is saved immediately - don't use regenerate() which can cause errors
            session()->save();
            
            // Also set a cookie as fallback
            $cookie = cookie('selected_branch', $branchId, 60*24*30); // 30 days
            
            return response()->json([
                'success' => true,
                'message' => 'Chi nhánh đã được chọn thành công',
                'branch_id' => $branchId,
                'session_has_branch' => session()->has('selected_branch'),
                'session_branch_id' => session('selected_branch')
            ])->cookie($cookie);
        } catch (\Exception $e) {
            Log::error('Error setting selected branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Find the nearest branch based on coordinates
     */
    public function findNearestBranch(Request $request)
    {
        try {
            $request->validate([
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
            ]);

            $latitude = $request->lat;
            $longitude = $request->lng;
            
            // Use Haversine formula to calculate distance
            $branches = Branch::select([
                'id',
                'name',
                'address',
                'latitude',
                'longitude',
                DB::raw("(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance")
            ])
            ->where('active', true)
            ->orderBy('distance', 'asc')
            ->limit(1)
            ->get();
            
            if ($branches->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy chi nhánh nào gần bạn'
                ]);
            }
            
            $nearestBranch = $branches->first();
            
            return response()->json([
                'success' => true,
                'branch_id' => $nearestBranch->id,
                'branch_name' => $nearestBranch->name,
                'distance' => round($nearestBranch->distance, 2) // Distance in km
            ]);
        } catch (\Exception $e) {
            Log::error('Error finding nearest branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
} 
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Combo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\BranchService;
use App\Events\Customer\FavoriteUpdated;

class WishlistController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->middleware('CustomerAuth'); // Sử dụng đúng tên class của middleware
        $this->branchService = $branchService;
    }

    public function index()
    {
        // Kiểm tra authentication đã được middleware xử lý
        $user = Auth::user();
        
        // Lấy wishlist items với eager loading để tối ưu performance
        $wishlistItems = $user->wishlist()
            ->with([
                'product' => function($query) {
                    $query->with(['primaryImage', 'category']);
                },
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Tính toán thống kê
        $stats = $this->calculateWishlistStats($wishlistItems);
        
        // Lấy danh sách categories để filter
        $categories = $this->getWishlistCategories($wishlistItems);
        
        return view('customer.wishlist.index', compact('wishlistItems', 'stats', 'categories'));
    }
    
    /**
     * Tính toán thống kê wishlist
     */
    private function calculateWishlistStats($wishlistItems)
    {
        $totalItems = $wishlistItems->count();
        $totalValue = 0;
        $categories = collect();
        
        foreach ($wishlistItems as $item) {
            if ($item->product) {
                $totalValue += $item->product->price ?? 0;
                if ($item->product->category) {
                    $categories->push($item->product->category->name);
                }
            } elseif ($item->combo) {
                $totalValue += $item->combo->price ?? 0;
                $categories->push('Combo');
            }
        }
        
        return [
            'total_items' => $totalItems,
            'total_value' => number_format($totalValue, 0, ',', '.') . 'đ',
            'categories_count' => $categories->unique()->count(),
            'categories' => $categories->unique()->values()
        ];
    }
    
    /**
     * Lấy danh sách categories từ wishlist
     */
    private function getWishlistCategories($wishlistItems)
    {
        $categories = collect();
        
        foreach ($wishlistItems as $item) {
            if ($item->product && $item->product->category) {
                $categories->push([
                    'id' => $item->product->category->id,
                    'name' => $item->product->category->name,
                    'slug' => $item->product->category->slug ?? strtolower(str_replace(' ', '-', $item->product->category->name))
                ]);
            } elseif ($item->combo) {
                $categories->push([
                    'id' => 'combo',
                    'name' => 'Combo',
                    'slug' => 'combo'
                ]);
            }
        }
        
        return $categories->unique('id')->values();
    }

    /**
     * Add a product or combo to the wishlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'combo_id' => 'nullable|exists:combos,id',
        ]);

        // Đảm bảo chỉ có một trong hai trường được gửi
        if (!$request->product_id && !$request->combo_id) {
            return response()->json(['message' => 'Phải cung cấp product_id hoặc combo_id'], 400);
        }

        if ($request->product_id && $request->combo_id) {
            return response()->json(['message' => 'Chỉ có thể thêm product hoặc combo, không thể thêm cả hai'], 400);
        }

        $user = Auth::user();
        $existingItem = WishlistItem::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('combo_id', $request->combo_id)
            ->first();

        if ($existingItem) {
            $itemType = $request->product_id ? 'Sản phẩm' : 'Combo';
            return response()->json(['message' => $itemType . ' đã có trong danh sách yêu thích'], 409);
        }

        WishlistItem::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'combo_id' => $request->combo_id,
        ]);

        $itemType = $request->product_id ? 'Sản phẩm' : 'Combo';
        return response()->json(['message' => 'Đã thêm ' . $itemType . ' vào danh sách yêu thích'], 201);
    }

    /**
     * Remove a product or combo from the wishlist.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'combo_id' => 'nullable|exists:combos,id',
        ]);

        // Đảm bảo chỉ có một trong hai trường được gửi
        if (!$request->product_id && !$request->combo_id) {
            return response()->json(['message' => 'Phải cung cấp product_id hoặc combo_id'], 400);
        }

        if ($request->product_id && $request->combo_id) {
            return response()->json(['message' => 'Chỉ có thể xóa product hoặc combo, không thể xóa cả hai'], 400);
        }

        $user = Auth::user();
        $wishlistItem = WishlistItem::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('combo_id', $request->combo_id)
            ->first();

        if (!$wishlistItem) {
            $itemType = $request->product_id ? 'Sản phẩm' : 'Combo';
            return response()->json(['message' => $itemType . ' không có trong danh sách yêu thích'], 404);
        }

        $wishlistItem->delete();

        $itemType = $request->product_id ? 'Sản phẩm' : 'Combo';
        return response()->json(['message' => 'Đã xoá ' . $itemType . ' khỏi danh sách yêu thích']);
    }
}
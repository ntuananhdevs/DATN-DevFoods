<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\BranchService;

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
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $wishlistItems = Auth::user()->wishlist()->with('product')->get();
        return view('customer.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add a product to the wishlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $user = Auth::user();
        $existingItem = WishlistItem::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($existingItem) {
            return response()->json(['message' => 'Sản phẩm đã có trong danh sách yêu thích'], 400);
        }

        WishlistItem::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id,
        ]);

        return response()->json(['message' => 'Đã thêm vào danh sách yêu thích'], 200);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy($id)
    {
        $wishlistItem = WishlistItem::where('user_id', Auth::id())->findOrFail($id);
        // Giảm favorite_count nếu có
        $product = $wishlistItem->product;
        if ($product && $product->favorite_count > 0) {
            $product->decrement('favorite_count');
        }
        $wishlistItem->delete();

        return response()->json(['message' => 'Đã xóa khỏi danh sách yêu thích'], 200);
    }
}

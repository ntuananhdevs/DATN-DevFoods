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
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $wishlistItems = Auth::user()->wishlist()->with(['product', 'productVariant', 'combo'])->get();
        return view('customer.wishlist.index', compact('wishlistItems'));
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
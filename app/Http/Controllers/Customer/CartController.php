<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\BranchService;

class CartController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Determine if user is authenticated or using session
        $userId = Auth::id();
        $sessionId = session()->getId();
        
        // Query the cart based on user_id or session_id
        $cartQuery = Cart::query()->where('status', 'active');
        
        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }
        
        $cart = $cartQuery->first();
        
        // Initialize cartItems as an empty collection rather than an array
        $cartItems = collect();
        $subtotal = 0;
        
        if ($cart) {
            $cartItems = CartItem::with([
                'variant.product.images',
                'variant.variantValues.attribute',
                'toppings'
            ])->where('cart_id', $cart->id)->get();
            
            // Calculate subtotal
            foreach ($cartItems as $item) {
                $subtotal += $item->variant->price * $item->quantity;
                
                // Add topping prices to subtotal
                foreach ($item->toppings as $topping) {
                    $subtotal += $topping->price * $item->quantity;
                }
                
                // Set primary image for display
                $item->variant->product->primary_image = $item->variant->product->images
                    ->where('is_primary', true)
                    ->first() ?? $item->variant->product->images->first();
            }
        }
        
        // Store cart count in session
        $cartCount = $cartItems->sum('quantity');
        session(['cart_count' => $cartCount]);
        
        return view("customer.cart.index", compact('cartItems', 'subtotal', 'cart'));
    }
}

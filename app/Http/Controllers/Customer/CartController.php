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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
    
    /**
     * Add a product to cart
     */
    public function addToCart(Request $request)
    {
        try {
            // Log request data
            \Log::debug('Add to cart request:', $request->all());
            
            // Log variant values details
            $variantValues = $request->variant_values;
            foreach ($variantValues as $valueId) {
                $variantValue = \App\Models\VariantValue::with('attribute')->find($valueId);
                \Log::debug('Variant Value Details:', [
                    'id' => $valueId,
                    'name' => $variantValue->value,
                    'variant_type' => $variantValue->attribute->name
                ]);
            }

            // Validate request
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_values' => 'required|array',
                'branch_id' => 'required|exists:branches,id',
                'quantity' => 'required|integer|min:1',
                'toppings' => 'nullable|array',
                'toppings.*' => 'exists:toppings,id'
            ]);

            // Get or create cart
            $userId = Auth::id();
            $sessionId = session()->getId();
            
            $cart = Cart::where('status', 'active')
                ->when($userId, function($query) use ($userId) {
                    return $query->where('user_id', $userId);
                }, function($query) use ($sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'status' => 'active'
                ]);
            }

            // Find product variant based on selected values
            $variant = ProductVariant::where('product_id', $request->product_id)
                ->whereHas('variantValues', function($query) use ($request) {
                    $query->whereIn('variant_value_id', $request->variant_values);
                }, '=', count($request->variant_values))
                ->first();

            if (!$variant) {
                Log::error('Variant not found:', [
                    'product_id' => $request->product_id,
                    'variant_values' => $request->variant_values
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy biến thể sản phẩm'
                ], 404);
            }

            // Check stock
            $stock = $variant->branchStocks()
                ->where('branch_id', $request->branch_id)
                ->first();

            if (!$stock || $stock->stock_quantity < $request->quantity) {
                Log::error('Insufficient stock:', [
                    'variant_id' => $variant->id,
                    'branch_id' => $request->branch_id,
                    'requested_quantity' => $request->quantity,
                    'available_stock' => $stock ? $stock->stock_quantity : 0
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng sản phẩm không đủ'
                ], 400);
            }

            // Begin transaction
            DB::beginTransaction();
            try {
                // Create cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'notes' => $request->notes
                ]);

                // Add toppings if any
                if ($request->has('toppings') && !empty($request->toppings)) {
                    $toppings = collect($request->toppings)->mapWithKeys(function($toppingId) {
                        return [$toppingId => ['quantity' => 1]];
                    })->all();
                    
                    $cartItem->toppings()->attach($toppings);
                }

                DB::commit();

                // Log success
                Log::info('Product added to cart successfully:', [
                    'cart_id' => $cart->id,
                    'cart_item_id' => $cartItem->id,
                    'product_id' => $request->product_id,
                    'variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'toppings' => $request->toppings
                ]);

                // Get updated cart count
                $cartCount = $cart->items()->sum('quantity');

                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
                    'count' => $cartCount
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error adding to cart:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Add to cart failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'cart_item_id' => 'required|exists:cart_items,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $userId = Auth::id();
            $sessionId = session()->getId();

            // Find cart item and ensure it belongs to current user/session
            $cartItem = CartItem::whereHas('cart', function($query) use ($userId, $sessionId) {
                $query->where('status', 'active')
                    ->when($userId, function($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    }, function($q) use ($sessionId) {
                        return $q->where('session_id', $sessionId);
                    });
            })->findOrFail($request->cart_item_id);

            // Check stock availability
            $stock = $cartItem->variant->branchStocks()
                ->where('branch_id', $cartItem->cart->branch_id ?? 1) // Default branch if not set
                ->first();

            if (!$stock || $stock->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng sản phẩm không đủ'
                ], 400);
            }

            // Update quantity
            $cartItem->update(['quantity' => $request->quantity]);

            // Get updated cart count
            $cartCount = $cartItem->cart->items()->sum('quantity');
            session(['cart_count' => $cartCount]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công',
                'cart_count' => $cartCount
            ]);

        } catch (\Exception $e) {
            Log::error('Update cart item failed:', [
                'error' => $e->getMessage(),
                'cart_item_id' => $request->cart_item_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật giỏ hàng'
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        try {
            $request->validate([
                'cart_item_id' => 'required|exists:cart_items,id'
            ]);

            $userId = Auth::id();
            $sessionId = session()->getId();

            // Find cart item and ensure it belongs to current user/session
            $cartItem = CartItem::whereHas('cart', function($query) use ($userId, $sessionId) {
                $query->where('status', 'active')
                    ->when($userId, function($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    }, function($q) use ($sessionId) {
                        return $q->where('session_id', $sessionId);
                    });
            })->findOrFail($request->cart_item_id);

            // Store cart reference before deletion
            $cart = $cartItem->cart;

            // Remove toppings first
            $cartItem->toppings()->detach();

            // Delete cart item
            $cartItem->delete();

            // Get updated cart count
            $cartCount = $cart->items()->sum('quantity');
            session(['cart_count' => $cartCount]);

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng',
                'cart_count' => $cartCount
            ]);

        } catch (\Exception $e) {
            Log::error('Remove cart item failed:', [
                'error' => $e->getMessage(),
                'cart_item_id' => $request->cart_item_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm'
            ], 500);
        }
    }
}

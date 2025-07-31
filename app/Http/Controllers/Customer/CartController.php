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
use App\Models\DiscountCode;
use Carbon\Carbon;

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
        // Debug session id và user id
        \Log::info('CartController@index - Session ID: ' . session()->getId());
        \Log::info('CartController@index - User ID: ' . (Auth::id() ?? 'null'));

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
        \Log::info('CartController@index - Cart found: ' . ($cart ? 'YES' : 'NO'));
        if ($cart) {
            \Log::info('CartController@index - Cart ID: ' . $cart->id . ' | session_id: ' . $cart->session_id . ' | user_id: ' . $cart->user_id);
        }

        // Initialize cartItems as an empty collection rather than an array
        $cartItems = collect();
        $subtotal = 0;

        if ($cart) {
            $cartItems = CartItem::with([
                'combo', // thêm dòng này để load combo cho cart item
                'variant.product' => function($query) {
                    $query->with('images', 'variants.variantValues');
                },
                'variant.variantValues.attribute',
                'toppings.topping'
            ])->where('cart_id', $cart->id)->get();

            // Calculate subtotal
            foreach ($cartItems as $item) {
                if ($item->combo_id && $item->combo) {
                    $subtotal += $item->combo->price * $item->quantity;
                } elseif ($item->variant) {
                $subtotal += $item->variant->price * $item->quantity;
                // Add topping prices to subtotal
                foreach ($item->toppings as $topping) {
                    $subtotal += $topping->price * $item->quantity;
                    }
                }

                // Set primary image for display
                if ($item->variant && $item->variant->product) {
                $item->variant->product->primary_image = $item->variant->product->images
                    ->where('is_primary', true)
                    ->first() ?? $item->variant->product->images->first();
                }
            }
        }

        // Store cart count in session
        $cartCount = $cartItems->count();
        session(['cart_count' => $cartCount]);

        // === SUGGESTED PRODUCTS LOGIC ===
        $suggestedProducts = collect();
        $cartProductIds = [];
        $cartCategoryIds = [];
        $cartProducts = collect();
        foreach ($cartItems as $item) {
            if ($item->variant && $item->variant->product) {
                $cartProducts->push([
                'product_id' => $item->variant->product->id,
                'category_id' => $item->variant->product->category_id
                ]);
                $cartProductIds[] = $item->variant->product->id;
                $cartCategoryIds[] = $item->variant->product->category_id;
            } elseif ($item->combo_id && $item->combo) {
                // Lấy các sản phẩm trong combo
                foreach ($item->combo->comboItems as $comboItem) {
                    if ($comboItem->productVariant && $comboItem->productVariant->product) {
                        $cartProducts->push([
                            'product_id' => $comboItem->productVariant->product->id,
                            'category_id' => $comboItem->productVariant->product->category_id
                        ]);
                        $cartProductIds[] = $comboItem->productVariant->product->id;
                        $cartCategoryIds[] = $comboItem->productVariant->product->category_id;
                    }
                }
            }
        }
        $cartProductIds = array_unique($cartProductIds);
        $cartCategoryIds = array_unique($cartCategoryIds);
        $cartCount = $cartProducts->count();
        $suggestionPlan = [];
        if ($cartCount == 1) {
            $suggestionPlan = [4];
        } elseif ($cartCount == 2) {
            $suggestionPlan = [2,2];
        } elseif ($cartCount == 3) {
            $suggestionPlan = [1,1,2];
        } elseif ($cartCount >= 4) {
            $suggestionPlan = array_fill(0, min(4, $cartCount), 1);
        }
        $usedProductIds = $cartProductIds;
        $suggested = collect();
        foreach ($suggestionPlan as $i => $num) {
            if (!isset($cartProducts[$i])) break;
            $catId = $cartProducts[$i]['category_id'];
            $query = Product::with(['primaryImage', 'images'])
                ->where('category_id', $catId)
                ->where('status', 'selling')
                ->whereNotIn('id', $usedProductIds)
                ->whereHas('variants.branchStocks', function($q) {
                    $q->where('stock_quantity', '>', 0);
                })
                ->orderByDesc('favorite_count')
                ->limit($num)
                ->get();
            foreach ($query as $p) {
                if (count($suggested) < 4 && !$usedProductIds || !in_array($p->id, $usedProductIds)) {
                    $suggested->push($p);
                    $usedProductIds[] = $p->id;
                }
            }
        }
        // Nếu chưa đủ 4 sản phẩm, lấy thêm sản phẩm yêu thích nhất ngoài giỏ hàng, không trùng
        if ($suggested->count() < 4) {
            $fill = Product::with(['primaryImage', 'images'])
                ->where('status', 'selling')
                ->whereNotIn('id', $usedProductIds)
                ->whereHas('variants.branchStocks', function($q) {
                    $q->where('stock_quantity', '>', 0);
                })
                ->orderByDesc('favorite_count')
                ->limit(4 - $suggested->count())
                ->get();
            foreach ($fill as $p) {
                if ($suggested->count() < 4 && !in_array($p->id, $usedProductIds)) {
                    $suggested->push($p);
                    $usedProductIds[] = $p->id;
                }
            }
        }
        $suggestedProducts = $suggested;

        // === DISCOUNT CODE LOGIC ===
        $now = Carbon::now();
        $selectedBranchId = $cart ? $cart->branch_id : null;
        $currentTime = $now->format('H:i:s');
        $activeDiscountCodesQuery = DiscountCode::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($query) use ($selectedBranchId) {
                if ($selectedBranchId) {
                    $query->whereDoesntHave('branches')
                        ->orWhereHas('branches', function($q) use ($selectedBranchId) {
                            $q->where('branches.id', $selectedBranchId);
                        });
                }
            });
        $activeDiscountCodesQuery->where(function($query) {
            $query->where('usage_type', 'public');
            if (Auth::check()) {
                $query->orWhere(function($q) {
                    $q->where('usage_type', 'personal')
                        ->whereHas('users', function($userQuery) {
                            $userQuery->where('user_id', Auth::id());
                        });
                });
            }
        });
        $activeDiscountCodes = $activeDiscountCodesQuery->with(['products' => function($query) {
            $query->with(['product', 'category']);
        }])->get()->filter(function($discountCode) use ($currentTime) {
            if ($discountCode->valid_from_time && $discountCode->valid_to_time) {
                $from = Carbon::parse($discountCode->valid_from_time)->format('H:i:s');
                $to = Carbon::parse($discountCode->valid_to_time)->format('H:i:s');
                if ($from < $to) {
                    if (!($currentTime >= $from && $currentTime <= $to)) return false;
                } else {
                    if (!($currentTime >= $from || $currentTime <= $to)) return false;
                }
            }
            return true;
        });
        // Tính min_price cho mỗi product trong cart (giống show/index)
        foreach ($cartItems as $item) {
            if ($item->variant && $item->variant->product) {
            $product = $item->variant->product;
            $product->min_price = $product->base_price;
            if ($product->variants && $product->variants->count() > 0) {
                $variantPrices = [];
                foreach ($product->variants as $variant) {
                    $variantPrice = $product->base_price;
                    if ($variant->variantValues && $variant->variantValues->count() > 0) {
                        $variantPrice += $variant->variantValues->sum('price_adjustment');
                    }
                    $variantPrices[] = $variantPrice;
                }
                if (!empty($variantPrices)) {
                    $product->min_price = min($variantPrices);
                }
            }
            // DEBUG: Log variant prices to find discrepancy
            \Illuminate\Support\Facades\Log::debug('CartController@index - Variant Prices for Product ID ' . $product->id, [
                'variant_prices' => $variantPrices ?? [],
                'calculated_min_price' => $product->min_price,
                'base_price' => $product->base_price
            ]);
            }
        }
        // Tính discount cho từng item
        foreach ($cartItems as $item) {
            if ($item->variant && $item->variant->product) {
            $product = $item->variant->product;
            $originPrice = $item->variant->price;
            $item->origin_price = $originPrice;
            $applicableDiscounts = $activeDiscountCodes->filter(function($discountCode) use ($item) {
                if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                    if ($discountCode->min_requirement_type && $discountCode->min_requirement_value > 0) {
                        if ($discountCode->min_requirement_type === 'order_amount') {
                            return true;
                        } elseif ($discountCode->min_requirement_type === 'product_price') {
                            if ($item->variant->product->min_price < $discountCode->min_requirement_value) {
                                return false;
                            }
                        }
                    }
                    return true;
                }
                $applies = $discountCode->products->contains(function($discountProduct) use ($item) {
                    if ($discountProduct->product_id === $item->variant->product->id) return true;
                    if ($discountProduct->category_id === $item->variant->product->category_id) return true;
                    return false;
                });
                if ($applies && $discountCode->min_requirement_type === 'product_price' && $discountCode->min_requirement_value > 0) {
                    if ($item->variant->product->min_price < $discountCode->min_requirement_value) {
                        return false;
                    }
                }
                return $applies;
            });
            $maxDiscount = null;
            $maxValue = 0;
            foreach ($applicableDiscounts as $discountCode) {
                $value = 0;
                if ($discountCode->discount_type === 'fixed_amount') {
                    $value = $discountCode->discount_value;
                } elseif ($discountCode->discount_type === 'percentage') {
                    $value = $originPrice * $discountCode->discount_value / 100;
                }
                if ($value > $maxValue) {
                    $maxValue = $value;
                    $maxDiscount = $discountCode;
                }
            }
            $item->best_discount = $maxDiscount;
            $item->best_discount_value = $maxValue;
            // Debug giá topping từng item
            $toppingSum = $item->toppings->sum('price');
            $toppingSumWithRelation = $item->toppings->sum(function($t) { return $t->topping->price ?? 0; });
            \Log::debug('CartItem debug', [
                'cart_item_id' => $item->id,
                'variant_price' => $originPrice,
                'topping_ids' => $item->toppings->pluck('topping_id'),
                'topping_sum' => $toppingSum,
                'topping_sum_with_relation' => $toppingSumWithRelation,
                'topping_names' => $item->toppings->map(function($t) { return $t->topping->name ?? null; }),
            ]);
            $item->final_price = max(0, $originPrice - $maxValue) + $toppingSumWithRelation;
            }
        }

        return view("customer.cart.index", compact('cartItems', 'subtotal', 'cart', 'suggestedProducts'));
    }

    /**
     * Add a product to cart
     */
    public function addToCart(Request $request)
    {
        try {
            // Log request data
            \Log::debug('Add to cart request:', $request->all());

            // Validate request
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_values' => 'nullable|array',
                'branch_id' => 'required|exists:branches,id',
                'quantity' => 'required|integer|min:1',
                'toppings' => 'nullable|array',
                'toppings.*' => 'exists:toppings,id'
            ]);

            // Get or create cart
            $userId = Auth::id();
            $sessionId = session()->getId();

            // Validate user exists if authenticated
            if ($userId) {
                $userExists = \App\Models\User::where('id', $userId)->exists();
                if (!$userExists) {
                    // User doesn't exist, clear authentication and use session-based cart
                    Auth::logout();
                    $userId = null;
                    Log::warning('User ID ' . $userId . ' does not exist, falling back to session-based cart');
                }
            }

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

            // Find product variant based on selected values or get default variant
            $variant = null;
            
            if ($request->has('variant_values') && !empty($request->variant_values)) {
                // Log variant values details
                $variantValues = $request->variant_values;
                foreach ($variantValues as $valueId) {
                    $variantValue = \App\Models\VariantValue::with('attribute')->find($valueId);
                    if ($variantValue) {
                        \Log::debug('Variant Value Details:', [
                            'id' => $valueId,
                            'name' => $variantValue->value,
                            'variant_type' => $variantValue->attribute->name
                        ]);
                    }
                }

                // Find variant based on selected values
                $variantValueIds = array_map('intval', $request->variant_values);
                $variant = ProductVariant::where('product_id', $request->product_id)
                    ->whereHas('variantValues', function($query) use ($variantValueIds) {
                        $query->whereIn('variant_value_id', $variantValueIds);
                    }, '=', count($variantValueIds))
                    ->whereHas('variantValues', function($query) use ($variantValueIds) {
                        $query->whereNotIn('variant_value_id', $variantValueIds);
                    }, '=', 0)
                    ->first();
            } else {
                // No variant_values provided, get the first available variant
                $variant = ProductVariant::where('product_id', $request->product_id)
                    ->whereHas('branchStocks', function($query) use ($request) {
                        $query->where('branch_id', $request->branch_id)
                              ->where('stock_quantity', '>', 0);
                    })
                    ->first();
                
                if (!$variant) {
                    // If no variant with stock, get any variant
                    $variant = ProductVariant::where('product_id', $request->product_id)->first();
                }
            }

            if (!$variant) {
                Log::error('Variant not found:', [
                    'product_id' => $request->product_id,
                    'variant_values' => $request->variant_values ?? 'none'
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
                // Get the array of topping IDs from the request, sort them for consistent comparison
                $requestToppingIds = $request->input('toppings', []);
                sort($requestToppingIds);

                // Find all existing cart items for the same product variant
                $existingCartItems = CartItem::where('cart_id', $cart->id)
                    ->where('product_variant_id', $variant->id)
                    ->with('toppings')
                    ->get();

                $matchingCartItem = null;

                // Loop through existing items to find an exact match (including toppings)
                foreach ($existingCartItems as $item) {
                    $itemToppingIds = $item->toppings->pluck('topping_id')->sort()->values()->all();

                    if ($itemToppingIds == $requestToppingIds) {
                        $matchingCartItem = $item;
                        break;
                    }
                }

                if ($matchingCartItem) {
                    // If a match is found, update the quantity
                    $matchingCartItem->quantity += $request->quantity;
                    $matchingCartItem->save();
                    $cartItem = $matchingCartItem;
                } else {
                    // If no match is found, create a new cart item
                    $cartItem = CartItem::create([
                        'cart_id' => $cart->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $request->quantity,
                        'notes' => $request->notes,
                    ]);

                    // Attach toppings if they exist in the request
                    if (!empty($requestToppingIds)) {
                        $toppingData = collect($requestToppingIds)->map(function ($toppingId) {
                            return [
                                'topping_id' => $toppingId,
                                'quantity' => 1
                            ];
                        })->all();
                        $cartItem->toppings()->createMany($toppingData);
                    }
                }

                DB::commit();

                // Log success
                Log::info('Product added/updated in cart successfully:', [
                    'cart_id' => $cart->id,
                    'cart_item_id' => $cartItem->id,
                    'product_id' => $request->product_id,
                    'variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'toppings' => $request->toppings
                ]);

                // Get updated cart count
                $cartCount = $cart->items()->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
                    'cart_count' => $cartCount
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
     * Thêm combo vào giỏ hàng
     */
    public function addComboToCart(Request $request)
    {
        $request->validate([
            'combo_id' => 'required|exists:combos,id',
            'quantity' => 'required|integer|min:1|max:20',
        ]);
        $userId = Auth::id();
        $sessionId = session()->getId();
        
        // Validate user exists if authenticated
        if ($userId) {
            $userExists = \App\Models\User::where('id', $userId)->exists();
            if (!$userExists) {
                // User doesn't exist, clear authentication and use session-based cart
                Auth::logout();
                $userId = null;
                Log::warning('User ID ' . $userId . ' does not exist, falling back to session-based cart');
            }
        }
        
        $cart = \App\Models\Cart::firstOrCreate(
            [
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'status' => 'active'
            ],
            [
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'status' => 'active'
            ]
        );
        $combo = \App\Models\Combo::find($request->combo_id);
        if (!$combo || $combo->status !== 'selling') {
            return response()->json(['success' => false, 'message' => 'Combo không khả dụng'], 400);
        }
        // Lấy branch_id hiện tại (ưu tiên cart->branch_id, fallback 1)
        $branchId = $cart->branch_id ?? 1;
        $comboStock = $combo->comboBranchStocks->where('branch_id', $branchId)->first();
        $stockQty = $comboStock ? $comboStock->quantity : 0;
        // Tổng số lượng combo này đã có trong cart
        $cartItem = $cart->items()->where('combo_id', $combo->id)->first();
        $currentQty = $cartItem ? $cartItem->quantity : 0;
        $newQty = $currentQty + $request->quantity;
        if ($stockQty > 0 && $newQty > $stockQty) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng combo trong kho không đủ. Chỉ còn ' . $stockQty . ' combo tại chi nhánh này.'
            ], 400);
        }
        if ($cartItem) {
            $cartItem->quantity = $newQty;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'combo_id' => $combo->id,
                'quantity' => $request->quantity,
            ]);
        }
        $cartCount = $cart->items()->count();
        session(['cart_count' => $cartCount]);
        return response()->json([
            'success' => true,
            'message' => 'Combo đã được thêm vào giỏ hàng',
            'cart_count' => $cartCount
        ]);
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

            // Nếu là combo thì kiểm tra tồn kho combo
            if ($cartItem->combo_id) {
                $branchId = $cartItem->cart->branch_id ?? 1;
                $comboStock = $cartItem->combo->comboBranchStocks->where('branch_id', $branchId)->first();
                $stockQty = $comboStock ? $comboStock->quantity : 0;
                if ($stockQty > 0 && $request->quantity > $stockQty) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Số lượng combo trong kho không đủ. Chỉ còn ' . $stockQty . ' combo tại chi nhánh này.'
                    ], 400);
                }
            } else {
                // Check stock availability for product variant
            $stock = $cartItem->variant->branchStocks()
                    ->where('branch_id', $cartItem->cart->branch_id ?? 1)
                ->first();
            if (!$stock || $stock->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng sản phẩm không đủ'
                ], 400);
                }
            }

            // Update quantity
            $cartItem->update(['quantity' => $request->quantity]);

            // Get updated cart count
            $cartCount = $cartItem->cart->items()->count();
            session(['cart_count' => $cartCount]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công',
                'cart_count' => $cartCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Update cart item failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            $cartItem->toppings()->delete();

            // Delete cart item
            $cartItem->delete();

            // Get updated cart count
            $cartCount = $cart->items()->count();
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

    /**
     * Xóa toàn bộ giỏ hàng của khách hàng hiện tại
     */
    public function clear(Request $request)
    {
        $userId = auth()->id();
        $sessionId = session()->getId();
        $cartQuery = \App\Models\Cart::query()->where('status', 'active');
        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }
        $cart = $cartQuery->first();
        if ($cart) {
            $cart->items()->delete();
            session(['cart_count' => 0]);
        }
        return response()->json(['success' => true]);
    }
}

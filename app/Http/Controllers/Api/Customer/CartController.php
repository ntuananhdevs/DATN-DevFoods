<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Events\Customer\CartUpdated;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Add a product to the cart
     */
    public function add(Request $request)
    {
        try {
            \Log::info('Cart add request:', $request->all());
            
            // First check if branch_id is provided
            if (!$request->has('branch_id') || empty($request->branch_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn chi nhánh trước khi thêm vào giỏ hàng'
                ], 400);
            }
            
            // Then validate other fields
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variant_id' => 'required|exists:product_variants,id',
                'branch_id' => 'required|exists:branches,id',
                'toppings' => 'nullable|array',
                'toppings.*' => 'exists:toppings,id'
            ]);

            // Verify selected branch matches session branch
            $sessionBranchId = session('selected_branch');
            if ($sessionBranchId && $sessionBranchId != $request->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chi nhánh đã thay đổi. Vui lòng làm mới trang và thử lại.'
                ], 400);
            }
            
            // Check product availability in branch
            $branchStock = \App\Models\BranchStock::where('branch_id', $request->branch_id)
                ->where('product_variant_id', $request->variant_id)
                ->first();
                
            if (!$branchStock || $branchStock->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm đã hết hàng tại chi nhánh này'
                ], 400);
            }
            
            // Check topping availability
            if ($request->has('toppings') && count($request->toppings) > 0) {
                foreach ($request->toppings as $toppingId) {
                    $toppingStock = \App\Models\ToppingStock::where('branch_id', $request->branch_id)
                        ->where('topping_id', $toppingId)
                        ->first();
                        
                    if (!$toppingStock || $toppingStock->stock_quantity <= 0) {
                        $topping = \App\Models\Topping::find($toppingId);
                        return response()->json([
                            'success' => false,
                            'message' => 'Topping ' . ($topping ? $topping->name : '') . ' đã hết hàng tại chi nhánh này'
                        ], 400);
                    }
                }
            }
            
            $userId = null;
            $sessionId = null;
            
            if (auth()->check()) {
                //Lấy ID người dùng đã đăng nhập
                $userId = auth()->id();
                \Log::info('Usuario autenticado:', ['user_id' => $userId]);
            } else {
                // Asegurarse de que estamos en una sesión
                if (!$request->hasSession()) {
                    \Log::warning('No hay sesión disponible en la solicitud');
                    // Generar un ID único si no hay sesión
                    $sessionId = uniqid('cart_', true);
                } else {
                    $sessionId = $request->session()->getId();
                }
                \Log::info('Usuario no autenticado, usando session_id:', ['session_id' => $sessionId]);
            }
            
            // Obtener o crear el carrito del usuario
            $cartQuery = Cart::query();
            
            if ($userId) {
                $cartQuery->where('user_id', $userId);
            } elseif ($sessionId) {
                $cartQuery->where('session_id', $sessionId);
            }
            
            $cartQuery->where('status', 'active');
            
            $cart = $cartQuery->first();
            
            if (!$cart) {
                $cartData = [
                    'status' => 'active'
                ];
                
                if ($userId) {
                    $cartData['user_id'] = $userId;
                } elseif ($sessionId) {
                    $cartData['session_id'] = $sessionId;
                }
                
                \Log::info('Creando nuevo carrito con:', $cartData);
                $cart = Cart::create($cartData);
            }
            
            \Log::info('Carrito obtenido/creado:', ['cart_id' => $cart->id]);
            
            // Verificar que tenemos un carrito válido
            if (!$cart || !$cart->id) {
                \Log::error('No se pudo crear un carrito válido');
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo crear un carrito válido'
                ], 500);
            }
            
            // Comprobar si el producto ya está en el carrito
            $query = CartItem::where('cart_id', $cart->id)
                ->where('product_variant_id', $request->variant_id)
                ->where('branch_id', $request->branch_id);
                
            \Log::info('Buscando item en carrito:', [
                'cart_id' => $cart->id, 
                'variant_id' => $request->variant_id,
                'branch_id' => $request->branch_id
            ]);
            $cartItem = $query->first();
                
            if ($cartItem) {
                \Log::info('Item encontrado en carrito:', ['cart_item_id' => $cartItem->id]);
                // Actualizar cantidad si ya existe
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
                \Log::info('Cantidad actualizada:', ['nueva_cantidad' => $cartItem->quantity]);
            } else {
                \Log::info('Creando nuevo item en carrito');
                // Crear nuevo item en el carrito
                $cartItemData = [
                    'cart_id' => $cart->id,
                    'product_variant_id' => $request->variant_id,
                    'combo_id' => null,
                    'quantity' => $request->quantity,
                    'branch_id' => $request->branch_id
                ];
                \Log::info('Datos para nuevo item:', $cartItemData);
                
                $cartItem = CartItem::create($cartItemData);
                \Log::info('Nuevo item creado:', ['cart_item_id' => $cartItem->id]);
                
                // Añadir toppings si hay
                if ($request->has('toppings') && count($request->toppings) > 0) {
                    \Log::info('Añadiendo toppings:', ['toppings' => $request->toppings]);
                    
                    foreach ($request->toppings as $toppingId) {
                        \Log::info('Añadiendo topping:', ['topping_id' => $toppingId]);
                        // Insertar directamente en la tabla pivot usando consulta SQL
                        DB::table('cart_item_toppings')->insert([
                            'cart_item_id' => $cartItem->id,
                            'topping_id' => $toppingId,
                            'quantity' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
            
            // Contar items en el carrito
            $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');
            \Log::info('Total items en carrito:', ['count' => $cartCount]);
            
            // Guardar en sesión para mostrar en el header
            session(['cart_count' => $cartCount]);
            
            // Broadcast del evento para actualizar en tiempo real
            \Log::info('Broadcasting cart update event', ['user_id' => $userId, 'count' => $cartCount]);
            event(new CartUpdated($userId, $cartCount));
            \Log::info('Event broadcast completed');
            
            return response()->json([
                'success' => true,
                'message' => 'Producto añadido al carrito',
                'cart_count' => $cartCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en add to cart: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Devolver información detallada del error
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
            
            $userId = auth()->id();
            $cartItem = CartItem::findOrFail($request->cart_item_id);
            
            // Verificar que el cart item pertenece al usuario
            $cart = Cart::where('user_id', $userId)
                ->where('id', $cartItem->cart_id)
                ->where('status', 'active')
                ->firstOrFail();
                
            // Actualizar cantidad
            $cartItem->quantity = $request->quantity;
            $cartItem->save();
            
            // Contar items en el carrito
            $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');
            
            // Guardar en sesión para mostrar en el header
            session(['cart_count' => $cartCount]);
            
            // Broadcast del evento para actualizar en tiempo real
            event(new CartUpdated($userId, $cartCount));
            
            return response()->json([
                'success' => true,
                'message' => 'Carrito actualizado',
                'cart_count' => $cartCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en update cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'error' => $e->getMessage()
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
            
            $userId = auth()->id();
            $cartItem = CartItem::findOrFail($request->cart_item_id);
            
            // Verificar que el cart item pertenece al usuario
            $cart = Cart::where('user_id', $userId)
                ->where('id', $cartItem->cart_id)
                ->where('status', 'active')
                ->firstOrFail();
                
            // Eliminar item
            $cartItem->delete();
            
            // Contar items en el carrito
            $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');
            
            // Guardar en sesión para mostrar en el header
            session(['cart_count' => $cartCount]);
            
            // Broadcast del evento para actualizar en tiempo real
            event(new CartUpdated($userId, $cartCount));
            
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart_count' => $cartCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en remove from cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply a coupon code
     */
    public function applyCoupon(Request $request)
    {
        try {
            $request->validate([
                'coupon_code' => 'required|string',
                'discount' => 'required|integer|min:0'
            ]);
            
            // Store discount in session
            session(['discount' => $request->discount]);
            
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully',
                'discount' => $request->discount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error applying coupon: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
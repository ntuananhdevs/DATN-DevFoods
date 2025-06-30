<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartTransferService
{
    /**
     * Chuyển giỏ hàng từ session sang user khi đăng nhập
     * 
     * @param int $userId ID của user vừa đăng nhập
     * @param string $sessionId Session ID hiện tại
     * @return bool
     */
    public function transferCartFromSessionToUser(int $userId, string $sessionId): bool
    {
        try {
            DB::beginTransaction();

            // Tìm giỏ hàng của session hiện tại
            $sessionCart = Cart::where('session_id', $sessionId)
                ->where('status', 'active')
                ->first();

            if (!$sessionCart) {
                Log::info('No session cart found for transfer', [
                    'session_id' => $sessionId,
                    'user_id' => $userId
                ]);
                DB::commit();
                return true; // Không có giỏ hàng để chuyển
            }

            // Tìm giỏ hàng hiện tại của user (nếu có)
            $userCart = Cart::where('user_id', $userId)
                ->where('status', 'active')
                ->first();

            if ($userCart) {
                // Nếu user đã có giỏ hàng, merge các items
                $this->mergeCartItems($sessionCart, $userCart);
                
                // Xóa giỏ hàng session
                $sessionCart->delete();
                
                Log::info('Cart merged from session to user', [
                    'session_cart_id' => $sessionCart->id,
                    'user_cart_id' => $userCart->id,
                    'user_id' => $userId
                ]);
            } else {
                // Nếu user chưa có giỏ hàng, chuyển trực tiếp
                $sessionCart->update([
                    'user_id' => $userId,
                    'session_id' => null
                ]);
                
                Log::info('Cart transferred from session to user', [
                    'cart_id' => $sessionCart->id,
                    'user_id' => $userId
                ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error transferring cart from session to user', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'session_id' => $sessionId,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Merge các items từ session cart vào user cart
     * 
     * @param Cart $sessionCart
     * @param Cart $userCart
     */
    private function mergeCartItems(Cart $sessionCart, Cart $userCart): void
    {
        $sessionItems = CartItem::where('cart_id', $sessionCart->id)->get();
        
        foreach ($sessionItems as $sessionItem) {
            // Tìm item tương tự trong user cart
            $existingItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_variant_id', $sessionItem->product_variant_id)
                ->where('combo_id', $sessionItem->combo_id)
                ->where('branch_id', $sessionItem->branch_id)
                ->first();

            if ($existingItem) {
                // Nếu item đã tồn tại, cộng dồn số lượng
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $sessionItem->quantity
                ]);

                // Merge toppings nếu có
                $this->mergeToppings($sessionItem, $existingItem);

                // Xóa session item
                $sessionItem->delete();
            } else {
                // Nếu item chưa tồn tại, chuyển sang user cart
                $sessionItem->update(['cart_id' => $userCart->id]);
            }
        }
    }

    /**
     * Merge toppings từ session item vào user item
     * 
     * @param CartItem $sessionItem
     * @param CartItem $userItem
     */
    private function mergeToppings(CartItem $sessionItem, CartItem $userItem): void
    {
        $sessionToppings = $sessionItem->toppings()->get();
        
        foreach ($sessionToppings as $sessionTopping) {
            $existingTopping = $userItem->toppings()
                ->where('topping_id', $sessionTopping->id)
                ->first();

            if ($existingTopping) {
                // Cộng dồn số lượng topping
                $userItem->toppings()->updateExistingPivot($sessionTopping->id, [
                    'quantity' => $existingTopping->pivot->quantity + $sessionTopping->pivot->quantity
                ]);
            } else {
                // Thêm topping mới
                $userItem->toppings()->attach($sessionTopping->id, [
                    'quantity' => $sessionTopping->pivot->quantity
                ]);
            }
        }
    }

    /**
     * Xóa giỏ hàng session cũ (cleanup)
     * 
     * @param string $sessionId
     * @return bool
     */
    public function cleanupSessionCart(string $sessionId): bool
    {
        try {
            $sessionCart = Cart::where('session_id', $sessionId)
                ->where('status', 'active')
                ->first();

            if ($sessionCart) {
                // Xóa tất cả cart items trước
                CartItem::where('cart_id', $sessionCart->id)->delete();
                
                // Xóa cart
                $sessionCart->delete();
                
                Log::info('Session cart cleaned up', ['session_id' => $sessionId]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error cleaning up session cart', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);
            return false;
        }
    }
} 
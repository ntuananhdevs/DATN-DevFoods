<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy giỏ hàng từ session
        $cartItems = Session::get('cart', []);
        $cartData = [];
        $totalPrice = 0;
        
        // Nếu giỏ hàng không trống, lấy thông tin sản phẩm
        if (!empty($cartItems)) {
            foreach ($cartItems as $key => $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    // Nếu có variant_id, lấy thông tin biến thể
                    if (isset($item['variant_id']) && $item['variant_id']) {
                        $variant = ProductVariant::find($item['variant_id']);
                        $price = $variant ? $variant->price : $product->base_price;
                        $image = $variant && $variant->image ? $variant->image : $product->image;
                        $variantName = $variant ? $variant->name : '';
                    } else {
                        $price = $product->base_price;
                        $image = $product->image;
                        $variantName = '';
                    }
                    
                    $itemTotal = $price * $item['quantity'];
                    $totalPrice += $itemTotal;
                    
                    $cartData[$key] = [
                        'id' => $key,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'variant_name' => $variantName,
                        'price' => $price,
                        'quantity' => $item['quantity'],
                        'image' => $image,
                        'total' => $itemTotal,
                        'attributes' => isset($item['attributes']) ? $item['attributes'] : []
                    ];
                }
            }
        }
        
        return view("customer.cart.cart-item", compact('cartData', 'totalPrice'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $variantId = $request->input('variant_id');
        $quantity = $request->input('quantity', 1);
        $attributes = $request->input('attributes', []);
        
        // Kiểm tra sản phẩm tồn tại
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại!'], 404);
        }
        
        // Xác định giá sản phẩm
        $price = $product->base_price;
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $price = $variant->price;
            }
        }
        
        // Lấy giỏ hàng từ session (hoặc khởi tạo mới)
        $cart = Session::get('cart', []);
        
        // Tạo key duy nhất cho sản phẩm
        $cartKey = $variantId ? $productId . '-' . $variantId : $productId;
        
        // Thêm hoặc cập nhật sản phẩm trong giỏ hàng
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
                'attributes' => $attributes
            ];
        }
        
        // Lưu lại giỏ hàng vào session
        Session::put('cart', $cart);
        
        return response()->json([
            'success' => true, 
            'message' => 'Đã thêm vào giỏ hàng!',
            'cart_count' => count($cart)
        ]);
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request)
    {
        $cartKey = $request->input('cart_key');
        $quantity = $request->input('quantity');
        
        // Lấy giỏ hàng từ session
        $cart = Session::get('cart', []);
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $quantity;
            Session::put('cart', $cart);
            return response()->json(['success' => true, 'message' => 'Giỏ hàng đã được cập nhật!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'], 404);
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove(Request $request)
    {
        $cartKey = $request->input('cart_key');
        
        // Lấy giỏ hàng từ session
        $cart = Session::get('cart', []);
        
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            Session::put('cart', $cart);
            return response()->json(['success' => true, 'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'], 404);
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        Session::forget('cart');
        return response()->json(['success' => true, 'message' => 'Giỏ hàng đã được xóa!']);
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("customer.cart.cart-item");
    }

    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $size = $request->input('size', 'Mặc định');
        $spice = $request->input('spice', 'Vừa');
        $price = $request->input('price');

        // Lấy giỏ hàng từ session (hoặc khởi tạo mới)
        $cart = Session::get('cart', []);

        // Tạo key duy nhất cho sản phẩm (dựa trên product_id, size, spice)
        $cartKey = $productId . '-' . $size . '-' . $spice;

        // Thêm hoặc cập nhật sản phẩm trong giỏ hàng
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'size' => $size,
                'spice' => $spice,
                'price' => $price,
            ];
        }

        // Lưu lại giỏ hàng vào session
        Session::put('cart', $cart);

        return response()->json(['message' => 'Đã thêm vào giỏ hàng!']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

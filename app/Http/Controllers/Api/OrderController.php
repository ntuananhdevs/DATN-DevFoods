<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Address;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Events\Order\NewOrderReceived;
use App\Models\Payment;

class OrderController extends Controller
{
    /**
     * Store a new order via API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate input data
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'address_id' => 'required|integer|exists:addresses,id',
                'payment_method' => 'required|string|in:cod,vnpay,balance',
                'note' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu đầu vào không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify user exists and address belongs to user
            $user = User::find($request->user_id);
            $address = Address::where('id', $request->address_id)
                             ->where('user_id', $request->user_id)
                             ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Địa chỉ không tồn tại hoặc không thuộc về người dùng này'
                ], 404);
            }

            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // Get the first available variant for this product
                $variant = ProductVariant::where('product_id', $product->id)
                                       ->where('active', true)
                                       ->first();

                if (!$variant) {
                    throw new \Exception("Không tìm thấy biến thể có sẵn cho sản phẩm: {$product->name}");
                }

                $unitPrice = $variant->price;
                $totalPrice = $unitPrice * $item['quantity'];
                $subtotal += $totalPrice;

                $orderItems[] = [
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'product_name' => $product->name // For reference
                ];
            }

            // Set shipping fee (using same logic as CheckoutController)
            $shippingFee = $subtotal > 200000 ? 0 : 25000;
            $totalAmount = $subtotal + $shippingFee;

            // Create order (Note: payment_method is stored in notes for now since DB only has payment_id)
            $order = Order::create([
                'order_code' => 'API-' . strtoupper(substr(uniqid(), -8)),
                'customer_id' => $request->user_id,
                'address_id' => $request->address_id,
                'branch_id' => 1, // Default branch - you may want to make this configurable
                'status' => 'awaiting_confirmation',
                'subtotal' => $subtotal,
                'delivery_fee' => $shippingFee,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'notes' => $request->note . ' [Payment Method: ' . $request->payment_method . ']',
                'delivery_address' => $address->address_line . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city,
                'order_date' => now(),
            ]);

            // Create order items
            foreach ($orderItems as $orderItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $orderItem['product_variant_id'],
                    'quantity' => $orderItem['quantity'],
                    'unit_price' => $orderItem['unit_price'],
                    'total_price' => $orderItem['total_price'],
                ]);
            }

            // Create payment and link to order
            $payment = Payment::create([
                'payment_method' => $request->payment_method,
                'payment_amount' => $totalAmount,
                'payment_status' => 'pending',
                'txn_ref' => 'API-' . time() . '-' . $order->id, // Unique transaction reference
            ]);
            $order->payment_id = $payment->id;
            $order->save();

            // Dispatch event to notify branch (sau khi đã có đầy đủ order_items và payment)
            NewOrderReceived::dispatch($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công',
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'total_amount' => $totalAmount,
                'payment' => $payment,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
} 
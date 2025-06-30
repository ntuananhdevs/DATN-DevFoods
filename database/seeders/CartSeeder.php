<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemTopping;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Combo;
use App\Models\Topping;
use App\Models\Branch;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all(); // Use all users instead of only customers
        $products = Product::with('variants')->get();
        $combos = Combo::all();
        $toppings = Topping::all();
        $branches = Branch::all();

        // Check if required collections are not empty
        if ($users->isEmpty()) {
            throw new \Exception('No users found. Please run UserSeeder or FastFoodSeeder first.');
        }
        
        if ($products->isEmpty()) {
            throw new \Exception('No products found. Please run FastFoodSeeder first.');
        }
        
        if ($combos->isEmpty()) {
            throw new \Exception('No combos found. Please run FastFoodSeeder first.');
        }
        
        if ($toppings->isEmpty()) {
            throw new \Exception('No toppings found. Please run FastFoodSeeder first.');
        }

        // Check if carts already exist to avoid duplicates
        if (Cart::count() > 0) {
            return; // Skip if carts already exist
        }

        foreach ($users as $user) {
            // 70% khả năng user có giỏ hàng
            if (rand(1, 100) <= 70) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'status' => 'active',
                ]);

                // Tạo 1-5 items trong giỏ hàng
                $itemCount = rand(1, 5);
                
                for ($i = 0; $i < $itemCount; $i++) {
                    $isCombo = rand(0, 1) === 1 && $combos->count() > 0;
                    
                    if ($isCombo) {
                        $combo = $combos->random();
                        $quantity = rand(1, 3);

                        $cartItem = CartItem::create([
                            'cart_id' => $cart->id,
                            'product_variant_id' => null,
                            'combo_id' => $combo->id,
                            'quantity' => $quantity,
                            'notes' => $this->getRandomNotes(),
                        ]);
                    } else {
                        $product = $products->random();
                        $variant = $product->variants->random();
                        $quantity = rand(1, 3);

                        $cartItem = CartItem::create([
                            'cart_id' => $cart->id,
                            'product_variant_id' => $variant->id,
                            'combo_id' => null,
                            'quantity' => $quantity,
                            'notes' => $this->getRandomNotes(),
                        ]);

                        // Thêm toppings cho product (40% khả năng)
                        if (rand(1, 100) <= 40 && $toppings->count() > 0) {
                            $toppingCount = rand(1, 3);
                            $selectedToppings = $toppings->random($toppingCount);
                            
                            foreach ($selectedToppings as $topping) {
                                CartItemTopping::create([
                                    'cart_item_id' => $cartItem->id,
                                    'topping_id' => $topping->id,
                                    'quantity' => rand(1, 2),
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            'Không hành',
            'Ít muối',
            'Không cay',
            'Thêm tương ớt',
            'Giao hàng cẩn thận',
            'Để ở cổng',
            'Gọi điện trước khi giao',
            null, // 50% khả năng không có ghi chú
            null,
            null,
            null,
            null,
        ];

        return $notes[array_rand($notes)];
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Branch;
use Carbon\Carbon;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $orders = Order::all();
        $branches = Branch::all();

        if ($users->isEmpty() || $products->isEmpty() || $orders->isEmpty()) {
            echo "Not enough data to seed product reviews.\n";
            return;
        }

        // Tạo 50 review mẫu
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $product = $products->random();
            $order = $orders->random();
            $branch = $branches->isNotEmpty() ? $branches->random() : null;

            ProductReview::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'branch_id' => $branch ? $branch->id : null,
                'rating' => rand(1, 5),
                'review' => fake()->optional()->sentence(10),
                'review_date' => Carbon::now()->subDays(rand(0, 30)),
                'approved' => rand(0, 1),
                'review_image' => null, // Có thể random ảnh nếu muốn
                'is_verified_purchase' => rand(0, 1),
                'is_anonymous' => rand(0, 1),
                'helpful_count' => rand(0, 20),
                'report_count' => rand(0, 5),
                'is_featured' => rand(0, 1),
            ]);
        }
    }
} 
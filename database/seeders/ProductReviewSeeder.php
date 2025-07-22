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

        // Danh sách câu review tiếng Việt
        $vietnameseReviews = [
            'Món ăn này rất ngon!',
            'Tôi rất hài lòng với chất lượng.',
            'Sẽ quay lại lần sau.',
            'Phục vụ nhanh và thân thiện.',
            'Hương vị tuyệt vời.',
            'Giá cả hợp lý.',
            'Không gian sạch sẽ, thoải mái.',
            'Món ăn trình bày đẹp mắt.',
            'Đồ ăn nóng hổi, vừa miệng.',
            'Rất đáng để thử!',
        ];

        // Đảm bảo mỗi sản phẩm có ít nhất 3 bình luận
        foreach ($products as $product) {
            for ($i = 0; $i < 3; $i++) {
                $user = $users->random();
                $order = $orders->random();
                $branch = $branches->isNotEmpty() ? $branches->random() : null;
                $reviewText = $vietnameseReviews[array_rand($vietnameseReviews)];

                ProductReview::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'branch_id' => $branch ? $branch->id : null,
                    'rating' => rand(4, 5),
                    'review' => $reviewText,
                    'review_date' => Carbon::now()->subDays(rand(0, 30)),
                    'review_image' => null,
                    'is_verified_purchase' => 1,
                    'helpful_count' => rand(0, 20),
                    'report_count' => rand(0, 2),
                    'is_featured' => rand(0, 1),
                ]);
            }
        }
    }
} 
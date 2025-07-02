<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $category = Category::inRandomOrder()->first();
        
        return [
            'category_id' => $category->id,
            'name' => $this->generateProductName($category->name),
            'sku' => 'PRD' . $this->faker->unique()->numberBetween(1000, 9999),
            'base_price' => $this->faker->numberBetween(30000, 200000),
            'available' => true,
            'preparation_time' => $this->faker->numberBetween(5, 30),
            'ingredients' => $this->generateIngredients($category->name),
            'short_description' => $this->faker->sentence(),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateProductName($category): string
    {
        $names = [
            'Burger' => [
                'Classic Burger', 'Cheese Burger', 'Double Burger', 'Chicken Burger',
                'Fish Burger', 'Veggie Burger', 'Spicy Burger', 'Mushroom Burger'
            ],
            'Pizza' => [
                'Margherita', 'Pepperoni', 'Hawaiian', 'Seafood',
                'Vegetarian', 'BBQ Chicken', 'Four Cheese', 'Meat Lovers'
            ],
            'Gà Rán' => [
                'Gà Rán Giòn', 'Gà Rán Cay', 'Gà Rán Mật Ong', 'Gà Rán Tỏi',
                'Gà Rán BBQ', 'Gà Rán Sốt Teriyaki', 'Gà Rán Sốt Mắm', 'Gà Rán Sốt Tiêu'
            ],
            'Cơm' => [
                'Cơm Gà Xối Mỡ', 'Cơm Sườn Nướng', 'Cơm Bò Lúc Lắc', 'Cơm Tôm Rang',
                'Cơm Cá Chiên', 'Cơm Thịt Kho', 'Cơm Gà Nướng', 'Cơm Bò Nướng'
            ],
            'Mì' => [
                'Mì Xào Hải Sản', 'Mì Xào Bò', 'Mì Xào Gà', 'Mì Xào Rau',
                'Mì Xào Tôm', 'Mì Xào Thập Cẩm', 'Mì Xào Cay', 'Mì Xào Tôm Khô'
            ],
            'Đồ Uống' => [
                'Coca Cola', 'Pepsi', 'Sprite', 'Fanta',
                'Nước Cam', 'Nước Chanh', 'Trà Đào', 'Trà Vải'
            ]
        ];

        return $this->faker->randomElement($names[$category]);
    }

    private function generateIngredients($category): array
    {
        $ingredients = [
            'Burger' => [
                'Bánh mì',
                'Thịt bò xay',
                'Rau xà lách',
                'Cà chua',
                'Dưa leo',
                'Sốt mayonnaise'
            ],
            'Pizza' => [
                'Bột bánh',
                'Sốt cà chua',
                'Phô mai Mozzarella',
                'Thịt xông khói',
                'Nấm',
                'Ớt chuông'
            ],
            'Gà Rán' => [
                'Thịt gà',
                'Bột chiên giòn',
                'Gia vị',
                'Dầu ăn'
            ],
            'Cơm' => [
                'Gạo',
                'Thịt gà/bò/heo',
                'Rau cải',
                'Nước mắm'
            ],
            'Mì' => [
                'Mì',
                'Thịt bò/gà/tôm',
                'Rau cải',
                'Gia vị'
            ],
            'Đồ Uống' => [
                'Nước lọc',
                'Đường',
                'Hương liệu'
            ]
        ];

        return $ingredients[$category] ?? ['Nguyên liệu cơ bản'];
    }
}

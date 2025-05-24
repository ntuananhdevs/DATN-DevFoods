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
            'ingredients' => json_encode($this->generateIngredients($category->name)),
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
                ['name' => 'Bánh mì', 'quantity' => '1 cái'],
                ['name' => 'Thịt bò xay', 'quantity' => '150g'],
                ['name' => 'Rau xà lách', 'quantity' => '30g'],
                ['name' => 'Cà chua', 'quantity' => '2 lát'],
                ['name' => 'Dưa leo', 'quantity' => '2 lát'],
                ['name' => 'Sốt mayonnaise', 'quantity' => '20g']
            ],
            'Pizza' => [
                ['name' => 'Bột bánh', 'quantity' => '200g'],
                ['name' => 'Sốt cà chua', 'quantity' => '100g'],
                ['name' => 'Phô mai Mozzarella', 'quantity' => '150g'],
                ['name' => 'Thịt xông khói', 'quantity' => '50g'],
                ['name' => 'Nấm', 'quantity' => '30g'],
                ['name' => 'Ớt chuông', 'quantity' => '30g']
            ],
            'Gà Rán' => [
                ['name' => 'Thịt gà', 'quantity' => '200g'],
                ['name' => 'Bột chiên giòn', 'quantity' => '100g'],
                ['name' => 'Gia vị', 'quantity' => '20g'],
                ['name' => 'Dầu ăn', 'quantity' => '50ml']
            ],
            'Cơm' => [
                ['name' => 'Gạo', 'quantity' => '200g'],
                ['name' => 'Thịt gà/bò/heo', 'quantity' => '150g'],
                ['name' => 'Rau cải', 'quantity' => '50g'],
                ['name' => 'Nước mắm', 'quantity' => '10ml']
            ],
            'Mì' => [
                ['name' => 'Mì', 'quantity' => '200g'],
                ['name' => 'Thịt bò/gà/tôm', 'quantity' => '100g'],
                ['name' => 'Rau cải', 'quantity' => '50g'],
                ['name' => 'Gia vị', 'quantity' => '20g']
            ],
            'Đồ Uống' => [
                ['name' => 'Nước lọc', 'quantity' => '300ml'],
                ['name' => 'Đường', 'quantity' => '30g'],
                ['name' => 'Hương liệu', 'quantity' => '10ml']
            ]
        ];

        return $ingredients[$category];
    }
}

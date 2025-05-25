<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use App\Models\ProductTopping;
use App\Models\ProductVariant;
use App\Models\ProductVariantDetail;
use App\Models\Topping;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FastFoodSeeder extends Seeder
{
    private function getIngredients($productName)
    {
        $commonIngredients = [
            'Burger' => [
                'base' => ['Bánh mì burger'],
                'vegetables' => ['Xà lách', 'Cà chua', 'Dưa chuột', 'Hành tây'],
                'sauces' => ['Sốt mayonnaise', 'Sốt cà chua'],
            ],
            'Pizza' => [
                'base' => ['Đế bánh pizza', 'Sốt cà chua', 'Phô mai Mozzarella'],
                'vegetables' => ['Ớt chuông', 'Nấm', 'Hành tây'],
                'herbs' => ['Húng quế', 'Oregano'],
            ],
            'Gà Rán' => [
                'main' => ['Thịt gà tươi'],
                'coating' => ['Bột chiên xù', 'Bột gia vị', 'Dầu chiên'],
                'spices' => ['Muối', 'Tiêu', 'Bột tỏi', 'Bột ớt'],
            ],
            'Cơm' => [
                'base' => ['Cơm trắng'],
                'vegetables' => ['Cà rốt', 'Đậu que', 'Bắp cải'],
                'spices' => ['Muối', 'Tiêu', 'Dầu hào'],
            ],
            'Mì' => [
                'base' => ['Mì Ý', 'Sốt cà chua'],
                'vegetables' => ['Cà rốt', 'Nấm', 'Hành tây'],
                'spices' => ['Muối', 'Tiêu', 'Oregano'],
            ],
            'Đồ Uống' => [
                'ice' => ['Đá viên'],
                'additives' => ['Đường', 'Chanh tươi'],
            ],
        ];

        foreach ($commonIngredients as $category => $ingredients) {
            if (str_contains($productName, $category)) {
                $specificIngredients = [];
                
                // Thêm nguyên liệu đặc biệt dựa trên tên sản phẩm
                if (str_contains($productName, 'Bò')) {
                    $specificIngredients['meat'] = ['Thịt bò Úc', 'Thịt bò xay'];
                }
                if (str_contains($productName, 'Gà')) {
                    $specificIngredients['meat'] = ['Thịt gà phi lê', 'Ức gà'];
                }
                if (str_contains($productName, 'Hải Sản')) {
                    $specificIngredients['seafood'] = ['Tôm', 'Mực', 'Cá hồi'];
                }
                if (str_contains($productName, 'Phô Mai')) {
                    $specificIngredients['cheese'] = ['Phô mai Mozzarella', 'Phô mai Cheddar'];
                }
                if (str_contains($productName, 'BBQ')) {
                    $specificIngredients['sauce'] = ['Sốt BBQ', 'Sốt tiêu đen'];
                }
                if (str_contains($productName, 'Cay')) {
                    $specificIngredients['spices'] = ['Ớt tươi', 'Bột ớt', 'Sốt cay'];
                }
                if (str_contains($productName, 'Xông Khói')) {
                    $specificIngredients['meat'] = ['Thịt xông khói', 'Bacon'];
                }
                if (str_contains($productName, 'Sườn')) {
                    $specificIngredients['meat'] = ['Sườn heo', 'Sườn non'];
                }

                return array_merge($ingredients, $specificIngredients);
            }
        }

        // Đồ uống đặc biệt
        if (str_contains($productName, 'Trà')) {
            return [
                'base' => ['Trà', 'Nước'],
                'additives' => ['Đường', 'Đá'],
                'extras' => str_contains($productName, 'Đào') ? ['Đào miếng'] : 
                           (str_contains($productName, 'Vải') ? ['Vải thiều'] : 
                           ['Chanh tươi'])
            ];
        }
        if (str_contains($productName, 'Cà Phê')) {
            return [
                'base' => ['Cà phê nguyên chất'],
                'additives' => ['Đường', 'Đá'],
                'extras' => str_contains($productName, 'Sữa') ? ['Sữa đặc'] : []
            ];
        }
        if (str_contains($productName, 'Sinh Tố')) {
            $fruit = str_contains($productName, 'Dâu') ? 'Dâu tây' : 
                    (str_contains($productName, 'Bơ') ? 'Bơ' : 'Trái cây hỗn hợp');
            return [
                'base' => [$fruit, 'Sữa tươi'],
                'additives' => ['Đường', 'Đá viên'],
                'extras' => ['Sữa đặc']
            ];
        }

        // Mặc định cho các đồ uống có ga
        return [
            'base' => ['Nước có ga'],
            'additives' => ['Đường', 'Đá viên']
        ];
    }

    public function run(): void
    {
        // Tạo danh mục
        $categories = [
            [
                'name' => 'Burger',
                'description' => 'Burger với nhiều lớp nhân thịt và rau củ tươi ngon',
                'image' => 'categories/burger.jpg',
                'status' => true,
                'products' => [
                    'Burger Bò Phô Mai', 'Burger Gà Giòn', 'Burger Cá', 'Burger Bò Nướng BBQ',
                    'Burger Tôm', 'Burger Bò 2 Lớp', 'Burger Gà Nướng', 'Burger Bò Trứng',
                    'Burger Phô Mai', 'Burger Bò Xông Khói', 'Burger Gà Phô Mai', 'Burger Cá Ngừ',
                    'Burger Bò Teriyaki', 'Burger Gà Sốt Cay', 'Burger Bò Deluxe'
                ]
            ],
            [
                'name' => 'Pizza',
                'description' => 'Pizza đa dạng hương vị',
                'image' => 'categories/pizza.jpg',
                'status' => true,
                'products' => [
                    'Pizza Hải Sản', 'Pizza Bò', 'Pizza Gà', 'Pizza Xúc Xích',
                    'Pizza Phô Mai', 'Pizza Nấm', 'Pizza Thịt Nguội', 'Pizza Hawaii',
                    'Pizza 5 Loại Thịt', 'Pizza Rau Củ', 'Pizza Bò BBQ', 'Pizza Gà Nướng',
                    'Pizza Hải Sản Cao Cấp', 'Pizza Thập Cẩm', 'Pizza Margherita'
                ]
            ],
            [
                'name' => 'Gà Rán',
                'description' => 'Gà rán giòn rụm, thơm ngon',
                'image' => 'categories/chicken.jpg',
                'status' => true,
                'products' => [
                    'Gà Rán Giòn', 'Gà Sốt Cay', 'Gà Sốt BBQ', 'Gà Không Xương',
                    'Gà Rán Phô Mai', 'Gà Sốt Teriyaki', 'Gà Rán Mật Ong', 'Gà Sốt Tỏi',
                    'Gà Rán Original', 'Gà Sốt Cay Ngọt', 'Gà Rán Giòn Cay', 'Gà Nướng BBQ',
                    'Gà Sốt Phô Mai', 'Gà Rán Không Cay', 'Gà Rán Sốt Đặc Biệt'
                ]
            ],
            [
                'name' => 'Cơm',
                'description' => 'Các món cơm đặc sắc',
                'image' => 'categories/rice.jpg',
                'status' => true,
                'products' => [
                    'Cơm Gà Rán', 'Cơm Bò Lúc Lắc', 'Cơm Sườn BBQ', 'Cơm Gà Teriyaki',
                    'Cơm Bò Xào', 'Cơm Gà Xối Mỡ', 'Cơm Bò BBQ', 'Cơm Sườn Cay',
                    'Cơm Gà Nướng', 'Cơm Bò Trứng', 'Cơm Gà Sốt Cay', 'Cơm Sườn Nướng',
                    'Cơm Bò Nướng', 'Cơm Gà Chiên', 'Cơm Đùi Gà Chiên'
                ]
            ],
            [
                'name' => 'Mì',
                'description' => 'Các loại mì ngon',
                'image' => 'categories/noodles.jpg',
                'status' => true,
                'products' => [
                    'Mì Ý Sốt Bò', 'Mì Ý Hải Sản', 'Mì Ý Gà', 'Mì Ý Carbonara',
                    'Mì Xào Hải Sản', 'Mì Ý Sốt Kem', 'Mì Xào Bò', 'Mì Ý Sốt Cà Chua',
                    'Mì Xào Gà', 'Mì Ý Sốt Nấm', 'Mì Hoàng Kim', 'Mì Ý Thịt Viên',
                    'Mì Xào Thập Cẩm', 'Mì Ý Chay', 'Mì Đặc Biệt'
                ]
            ],
            [
                'name' => 'Đồ Uống',
                'description' => 'Đồ uống giải khát',
                'image' => 'categories/drinks.jpg',
                'status' => true,
                'products' => [
                    'Coca Cola', 'Pepsi', '7 Up', 'Fanta',
                    'Trà Đào', 'Trà Vải', 'Trà Chanh', 'Cà Phê Đen',
                    'Cà Phê Sữa', 'Sinh Tố Dâu', 'Sinh Tố Bơ', 'Nước Cam',
                    'Nước Ép Táo', 'Trà Sữa', 'Matcha Đá Xay'
                ]
            ]
        ];

        // Tạo categories và products
        foreach ($categories as $categoryData) {
            $products = $categoryData['products'];
            unset($categoryData['products']);
            
            // Tạo category và lấy short_name từ model
            $category = new Category($categoryData);
            $shortName = $category->getShortNameAttribute();
            $category->save();
            echo "Created category: {$category->name} with short name: {$shortName}\n";

            // Tạo products cho category
            foreach ($products as $productName) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $productName,
                    'sku' => $shortName . '-' . Str::random(5),
                    'description' => "Đây là món {$productName} ngon tuyệt",
                    'short_description' => "Món {$productName} đặc biệt",
                    'base_price' => rand(30000, 200000),
                    'available' => true,
                    'preparation_time' => rand(10, 30),
                    'ingredients' => json_encode($this->getIngredients($productName)),
                    'status' => 'selling',
                    'is_featured' => rand(0, 1) === 1
                ]);
                echo "Created product: {$product->name}\n";
            }
        }

        // Tạo variants
        $variants = [
            'Size' => ['Small', 'Medium', 'Large'],
            'Topping' => ['Extra Cheese', 'Extra Meat', 'Extra Vegetables'],
            'Spice Level' => ['Mild', 'Medium', 'Hot'],
        ];

        foreach ($variants as $variantName => $values) {
            $variant = VariantAttribute::create(['name' => $variantName]);
            foreach ($values as $value) {
                VariantValue::create([
                    'variant_attribute_id' => $variant->id,
                    'value' => $value,
                    'price_adjustment' => rand(5000, 20000)
                ]);
            }
        }

        // Tạo variants cho mỗi product và branch stocks
        $products = Product::all();
        $branches = Branch::all();

        foreach ($products as $product) {
            // Tạo 2 variant cho mỗi sản phẩm
            for ($i = 0; $i < 2; $i++) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'image' => null,
                    'active' => true
                ]);

                // Thêm variant values
                $variantValues = VariantValue::inRandomOrder()->take(2)->get();
                foreach ($variantValues as $value) {
                    ProductVariantDetail::create([
                        'product_variant_id' => $variant->id,
                        'variant_value_id' => $value->id
                    ]);
                }

                // Tạo stock cho mỗi branch
                foreach ($branches as $branch) {
                    $branch->stocks()->create([
                        'product_variant_id' => $variant->id,
                        'stock_quantity' => rand(10, 100)
                    ]);
                }
            }
        }

        // Tạo combos
        $combos = Combo::factory(10)->create();

        // Tạo combo items
        foreach ($combos as $combo) {
            $products = Product::inRandomOrder()->take(rand(2, 4))->get();
            foreach ($products as $product) {
                // Lấy ngẫu nhiên một biến thể của sản phẩm
                $variant = $product->variants()->inRandomOrder()->first();
                if ($variant) {
                    ComboItem::create([
                        'combo_id' => $combo->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => rand(1, 3)
                    ]);
                }
            }
        }        

        // Tạo toppings
        $toppings = Topping::factory(20)->create();

        // Tạo product toppings
        foreach ($products as $product) {
            $productToppings = $toppings->random(rand(2, 5));
            foreach ($productToppings as $topping) {
                ProductTopping::create([
                    'product_id' => $product->id,
                    'topping_id' => $topping->id
                ]);
            }
        }
    }
} 
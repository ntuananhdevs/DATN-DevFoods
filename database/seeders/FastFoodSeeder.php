<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTopping;
use App\Models\ProductVariant;
use App\Models\ProductVariantDetail;
use App\Models\ProductReview;
use App\Models\Topping;
use App\Models\User;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
                    'preparation_time' => rand(10, 30),
                    'ingredients' => json_encode($this->getIngredients($productName)),
                    'status' => 'selling',
                    'is_featured' => rand(0, 1) === 1
                ]);
                echo "Created product: {$product->name}\n";
                
                // Tạo hình ảnh cho sản phẩm (product_imgs table)
                $this->createProductImages($product);
            }
        }

        // Tạo variants
        $variants = [
            'Size' => ['Small', 'Medium', 'Large'],
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
        $variantAttributes = VariantAttribute::with('values')->get();

        foreach ($products as $product) {
            // Tạo mảng chứa các giá trị của từng attribute
            $attributeValues = [];
            foreach ($variantAttributes as $attribute) {
                $attributeValues[] = $attribute->values->pluck('id')->toArray();
            }

            // Tạo tất cả các tổ hợp có thể có của variant values
            $combinations = $this->generateCombinations($attributeValues);

            // Tạo variant cho mỗi tổ hợp
            foreach ($combinations as $combination) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'image' => $this->getRandomVariantImage(),
                    'active' => true
                ]);

                // Thêm variant values cho variant này
                foreach ($combination as $valueId) {
                    ProductVariantDetail::create([
                        'product_variant_id' => $variant->id,
                        'variant_value_id' => $valueId
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

        // Tạo toppings
        $this->createToppings();
        
        // Tạo product-topping relationships
        $this->createProductToppings();
        
        // Tạo product reviews (nếu có users)
        $this->createProductReviews();

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
    }
    
    /**
     * Tạo hình ảnh cho sản phẩm
     */
    private function createProductImages($product)
    {
        $imageCount = rand(1, 4); // Mỗi sản phẩm có 1-4 hình ảnh
        
        for ($i = 0; $i < $imageCount; $i++) {
            ProductImage::create([
                'product_id' => $product->id,
                'img' => "products/{$product->sku}_image_{$i}.jpg",
                'is_primary' => $i === 0 // Hình đầu tiên là primary
            ]);
        }
        
        echo "Created {$imageCount} images for product: {$product->name}\n";
    }
    
    /**
     * Tạo toppings
     */
    private function createToppings()
    {
        $toppings = [
            ['name' => 'Phô Mai Thêm', 'price' => 15000, 'active' => true],
            ['name' => 'Thịt Bò Thêm', 'price' => 25000, 'active' => true],
            ['name' => 'Thịt Gà Thêm', 'price' => 20000, 'active' => true],
            ['name' => 'Bacon', 'price' => 18000, 'active' => true],
            ['name' => 'Trứng Ốp La', 'price' => 12000, 'active' => true],
            ['name' => 'Xà Lách Thêm', 'price' => 5000, 'active' => true],
            ['name' => 'Cà Chua Thêm', 'price' => 5000, 'active' => true],
            ['name' => 'Hành Tây Thêm', 'price' => 5000, 'active' => true],
            ['name' => 'Dưa Chuột Thêm', 'price' => 5000, 'active' => true],
            ['name' => 'Sốt BBQ', 'price' => 8000, 'active' => true],
            ['name' => 'Sốt Cay', 'price' => 8000, 'active' => true],
            ['name' => 'Sốt Mayonnaise', 'price' => 8000, 'active' => true],
            ['name' => 'Sốt Tỏi', 'price' => 8000, 'active' => true],
            ['name' => 'Nấm Thêm', 'price' => 10000, 'active' => true],
            ['name' => 'Ớt Jalapeño', 'price' => 7000, 'active' => true],
            ['name' => 'Tôm Thêm', 'price' => 30000, 'active' => true],
            ['name' => 'Mực Thêm', 'price' => 25000, 'active' => true],
            ['name' => 'Xúc Xích', 'price' => 15000, 'active' => true],
            ['name' => 'Pepperoni', 'price' => 20000, 'active' => true],
            ['name' => 'Dứa Thêm', 'price' => 8000, 'active' => true],
        ];
        
        foreach ($toppings as $toppingData) {
            $topping = Topping::create([
                'name' => $toppingData['name'],
                'price' => $toppingData['price'],
                'active' => $toppingData['active'],
                'image' => "toppings/" . Str::slug($toppingData['name']) . ".jpg"
            ]);
            echo "Created topping: {$topping->name}\n";
        }
    }
    
    /**
     * Tạo mối quan hệ product-topping
     */
    private function createProductToppings()
    {
        $products = Product::all();
        $toppings = Topping::all();
        
        foreach ($products as $product) {
            // Mỗi sản phẩm có 3-8 toppings ngẫu nhiên
            $productToppings = $toppings->random(rand(3, 8));
            
            foreach ($productToppings as $topping) {
                ProductTopping::create([
                    'product_id' => $product->id,
                    'topping_id' => $topping->id
                ]);
            }
            
            echo "Created toppings for product: {$product->name}\n";
        }
    }
    
    /**
     * Tạo product reviews (chỉ tạo nếu có users và orders)
     */
    private function createProductReviews()
    {
        // Kiểm tra xem có users không
        $users = User::all();
        if ($users->isEmpty()) {
            echo "No users found, skipping product reviews creation\n";
            return;
        }
        
        // Tạo một số orders giả để có thể tạo reviews
        // Vì không có model Order, ta sẽ tạo reviews mà không cần order_id
        // Hoặc có thể bỏ qua việc tạo reviews nếu không có orders
        
        $products = Product::all();
        $branches = Branch::all();
        
        foreach ($products as $product) {
            // Mỗi sản phẩm có 0-10 reviews
            $reviewCount = rand(0, 10);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                $branch = $branches->random();
                
                // Vì không có orders, ta sẽ comment phần này
                // hoặc tạo một order_id giả
                /*
                ProductReview::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'order_id' => 1, // Giả sử có order với id = 1
                    'branch_id' => $branch->id,
                    'rating' => rand(1, 5),
                    'review' => $this->getRandomReview(),
                    'review_date' => Carbon::now()->subDays(rand(1, 365)),
                    'approved' => rand(0, 1) === 1,
                    'review_image' => rand(0, 1) === 1 ? "reviews/review_" . Str::random(10) . ".jpg" : null,
                    'is_verified_purchase' => true,
                    'is_anonymous' => rand(0, 1) === 1,
                    'helpful_count' => rand(0, 50),
                    'report_count' => rand(0, 5),
                    'is_featured' => rand(0, 1) === 1
                ]);
                */
            }
        }
        
        echo "Product reviews creation skipped (no orders table found)\n";
    }
    
    /**
     * Lấy review ngẫu nhiên
     */
    private function getRandomReview()
    {
        $reviews = [
            "Món ăn rất ngon, tôi sẽ quay lại!",
            "Chất lượng tốt, giá cả hợp lý",
            "Phục vụ nhanh, đồ ăn nóng hổi",
            "Vị rất đậm đà, rất hài lòng",
            "Không gian thoải mái, nhân viên thân thiện",
            "Đồ ăn ngon nhưng hơi mặn",
            "Tuyệt vời! Sẽ giới thiệu cho bạn bè",
            "Bình thường, không có gì đặc biệt",
            "Rất ngon, đáng đồng tiền bát gạo",
            "Giao hàng nhanh, đồ ăn còn nóng"
        ];
        
        return $reviews[array_rand($reviews)];
    }
    
    /**
     * Lấy hình ảnh variant ngẫu nhiên
     */
    private function getRandomVariantImage()
    {
        $images = [
            "variants/variant_1.jpg",
            "variants/variant_2.jpg",
            "variants/variant_3.jpg",
            "variants/variant_4.jpg",
            "variants/variant_5.jpg"
        ];
        
        return $images[array_rand($images)];
    }

    // Move the helper function outside of run() method
    private function generateCombinations($arrays) {
        $result = [[]];
        foreach ($arrays as $array) {
            $tmp = [];
            foreach ($result as $combination) {
                foreach ($array as $value) {
                    $tmp[] = array_merge($combination, [$value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
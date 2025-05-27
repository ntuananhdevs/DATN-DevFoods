<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTopping;
use App\Models\ProductVariant;
use App\Models\ProductVariantDetail;
use App\Models\ProductReview;
use App\Models\ProductImg;
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
                
                // Tạo hình ảnh cho sản phẩm
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

        // Tạo variants cho mỗi product
        $products = Product::all();
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
            }
        }

        // Tạo branch stocks riêng biệt
        $this->createBranchStocks();

        // Tạo toppings
        $this->createToppings();
        
        // Tạo product-topping relationships
        $this->createProductToppings();

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

        // Tạo orders trước
        $this->createOrders();
        
        // Sau đó mới tạo reviews
        $this->createProductReviews();
    }
    
    /**
     * Tạo hình ảnh cho sản phẩm
     */
    private function createProductImages($product)
    {
        $imageCount = rand(1, 4); // Mỗi sản phẩm có 1-4 hình ảnh
        
        for ($i = 0; $i < $imageCount; $i++) {
            ProductImg::create([
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

    /**
     * Lấy review ngẫu nhiên với nội dung chi tiết
     */
    private function getRandomReview()
    {
        $positivePoints = [
            "hương vị tuyệt vời, đậm đà",
            "phục vụ chuyên nghiệp, nhanh chóng",
            "đóng gói cẩn thận, sạch sẽ",
            "giao hàng đúng giờ, shipper thân thiện",
            "giá cả phải chăng cho chất lượng này",
            "phần ăn đầy đặn, nhiều nhân",
            "nguyên liệu tươi ngon, chọn lọc kỹ",
            "chất lượng ổn định qua nhiều lần order",
            "nhân viên nhiệt tình, chu đáo",
            "đồ ăn nóng hổi khi nhận được"
        ];

        $negativePoints = [
            "vị có thể đậm đà hơn một chút",
            "thời gian chờ hơi lâu vào giờ cao điểm",
            "giá hơi cao so với portion size",
            "phần ăn có thể nhiều hơn một chút",
            "bao bì đóng gói cần cải thiện thêm",
            "nước chấm hơi ít so với khẩu phần",
            "đồ ăn hơi nguội khi giao đến",
            "rau củ garnish có thể tươi hơn",
            "vị có thể đa dạng hơn",
            "nên có thêm option về độ cay"
        ];

        $openings = [
            "Đây là một trong những món tôi thích nhất ở đây vì",
            "Tôi đã thử nhiều nơi nhưng vẫn thích ở đây nhất bởi",
            "Điểm khiến tôi ấn tượng với món này là",
            "Sau nhiều lần order, tôi vẫn đánh giá cao vì",
            "Món này luôn là lựa chọn hàng đầu của tôi bởi",
            "Tôi rất hài lòng với chất lượng món này vì",
            "Điều làm tôi thích thú với món này là",
            "Tôi thường xuyên đặt món này vì",
            "Món ăn để lại ấn tượng tốt nhờ",
            "Tôi đánh giá rất cao món này vì"
        ];

        // Tạo review chi tiết
        $review = $openings[array_rand($openings)] . " " . 
                 $positivePoints[array_rand($positivePoints)] . ". ";

        // 40% chance để thêm một điểm tích cực khác
        if (rand(1, 100) <= 40) {
            $review .= "Không chỉ vậy, " . 
                      $positivePoints[array_rand($positivePoints)] . ". ";
        }

        // 30% chance để thêm góp ý cải thiện
        if (rand(1, 100) <= 30) {
            $suggestions = [
                "Tuy nhiên, ",
                "Điểm cần cải thiện là ",
                "Góp ý nhỏ là ",
                "Mong rằng ",
                "Hy vọng lần sau "
            ];
            $review .= $suggestions[array_rand($suggestions)] . 
                      $negativePoints[array_rand($negativePoints)] . ". ";
        }

        // Thêm kết luận
        $conclusions = [
            "Nhìn chung vẫn rất đáng để thử!",
            "Chắc chắn sẽ quay lại lần nữa!",
            "Recommended cho mọi người!",
            "Rất đáng đồng tiền!",
            "Sẽ tiếp tục ủng hộ dài dài!"
        ];

        $review .= $conclusions[array_rand($conclusions)];

        return $review;
    }

    /**
     * Tạo orders và order items
     */
    private function createOrders()
    {
        $users = User::all();
        $branches = Branch::all();
        $drivers = \App\Models\Driver::all();
        $productVariants = ProductVariant::all();
        $combos = Combo::all();
        
        // Tạo 100 orders
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random(); // Random user
            $branch = $branches->random(); // Random branch
            $driver = $drivers->random(); // Random driver
            $orderDate = Carbon::now()->subDays(rand(1, 90)); // Random date in last 90 days
            
            // Calculate random amounts
            $subtotal = 0;
            $deliveryFee = rand(15000, 30000);
            $discountAmount = rand(0, 50000);
            $taxAmount = 0;
            
            // Create order
            $order = \App\Models\Order::create([
                'customer_id' => $user->id,
                'branch_id' => $branch->id,
                'driver_id' => $driver->id,
                'order_date' => $orderDate,
                'delivery_date' => $orderDate->copy()->addMinutes(rand(30, 120)),
                'status' => $this->getRandomOrderStatus(),
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'subtotal' => 0, // Will be updated after adding items
                'total_amount' => 0, // Will be updated after adding items
                'notes' => $this->getRandomOrderNote(),
                'points_earned' => rand(10, 100),
                'estimated_delivery_time' => $orderDate->copy()->addMinutes(45),
                'actual_delivery_time' => $orderDate->copy()->addMinutes(rand(30, 90))
            ]);
            
            // Add 1-5 items to order
            $itemCount = rand(1, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                // 80% chance for product variant, 20% chance for combo
                if (rand(1, 100) <= 80) {
                    $variant = $productVariants->random();
                    $quantity = rand(1, 3);
                    $unitPrice = $variant->product->base_price;
                    
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $unitPrice * $quantity
                    ]);
                    
                    $subtotal += $unitPrice * $quantity;
                } else {
                    $combo = $combos->random();
                    $quantity = rand(1, 2);
                    $unitPrice = $combo->price;
                    
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'combo_id' => $combo->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $unitPrice * $quantity
                    ]);
                    
                    $subtotal += $unitPrice * $quantity;
                }
            }
            
            // Update order totals
            $taxAmount = $subtotal * 0.1; // 10% tax
            $totalAmount = $subtotal + $deliveryFee + $taxAmount - $discountAmount;
            
            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount
            ]);
            
            echo "Created order #{$order->id} with {$itemCount} items\n";
        }
    }

    /**
     * Get random order status
     */
    private function getRandomOrderStatus()
    {
        $statuses = [
            'pending',
            'confirmed',
            'preparing',
            'ready_for_delivery',
            'delivering',
            'delivered',
            'completed',
            'cancelled'
        ];
        
        return $statuses[array_rand($statuses)];
    }

    /**
     * Get random order note
     */
    private function getRandomOrderNote()
    {
        $notes = [
            'Không cần ớt',
            'Giao trong giờ hành chính',
            'Gọi điện trước khi giao',
            'Thêm đồ dùng dùng một lần',
            'Không cần gọi điện',
            null,
            null,
            null
        ];
        
        return $notes[array_rand($notes)];
    }

    /**
     * Tạo product reviews với order reference
     */
    private function createProductReviews()
    {
        $users = User::all();
        $branches = Branch::all();
        $products = Product::all();
        $orders = Order::all(); // Lấy tất cả orders thay vì chỉ lấy completed

        foreach ($products as $product) {
            // Mỗi sản phẩm có đúng 10 reviews
            for ($i = 0; $i < 10; $i++) {
                $user = $users->random();
                $branch = $branches->random();
                $order = $orders->random();

                // Tạo review
                ProductReview::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'branch_id' => $branch->id,
                    'rating' => rand(3, 5), // Bias towards positive reviews
                    'review' => $this->getRandomReview(),
                    'review_date' => now()->subDays(rand(1, 30)),
                    'approved' => true,
                    'review_image' => rand(0, 1) === 1 ? "reviews/review_" . Str::random(10) . ".jpg" : null,
                    'is_verified_purchase' => true,
                    'is_anonymous' => rand(0, 1) === 1,
                    'helpful_count' => rand(0, 50),
                    'report_count' => rand(0, 5),
                    'is_featured' => rand(0, 1) === 1
                ]);
            }
            
            echo "Created 10 reviews for product: {$product->name}\n";
        }
    }

    private function createBranchStocks()
    {
        $branches = Branch::all();
        $variants = ProductVariant::all();
        
        echo "Creating branch stocks...\n";
        
        foreach ($branches as $branch) {
            echo "Creating stocks for branch: {$branch->name}\n";
            
            foreach ($variants as $variant) {
                // Tạo stock với số lượng ngẫu nhiên từ 0 đến 100
                $stockQuantity = rand(0, 100);
                
                $branch->stocks()->create([
                    'product_variant_id' => $variant->id,
                    'stock_quantity' => $stockQuantity
                ]);
                
                echo "Created stock for variant {$variant->id} with quantity: {$stockQuantity}\n";
            }
        }
    }
}
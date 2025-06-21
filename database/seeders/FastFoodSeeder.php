<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImg;
use App\Models\ProductReview;
use App\Models\ProductTopping;
use App\Models\ProductVariant;
use App\Models\ProductVariantDetail;
use App\Models\Role;
use App\Models\Topping;
use App\Models\ToppingStock;
use App\Models\User;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'Combo' => [
                'components' => ['Món chính', 'Món phụ', 'Đồ uống'],
                'benefits' => ['Tiết kiệm chi phí', 'Đa dạng hương vị', 'Phù hợp chia sẻ'],
            ],
        ];

        foreach ($commonIngredients as $category => $ingredients) {
            if (str_contains($productName, $category)) {
                $specificIngredients = [];
                
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

        return [
            'base' => ['Nước có ga'],
            'additives' => ['Đường', 'Đá viên']
        ];
    }

    private function generateSku($category)
    {
        static $counters = [];
        
        // Mapping Vietnamese category names to safe English abbreviations
        $categoryMapping = [
            'Burger' => 'BUR',
            'Pizza' => 'PIZ',
            'Gà Rán' => 'CHI',
            'Cơm' => 'RIC',
            'Mì' => 'NOO',
            'Đồ Uống' => 'DRI',
            'Combo' => 'COM'
        ];
        
        $shortName = $categoryMapping[$category->name] ?? strtoupper(substr($category->name, 0, 3));
        
        // Initialize counter for this category if not exists
        if (!isset($counters[$shortName])) {
            $counters[$shortName] = 1;
        } else {
            $counters[$shortName]++;
        }
        
        $timestamp = now()->format('ymd');
        $counter = str_pad($counters[$shortName], 3, '0', STR_PAD_LEFT);
        
        return $shortName . $timestamp . $counter;
    }

    private function generateCombinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public function run(): void
    {
        try {
            if (!class_exists(\App\Models\Driver::class)) {
                echo "Error: Driver model not found. Make sure it exists before running this seeder.\n";
                return;
            }

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
                ],
                [
                    'name' => 'Combo',
                    'description' => 'Combo tiết kiệm với nhiều món ăn hấp dẫn',
                    'image' => 'categories/combo.jpg',
                    'status' => true,
                    'products' => [
                        'Combo Burger Bò Phô Mai', 'Combo Gà Rán Gia Đình', 'Combo Pizza Hải Sản',
                        'Combo Mì Ý Đặc Biệt', 'Combo Cơm Sườn BBQ', 'Combo Burger Gà Giòn',
                        'Combo Pizza Thập Cẩm', 'Combo Gà BBQ Deluxe', 'Combo Burger Bò 2 Lớp',
                        'Combo Hải Sản Cao Cấp', 'Combo Gia Đình Vui Vẻ', 'Combo Tiệc Nhỏ',
                        'Combo Cặp Đôi', 'Combo Học Sinh', 'Combo Văn Phòng'
                    ]
                ]
            ];

            if (\App\Models\Driver::count() === 0) {
                echo "No drivers found. Creating drivers...\n";
                \App\Models\Driver::factory(10)->create();
                echo "Created 10 drivers.\n";
            }

            if (User::count() === 0) {
                echo "No users found. Creating users...\n";
                User::factory(20)->create();
                echo "Created 20 users.\n";
            }

            if (Branch::count() === 0) {
                echo "No branches found. Creating branches...\n";
                Branch::factory(5)->create();
                echo "Created 5 branches.\n";
            }

            // Tạo categories và products
            foreach ($categories as $categoryData) {
                $products = $categoryData['products'];
                unset($categoryData['products']);
                
                $category = new Category($categoryData);
                $shortName = $category->getShortNameAttribute();
                $category->save();
                echo "Created category: {$category->name} with short name: {$shortName}\n";

                foreach ($products as $productName) {
                    // Xác định giá và thời gian chuẩn bị dựa trên loại sản phẩm
                    if ($category->name === 'Combo') {
                        $basePrice = rand(80000, 450000); // Combo có giá cao hơn
                        $preparationTime = rand(15, 45); // Combo cần thời gian chuẩn bị lâu hơn
                        $description = "Combo tiết kiệm {$productName} bao gồm nhiều món ăn ngon và đồ uống";
                        $shortDescription = "Combo {$productName} - Tiết kiệm và ngon miệng";
                    } else {
                        $basePrice = rand(30000, 200000);
                        $preparationTime = rand(10, 30);
                        $description = "Đây là món {$productName} ngon tuyệt";
                        $shortDescription = "Món {$productName} đặc biệt";
                    }
                    
                    $product = Product::create([
                        'category_id' => $category->id,
                        'name' => $productName,
                        'sku' => $this->generateSku($category),
                        'description' => $description,
                        'short_description' => $shortDescription,
                        'base_price' => $basePrice,
                        'preparation_time' => $preparationTime,
                        'ingredients' => json_encode($this->getIngredients($productName)),
                        'status' => 'selling',
                        'is_featured' => rand(0, 1) === 1
                    ]);
                    echo "Created product: {$product->name}\n";
                }
            }

            $variantAttributesData = [
                'Kích thước' => ['Nhỏ', 'Lớn'],
                'Đường' => ['Ít đường', 'Nhiều đường']
            ];

            $variantAttributes = [];
            foreach ($variantAttributesData as $variantName => $values) {
                $attribute = VariantAttribute::where('name', $variantName)->first();
                if (!$attribute) {
                    echo "Warning: VariantAttribute '{$variantName}' not found. Creating new one.\n";
                    $attribute = VariantAttribute::create(['name' => $variantName]);
                }
                
                $variantAttributes[$variantName] = [
                    'attribute' => $attribute,
                    'values' => $values
                ];
            }

            $products = Product::all();

            foreach ($products as $product) {
                $productVariantValues = [];
                
                foreach ($variantAttributes as $attributeName => $attributeData) {
                    $attribute = $attributeData['attribute'];
                    $values = $attributeData['values'];
                    
                    $productVariantValues[$attributeName] = [];
                    
                    foreach ($values as $value) {
                        $variantValue = VariantValue::create([
                            'variant_attribute_id' => $attribute->id,
                            'value' => $value,
                            'price_adjustment' => rand(5000, 20000)
                        ]);
                        
                        $productVariantValues[$attributeName][] = $variantValue->id;
                        echo "Created unique VariantValue for product {$product->id}: {$value} (ID: {$variantValue->id})\n";
                    }
                }

                $attributeValueIds = array_values($productVariantValues);
                $combinations = $this->generateCombinations($attributeValueIds);

                foreach ($combinations as $combination) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'active' => true
                    ]);

                    foreach ($combination as $valueId) {
                        ProductVariantDetail::create([
                            'product_variant_id' => $variant->id,
                            'variant_value_id' => $valueId
                        ]);
                    }
                }
            }

            $this->createToppings();
            $this->createProductToppings();
            $this->createToppingStocks();
            $this->createCombos();
            $this->createBranchStocks();
            $this->createOrders();
            $this->createProductReviews();

        } catch (\Exception $e) {
            echo "Error in FastFoodSeeder: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
        }
    }
    
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
            ['name' => 'Ớt Chuông', 'price' => 6000, 'active' => true]
        ];

        foreach ($toppings as $toppingData) {
            Topping::create($toppingData);
            echo "Created topping: {$toppingData['name']}\n";
        }
    }

    private function createProductToppings()
    {
        $products = Product::all();
        $toppings = Topping::all();

        foreach ($products as $product) {
            $randomToppings = $toppings->random(rand(3, 8));
            foreach ($randomToppings as $topping) {
                ProductTopping::create([
                    'product_id' => $product->id,
                    'topping_id' => $topping->id
                ]);
            }
        }
        echo "Created product-topping relationships\n";
    }

    /**
     * Tạo kho topping cho từng chi nhánh
     */
    private function createToppingStocks()
    {
        $branches = Branch::all();
        $toppings = Topping::all();

        foreach ($branches as $branch) {
            foreach ($toppings as $topping) {
                \App\Models\ToppingStock::create([
                    'branch_id' => $branch->id,
                    'topping_id' => $topping->id,
                    'stock_quantity' => rand(20, 100), // Số lượng ngẫu nhiên từ 20-100
                ]);
            }
        }
        echo "Created topping stocks for all branches\n";
    }

    private function createCombos()
    {
        $comboData = [
            [
                'name' => 'Combo Burger Bò Phô Mai',
                'description' => 'Burger Bò Phô Mai + Khoai Tây Chiên + Coca Cola',
                'price' => 120000,
                'active' => true,
                'products' => ['Burger Bò Phô Mai', 'Coca Cola']
            ],
            [
                'name' => 'Combo Gà Rán Gia Đình',
                'description' => '8 miếng Gà Rán + 2 Cơm + 2 Pepsi',
                'price' => 350000,
                'active' => true,
                'products' => ['Gà Rán Giòn', 'Cơm Gà Rán', 'Pepsi']
            ],
            [
                'name' => 'Combo Pizza Hải Sản',
                'description' => 'Pizza Hải Sản size L + 2 Trà Đào',
                'price' => 280000,
                'active' => true,
                'products' => ['Pizza Hải Sản', 'Trà Đào']
            ],
            [
                'name' => 'Combo Mì Ý Đặc Biệt',
                'description' => 'Mì Ý Sốt Bò + Gà Rán + Sinh Tố Dâu',
                'price' => 180000,
                'active' => true,
                'products' => ['Mì Ý Sốt Bò', 'Gà Rán Giòn', 'Sinh Tố Dâu']
            ],
            [
                'name' => 'Combo Cơm Sườn BBQ',
                'description' => 'Cơm Sườn BBQ + Trà Chanh + Bánh Ngọt',
                'price' => 150000,
                'active' => true,
                'products' => ['Cơm Sườn BBQ', 'Trà Chanh']
            ],
            [
                'name' => 'Combo Burger Gà Giòn',
                'description' => 'Burger Gà Giòn + Khoai Tây + 7 Up',
                'price' => 110000,
                'active' => true,
                'products' => ['Burger Gà Giòn', '7 Up']
            ],
            [
                'name' => 'Combo Pizza Thập Cẩm',
                'description' => 'Pizza Thập Cẩm + 2 Cà Phê Sữa',
                'price' => 250000,
                'active' => true,
                'products' => ['Pizza Thập Cẩm', 'Cà Phê Sữa']
            ],
            [
                'name' => 'Combo Gà BBQ Deluxe',
                'description' => 'Gà Sốt BBQ + Cơm + Mì Ý + Fanta',
                 'price' => 220000,
                'active' => true,
                'products' => ['Gà Sốt BBQ', 'Cơm Gà Rán', 'Mì Ý Sốt Cà Chua', 'Fanta']
            ],
            [
                'name' => 'Combo Burger Bò 2 Lớp',
                'description' => 'Burger Bò 2 Lớp + Gà Rán + Trà Sữa',
                'price' => 190000,
                'active' => true,
                'products' => ['Burger Bò 2 Lớp', 'Gà Rán Original', 'Trà Sữa']
            ],
            [
                'name' => 'Combo Hải Sản Cao Cấp',
                'description' => 'Pizza Hải Sản Cao Cấp + Mì Ý Hải Sản + 2 Sinh Tố Bơ',
                'price' => 450000,
                'active' => true,
                'products' => ['Pizza Hải Sản Cao Cấp', 'Mì Ý Hải Sản', 'Sinh Tố Bơ']
            ],
            [
                'name' => 'Combo Gia Đình Vui Vẻ',
                'description' => '2 Burger + 2 Gà Rán + 2 Cơm + 4 Đồ Uống',
                'price' => 380000,
                'active' => true,
                'products' => ['Burger Bò Phô Mai', 'Burger Gà Giòn', 'Gà Rán Giòn', 'Cơm Gà Rán', 'Coca Cola', 'Pepsi']
            ],
            [
                'name' => 'Combo Tiệc Nhỏ',
                'description' => 'Pizza 5 Loại Thịt + 6 Gà Rán + 3 Đồ Uống',
                'price' => 420000,
                'active' => true,
                'products' => ['Pizza 5 Loại Thịt', 'Gà Rán Giòn', 'Coca Cola', 'Pepsi', '7 Up']
            ],
            [
                'name' => 'Combo Cặp Đôi',
                'description' => '2 Burger + 2 Mì Ý + 2 Sinh Tố',
                'price' => 240000,
                'active' => true,
                'products' => ['Burger Bò Teriyaki', 'Burger Gà Phô Mai', 'Mì Ý Carbonara', 'Sinh Tố Dâu', 'Sinh Tố Bơ']
            ],
            [
                'name' => 'Combo Học Sinh',
                'description' => 'Burger + Gà Rán + Đồ Uống (Giá ưu đãi)',
                'price' => 85000,
                'active' => true,
                'products' => ['Burger Phô Mai', 'Gà Rán Original', 'Coca Cola']
            ],
            [
                'name' => 'Combo Văn Phòng',
                'description' => 'Cơm + Gà + Trà (Giao nhanh trong giờ)',
                'price' => 95000,
                'active' => true,
                'products' => ['Cơm Gà Teriyaki', 'Gà Rán Không Cay', 'Trà Chanh']
            ]
        ];

        foreach ($comboData as $data) {
            try {
                $productNames = $data['products'];
                unset($data['products']);
                
                $combo = Combo::create($data);
                echo "Created combo: {$combo->name}\n";

                foreach ($productNames as $productName) {
                    $product = Product::where('name', $productName)->first();
                    if ($product) {
                        $variant = $product->variants()->inRandomOrder()->first();
                        if ($variant) {
                            ComboItem::create([
                                'combo_id' => $combo->id,
                                'product_variant_id' => $variant->id,
                                'quantity' => rand(1, 2)
                            ]);
                        } else {
                            echo "Warning: No variant found for product {$productName}\n";
                        }
                    } else {
                        echo "Warning: Product {$productName} not found\n";
                    }
                }
            } catch (\Exception $e) {
                echo "Error creating combo: " . $e->getMessage() . "\n";
            }
        }
    }

    private function createBranchStocks()
    {
        $branches = Branch::all();
        $products = Product::all();

        foreach ($branches as $branch) {
            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    \App\Models\BranchStock::create([
                        'branch_id' => $branch->id,
                        'product_variant_id' => $variant->id,
                        'stock_quantity' => rand(10, 100)
                    ]);
                }
            }
        }
        echo "Created branch stocks\n";
    }

    private function createOrders()
    {
        $users = User::take(10)->get();
        $branches = Branch::all();
        $drivers = \App\Models\Driver::all();

        foreach ($users as $user) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $branch = $branches->random();
                $driver = $drivers->random();
                
                $subtotal = rand(80000, 400000);
                $deliveryFee = rand(15000, 30000);
                $totalAmount = $subtotal + $deliveryFee;
                
                $order = Order::create([
                    'customer_id' => $user->id,
                    'branch_id' => $branch->id,
                    'driver_id' => $driver->id,
                    'status' => collect(['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'delivered'])->random(),
                    'subtotal' => $subtotal,
                    'total_amount' => $totalAmount,
                    'delivery_fee' => $deliveryFee,
                    'delivery_address' => $user->addresses()->first()?->full_address ?? 'Địa chỉ mặc định',
                    'notes' => 'Ghi chú đơn hàng',
                    'created_at' => now()->subDays(rand(0, 30))
                ]);

                $variants = ProductVariant::inRandomOrder()->take(rand(1, 5))->get();
                foreach ($variants as $variant) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => rand(1, 3),
                        'unit_price' => $variant->product->base_price,
                        'total_price' => $variant->product->base_price * rand(1, 3)
                    ]);
                }
            }
        }
        echo "Created orders\n";
    }

    /**
     * Tạo đánh giá sản phẩm cho các sản phẩm
     */
    private function createProductReviews()
    {
        $users = User::all();
        $products = Product::all();
        $branches = Branch::all();

        // Danh sách comment mẫu cho đánh giá
        $reviewComments = [
            'Sản phẩm rất ngon, tôi sẽ đặt lại!',
            'Chất lượng tốt, giao hàng nhanh.',
            'Vị rất đậm đà, phù hợp khẩu vị.',
            'Giá cả hợp lý, chất lượng ổn.',
            'Sản phẩm tươi ngon, đóng gói cẩn thận.',
            'Rất hài lòng với dịch vụ.',
            'Sẽ giới thiệu cho bạn bè.',
            'Đúng như mong đợi.',
            'Món ăn ngon, phục vụ tốt.',
            'Đóng gói cẩn thận, giao đúng giờ.',
            'Vị rất đặc biệt, sẽ quay lại.',
            'Giá hợp lý so với chất lượng.',
        ];
        
        // Lấy tất cả đơn hàng để tạo reviews dựa trên đơn hàng thực tế
        $allOrders = Order::all();
        
        // Tạo 3-5 reviews cho mỗi sản phẩm dựa trên đơn hàng có sẵn
        foreach ($products as $product) {
            $reviewCount = rand(3, 5);
            $availableOrders = $allOrders->where('status', '!=', 'cancelled')->random(min($reviewCount, $allOrders->count()));
            
            foreach ($availableOrders as $order) {
                // Kiểm tra xem đã có review cho sản phẩm này từ user này chưa
                $existingReview = ProductReview::where('user_id', $order->customer_id)
                    ->where('product_id', $product->id)
                    ->first();
                
                if (!$existingReview) {
                    ProductReview::create([
                        'user_id' => $order->customer_id,
                        'product_id' => $product->id,
                        'order_id' => $order->id, // Sử dụng order_id thực tế
                        'branch_id' => $order->branch_id,
                        'rating' => rand(3, 5),
                        'review' => collect($reviewComments)->random(),
                        'review_date' => $order->created_at->addDays(rand(1, 7)),
                        'approved' => true,
                        'created_at' => $order->created_at->addDays(rand(1, 7))
                    ]);
                }
            }
        }
        
        // Tạo thêm reviews từ đơn hàng đã giao (nếu có)
        $deliveredOrders = Order::where('status', 'delivered')->get();
        foreach ($deliveredOrders as $order) {
            if (rand(1, 100) <= 50) { // 50% đơn hàng có review
                foreach ($order->orderItems as $orderItem) {
                    // Kiểm tra xem đã có review cho sản phẩm này từ user này chưa
                    $existingReview = ProductReview::where('user_id', $order->customer_id)
                        ->where('product_id', $orderItem->productVariant->product_id)
                        ->first();
                    
                    if (!$existingReview) {
                        ProductReview::create([
                            'user_id' => $order->customer_id,
                            'product_id' => $orderItem->productVariant->product_id,
                            'order_id' => $order->id,
                            'branch_id' => $order->branch_id,
                            'rating' => rand(3, 5),
                            'review' => collect($reviewComments)->random(),
                            'review_date' => $order->created_at->addDays(rand(1, 7)),
                            'approved' => true,
                            'created_at' => $order->created_at->addDays(rand(1, 7))
                        ]);
                    }
                }
            }
        }
        
        echo "Created product reviews\n";
    }
}
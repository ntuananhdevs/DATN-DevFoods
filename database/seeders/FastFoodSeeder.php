<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchStock;
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
use App\Models\ComboBranchStock;

class FastFoodSeeder extends Seeder
{
    private $adminUser;
    private $branches;
    private $toppings;
    private $globalVariantValues = [];
    
    private function getIngredients($productName)
    {
        $commonIngredients = [
            'Burger' => [
                'Bánh mì burger', 'Xà lách', 'Cà chua', 'Dưa chuột', 'Hành tây', 'Sốt mayonnaise', 'Sốt cà chua'
            ],
            'Pizza' => [
                'Đế bánh pizza', 'Sốt cà chua', 'Phô mai Mozzarella', 'Ớt chuông', 'Nấm', 'Hành tây', 'Húng quế', 'Oregano'
            ],
            'Gà Rán' => [
                'Thịt gà tươi', 'Bột chiên xù', 'Bột gia vị', 'Dầu chiên', 'Muối', 'Tiêu', 'Bột tỏi', 'Bột ớt'
            ],
            'Cơm' => [
                'Cơm trắng', 'Cà rốt', 'Đậu que', 'Bắp cải', 'Muối', 'Tiêu', 'Dầu hào'
            ],
            'Mì' => [
                'Mì Ý', 'Sốt cà chua', 'Cà rốt', 'Nấm', 'Hành tây', 'Muối', 'Tiêu', 'Oregano'
            ],
            'Đồ Uống' => [
                'Đá viên', 'Đường', 'Chanh tươi'
            ],
            'Combo' => [
                'Món chính', 'Món phụ', 'Đồ uống'
            ],
        ];

        foreach ($commonIngredients as $category => $ingredients) {
            if (str_contains($productName, $category)) {
                $specificIngredients = [];
                if (str_contains($productName, 'Bò')) {
                    $specificIngredients = array_merge($specificIngredients, ['Thịt bò Úc', 'Thịt bò xay']);
                }
                if (str_contains($productName, 'Gà')) {
                    $specificIngredients = array_merge($specificIngredients, ['Thịt gà phi lê', 'Ức gà']);
                }
                if (str_contains($productName, 'Hải Sản')) {
                    $specificIngredients = array_merge($specificIngredients, ['Tôm', 'Mực', 'Cá hồi']);
                }
                if (str_contains($productName, 'Phô Mai')) {
                    $specificIngredients = array_merge($specificIngredients, ['Phô mai Mozzarella', 'Phô mai Cheddar']);
                }
                if (str_contains($productName, 'BBQ')) {
                    $specificIngredients = array_merge($specificIngredients, ['Sốt BBQ', 'Sốt tiêu đen']);
                }
                if (str_contains($productName, 'Cay')) {
                    $specificIngredients = array_merge($specificIngredients, ['Ớt tươi', 'Bột ớt', 'Sốt cay']);
                }
                if (str_contains($productName, 'Xông Khói')) {
                    $specificIngredients = array_merge($specificIngredients, ['Thịt xông khói', 'Bacon']);
                }
                if (str_contains($productName, 'Sườn')) {
                    $specificIngredients = array_merge($specificIngredients, ['Sườn heo', 'Sườn non']);
                }
                return array_values(array_unique(array_merge($ingredients, $specificIngredients)));
            }
        }

        if (str_contains($productName, 'Trà')) {
            $extras = [];
            if (str_contains($productName, 'Đào')) {
                $extras[] = 'Đào miếng';
            } elseif (str_contains($productName, 'Vải')) {
                $extras[] = 'Vải thiều';
            } else {
                $extras[] = 'Chanh tươi';
            }
            return array_merge(['Trà', 'Nước', 'Đường', 'Đá'], $extras);
        }
        if (str_contains($productName, 'Cà Phê')) {
            $extras = [];
            if (str_contains($productName, 'Sữa')) {
                $extras[] = 'Sữa đặc';
            }
            return array_merge(['Cà phê nguyên chất', 'Đường', 'Đá'], $extras);
        }
        if (str_contains($productName, 'Sinh Tố')) {
            $fruit = 'Trái cây hỗn hợp';
            if (str_contains($productName, 'Dâu')) {
                $fruit = 'Dâu tây';
            } elseif (str_contains($productName, 'Bơ')) {
                $fruit = 'Bơ';
            }
            return [$fruit, 'Sữa tươi', 'Đường', 'Đá viên', 'Sữa đặc'];
        }

        return ['Nước có ga', 'Đường', 'Đá viên'];
    }

    private function generateSku($category, $type = 'product')
    {
        static $counters = [];
        
        $categoryMapping = [
            'Burger' => 'BUR',
            'Pizza' => 'PIZ',
            'Gà Rán' => 'CHI',
            'Cơm' => 'RIC',
            'Mì' => 'NOO',
            'Đồ Uống' => 'DRI',
            'Combo' => 'COM'
        ];
        
        $shortName = $categoryMapping[$category] ?? strtoupper(substr($category, 0, 3));
        
        if ($type === 'topping') {
            $shortName = 'TOP';
        } elseif ($type === 'combo') {
            $shortName = 'CMB';
        } elseif ($type === 'variant') {
            $shortName = 'VAR';
        }
        
        $key = $shortName . '_' . $type;
        
        if (!isset($counters[$key])) {
            $counters[$key] = 1;
        } else {
            $counters[$key]++;
        }
        
        $timestamp = now()->format('ymd');
        $counter = str_pad($counters[$key], 3, '0', STR_PAD_LEFT);
        
        do {
            $sku = $shortName . $timestamp . $counter;
            $exists = false;
            
            if ($type === 'product') {
                $exists = Product::where('sku', $sku)->exists();
            } elseif ($type === 'topping') {
                $exists = Topping::where('sku', $sku)->exists();
            } elseif ($type === 'combo') {
                $exists = Combo::where('sku', $sku)->exists();
            } elseif ($type === 'variant') {
                $exists = ProductVariant::where('sku', $sku)->exists();
            }
            
            if ($exists) {
                $counters[$key]++;
                $counter = str_pad($counters[$key], 3, '0', STR_PAD_LEFT);
            }
        } while ($exists);
        
        return $sku;
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

    private function cleanupData()
    {
        echo "Cleaning up existing data...\n";
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'combo_items',
            'combos',
            'order_item_toppings',
            'cart_item_toppings', 
            'product_toppings',
            'topping_stocks',
            'toppings',
            'branch_stocks',
            'product_variant_details',
            'product_variants',
            'variant_values',
            'variant_attributes',
            'product_imgs',
            'product_reviews',
            'products',
            'categories'
        ];
        
        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                echo "Truncated {$table}\n";
            }
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        echo "Data cleanup completed\n";
    }

    private function createUserRanks()
    {
        if (\App\Models\UserRank::count() === 0) {
            $ranks = [
                ['name' => 'Đồng', 'slug' => 'bronze', 'color' => '#CD7F32', 'min_spending' => 0, 'min_orders' => 0, 'discount_percentage' => 0],
                ['name' => 'Bạc', 'slug' => 'silver', 'color' => '#C0C0C0', 'min_spending' => 1000, 'min_orders' => 5, 'discount_percentage' => 5],
                ['name' => 'Vàng', 'slug' => 'gold', 'color' => '#FFD700', 'min_spending' => 5000, 'min_orders' => 10, 'discount_percentage' => 10],
                ['name' => 'Bạch Kim', 'slug' => 'platinum', 'color' => '#E5E4E2', 'min_spending' => 10000, 'min_orders' => 20, 'discount_percentage' => 15],
                ['name' => 'Kim Cương', 'slug' => 'diamond', 'color' => '#B9F2FF', 'min_spending' => 20000, 'min_orders' => 50, 'discount_percentage' => 20],
            ];
            
            foreach ($ranks as $index => $rank) {
                \App\Models\UserRank::create([
                    'name' => $rank['name'],
                    'slug' => $rank['slug'],
                    'color' => $rank['color'],
                    'icon' => "icons/{$rank['slug']}.png",
                    'min_spending' => $rank['min_spending'],
                    'min_orders' => $rank['min_orders'],
                    'discount_percentage' => $rank['discount_percentage'],
                    'benefits' => json_encode(['free_shipping' => $rank['min_spending'] >= 5000, 'priority_support' => $rank['min_spending'] >= 1000]),
                    'display_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
            echo "Created user ranks\n";
        }
    }

    private function createBasicData()
    {
        // Tạo admin user nếu chưa có
        $this->adminUser = User::where('email', 'admin@devfoods.com')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::factory()->create([
                'full_name' => 'Admin User',
                'email' => 'admin@devfoods.com',
                'email_verified_at' => now(),
            ]);
            echo "Created admin user\n";
        }

        // Tạo drivers nếu chưa có
        if (\App\Models\Driver::count() === 0) {
            \App\Models\Driver::factory(5)->create();
            echo "Created 5 drivers\n";
        }

        // Tạo users nếu chưa có
        if (User::count() <= 1) {
            User::factory(5)->create();
            echo "Created 5 additional users\n";
        }

        // Tạo branches nếu chưa có
        if (Branch::count() === 0) {
            $this->branches = Branch::factory(5)->create();
            echo "Created 5 branches\n";
        } else {
            $this->branches = Branch::all();
        }
    }

    private function createCategories()
    {
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

        foreach ($categories as $categoryData) {
            $products = $categoryData['products'];
            unset($categoryData['products']);
            
            $category = Category::create($categoryData);
            echo "Created category: {$category->name}\n";

            foreach ($products as $productName) {
                $basePrice = rand(30000, 200000);
                $preparationTime = rand(10, 30);
                $description = "Đây là món {$productName} ngon tuyệt";
                $shortDescription = "Món {$productName} đặc biệt";
                
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $productName,
                    'slug' => Str::slug($productName),
                    'sku' => $this->generateSku($category->name, 'product'),
                    'description' => $description,
                    'short_description' => $shortDescription,
                    'base_price' => $basePrice,
                    'preparation_time' => $preparationTime,
                    'ingredients' => $this->getIngredients($productName),
                    'status' => 'selling',
                    'is_featured' => rand(0, 1) === 1,
                    'created_by' => $this->adminUser->id,
                    'updated_by' => $this->adminUser->id
                ]);
                echo "Created product: {$product->name}\n";
            }
        }
    }

    private function createVariantAttributes()
    {
        // Tạo thuộc tính chung cho tất cả sản phẩm: Size và Vị
        $sizeAttribute = VariantAttribute::firstOrCreate(['name' => 'Size']);
        $flavorAttribute = VariantAttribute::firstOrCreate(['name' => 'Vị']);
        
        echo "Created/Found VariantAttribute: Size (ID: {$sizeAttribute->id})\n";
        echo "Created/Found VariantAttribute: Vị (ID: {$flavorAttribute->id})\n";
        
        $products = Product::all();
        
        foreach ($products as $product) {
            $this->globalVariantValues[$product->id] = [
                'size' => [],
                'flavor' => []
            ];
            
            // Tạo các giá trị size riêng biệt cho từng sản phẩm
            $sizeValues = $this->getProductSizeValues($product);
            foreach ($sizeValues as $valueData) {
                // Tạo VariantValue với tên ngắn gọn, chỉ giữ tên thuộc tính
                $variantValue = VariantValue::create([
                    'variant_attribute_id' => $sizeAttribute->id,
                    'value' => $valueData['name'], // Chỉ sử dụng tên thuộc tính, không ghép tên sản phẩm
                    'price_adjustment' => $valueData['price_adjustment']
                ]);
                
                $this->globalVariantValues[$product->id]['size'][] = $variantValue->id;
                echo "Created Size Value: {$valueData['name']} (ID: {$variantValue->id}) for {$product->name}\n";
            }
            
            // Tạo các giá trị vị riêng biệt cho từng sản phẩm
            $flavorValues = $this->getProductFlavorValues($product);
            foreach ($flavorValues as $valueData) {
                // Tạo VariantValue với tên ngắn gọn, chỉ giữ tên thuộc tính
                $variantValue = VariantValue::create([
                    'variant_attribute_id' => $flavorAttribute->id,
                    'value' => $valueData['name'], // Chỉ sử dụng tên thuộc tính, không ghép tên sản phẩm
                    'price_adjustment' => $valueData['price_adjustment']
                ]);
                
                $this->globalVariantValues[$product->id]['flavor'][] = $variantValue->id;
                echo "Created Flavor Value: {$valueData['name']} (ID: {$variantValue->id}) for {$product->name}\n";
            }
        }
    }
    
    private function getProductSizeValues($product)
    {
        $categoryName = $product->category->name;
        
        switch ($categoryName) {
            case 'Burger':
                return [
                    ['name' => 'S', 'price_adjustment' => 0],
                    ['name' => 'M', 'price_adjustment' => 10000],
                    ['name' => 'L', 'price_adjustment' => 20000],
                ];
            case 'Pizza':
                return [
                    ['name' => '6in', 'price_adjustment' => 0],
                    ['name' => '9in', 'price_adjustment' => 25000],
                    ['name' => '12in', 'price_adjustment' => 50000],
                ];
            case 'Gà Rán':
                return [
                    ['name' => '1 miếng', 'price_adjustment' => 0],
                    ['name' => '2 miếng', 'price_adjustment' => 15000],
                    ['name' => '3 miếng', 'price_adjustment' => 30000],
                ];
            case 'Cơm':
                return [
                    ['name' => 'Phần nhỏ', 'price_adjustment' => 0],
                    ['name' => 'Phần vừa', 'price_adjustment' => 8000],
                    ['name' => 'Phần lớn', 'price_adjustment' => 15000],
                ];
            case 'Mì':
                return [
                    ['name' => 'Bình thường', 'price_adjustment' => 0],
                    ['name' => 'Thêm topping', 'price_adjustment' => 12000],
                    ['name' => 'Đặc biệt', 'price_adjustment' => 25000],
                ];
            case 'Đồ Uống':
                return [
                    ['name' => 'S', 'price_adjustment' => 0],
                    ['name' => 'M', 'price_adjustment' => 5000],
                    ['name' => 'L', 'price_adjustment' => 10000],
                ];
            default:
                return [
                    ['name' => 'Cơ bản', 'price_adjustment' => 0],
                    ['name' => 'Nâng cao', 'price_adjustment' => 10000],
                ];
        }
    }
    
    private function getProductFlavorValues($product)
    {
        $categoryName = $product->category->name;
        $productName = $product->name;
        
        switch ($categoryName) {
            case 'Burger':
                if (str_contains($productName, 'Bò')) {
                    return [
                        ['name' => 'Vị nguyên bản', 'price_adjustment' => 0],
                        ['name' => 'Vị BBQ', 'price_adjustment' => 5000],
                    ];
                } elseif (str_contains($productName, 'Gà')) {
                    return [
                        ['name' => 'Vị truyền thống', 'price_adjustment' => 0],
                        ['name' => 'Vị cay', 'price_adjustment' => 3000],
                    ];
                } else {
                    return [
                        ['name' => 'Vị cổ điển', 'price_adjustment' => 0],
                        ['name' => 'Vị đặc biệt', 'price_adjustment' => 5000],
                    ];
                }
            case 'Pizza':
                return [
                    ['name' => 'Vị truyền thống', 'price_adjustment' => 0],
                    ['name' => 'Vị cay', 'price_adjustment' => 8000],
                ];
            case 'Gà Rán':
                return [
                    ['name' => 'Vị nguyên bản', 'price_adjustment' => 0],
                    ['name' => 'Vị cay', 'price_adjustment' => 5000],
                ];
            case 'Cơm':
                return [
                    ['name' => 'Vị nhẹ', 'price_adjustment' => 0],
                    ['name' => 'Vị đậm đà', 'price_adjustment' => 3000],
                ];
            case 'Mì':
                return [
                    ['name' => 'Vị cổ điển', 'price_adjustment' => 0],
                    ['name' => 'Vị cay', 'price_adjustment' => 5000],
                ];
            case 'Đồ Uống':
                if (str_contains($productName, 'Trà')) {
                    return [
                        ['name' => 'Vị ngọt vừa', 'price_adjustment' => 0],
                        ['name' => 'Vị ngọt đậm', 'price_adjustment' => 2000],
                    ];
                } elseif (str_contains($productName, 'Cà Phê')) {
                    return [
                        ['name' => 'Vị đắng', 'price_adjustment' => 0],
                        ['name' => 'Vị ngọt', 'price_adjustment' => 3000],
                    ];
                } else {
                    return [
                        ['name' => 'Vị nguyên bản', 'price_adjustment' => 0],
                        ['name' => 'Vị đặc biệt', 'price_adjustment' => 2000],
                    ];
                }
            default:
                return [
                    ['name' => 'Vị cơ bản', 'price_adjustment' => 0],
                    ['name' => 'Vị cao cấp', 'price_adjustment' => 5000],
                ];
        }
    }

    private function createProductVariants()
    {
        $products = Product::all();
        $createdVariantsCount = 0;

        foreach ($products as $product) {
            // Kiểm tra xem sản phẩm có variant values không
            if (!isset($this->globalVariantValues[$product->id])) {
                echo "Warning: No variant values found for product {$product->name}\n";
                continue;
            }

            $sizeValues = $this->globalVariantValues[$product->id]['size'];
            $flavorValues = $this->globalVariantValues[$product->id]['flavor'];
            
            // Tạo tất cả các tổ hợp của size và flavor
            foreach ($sizeValues as $sizeValueId) {
                foreach ($flavorValues as $flavorValueId) {
                    $sizeValue = VariantValue::find($sizeValueId);
                    $flavorValue = VariantValue::find($flavorValueId);
                    
                    if (!$sizeValue || !$flavorValue) {
                        echo "Warning: Invalid VariantValue IDs for product {$product->name}\n";
                        continue;
                    }

                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $this->generateSku($product->category->name, 'variant'),
                        'active' => true
                    ]);

                    // Tạo chi tiết variant cho size
                    ProductVariantDetail::create([
                        'product_variant_id' => $variant->id,
                        'variant_value_id' => $sizeValueId
                    ]);
                    
                    // Tạo chi tiết variant cho flavor
                    ProductVariantDetail::create([
                        'product_variant_id' => $variant->id,
                        'variant_value_id' => $flavorValueId
                    ]);

                    $createdVariantsCount++;
                    echo "Created variant for {$product->name}: {$sizeValue->value} - {$flavorValue->value}\n";
                }
            }
        }

        echo "Created {$createdVariantsCount} product variants\n";
    }

    private function createToppings()
    {
        $toppingsData = [
            ['name' => 'Phô mai thêm', 'price' => 15000, 'description' => 'Phô mai Mozzarella cao cấp'],
            ['name' => 'Thịt xông khói', 'price' => 20000, 'description' => 'Thịt xông khói giòn tan'],
            ['name' => 'Trứng ốp la', 'price' => 10000, 'description' => 'Trứng gà tươi ốp la'],
            ['name' => 'Xà lách thêm', 'price' => 5000, 'description' => 'Xà lách tươi giòn'],
            ['name' => 'Cà chua thêm', 'price' => 5000, 'description' => 'Cà chua tươi ngon'],
            ['name' => 'Hành tây chiên', 'price' => 8000, 'description' => 'Hành tây chiên giòn'],
            ['name' => 'Nấm chiên', 'price' => 12000, 'description' => 'Nấm tươi chiên thơm'],
            ['name' => 'Ớt jalapeño', 'price' => 7000, 'description' => 'Ớt jalapeño cay nồng'],
            ['name' => 'Dưa chuột muối', 'price' => 6000, 'description' => 'Dưa chuột muối chua ngọt'],
            ['name' => 'Sốt BBQ thêm', 'price' => 3000, 'description' => 'Sốt BBQ đậm đà']
        ];

        $this->toppings = collect();
        
        foreach ($toppingsData as $toppingData) {
            $topping = Topping::create([
                'sku' => $this->generateSku('', 'topping'),
                'name' => $toppingData['name'],
                'price' => $toppingData['price'],
                'description' => $toppingData['description'],
                'active' => true,
                'created_by' => $this->adminUser->id,
                'updated_by' => $this->adminUser->id
            ]);
            
            $this->toppings->push($topping);
            echo "Created topping: {$topping->name}\n";
        }
    }

    private function createCombos()
    {
        // Lấy category "Combo" hoặc "Combo Set", tạo mới nếu chưa có
        $comboCategory = Category::where('name', 'Combo Set')
                               ->orWhere('name', 'Combo')
                               ->first();
        
        if (!$comboCategory) {
            // Tạo category "Combo" nếu chưa có
            $comboCategory = Category::create([
                'name' => 'Combo',
                'description' => 'Các combo sản phẩm với giá ưu đãi',
                'image' => 'combo-category.jpg',
                'status' => true
            ]);
            echo "Created category: {$comboCategory->name}\n";
        }

        $combosData = [
            [
                'name' => 'Combo Burger Bò Phô Mai Deluxe',
                'description' => 'Burger bò phô mai + khoai tây chiên + nước ngọt',
                'original_price' => 150000, // Giá gốc
                'price' => 120000, // Giá ưu đãi
                'products' => ['Burger Bò Phô Mai', 'Coca Cola']
            ],
            [
                'name' => 'Combo Gà Rán Gia Đình',
                'description' => 'Gà rán giòn + cơm + nước ngọt cho cả gia đình',
                'original_price' => 300000,
                'price' => 250000,
                'products' => ['Gà Rán Giòn', 'Cơm Gà Rán', 'Pepsi']
            ],
            [
                'name' => 'Combo Pizza Hải Sản Cao Cấp',
                'description' => 'Pizza hải sản + salad + nước ép',
                'original_price' => 220000,
                'price' => 180000,
                'products' => ['Pizza Hải Sản', 'Nước Cam']
            ],
            [
                'name' => 'Combo Học Sinh Tiết Kiệm',
                'description' => 'Burger gà + nước ngọt với giá ưu đãi',
                'original_price' => 80000,
                'price' => 65000,
                'products' => ['Burger Gà Giòn', '7 Up']
            ],
            [
                'name' => 'Combo Cặp Đôi Lãng Mạn',
                'description' => '2 burger + 2 nước ngọt + khoai tây chiên',
                'original_price' => 180000,
                'price' => 150000,
                'products' => ['Burger Bò Phô Mai', 'Burger Gà Giòn', 'Coca Cola', 'Pepsi']
            ]
        ];

        foreach ($combosData as $comboData) {
            $combo = Combo::create([
                'sku' => $this->generateSku('', 'combo'),
                'name' => $comboData['name'],
                'slug' => Str::slug($comboData['name']),
                'description' => $comboData['description'],
                'original_price' => $comboData['original_price'],
                'price' => $comboData['price'],
                'category_id' => $comboCategory->id,
                'active' => true,
                'created_by' => $this->adminUser->id,
                'updated_by' => $this->adminUser->id
            ]);

            foreach ($comboData['products'] as $productName) {
                $product = Product::where('name', $productName)->first();
                if ($product) {
                    $productVariant = ProductVariant::where('product_id', $product->id)->first();
                    if ($productVariant) {
                        ComboItem::create([
                            'combo_id' => $combo->id,
                            'product_variant_id' => $productVariant->id,
                            'quantity' => 1
                        ]);
                    }
                }
            }

            echo "Created combo: {$combo->name}\n";
        }
    }

    private function createStocks()
    {
        $toppingStocksCreated = 0;
        foreach ($this->branches as $branch) {
            foreach ($this->toppings as $topping) {
                ToppingStock::create([
                    'branch_id' => $branch->id,
                    'topping_id' => $topping->id,
                    'stock_quantity' => rand(50, 200)
                ]);
                $toppingStocksCreated++;
            }
        }
        echo "Created {$toppingStocksCreated} topping stock entries\n";
        $branchStocksCreated = 0;
        $productVariants = ProductVariant::all();
        foreach ($this->branches as $branch) {
            foreach ($productVariants as $variant) {
                // Ensure unique branch_id + product_variant_id
                BranchStock::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'product_variant_id' => $variant->id
                    ],
                    [
                        'stock_quantity' => rand(30, 150)
                    ]
                );
                $branchStocksCreated++;
            }
        }
        echo "Created {$branchStocksCreated} branch stock entries\n";
        
        // Tạo combo branch stocks
        $comboBranchStocksCreated = 0;
        $combos = Combo::all();
        foreach ($this->branches as $branch) {
            foreach ($combos as $combo) {
                ComboBranchStock::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'combo_id' => $combo->id
                    ],
                    [
                        'quantity' => rand(20, 100)
                    ]
                );
                $comboBranchStocksCreated++;
            }
        }
        echo "Created {$comboBranchStocksCreated} combo branch stock entries\n";
    }

    private function createProductToppings()
    {
        $products = Product::all();
        $toppings = $this->toppings;
        
        foreach ($products as $product) {
            $categoryName = $product->category->name;
            
            // Chỉ tạo product toppings cho các loại sản phẩm phù hợp
            if (in_array($categoryName, ['Burger', 'Pizza', 'Gà Rán', 'Cơm', 'Mì'])) {
                $numberOfToppings = rand(3, 7);
                $selectedToppings = $toppings->random($numberOfToppings);
                
                foreach ($selectedToppings as $topping) {
                    ProductTopping::firstOrCreate([
                        'product_id' => $product->id,
                        'topping_id' => $topping->id
                    ]);
                }
            }
        }
        
        echo "Created product-topping relationships\n";
    }

    public function run(): void
    {
        try {
            echo "Starting FastFood Seeder...\n";
            
            // Kiểm tra model Driver
            if (!class_exists(\App\Models\Driver::class)) {
                echo "Error: Driver model not found. Make sure it exists before running this seeder.\n";
                return;
            }

            // 1. Cleanup dữ liệu cũ
            $this->cleanupData();
            
            // 2. Tạo user ranks
            $this->createUserRanks();
            
            // 3. Tạo dữ liệu cơ bản (users, drivers, branches)
            $this->createBasicData();
            
            // 4. Tạo categories và products
            $this->createCategories();
            
            // 5. Tạo variant attributes và values
            $this->createVariantAttributes();
            
            // 6. Tạo product variants
            $this->createProductVariants();
            
            // 7. Tạo toppings
            $this->createToppings();
            
            // 8. Tạo combos
            $this->createCombos();
            
            // 9. Tạo stocks
            $this->createStocks();
            
            // 10. Tạo product-topping relationships
            $this->createProductToppings();
            
            echo "FastFood Seeder completed successfully!\n";
            
        } catch (\Exception $e) {
            echo "Error in FastFood Seeder: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }
}
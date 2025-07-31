<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa tất cả banner cũ trước khi tạo mới
        $this->command->info("🗑️ Xóa tất cả banner cũ...");
        Banner::truncate();
        $this->command->info("✅ Đã xóa tất cả banner cũ.");
        
        $this->command->info("🚀 Bắt đầu tạo banner mới với slug...");
        
        // === LẤY DANH SÁCH SẢN PHẨM CÓ SLUG ===
        $products = Product::where('status', 'selling')
                          ->whereNotNull('slug')
                          ->where('slug', '!=', '')
                          ->inRandomOrder()
                          ->take(20)
                          ->get(['id', 'name', 'slug']);

        if ($products->isEmpty()) {
            $this->command->error("❌ Không tìm thấy sản phẩm nào có slug để tạo banner.");
            return;
        }

        // === LẤY DANH SÁCH ẢNH TỪ S3 THƯ MỤC 'banners/' ===
        $imageFiles = Storage::disk('s3')->files('banners');

        if (empty($imageFiles)) {
            $this->command->error("❌ Không tìm thấy ảnh nào trong thư mục 'banners/' trên S3.");
            return;
        }

        $index = 0;
        foreach ($imageFiles as $path) {
            // Bỏ qua nếu không phải định dạng ảnh
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $this->command->warn("⚠️ Bỏ qua file không phải ảnh: " . $path);
                continue;
            }

            $imageUrl = Storage::disk('s3')->url($path);
            $filename = basename($path);
            
            // Lấy sản phẩm ngẫu nhiên để tạo link
            $randomProduct = $products->random();

            Banner::create([
                'title' => 'Banner mẫu ' . ($index + 1),
                'position' => 'homepage',
                'order' => $index,
                'image_path' => $imageUrl,
                'link' => '/shop/products/' . $randomProduct->slug,
                'description' => 'Banner tự động tạo từ ảnh S3: ' . $filename . ' - Link đến: ' . $randomProduct->name,
                'start_at' => now(),
                'end_at' => now()->addDays(30),
                'is_active' => true
            ]);

            $this->command->info("✅ Đã tạo banner từ S3: {$filename} -> Link: /shop/products/{$randomProduct->slug}");
            $index++;
        }

        // === PHẦN VỊ TRÍ KHÁC - SỬ DỤNG SLUG CHO BANNER CÓ LINK SẢN PHẨM ===
        $extraBanners = [
            [
                'title' => 'Banner chân trang',
                'position' => 'footers',
                'image_path' => 'https://example.com/banners/footer.jpg',
                'link' => '/footer/info',
                'description' => 'Banner cho phần chân trang'
            ],
            [
                'title' => 'Banner khuyến mãi',
                'position' => 'promotions',
                'image_path' => 'https://example.com/banners/promotion.jpg',
                'link' => '/shop/products/' . ($products->isNotEmpty() ? $products->random()->slug : 'san-pham-khuyen-mai'),
                'description' => 'Banner chương trình khuyến mãi'
            ],
            [
                'title' => 'Banner menu',
                'position' => 'menu',
                'image_path' => 'https://example.com/banners/menu.jpg',
                'link' => '/shop/products/' . ($products->isNotEmpty() ? $products->random()->slug : 'mon-an-dac-biet'),
                'description' => 'Banner cho thanh menu chính'
            ],
            [
                'title' => 'Banner chi nhánh',
                'position' => 'branch',
                'image_path' => 'https://example.com/banners/branch.jpg',
                'link' => '/branches',
                'description' => 'Banner giới thiệu chi nhánh'
            ],
            [
                'title' => 'Banner giới thiệu',
                'position' => 'abouts',
                'image_path' => 'https://example.com/banners/about.jpg',
                'link' => '/about-us',
                'description' => 'Banner phần giới thiệu'
            ],
            [
                'title' => 'Banner hỗ trợ',
                'position' => 'supports',
                'image_path' => 'https://example.com/banners/support.jpg',
                'link' => '/support',
                'description' => 'Banner phần hỗ trợ khách hàng'
            ],
            [
                'title' => 'Banner liên hệ',
                'position' => 'contacts',
                'image_path' => 'https://example.com/banners/contact.jpg',
                'link' => '/contact',
                'description' => 'Banner phần liên hệ'
            ]
        ];

        foreach ($extraBanners as $item) {
            Banner::create([
                'title' => $item['title'],
                'position' => $item['position'],
                'order' => null,
                'image_path' => $item['image_path'],
                'link' => $item['link'],
                'description' => $item['description'],
                'start_at' => now(),
                'end_at' => now()->addDays(30),
                'is_active' => true
            ]);

            $linkInfo = str_contains($item['link'], '/shop/products/') ? 
                        " -> Link: {$item['link']}" : 
                        " -> Static link: {$item['link']}";
            $this->command->info("📝 Đã tạo banner tĩnh cho vị trí: {$item['position']}{$linkInfo}");
        }
        $this->command->info("🎉 Seeder hoàn tất: tạo banner từ S3, các vị trí tĩnh");
    }

}

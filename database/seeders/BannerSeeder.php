<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

            Banner::create([
                'title' => 'Banner mẫu ' . ($index + 1),
                'position' => 'homepage',
                'order' => $index,
                'image_path' => $imageUrl,
                'link' => '/shop/products/' . rand(1, 100),
                'description' => 'Banner tự động tạo từ ảnh S3: ' . $filename,
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'is_active' => true
            ]);

            $this->command->info("✅ Đã tạo banner từ S3: {$filename}");
            $index++;
        }

        // === PHẦN VỊ TRÍ KHÁC VẪN GIỮ NGUYÊN ===
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
                'link' => '/promotions',
                'description' => 'Banner chương trình khuyến mãi'
            ],
            [
                'title' => 'Banner menu',
                'position' => 'menu',
                'image_path' => 'https://example.com/banners/menu.jpg',
                'link' => '/menu',
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
                'end_at' => now()->addDays(7),
                'is_active' => true
            ]);

            $this->command->info("📝 Đã tạo banner tĩnh cho vị trí: {$item['position']}");
        }

        $this->command->info("🎉 Seeder hoàn tất: tạo banner từ S3 và các vị trí tĩnh.");
    }
}

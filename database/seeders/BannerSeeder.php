<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Banner;
use Illuminate\Database\Seeder;
use File;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folderPath = storage_path('app/public/banners');

        if (!File::exists($folderPath)) {
            $this->command->error("❌ Thư mục ảnh không tồn tại: {$folderPath}");
            return;
        }

        $imageFiles = File::files($folderPath);

        if (empty($imageFiles)) {
            $this->command->error("❌ Không có ảnh nào trong thư mục: {$folderPath}");
            return;
        }

        // === PHẦN HOMEPAGE GIỮ NGUYÊN NHƯ CŨ ===
        foreach ($imageFiles as $index => $file) {
            $extension = strtolower($file->getExtension());

            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $this->command->warn("⚠️ Bỏ qua file không phải ảnh: " . $file->getFilename());
                continue;
            }

            $originalName = $file->getFilename();
            $s3Filename = 'banners/' . $originalName;

            if (Storage::disk('s3')->exists($s3Filename)) {
                $this->command->line("ℹ️  Ảnh đã tồn tại trên S3: $originalName");
            } else {
                $imageContent = File::get($file);
                Storage::disk('s3')->put($s3Filename, $imageContent);
                $this->command->info("✅ Đã upload ảnh lên S3: $originalName");
            }

            $imageUrl = Storage::disk('s3')->url($s3Filename);

            Banner::create([
                'title' => 'Banner mẫu ' . ($index + 1),
                'position' => 'homepage',
                'order' => $index,
                'image_path' => $imageUrl,
                'link' => '/shop/products/' . rand(1, 100),
                'description' => 'Banner được tạo tự động từ ảnh mẫu',
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'is_active' => true
            ]);

            $this->command->info("📝 Đã tạo banner từ ảnh: " . $originalName);
        }

        // === PHẦN VỊ TRÍ KHÁC DÙNG MẢNG CỐ ĐỊNH ===
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

        $this->command->info("🎉 Seeder hoàn tất tạo banner cho homepage và các vị trí đặc biệt.");
    }
}

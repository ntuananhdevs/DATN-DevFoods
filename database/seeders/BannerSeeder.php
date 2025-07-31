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
        
        $this->command->info("🚀 Bắt đầu tạo banner mới chỉ hiển thị ảnh...");
        
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
                'title' => null, // Không có title
                'position' => 'homepage',
                'order' => $index,
                'image_path' => $imageUrl,
                'link' => null, // Không có link
                'description' => null, // Không có mô tả
                'start_at' => now(),
                'end_at' => now()->addDays(30),
                'is_active' => true
            ]);

            $this->command->info("✅ Đã tạo banner từ S3: {$filename} (chỉ hiển thị ảnh)");
            $index++;
        }

        // === PHẦN VỊ TRÍ KHÁC - CHỈ HIỂN THỊ ẢNH ===
        $extraBanners = [
            [
                'position' => 'footers',
                'image_path' => 'https://example.com/banners/footer.jpg'
            ],
            [
                'position' => 'promotions',
                'image_path' => 'https://example.com/banners/promotion.jpg'
            ],
            [
                'position' => 'menu',
                'image_path' => 'https://example.com/banners/menu.jpg'
            ],
            [
                'position' => 'branch',
                'image_path' => 'https://example.com/banners/branch.jpg'
            ],
            [
                'position' => 'abouts',
                'image_path' => 'https://example.com/banners/about.jpg'
            ],
            [
                'position' => 'supports',
                'image_path' => 'https://example.com/banners/support.jpg'
            ],
            [
                'position' => 'contacts',
                'image_path' => 'https://example.com/banners/contact.jpg'
            ]
        ];

        foreach ($extraBanners as $item) {
            Banner::create([
                'title' => null, // Không có title
                'position' => $item['position'],
                'order' => null,
                'image_path' => $item['image_path'],
                'link' => null, // Không có link
                'description' => null, // Không có mô tả
                'start_at' => now(),
                'end_at' => now()->addDays(30),
                'is_active' => true
            ]);

            $this->command->info("📝 Đã tạo banner tĩnh cho vị trí: {$item['position']} (chỉ hiển thị ảnh)");
        }
        $this->command->info("🎉 Seeder hoàn tất: tạo banner chỉ hiển thị ảnh, không có title, link và mô tả");
    }

}

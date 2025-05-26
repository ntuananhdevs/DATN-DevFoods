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

        foreach ($imageFiles as $index => $file) {
            $extension = strtolower($file->getExtension());

            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $this->command->warn("⚠️ Bỏ qua file không phải ảnh: " . $file->getFilename());
                continue;
            }

            $originalName = $file->getFilename(); // tên gốc trong thư mục local
            $s3Filename = 'banners/' . $originalName;

            // Kiểm tra xem file đã tồn tại trên S3 chưa
            if (Storage::disk('s3')->exists($s3Filename)) {
                $this->command->line("ℹ️  Ảnh đã tồn tại trên S3: $originalName");
            } else {
                // Upload lên AWS S3 nếu chưa có
                $imageContent = File::get($file);
                Storage::disk('s3')->put($s3Filename, $imageContent);
                $this->command->info("✅ Đã upload ảnh lên S3: $originalName");
            }

            // Lấy URL dù upload hay đã có sẵn
            $imageUrl = Storage::disk('s3')->url($s3Filename);

            // Tạo bản ghi trong database
            Banner::create([
                'title' => 'Banner mẫu ' . ($index + 1),
                'position' => 'homepage',
                'order' => $index,
                'image_path' => $imageUrl,
                'link' => '/shop/products/show/' . rand(1, 100),
                'description' => 'Banner được tạo tự động từ ảnh mẫu',
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'is_active' => true
            ]);

            $this->command->info("📝 Đã tạo banner từ ảnh: " . $originalName);
        }

        $this->command->info("🎉 Hoàn tất tạo banner từ " . count($imageFiles) . " ảnh.");
    }
}

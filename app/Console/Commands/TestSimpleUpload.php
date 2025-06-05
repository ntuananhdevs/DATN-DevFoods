<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestSimpleUpload extends Command
{
    protected $signature = 'test:simple-upload';
    protected $description = 'Simple test for upload functionality';

    public function handle()
    {
        $this->info('🧪 Starting Simple Upload Test...');

        // Test 1: Simple S3 upload
        $this->info('1. Testing basic S3 upload...');
        try {
            $content = 'Test content ' . now();
            $path = 'users/avatars/test-' . time() . '.txt';
            
            $result = Storage::disk('s3')->put($path, $content);
            
            if ($result) {
                $this->info('✅ S3 Upload: SUCCESS');
                $this->info('📁 Path: ' . $path);
                
                // Try to get URL
                $url = Storage::disk('s3')->url($path);
                $this->info('🔗 URL: ' . $url);
            } else {
                $this->error('❌ S3 Upload: FAILED');
            }
        } catch (\Exception $e) {
            $this->error('❌ S3 Upload Exception: ' . $e->getMessage());
        }

        // Test 2: cURL download test
        $this->info('2. Testing cURL download...');
        $testUrl = 'https://lh3.googleusercontent.com/a/ACg8ocK_Ww5eI0kXytYemXzCzupt1cYu7ws74-M5csKSxUkK_1gfng=s96-c';
        
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $testUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($content && $httpCode == 200) {
                $this->info('✅ cURL Download: SUCCESS');
                $this->info('📊 Size: ' . strlen($content) . ' bytes');
                $this->info('🌐 HTTP Code: ' . $httpCode);
                
                // Try to upload this to S3
                $imagePath = 'users/avatars/test-avatar-' . time() . '.jpg';
                $uploadResult = Storage::disk('s3')->put($imagePath, $content);
                
                if ($uploadResult) {
                    $this->info('✅ Image Upload to S3: SUCCESS');
                    $imageUrl = Storage::disk('s3')->url($imagePath);
                    $this->info('🖼️  Image URL: ' . $imageUrl);
                } else {
                    $this->error('❌ Image Upload to S3: FAILED');
                }
                
            } else {
                $this->error('❌ cURL Download FAILED');
                $this->error('🌐 HTTP Code: ' . $httpCode);
                $this->error('❌ Error: ' . $error);
            }
        } catch (\Exception $e) {
            $this->error('❌ cURL Exception: ' . $e->getMessage());
        }

        $this->info('🏁 Test completed!');
        return 0;
    }
} 
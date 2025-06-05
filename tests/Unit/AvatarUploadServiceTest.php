<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AvatarUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AvatarUploadServiceTest extends TestCase
{
    protected $avatarUploadService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->avatarUploadService = new AvatarUploadService();
    }

    public function test_cleanup_temp_files_removes_old_files()
    {
        // Arrange: Create temp directory and old file
        Storage::fake('local');
        $tempDir = 'temp/avatars';
        $oldFile = $tempDir . '/old_avatar.jpg';
        
        Storage::disk('local')->makeDirectory($tempDir);
        Storage::disk('local')->put($oldFile, 'fake image content');
        
        // Simulate old file by setting timestamp to 2 hours ago
        $oldTimestamp = time() - 7200; // 2 hours ago
        touch(Storage::disk('local')->path($oldFile), $oldTimestamp);

        // Act
        $cleanedCount = $this->avatarUploadService->cleanupTempFiles();

        // Assert
        $this->assertEquals(1, $cleanedCount);
        $this->assertFalse(Storage::disk('local')->exists($oldFile));
    }

    public function test_cleanup_temp_files_keeps_recent_files()
    {
        // Arrange: Create temp directory and recent file
        Storage::fake('local');
        $tempDir = 'temp/avatars';
        $recentFile = $tempDir . '/recent_avatar.jpg';
        
        Storage::disk('local')->makeDirectory($tempDir);
        Storage::disk('local')->put($recentFile, 'fake image content');

        // Act
        $cleanedCount = $this->avatarUploadService->cleanupTempFiles();

        // Assert
        $this->assertEquals(0, $cleanedCount);
        $this->assertTrue(Storage::disk('local')->exists($recentFile));
    }

    public function test_cleanup_temp_files_handles_missing_directory()
    {
        // Arrange: No temp directory exists
        Storage::fake('local');

        // Act
        $cleanedCount = $this->avatarUploadService->cleanupTempFiles();

        // Assert
        $this->assertEquals(0, $cleanedCount);
    }
}

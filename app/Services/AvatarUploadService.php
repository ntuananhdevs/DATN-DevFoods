<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class AvatarUploadService
{
    /**
     * Download avatar from Google and upload to S3
     *
     * @param string|null $photoURL Google photo URL
     * @param string $userEmail User email for file naming
     * @return string|null Filename only (not including users/avatars/ path) or null if failed
     */
    public function uploadGoogleAvatar($photoURL, $userEmail)
    {
        if (empty($photoURL)) {
            return null;
        }

        try {
            // Download image from Google to local temp file
            $tempFilePath = $this->downloadImageToLocal($photoURL, $userEmail);
            
            if (!$tempFilePath) {
                return null;
            }

            // Read the local file
            $imageContent = Storage::disk('local')->get($tempFilePath);
            
            if (!$imageContent) {
                return null;
            }

            // Generate file name only (without path)
            $fileName = $this->generateFileName($userEmail, $photoURL);
            
            // Upload to S3 in users/avatars folder
            $s3Path = "users/avatars/{$fileName}";
            
            // Simple upload
            $uploaded = Storage::disk('s3')->put($s3Path, $imageContent);

            if ($uploaded) {
                return $fileName; // Return only filename (e.g., "avatar_hash_timestamp_random.jpg")
            } else {
                throw new \Exception('S3 upload failed');
            }

        } catch (\Exception $e) {
            Log::error('Avatar upload failed', [
                'error' => $e->getMessage(),
                'url' => $photoURL,
                'user_email' => $userEmail
            ]);
            throw $e;
        } finally {
            // Clean up temp file
            if (isset($tempFilePath) && Storage::disk('local')->exists($tempFilePath)) {
                Storage::disk('local')->delete($tempFilePath);
            }
        }
    }

    /**
     * Download image from URL to local temp storage
     *
     * @param string $url
     * @param string $userEmail
     * @return string|null Path to temp file or null if failed
     */
    private function downloadImageToLocal($url, $userEmail)
    {
        try {
            // Add size parameter to get higher quality image
            $highQualityUrl = $this->getHighQualityGooglePhotoUrl($url);
            
            Log::info('Attempting to download image to local', ['url' => $highQualityUrl]);

            // Create temp directory if it doesn't exist
            $tempDir = 'temp/avatars';
            if (!Storage::disk('local')->exists($tempDir)) {
                Storage::disk('local')->makeDirectory($tempDir);
            }

            // Generate temp file name
            $tempFileName = 'temp_avatar_' . md5($userEmail) . '_' . time() . '_' . Str::random(8) . '.jpg';
            $tempFilePath = $tempDir . '/' . $tempFileName;

            // Try to download with cURL first
            $imageContent = $this->downloadWithCurl($highQualityUrl);
            
            if ($imageContent === false && $highQualityUrl !== $url) {
                Log::warning('High quality download failed, trying original URL', ['original_url' => $url]);
                $imageContent = $this->downloadWithCurl($url);
            }

            if ($imageContent === false) {
                Log::error('Failed to download image with cURL', ['url' => $url]);
                return null;
            }

            // Validate image content
            if (!$this->isValidImageContent($imageContent)) {
                Log::error('Downloaded content is not a valid image', ['url' => $url]);
                return null;
            }

            // Save to local temp file
            $saved = Storage::disk('local')->put($tempFilePath, $imageContent);
            
            if ($saved) {
                Log::info('Image downloaded successfully to local', [
                    'url' => $highQualityUrl,
                    'temp_path' => $tempFilePath,
                    'size' => strlen($imageContent) . ' bytes'
                ]);
                return $tempFilePath;
            } else {
                Log::error('Failed to save image to local storage', [
                    'temp_path' => $tempFilePath,
                    'url' => $url
                ]);
            }

        } catch (Exception $e) {
            Log::error('Error downloading image to local', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return null;
    }

    /**
     * Validate if content is a valid image
     *
     * @param string $content
     * @return bool
     */
    private function isValidImageContent($content)
    {
        if (empty($content) || strlen($content) < 100) {
            return false;
        }

        // Check for common image file signatures
        $imageSignatures = [
            "\xFF\xD8\xFF", // JPEG
            "\x89PNG\r\n\x1a\n", // PNG
            "GIF87a", // GIF87a
            "GIF89a", // GIF89a
            "RIFF", // WebP (starts with RIFF)
        ];

        foreach ($imageSignatures as $signature) {
            if (strpos($content, $signature) === 0) {
                return true;
            }
        }

        // Additional check for WebP
        if (strpos($content, "RIFF") === 0 && strpos($content, "WEBP") !== false) {
            return true;
        }

        return false;
    }

    /**
     * Download image using cURL with better SSL handling
     *
     * @param string $url
     * @return string|false
     */
    private function downloadWithCurl($url)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 60, // Increased timeout for large images
            CURLOPT_CONNECTTIMEOUT => 15, // Increased connection timeout
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for development
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Cache-Control: no-cache',
                'DNT: 1',
                'Upgrade-Insecure-Requests: 1',
            ],
            CURLOPT_ENCODING => '', // Enable all supported encodings
        ]);

        $imageContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error = curl_error($ch);
        $downloadSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
        
        curl_close($ch);

        if ($imageContent === false || !empty($error)) {
            Log::error('cURL download failed', [
                'url' => $url,
                'http_code' => $httpCode,
                'curl_error' => $error,
                'content_type' => $contentType
            ]);
            return false;
        }

        if ($httpCode >= 400) {
            Log::error('HTTP error during download', [
                'url' => $url,
                'http_code' => $httpCode,
                'content_type' => $contentType
            ]);
            return false;
        }

        // Check if we actually got image content
        if (!str_starts_with($contentType, 'image/') && !empty($contentType)) {
            Log::warning('Downloaded content may not be an image', [
                'url' => $url,
                'content_type' => $contentType,
                'size' => $downloadSize
            ]);
        }

        Log::info('Image download successful', [
            'url' => $url,
            'http_code' => $httpCode,
            'content_type' => $contentType,
            'size' => $downloadSize . ' bytes'
        ]);

        return $imageContent;
    }

    /**
     * Get high quality Google photo URL
     *
     * @param string $url
     * @return string
     */
    private function getHighQualityGooglePhotoUrl($url)
    {
        // Remove size parameters and add high quality size
        $url = preg_replace('/=s\d+-c/', '=s400-c', $url);
        $url = preg_replace('/=w\d+-h\d+/', '=w400-h400', $url);
        
        // If no size parameter exists, add one
        if (!preg_match('/=s\d+/', $url) && !preg_match('/=w\d+/', $url)) {
            $url .= '=s400-c';
        }
        
        return $url;
    }

    /**
     * Generate unique file name for avatar
     *
     * @param string $userEmail
     * @param string $photoURL
     * @return string
     */
    private function generateFileName($userEmail, $photoURL)
    {
        $extension = $this->getFileExtension($photoURL);
        $emailHash = md5($userEmail);
        $timestamp = time();
        $random = Str::random(8);
        
        return "avatar_{$emailHash}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get file extension from URL
     *
     * @param string $url
     * @return string
     */
    private function getFileExtension($url)
    {
        // Try to get extension from URL
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Default to jpg for Google photos
        if (empty($extension) || !in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $extension = 'jpg';
        }
        
        return strtolower($extension);
    }

    /**
     * Get content type for file
     *
     * @param string $url
     * @return string
     */
    private function getContentType($url)
    {
        $extension = $this->getFileExtension($url);
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        return $mimeTypes[$extension] ?? 'image/jpeg';
    }

    /**
     * Delete avatar from S3
     *
     * @param string $s3Url
     * @return bool
     */
    public function deleteAvatar($s3Url)
    {
        try {
            if (empty($s3Url) || !str_contains($s3Url, 's3.amazonaws.com')) {
                return true; // Not an S3 URL, nothing to delete
            }

            // Extract S3 path from URL
            $s3Path = $this->extractS3PathFromUrl($s3Url);
            
            if ($s3Path) {
                Storage::disk('s3')->delete($s3Path);
                Log::info('Deleted avatar from S3', ['path' => $s3Path]);
                return true;
            }
        } catch (Exception $e) {
            Log::error('Error deleting avatar from S3', [
                'url' => $s3Url,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }

    /**
     * Extract S3 path from full URL
     *
     * @param string $url
     * @return string|null
     */
    private function extractS3PathFromUrl($url)
    {
        // Extract path from S3 URL
        $pattern = '/https:\/\/[^\/]+\.s3[^\/]*\.amazonaws\.com\/(.+)/';
        
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Clean up old temporary files (older than 1 hour)
     *
     * @return int Number of files cleaned up
     */
    public function cleanupTempFiles()
    {
        try {
            $tempDir = 'temp/avatars';
            $cleanedCount = 0;
            
            if (!Storage::disk('local')->exists($tempDir)) {
                return 0;
            }

            $files = Storage::disk('local')->files($tempDir);
            $cutoffTime = time() - 3600; // 1 hour ago

            foreach ($files as $file) {
                $lastModified = Storage::disk('local')->lastModified($file);
                
                if ($lastModified < $cutoffTime) {
                    Storage::disk('local')->delete($file);
                    $cleanedCount++;
                    Log::info('Cleaned up old temp file', ['file' => $file]);
                }
            }

            Log::info('Temp file cleanup completed', ['files_cleaned' => $cleanedCount]);
            return $cleanedCount;
            
        } catch (Exception $e) {
            Log::error('Error during temp file cleanup', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Delete avatar from S3 using filename
     *
     * @param string $filename
     * @return bool
     */
    public function deleteAvatarByFilename($filename)
    {
        try {
            if (empty($filename)) {
                return true;
            }

            // If it's a full URL, extract filename
            if (str_starts_with($filename, 'http')) {
                $filename = basename($filename);
            }

            $s3Path = "users/avatars/{$filename}";
            Storage::disk('s3')->delete($s3Path);
            Log::info('Deleted avatar from S3', ['filename' => $filename]);
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting avatar from S3', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }
} 
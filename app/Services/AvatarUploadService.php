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
     * @return string|null S3 URL or null if failed
     */
    public function uploadGoogleAvatar($photoURL, $userEmail)
    {
        if (empty($photoURL)) {
            return null;
        }

        try {
            // Download image from Google
            $imageContent = $this->downloadImage($photoURL);
            
            if (!$imageContent) {
                Log::warning('Failed to download Google avatar', ['url' => $photoURL]);
                return $photoURL; // Fallback to original URL
            }

            // Generate file name
            $fileName = $this->generateFileName($userEmail, $photoURL);
            
            // Upload to S3
            $s3Path = "avatars/google/{$fileName}";
            
            $uploaded = Storage::disk('s3')->put($s3Path, $imageContent, [
                'visibility' => 'public',
                'ContentType' => $this->getContentType($photoURL),
            ]);

            if ($uploaded) {
                // Build S3 URL manually
                $s3Url = $this->buildS3Url($s3Path);
                Log::info('Successfully uploaded Google avatar to S3', [
                    'original_url' => $photoURL,
                    's3_url' => $s3Url,
                    'user_email' => $userEmail
                ]);
                return $s3Url;
            }

        } catch (Exception $e) {
            Log::error('Error uploading Google avatar to S3', [
                'error' => $e->getMessage(),
                'url' => $photoURL,
                'user_email' => $userEmail
            ]);
        }

        // Return original URL as fallback
        return $photoURL;
    }

    /**
     * Download image from URL
     *
     * @param string $url
     * @return string|false
     */
    private function downloadImage($url)
    {
        try {
            // Add size parameter to get higher quality image
            $highQualityUrl = $this->getHighQualityGooglePhotoUrl($url);
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'FastFood App/1.0'
                ]
            ]);

            $imageContent = file_get_contents($highQualityUrl, false, $context);
            
            if ($imageContent === false) {
                // Try original URL if high quality fails
                $imageContent = file_get_contents($url, false, $context);
            }

            return $imageContent;
        } catch (Exception $e) {
            Log::error('Error downloading image', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
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
     * Build S3 URL manually
     *
     * @param string $s3Path
     * @return string
     */
    private function buildS3Url($s3Path)
    {
        $bucket = env('AWS_BUCKET');
        $region = env('AWS_DEFAULT_REGION', 'us-east-1');
        $customUrl = env('AWS_URL');
        
        // If custom URL is set, use it
        if ($customUrl) {
            return rtrim($customUrl, '/') . '/' . ltrim($s3Path, '/');
        }
        
        // Build standard S3 URL
        if ($region === 'us-east-1') {
            return "https://{$bucket}.s3.amazonaws.com/{$s3Path}";
        } else {
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$s3Path}";
        }
    }
} 
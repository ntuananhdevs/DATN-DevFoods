<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestController extends Controller
{
    /**
     * Show upload form for testing
     */
    public function showUploadForm()
    {
        return view('test.upload');
    }

    /**
     * Handle the image upload to S3
     */
    public function uploadImage(Request $request)
    {
        try {
            // Validate the uploaded file
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                \Log::info('Uploading image', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType()
                ]);
                // Generate unique filename
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                // Upload to S3
                $path = Storage::disk('s3')->put('test-uploads/' . $filename, file_get_contents($image));
                \Log::info('S3 put result', ['path' => $path, 'filename' => $filename]);
                if ($path) {
                    // Get the URL of uploaded file
                    $url = Storage::disk('s3')->url('test-uploads/' . $filename);
                    \Log::info('S3 file url', ['url' => $url]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Ảnh đã được upload thành công!',
                        'filename' => $filename,
                        'path' => $path,
                        'url' => $url,
                        'size' => $image->getSize(),
                        'original_name' => $image->getClientOriginalName()
                    ]);
                }
            }
            \Log::error('Upload failed: No file or S3 put failed');
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi upload ảnh'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Upload Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all uploaded test images
     */
    public function listImages()
    {
        try {
            // Check S3 connection first
            \Log::info('Listing images from S3...');
            if (!Storage::disk('s3')->exists('test-uploads')) {
                Storage::disk('s3')->makeDirectory('test-uploads');
            }
            $files = Storage::disk('s3')->files('test-uploads');
            $images = [];
            foreach ($files as $file) {
                try {
                    $images[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'url' => Storage::disk('s3')->url($file),
                        'size' => Storage::disk('s3')->size($file),
                        'last_modified' => Storage::disk('s3')->lastModified($file)
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error processing file ' . $file . ': ' . $e->getMessage());
                    continue;
                }
            }
            \Log::info('S3 images listed', ['count' => count($images)]);
            return response()->json([
                'success' => true,
                'images' => $images,
                'total' => count($images)
            ]);
        } catch (\Exception $e) {
            \Log::error('S3 List Images Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách ảnh: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Delete a test image from S3
     */
    public function deleteImage(Request $request, $filename = null)
    {
        try {
            $filename = $filename ?: $request->input('filename');
            \Log::info('Delete image request', ['filename' => $filename]);
            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên file không được để trống'
                ], 400);
            }
            $path = 'test-uploads/' . $filename;
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
                \Log::info('Deleted image from S3', ['path' => $path]);
                return response()->json([
                    'success' => true,
                    'message' => 'Ảnh đã được xóa thành công!'
                ]);
            }
            \Log::warning('Delete failed: file not found', ['path' => $path]);
            return response()->json([
                'success' => false,
                'message' => 'File không tồn tại'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Delete Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test S3 connection
     */
    public function testConnection()
    {
        try {
            \Log::info('Testing S3 connection...');
            Storage::disk('s3')->files();
            \Log::info('S3 connection successful');
            return response()->json([
                'success' => true,
                'message' => 'Kết nối S3 thành công!',
                'config' => [
                    'bucket' => config('filesystems.disks.s3.bucket'),
                    'region' => config('filesystems.disks.s3.region'),
                    'url' => config('filesystems.disks.s3.url')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('S3 Connection Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối S3: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
} 
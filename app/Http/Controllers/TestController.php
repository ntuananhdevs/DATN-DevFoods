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
                
                // Generate unique filename
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                
                // Upload to S3
                $path = Storage::disk('s3')->put('test-uploads/' . $filename, file_get_contents($image));
                
                if ($path) {
                    // Get the URL of uploaded file
                    $url = Storage::disk('s3')->url('test-uploads/' . $filename);
                    
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

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi upload ảnh'
            ], 400);

        } catch (\Exception $e) {
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
            $files = Storage::disk('s3')->files('test-uploads');
            $images = [];

            foreach ($files as $file) {
                $images[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'url' => Storage::disk('s3')->url($file),
                    'size' => Storage::disk('s3')->size($file),
                    'last_modified' => Storage::disk('s3')->lastModified($file)
                ];
            }

            return response()->json([
                'success' => true,
                'images' => $images,
                'total' => count($images)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a test image from S3
     */
    public function deleteImage(Request $request, $filename = null)
    {
        try {
            // Get filename from URL parameter or request body
            $filename = $filename ?: $request->input('filename');
            
            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên file không được để trống'
                ], 400);
            }

            $path = 'test-uploads/' . $filename;
            
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Ảnh đã được xóa thành công!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File không tồn tại'
            ], 404);

        } catch (\Exception $e) {
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
            // Test by trying to list files in the bucket
            Storage::disk('s3')->files();
            
            return response()->json([
                'success' => true,
                'message' => 'Kết nối S3 thành công!',
                'config' => [
                    'bucket' => config('filesystems.disks.s3.bucket'),
                    'region' => config('filesystems.disks.s3.region'),
                    'url' => config('filesystems.disks.s3.url')
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối S3: ' . $e->getMessage()
            ], 500);
        }
    }
}
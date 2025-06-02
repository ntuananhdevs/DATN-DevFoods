<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverApplicationController extends Controller
{
    /**
     * Display a listing of driver applications
     */
    public function index(Request $request)
    {
        $pendingQuery = DriverApplication::where('status', 'pending');
        $processedQuery = DriverApplication::whereIn('status', ['approved', 'rejected']);

        // Tìm kiếm cho bảng pending
        if ($request->has('pending_search') && !empty($request->pending_search)) {
            $searchTerm = $request->pending_search;
            $pendingQuery->where(function ($query) use ($searchTerm) {
                $query->where('full_name', 'like', "%{$searchTerm}%")
                    ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                    ->orWhere('license_plate', 'like', "%{$searchTerm}%");
            });
        }

        // Tìm kiếm cho bảng processed
        if ($request->has('processed_search') && !empty($request->processed_search)) {
            $searchTerm = $request->processed_search;
            $processedQuery->where(function ($query) use ($searchTerm) {
                $query->where('full_name', 'like', "%{$searchTerm}%")
                    ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                    ->orWhere('license_plate', 'like', "%{$searchTerm}%");
            });
        }

        $pendingApplications = $pendingQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'pending_page');
        $processedApplications = $processedQuery->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'processed_page');

        // Nếu là AJAX request, trả về JSON
        if ($request->ajax()) {
            return response()->json([
                'pendingApplications' => $pendingApplications,
                'processedApplications' => $processedApplications
            ]);
        }

        return view('admin.driver.applications', compact('pendingApplications', 'processedApplications'));
    }

    /**
     * Display the specified driver application
     */
    public function show(DriverApplication $application)
    {
        // Generate secure URLs for all images
        $imageUrls = [
            'profile_image' => $this->getSecureImageUrl($application->profile_image),
            'id_card_front_image' => $this->getSecureImageUrl($application->id_card_front_image),
            'id_card_back_image' => $this->getSecureImageUrl($application->id_card_back_image),
            'driver_license_image' => $this->getSecureImageUrl($application->driver_license_image),
            'vehicle_registration_image' => $this->getSecureImageUrl($application->vehicle_registration_image),
        ];

        return view('admin.driver.show', compact('application', 'imageUrls'));
    }

    /**
     * Update the status of a driver application
     */
    public function updateStatus(Request $request, DriverApplication $application)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->notes,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /**
     * Generate secure URL for S3 image
     */
    public function getSecureImageUrl($path, $expiration = 60)
    {
        if (!$path) {
            return null;
        }

        try {
            return Storage::disk('driver_documents')->temporaryUrl($path, now()->addMinutes($expiration));
        } catch (\Exception $e) {
            \Log::error('Failed to generate image URL for path: ' . $path . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Stream image from S3 (alternative method)
     */
    public function streamImage(Request $request, $path)
    {
        try {
            // Decode the path
            $decodedPath = base64_decode($path);
            
            // Check if file exists
            if (!Storage::disk('driver_documents')->exists($decodedPath)) {
                abort(404, 'Image not found');
            }

            // Get file contents
            $file = Storage::disk('driver_documents')->get($decodedPath);
            $mimeType = Storage::disk('driver_documents')->mimeType($decodedPath);

            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            \Log::error('Failed to stream image: ' . $e->getMessage());
            abort(404, 'Image not found');
        }
    }

    /**
     * Export applications to Excel/PDF/CSV
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'excel');
        
        // Implementation for export functionality
        // This would typically use packages like Laravel Excel
        
        return response()->download(/* file path */);
    }

    /**
     * Delete a driver application
     */
    public function destroy(DriverApplication $application)
    {
        try {
            // Delete files from S3
            $filePaths = [
                $application->profile_image,
                $application->id_card_front_image,
                $application->id_card_back_image,
                $application->driver_license_image,
                $application->vehicle_registration_image,
            ];

            foreach ($filePaths as $path) {
                if ($path && Storage::disk('driver_documents')->exists($path)) {
                    Storage::disk('driver_documents')->delete($path);
                }
            }

            // Delete application record
            $application->delete();

            return redirect()->route('admin.drivers.applications.index')
                ->with('success', 'Đã xóa đơn đăng ký thành công!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete application: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa đơn đăng ký!');
        }
    }

    /**
     * Get application statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => DriverApplication::count(),
            'pending' => DriverApplication::where('status', 'pending')->count(),
            'approved' => DriverApplication::where('status', 'approved')->count(),
            'rejected' => DriverApplication::where('status', 'rejected')->count(),
        ];

        return response()->json($stats);
    }
} 
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverApplication;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DriverApplicationStatusUpdated;

class DriverController extends Controller
{
    public function listApplications(Request $request){
        try {
            $search = $request->search;
            $pendingApplications = DriverApplication::where('status', 'pending')
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('license_plate', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(5, ['*'], 'pending_page');
            
            $processedApplications = DriverApplication::whereIn('status', ['approved', 'rejected'])
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('license_plate', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(5, ['*'], 'processed_page');

            return view('admin.driver.applications', compact('pendingApplications', 'processedApplications'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể tải danh sách đơn: ' . $e->getMessage());
        }
    }
    
    public function viewApplicationDetails(DriverApplication $application)
    {
        try {
            return view('admin.driver.show-apply-detail', compact('application'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xem chi tiết đơn: ' . $e->getMessage());
        }
    }

    public function approveApplication(DriverApplication $application)
    {
        try {
            DB::beginTransaction();

            // Cập nhật trạng thái đơn
            $application->update([
                'status' => 'approved',
                'admin_notes' => 'Đơn được phê duyệt bởi quản trị viên'
            ]);

            // Tạo bản ghi tài xế
            Driver::create([
                'user_id' => null, // Sẽ được thiết lập khi tài xế tạo tài khoản
                'driver_application_id' => $application->id,
                'driver_license_number' => $application->driver_license_number,
                'vehicle_type' => $application->vehicle_type,
                'vehicle_registration' => $application->vehicle_registration_image,
                'vehicle_color' => $application->vehicle_color,
                'status' => 'active',
                'is_available' => true
            ]);

            // Gửi thông báo
            if ($application->email) {
                Notification::route('mail', $application->email)
                    ->notify(new DriverApplicationStatusUpdated($application, 'approved'));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Phê duyệt đơn thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Không thể phê duyệt đơn: ' . $e->getMessage());
        }
    }

    public function rejectApplication(Request $request, DriverApplication $application)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => $request->admin_notes ?? 'Quản trị viên chưa nhập ghi chú'
            ]);

            // Gửi thông báo
            if ($application->email) {
                Notification::route('mail', $application->email)
                    ->notify(new DriverApplicationStatusUpdated($application, 'rejected'));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Từ chối đơn thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Không thể từ chối đơn: ' . $e->getMessage());
        }
    }
}
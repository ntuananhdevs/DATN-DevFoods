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
    public function pendingApplies(Request $request)
    {
        $applications = DriverApplication::where('status', 'pending')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('license_plate', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.driver-applications.pending', compact('applications'));
    }

    public function index()
    {
        $applications = DriverApplication::latest()->paginate(10);
        return view('admin.driver-applications.index', compact('applications'));
    }

    public function show(DriverApplication $application)
    {
        return view('admin.driver-applications.show', compact('application'));
    }

    public function approve(DriverApplication $application)
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

    public function reject(Request $request, DriverApplication $application)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => 'Đơn bị từ chối bởi quản trị viên'
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
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverApplication;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DriverApplicationStatusUpdated;
use App\Services\MailService;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;

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

    public function approveApplication(Request $request, DriverApplication $application)
    {
        try {
            DB::beginTransaction();

            // Cập nhật trạng thái đơn
            $application->update([
                'status' => 'approved',
                'admin_notes' => request('admin_notes', 'Đơn được phê duyệt bởi quản trị viên')
            ]);

            // Tạo bản ghi tài xế
            // \App\Models\Driver::create([
            //     'user_id' => $application->user_id,
            //     'application_id' => $application->id,
            //     'license_number' => request('license_number'),
            //     'vehicle_type' => request('vehicle_type'),
            //     'vehicle_registration' => request('vehicle_registration_image'),
            //     'vehicle_color' => request('vehicle_color'),
            //     'status' => 'active',
            //     'is_available' => true,
            //     'current_latitude' => null,
            //     'current_longitude' => null,
            //     'balance' => 0,
            //     'rating' => 5.00,
            //     'cancellation_count' => 0,
            //     'reliability_score' => 100,
            //     'penalty_count' => 0,
            //     'auto_deposit_earnings' => false
            // ]);

            // Gửi thông báo qua mail
            

            DB::commit();
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Phê duyệt đơn thành công.'
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Không thể phê duyệt đơn: ' . $e->getMessage());
        }
    }

    public function rejectApplication(Request $request, DriverApplication $application)
    {
        try {
            $request->validate([
                'admin_notes' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            $application->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes
            ]);

            // Gửi thông báo qua mail
            $toEmail = $application->email;
            $subject = 'Thông báo đơn ứng tuyển tài xế đã được từ chối';
            $content = 'Đơn bị từ chối với lí do: ' . $request->admin_notes;

            Mail::to($toEmail)->send(new GenericMail($subject, $content));

            DB::commit();
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Từ chối đơn thành công.'
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Không thể từ chối đơn: ' . $e->getMessage());
        }
    }
}
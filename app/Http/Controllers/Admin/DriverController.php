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
use App\Mail\EmailFactory;

class DriverController extends Controller
{
    // Hiển thị danh sách đơn đăng ký tài xế, phân loại theo trạng thái chờ xử lý và đã xử lý
    public function listApplications(Request $request){
        try {
            $search = $request->search;

            // Lọc danh sách đơn đang chờ xử lý
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

            // Lọc danh sách đơn đã được xử lý (phê duyệt hoặc từ chối)
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

    // Xem chi tiết một đơn đăng ký tài xế cụ thể
    public function viewApplicationDetails(DriverApplication $application)
    {
        try {
            return view('admin.driver.show-apply-detail', compact('application'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xem chi tiết đơn: ' . $e->getMessage());
        }
    }

    // Phê duyệt đơn đăng ký tài xế
    public function approveApplication(Request $request, DriverApplication $application)
    {
        try {
            DB::beginTransaction();

            // Cập nhật trạng thái đơn sang 'approved' và lưu ghi chú từ admin
            $application->update([
                'status' => 'approved',
                'admin_notes' => request('admin_notes', 'Đơn được phê duyệt bởi quản trị viên')
            ]);

            // (Tuỳ chọn) Tạo bản ghi tài xế mới từ đơn đăng ký - hiện đang bị comment
            // Nếu sử dụng, cần đảm bảo đã xử lý đầy đủ dữ liệu từ form

            /*
            Driver::create([
                'user_id' => $application->user_id,
                'application_id' => $application->id,
                'license_number' => request('license_number'),
                'vehicle_type' => request('vehicle_type'),
                'vehicle_registration' => request('vehicle_registration_image'),
                'vehicle_color' => request('vehicle_color'),
                'status' => 'active',
                'is_available' => true,
                'current_latitude' => null,
                'current_longitude' => null,
                'balance' => 0,
                'rating' => 5.00,
                'cancellation_count' => 0,
                'reliability_score' => 100,
                'penalty_count' => 0,
                'auto_deposit_earnings' => false
            ]);
            */

            // TODO: Gửi email thông báo phê duyệt đơn cho người đăng ký
            // Có thể sử dụng EmailFactory hoặc Mail::to(...)->send(...)

            DB::commit();

            // Hiển thị thông báo thành công
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

    // Từ chối đơn đăng ký tài xế
    public function rejectApplication(Request $request, DriverApplication $application)
    {
        try {
            // Xác thực nội dung ghi chú bắt buộc khi từ chối
            $request->validate([
                'admin_notes' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            // Cập nhật trạng thái đơn sang 'rejected' và lưu ghi chú của admin
            $application->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes
            ]);

            // Gửi email thông báo từ chối đến người đăng ký
            EmailFactory::sendDriverRejection($application, $request->admin_notes);

            DB::commit();

            // Hiển thị thông báo thành công
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

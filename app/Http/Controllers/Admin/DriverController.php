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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

            // Tạo tài khoản tài xế mới
            $password = Str::random(8) . rand(0, 9) . chr(rand(65, 90)) . chr(rand(97, 122)) . '!@#&*)(^';
            $hashedPassword = Hash::make($password);
            
            Log::info('Tạo tài khoản tài xế mới', [
                'application_id' => $application->id,
                'email' => $application->email
            ]);

            $driver = Driver::create([
                'application_id' => $application->id,
                'license_number' => $application->driver_license_number,
                'vehicle_type' => $application->vehicle_type,
                'vehicle_registration' => $application->vehicle_registration,
                'vehicle_color' => $application->vehicle_color,
                'status' => 'active',
                'is_available' => true,
                'current_latitude' => 0,
                'current_longitude' => 0,
                'balance' => 0,
                'rating' => 5.00,
                'cancellation_count' => 0,
                'reliability_score' => 100,
                'penalty_count' => 0,
                'auto_deposit_earnings' => false,
                'email' => $application->email,
                'password' => $hashedPassword,
                'phone_number' => $application->phone_number,
                'full_name' => $application->full_name
            ]);

            Log::info('Gửi email thông báo chấp nhận', [
                'application_id' => $application->id,
                'email' => $application->email
            ]);

            // Gửi email thông báo chấp nhận
            EmailFactory::sendDriverApproval($application, $password);

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
            Log::error('Lỗi khi phê duyệt đơn: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'exception' => $e
            ]);
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

    public function index(Request $request)
    {
        try {
            $search = $request->search;
            $drivers = Driver::when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
                })
                ->when($request->vehicle_type, function ($query, $vehicleType) {
                    $query->where('vehicle_type', $vehicleType);
                })
                ->when($request->status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->when($request->rating_min, function ($query, $ratingMin) {
                    $query->where('rating', '>=', $ratingMin);
                })
                ->latest()
                ->paginate(10);

            return view('admin.driver.index', compact('drivers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể tải danh sách tài xế: ' . $e->getMessage());
        }
    }

    public function show(Driver $driver)
    {
        return view('admin.driver.show', compact('driver'));
    }
}

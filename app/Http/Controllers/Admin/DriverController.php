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
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class DriverController extends Controller
{
    // Hiển thị danh sách đơn đăng ký tài xế, phân loại theo trạng thái chờ xử lý và đã xử lý
    public function listApplications(Request $request){
        try {
            $pendingSearch = $request->input('pending_search');
            $processedSearch = $request->input('processed_search');
            $pendingPage = $request->input('pending_page', 1);
            $processedPage = $request->input('processed_page', 1);

            // Lọc danh sách đơn đang chờ xử lý
            $pendingApplications = DriverApplication::where('status', 'pending')
                ->when($pendingSearch, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('license_plate', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10, ['*'], 'pending_page', $pendingPage);

            // Lọc danh sách đơn đã được xử lý (phê duyệt hoặc từ chối)
            $processedApplications = DriverApplication::whereIn('status', ['approved', 'rejected'])
                ->when($processedSearch, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('license_plate', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10, ['*'], 'processed_page', $processedPage);

            // Xử lý phản hồi dựa trên loại yêu cầu
            if ($request->ajax()) {
                // Định dạng dữ liệu để trả về JSON phù hợp cho phân trang và hiển thị
                return response()->json([
                    'success' => true,
                    'pendingApplications' => [
                        'current_page' => $pendingApplications->currentPage(),
                        'data' => $pendingApplications->items(),
                        'from' => $pendingApplications->firstItem(),
                        'to' => $pendingApplications->lastItem(),
                        'last_page' => $pendingApplications->lastPage(),
                        'per_page' => $pendingApplications->perPage(),
                        'total' => $pendingApplications->total(),
                        'has_more_pages' => $pendingApplications->hasMorePages(),
                        'links' => [
                            'first' => $pendingApplications->url(1),
                            'last' => $pendingApplications->url($pendingApplications->lastPage()),
                            'prev' => $pendingApplications->previousPageUrl(),
                            'next' => $pendingApplications->nextPageUrl(),
                        ]
                    ],
                    'processedApplications' => [
                        'current_page' => $processedApplications->currentPage(),
                        'data' => $processedApplications->items(),
                        'from' => $processedApplications->firstItem(),
                        'to' => $processedApplications->lastItem(),
                        'last_page' => $processedApplications->lastPage(),
                        'per_page' => $processedApplications->perPage(),
                        'total' => $processedApplications->total(),
                        'has_more_pages' => $processedApplications->hasMorePages(),
                        'links' => [
                            'first' => $processedApplications->url(1),
                            'last' => $processedApplications->url($processedApplications->lastPage()),
                            'prev' => $processedApplications->previousPageUrl(),
                            'next' => $processedApplications->nextPageUrl(),
                        ]
                    ],
                    'searchTerms' => [
                        'pending_search' => $pendingSearch,
                        'processed_search' => $processedSearch
                    ]
                ]);
            }

            return view('admin.driver.applications', compact(
                'pendingApplications', 
                'processedApplications', 
                'pendingSearch', 
                'processedSearch'
            ));
        } catch (\Exception $e) {
            Log::error('Error in listApplications: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không thể tải danh sách đơn ứng tuyển',
                    'message' => $e->getMessage()
                ], 500);
            }
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

            // 1. Tạo driver
            $driver = Driver::create([
                'application_id' => $application->id,
                'status' => 'active',
                'is_available' => true,
                'balance' => 0,
                'rating' => 5.00,
                'cancellation_count' => 0,
                'reliability_score' => 100,
                'penalty_count' => 0,
                'auto_deposit_earnings' => false,
                'email' => $application->email,
                'password' => $hashedPassword,
                'phone_number' => $application->phone_number,
                'full_name' => $application->full_name,
                'address' => $application->address,
                'profile_image' => $application->profile_image,
                'bank_account_number' => $application->bank_account_number,
                'bank_account_name' => $application->bank_account_name,
                'bank_name' => $application->bank_name,
                'emergency_contact_name' => $application->emergency_contact_name,
                'emergency_contact_phone' => $application->emergency_contact_phone,
                'note' => $application->note,
            ]);

            // 2. Tạo driver_documents
            $driver->documents()->create([
                'license_number' => $application->driver_license_number,
                'license_class' => $application->license_class,
                'license_expiry' => $application->license_expiry,
                'license_front' => $application->license_front,
                'license_back' => $application->license_back,
                'id_card_front' => $application->id_card_front,
                'id_card_back' => $application->id_card_back,
                'vehicle_type' => $application->vehicle_type,
                'vehicle_registration' => $application->vehicle_registration,
                'vehicle_color' => $application->vehicle_color,
                'license_plate' => $application->license_plate,
                'vehicle_brand' => $application->vehicle_brand,
                'vehicle_model' => $application->vehicle_model,
                'vehicle_year' => $application->vehicle_year,
                'vehicle_image' => $application->vehicle_image,
            ]);

            // 3. Tạo driver_locations (nếu có thông tin vị trí ban đầu)
            $driver->location()->create([
                'latitude' => $application->latitude ?? 0,
                'longitude' => $application->longitude ?? 0,
                'updated_at' => now(),
                'address' => $application->address,
            ]);

            Log::info('Gửi email thông báo chấp nhận', [
                'application_id' => $application->id,
                'email' => $application->email
            ]);

            // Gửi email thông báo chấp nhận
            EmailFactory::sendDriverApproval($application, $password);

            // Phát sự kiện cập nhật trạng thái tài xế
            // Vì đây là tài xế mới, nên trạng thái cũ là null
            event(new \App\Events\Driver\DriverStatusUpdated($driver, null));

            DB::commit();

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

    public function show($id)
    {
        $driver = Driver::with(['documents', 'location', 'violations', 'orders'])->findOrFail($id);
        // Thống kê, nếu có
        $stats = [
            'total_orders' => $driver->orders->count(),
            'completed_orders' => $driver->orders->where('status', 'completed')->count(),
            'cancelled_orders' => $driver->orders->where('status', 'cancelled')->count(),
            'total_earnings' => $driver->orders->sum('driver_earning'),
            'total_violations' => $driver->violations->count(),
        ];
        return view('admin.driver.show', compact('driver', 'stats'));
    }

    /**
     * Show form to create new driver
     */
    public function create()
    {
        return view('admin.driver.create');
    }

    /**
     * Store new driver
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:drivers,email',
                'phone_number' => 'required|string|max:20',
                'license_number' => 'required|string|max:50',
                'vehicle_type' => 'required|string|max:100',
                'vehicle_color' => 'required|string|max:50',
                'password' => 'required|string|min:6',
            ]);

            $driver = Driver::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'license_number' => $request->license_number,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_color' => $request->vehicle_color,
                'vehicle_registration' => $request->vehicle_registration,
                'password' => Hash::make($request->password),
                'status' => 'active',
                'is_available' => true,
                'balance' => 0,
                'rating' => 5.00,
                'cancellation_count' => 0,
                'reliability_score' => 100,
                'penalty_count' => 0,
                'auto_deposit_earnings' => false,
                'current_latitude' => 0,
                'current_longitude' => 0,
            ]);

            // Phát sự kiện cập nhật trạng thái tài xế
            // Vì đây là tài xế mới, nên trạng thái cũ là null
            event(new \App\Events\Driver\DriverStatusUpdated($driver, null));

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Tạo tài khoản tài xế thành công.'
            ]);

            return redirect()->route('admin.drivers.index');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Không thể tạo tài xế: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit driver
     */
    public function edit(Driver $driver)
    {
        // Load related data
        $driver->load([
            'documents',
            'location',
            'violations',
            'orders',
            'earnings',
            'reviews'
        ]);

        // Get driver statistics
        $stats = [
            'total_orders' => $driver->orders->count(),
            'completed_orders' => $driver->orders->where('status', 'completed')->count(),
            'cancelled_orders' => $driver->orders->where('status', 'cancelled')->count(),
            'total_earnings' => $driver->earnings->sum('amount'),
            'average_rating' => $driver->reviews->avg('rating'),
            'total_violations' => $driver->violations->count()
        ];

        return view('admin.driver.edit', compact('driver', 'stats'));
    }

    /**
     * Reset driver password
     */
    public function resetPassword(Request $request, Driver $driver)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            // Generate new random password with 15 characters
            $newPassword = $this->generateSecurePassword(15);
            
            // Update driver password
            $driver->update([
                'password' => Hash::make($newPassword),
                'password_reset_at' => now(),
                'must_change_password' => true
            ]);

            // Log the password reset activity
            Log::info('Admin reset driver password', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'driver_email' => $driver->email,
                'reason' => $request->reason,
                'timestamp' => now(),
                'ip_address' => $request->ip()
            ]);

            // Send email with new password to driver
            try {
                EmailFactory::sendPasswordReset($driver, $newPassword, $request->reason);
                Log::info('Password reset email queued successfully', [
                    'driver_id' => $driver->id,
                    'driver_email' => $driver->email
                ]);
            } catch (\Exception $emailException) {
                Log::error('Failed to queue password reset email', [
                    'driver_id' => $driver->id,
                    'error' => $emailException->getMessage()
                ]);
                // Don't fail the transaction for email queue issues
                // The password has been reset successfully, just log the email issue
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mật khẩu đã được reset và gửi email cho tài xế thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting driver password: ' . $e->getMessage(), [
                'driver_id' => $driver->id,
                'admin_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể reset mật khẩu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate secure password with 15 characters
     * Includes uppercase, lowercase, numbers, and special characters
     */
    private function generateSecurePassword($length = 15)
    {
        // Define character sets
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specialChars = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        // Ensure at least one character from each set
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];
        
        // Fill the rest with random characters from all sets
        $allChars = $lowercase . $uppercase . $numbers . $specialChars;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Shuffle the password to randomize character positions
        return str_shuffle($password);
    }

    /**
     * Update driver information
     */
    public function update(Request $request, Driver $driver)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:drivers,email,' . $driver->id,
                'phone_number' => 'required|string|max:20',
                'address' => 'nullable|string|max:500',
                'license_number' => 'required|string|max:50',
                'license_class' => 'nullable|string|max:10',
                'license_expiry' => 'nullable|date|after:today',
                'license_plate' => 'nullable|string|max:20',
                'vehicle_type' => 'required|string|max:100',
                'vehicle_color' => 'required|string|max:50',
                'status' => 'required|in:active,inactive,locked',
                'is_available' => 'boolean',
                'auto_deposit_earnings' => 'boolean',
                'balance' => 'nullable|numeric|min:0',
                'rating' => 'nullable|numeric|min:1|max:5',
                'reliability_score' => 'nullable|integer|min:0|max:100',
                'cancellation_count' => 'nullable|integer|min:0',
                'penalty_count' => 'nullable|integer|min:0',
                'admin_notes' => 'nullable|string|max:1000',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            DB::beginTransaction();

            $updateData = [
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'license_number' => $request->license_number,
                'license_class' => $request->license_class,
                'license_expiry' => $request->license_expiry,
                'license_plate' => $request->license_plate,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_color' => $request->vehicle_color,
                'vehicle_registration' => $request->vehicle_registration,
                'status' => $request->status,
                'is_available' => $request->has('is_available'),
                'auto_deposit_earnings' => $request->has('auto_deposit_earnings'),
                'balance' => $request->balance ?? $driver->balance,
                'rating' => $request->rating ?? $driver->rating,
                'reliability_score' => $request->reliability_score ?? $driver->reliability_score,
                'cancellation_count' => $request->cancellation_count ?? $driver->cancellation_count,
                'penalty_count' => $request->penalty_count ?? $driver->penalty_count,
                'admin_notes' => $request->admin_notes,
                'updated_by' => auth()->id(),
                'updated_at' => now()
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                $updateData['password_changed_at'] = now();
                
                // Log password change
                Log::info('Admin changed driver password', [
                    'admin_id' => auth()->id(),
                    'admin_name' => auth()->user()->name,
                    'driver_id' => $driver->id,
                    'driver_name' => $driver->full_name,
                    'timestamp' => now()
                ]);
            }

            // Store old values for change tracking
            $oldValues = [
                'status' => $driver->status,
                'email' => $driver->email,
                'phone_number' => $driver->phone_number,
                'license_number' => $driver->license_number
            ];
            
            // Store old driver status for event
            $oldDriverStatus = $driver->driver_status;
            $oldIsAvailable = $driver->is_available;

            $driver->update($updateData);

            // Log important changes
            $changes = [];
            foreach ($oldValues as $field => $oldValue) {
                if ($oldValue !== $updateData[$field]) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $updateData[$field]
                    ];
                }
            }

            if (!empty($changes)) {
                Log::info('Driver information updated', [
                    'admin_id' => auth()->id(),
                    'admin_name' => auth()->user()->name,
                    'driver_id' => $driver->id,
                    'driver_name' => $driver->full_name,
                    'changes' => $changes,
                    'timestamp' => now()
                ]);
            }

            // Dispatch driver status updated event if status or is_available changed
            if ($oldValues['status'] !== $updateData['status'] || $oldIsAvailable !== $updateData['is_available']) {
                event(new \App\Events\Driver\DriverStatusUpdated($driver, $oldDriverStatus));
            }
            
            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Cập nhật thông tin tài xế thành công.'
            ]);

            return redirect()->route('admin.drivers.show', $driver);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating driver: ' . $e->getMessage(), [
                'driver_id' => $driver->id,
                'admin_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return redirect()->back()->withInput()->with('error', 'Không thể cập nhật tài xế: ' . $e->getMessage());
        }
    }

    /**
     * Delete driver
     */
    public function destroy(Driver $driver)
    {
        try {
            DB::beginTransaction();

            // Check if driver has any active orders or deliveries
            // You might want to add this check based on your Order model
            // $hasActiveOrders = $driver->orders()->whereIn('status', ['pending', 'confirmed', 'in_delivery'])->exists();
            // if ($hasActiveOrders) {
            //     return redirect()->back()->with('error', 'Không thể xóa tài xế đang có đơn hàng active.');
            // }

            $driverName = $driver->full_name;
            $driver->delete();

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => "Đã xóa tài xế {$driverName} thành công."
            ]);

            return redirect()->route('admin.drivers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Không thể xóa tài xế: ' . $e->getMessage());
        }
    }

    /**
     * Export drivers data
     */
    public function export(Request $request)
    {
        try {
            $type = $request->type ?? 'excel';
            $query = Driver::query();
            
            // Lọc theo loại phương tiện
            if ($request->has('vehicle_type') && $request->vehicle_type) {
                $query->where('vehicle_type', $request->vehicle_type);
            }
            
            // Lọc theo đánh giá tối thiểu
            if ($request->has('rating_min') && $request->rating_min) {
                $query->where('rating', '>=', $request->rating_min);
            }
            
            // Lọc theo trạng thái
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Tìm kiếm theo tên hoặc email hoặc số điện thoại
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('full_name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $request->search . '%');
                });
            }
            
            $drivers = $query->latest()->get();
            
            // Xử lý xuất dữ liệu theo định dạng
            switch ($type) {
                case 'excel':
                    return Excel::download(new \App\Exports\DriverExport($drivers), 'drivers.xlsx');
                    
                case 'pdf':
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.drivers', compact('drivers'));
                    return $pdf->download('drivers.pdf');
                    
                case 'csv':
                    return Excel::download(new \App\Exports\DriverExport($drivers), 'drivers.csv', \Maatwebsite\Excel\Excel::CSV);
                    
                default:
                    return $this->exportDriversJson($drivers, 'drivers.json');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xuất dữ liệu: ' . $e->getMessage());
        }
    }
    
    /**
     * Temporary JSON export method for drivers
     */
    private function exportDriversJson($drivers, $filename)
    {
        $data = [];
        
        foreach ($drivers as $driver) {
            $data[] = [
                'id' => $driver->id,
                'full_name' => $driver->full_name,
                'email' => $driver->email,
                'phone_number' => $driver->phone_number,
                'vehicle_type' => $driver->vehicle_type,
                'vehicle_color' => $driver->vehicle_color,
                'license_number' => $driver->license_number,
                'rating' => $driver->rating,
                'status' => $driver->status,
                'balance' => $driver->balance,
                'created_at' => $driver->created_at->format('d/m/Y H:i:s'),
                'updated_at' => $driver->updated_at->format('d/m/Y H:i:s'),
            ];
        }
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = storage_path('drivers-export.json');
        file_put_contents($file, $json);
        
        return Response::download($file, $filename);
    }

    /**
     * Export driver applications data
     */
    public function exportApplications(Request $request)
    {
        try {
            $type = $request->type ?? 'excel';
            $query = DriverApplication::query();
            
            // Lọc theo trạng thái đơn
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Tìm kiếm theo tên hoặc số điện thoại
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('full_name', 'like', '%' . $request->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $request->search . '%')
                      ->orWhere('license_plate', 'like', '%' . $request->search . '%');
                });
            }
            
            $applications = $query->latest()->get();
            
            // Xử lý xuất dữ liệu theo định dạng
            switch ($type) {
                case 'excel':
                    return Excel::download(new \App\Exports\DriverApplicationsExport($applications), 'driver-applications.xlsx');
                    
                case 'pdf':
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.driver-applications', compact('applications'));
                    return $pdf->download('driver-applications.pdf');
                    
                case 'csv':
                    return Excel::download(new \App\Exports\DriverApplicationsExport($applications), 'driver-applications.csv', \Maatwebsite\Excel\Excel::CSV);
                    
                default:
                    return $this->exportApplicationsJson($applications, 'driver-applications.json');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xuất dữ liệu: ' . $e->getMessage());
        }
    }
    
    /**
     * Temporary JSON export method for driver applications
     */
    private function exportApplicationsJson($applications, $filename)
    {
        $data = [];
        
        foreach ($applications as $application) {
            $data[] = [
                'id' => $application->id,
                'full_name' => $application->full_name,
                'phone_number' => $application->phone_number,
                'email' => $application->email,
                'license_plate' => $application->license_plate,
                'vehicle_type' => $application->vehicle_type,
                'status' => $application->status,
                'admin_notes' => $application->admin_notes,
                'created_at' => $application->created_at->format('d/m/Y H:i:s'),
                'updated_at' => $application->updated_at->format('d/m/Y H:i:s'),
            ];
        }
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = storage_path('driver-applications-export.json');
        file_put_contents($file, $json);
        
        return Response::download($file, $filename);
    }

    /**
     * Toggle driver account status (active/inactive)
     */
    public function toggleStatus(Request $request, Driver $driver)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            $newStatus = $driver->status === 'active' ? 'inactive' : 'active';
            $oldStatus = $driver->status;
            $oldDriverStatus = $driver->driver_status;
            
            $driver->update([
                'status' => $newStatus,
                'status_changed_at' => now(),
                'status_changed_by' => auth()->id(),
                'admin_notes' => $request->reason
            ]);

            // Log status change
            Log::info('Driver status changed', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $request->reason,
                'timestamp' => now(),
                'ip_address' => $request->ip()
            ]);

            // Send notification email if deactivated
            if ($newStatus === 'inactive') {
                // You might want to send notification email here
            }

            // Dispatch driver status updated event
            event(new \App\Events\Driver\DriverStatusUpdated($driver, $oldDriverStatus));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $newStatus === 'active' ? 'Kích hoạt tài khoản thành công' : 'Vô hiệu hóa tài khoản thành công',
                'new_status' => $newStatus,
                'driver_status' => $driver->driver_status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling driver status: ' . $e->getMessage(), [
                'driver_id' => $driver->id,
                'admin_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể thay đổi trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lock driver account temporarily
     */
    public function lockAccount(Request $request, Driver $driver)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
                'lock_until' => 'nullable|date|after:now'
            ]);

            DB::beginTransaction();

            // Store old driver status for event
            $oldDriverStatus = $driver->driver_status;

            $driver->update([
                'status' => 'locked',
                'locked_at' => now(),
                'locked_until' => $request->lock_until,
                'locked_by' => auth()->id(),
                'lock_reason' => $request->reason,
                'admin_notes' => $request->reason
            ]);

            // Log account lock
            Log::info('Driver account locked', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'reason' => $request->reason,
                'lock_until' => $request->lock_until,
                'timestamp' => now(),
                'ip_address' => $request->ip()
            ]);

            // Dispatch driver status updated event
            event(new \App\Events\Driver\DriverStatusUpdated($driver, $oldDriverStatus));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Khóa tài khoản tài xế thành công',
                'driver_status' => $driver->driver_status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error locking driver account: ' . $e->getMessage(), [
                'driver_id' => $driver->id,
                'admin_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể khóa tài khoản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlock driver account
     */
    public function unlockAccount(Request $request, Driver $driver)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            // Store old driver status for event
            $oldDriverStatus = $driver->driver_status;

            $driver->update([
                'status' => 'active',
                'locked_at' => null,
                'locked_until' => null,
                'locked_by' => null,
                'lock_reason' => null,
                'unlocked_at' => now(),
                'unlocked_by' => auth()->id(),
                'admin_notes' => $request->reason
            ]);

            // Log account unlock
            Log::info('Driver account unlocked', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'reason' => $request->reason,
                'timestamp' => now(),
                'ip_address' => $request->ip()
            ]);

            // Dispatch driver status updated event
            event(new \App\Events\Driver\DriverStatusUpdated($driver, $oldDriverStatus));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mở khóa tài khoản tài xế thành công',
                'driver_status' => $driver->driver_status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error unlocking driver account: ' . $e->getMessage(), [
                'driver_id' => $driver->id,
                'admin_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể mở khóa tài khoản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add violation to driver
     */
    public function addViolation(Request $request, Driver $driver)
    {
        try {
            $request->validate([
                'violation_type' => 'required|string|max:100',
                'description' => 'required|string|max:1000',
                'severity' => 'required|in:low,medium,high,critical',
                'penalty_amount' => 'nullable|numeric|min:0'
            ]);

            DB::beginTransaction();

            // Create violation record
            $violation = $driver->violations()->create([
                'violation_type' => $request->violation_type,
                'description' => $request->description,
                'severity' => $request->severity,
                'penalty_amount' => $request->penalty_amount,
                'reported_by' => auth()->id(),
                'reported_at' => now(),
                'status' => 'active'
            ]);

            // Update driver penalty count
            $driver->increment('penalty_count');

            // Reduce reliability score based on severity
            $scoreReduction = [
                'low' => 5,
                'medium' => 10, 
                'high' => 20,
                'critical' => 50
            ];
            
            $driver->decrement('reliability_score', $scoreReduction[$request->severity]);
            
            // Ensure reliability score doesn't go below 0
            if ($driver->reliability_score < 0) {
                $driver->update(['reliability_score' => 0]);
            }

            // Auto-lock account for critical violations
            if ($request->severity === 'critical') {
                // Store old driver status for event
                $oldDriverStatus = $driver->driver_status;
                
                $driver->update([
                    'status' => 'locked',
                    'locked_at' => now(),
                    'locked_by' => auth()->id(),
                    'lock_reason' => 'Vi phạm nghiêm trọng: ' . $request->description
                ]);
                
                // Dispatch driver status updated event
                event(new \App\Events\Driver\DriverStatusUpdated($driver, $oldDriverStatus));
            }

            // Log violation
            Log::info('Driver violation added', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'violation_id' => $violation->id,
                'violation_type' => $request->violation_type,
                'severity' => $request->severity,
                'timestamp' => now(),
                'ip_address' => $request->ip()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thêm vi phạm thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding driver violation: ' . $e->getMessage(), [
                'driver_id' => $driver->id,
                'admin_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm vi phạm: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tracking()
    {
        // Lấy tất cả tài xế active và eager load documents và location
        $drivers = \App\Models\Driver::where('status', 'active')
            ->with(['location', 'documents'])
            ->get();
        
        // Tính toán thống kê dựa trên accessor
        $totalDrivers = $drivers->count();
        
        $availableDrivers = $drivers->filter(function($driver) {
            return $driver->driver_status === 'available';
        })->count();
        
        $deliveringDrivers = $drivers->filter(function($driver) {
            return $driver->driver_status === 'delivering';
        })->count();
        
        $offlineDrivers = $drivers->filter(function($driver) {
            return $driver->driver_status === 'offline';
        })->count();
        
        return view('admin.driver.tracking', [
            'stats' => [
                'total' => $totalDrivers,
                'available' => $availableDrivers,
                'delivering' => $deliveringDrivers,
                'offline' => $offlineDrivers
            ],
            'drivers' => $drivers
        ]);
    }
}

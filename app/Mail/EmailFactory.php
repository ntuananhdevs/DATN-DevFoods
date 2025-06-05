<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEmailJob;

class EmailFactory
{
    /**
     * Gửi email thông báo từ chối đơn ứng tuyển tài xế
     *
     * @param object $driver Thông tin tài xế (DriverApplication)
     * @param string|null $reason Lý do từ chối
     * @param string $email Địa chỉ email nhận
     * @return void
     */
    public static function sendDriverRejection($application, $reason = null, $email = null)
    {
        $to = $email ?? $application->email;

        // Đảm bảo dữ liệu phù hợp với template
        $driverData = [
            'driver' => [
                'full_name' => $application->full_name,
                'email' => $application->email,
                'phone_number' => $application->phone_number ?? '',
            ],
            'reason' => $reason,
            'application' => $application // Truyền cả đối tượng application để có thể truy cập các trường khác
        ];

        $mailable = new NotificationMail(
            'driver_rejection',
            $driverData
        );

        SendEmailJob::dispatch($to, $mailable);
    }


    /**
     * Gửi email thông báo phê duyệt đơn ứng tuyển tài xế
     *
     * @param object $application Thông tin đơn ứng tuyển
     * @param string $password Mật khẩu mới
     * @param string|null $email Địa chỉ email nhận
     * @return void
     */
    public static function sendDriverApproval($application, $password, $email = null)
    {
        $to = $email ?? $application->email;

        // Đảm bảo dữ liệu phù hợp với template
        $driverData = [
            'driver' => [
                'full_name' => $application->full_name,
                'email' => $application->email,
                'phone_number' => $application->phone_number ?? '',
            ],
            'password' => $password,
            'application' => $application // Truyền cả đối tượng application để có thể truy cập các trường khác
        ];

        $mailable = new NotificationMail(
            'driver_approval',
            $driverData,
            "Đơn đăng ký tài xế được chấp nhận"
        );

        SendEmailJob::dispatch($to, $mailable);
    }

    /**
     * Gửi email reset mật khẩu cho tài xế
     *
     * @param object $driver Thông tin tài xế
     * @param string $newPassword Mật khẩu mới
     * @param string $reason Lý do reset mật khẩu
     * @param string|null $email Địa chỉ email nhận
     * @return void
     */
    public static function sendPasswordReset($driver, $newPassword, $reason, $email = null)
    {
        $to = $email ?? $driver->email;

        // Chuẩn bị dữ liệu cho template
        $resetData = [
            'driver' => [
                'full_name' => $driver->full_name,
                'email' => $driver->email,
                'phone_number' => $driver->phone_number ?? '',
                'id' => $driver->id
            ],
            'newPassword' => $newPassword,
            'reason' => $reason,
            'resetDate' => now()->format('d/m/Y H:i:s'),
            'loginUrl' => route('driver.login'),
            'supportEmail' => config('mail.support_email', 'support@devfoods.com'),
            'companyName' => config('app.name', 'DevFoods')
        ];

        $mailable = new NotificationMail(
            'driver_password_reset',
            $resetData,
            "Mật khẩu tài khoản tài xế đã được reset"
        );

        // Sử dụng queue để gửi email
        SendEmailJob::dispatch($to, $mailable);
    }



    /**
     * Gửi email chào mừng người dùng mới
     *
     * @param object $user Thông tin người dùng
     * @return void
     */
    public static function sendWelcome($user)
    {
        $mailable = new NotificationMail(
            'welcome',
            [
                'user' => $user
            ]
        );

        SendEmailJob::dispatch($user->email, $mailable);
    }

    /**
     * Gửi email xác minh tài khoản
     *
     * @param object $user Thông tin người dùng
     * @param string $verificationUrl URL xác minh
     * @return void
     */
    public static function sendVerification($user, $verificationUrl)
    {
        $mailable = new NotificationMail(
            'verification',
            [
                'user' => $user,
                'verificationUrl' => $verificationUrl
            ]
        );

        SendEmailJob::dispatch($user->email, $mailable);
    }

    /**
     * Gửi email thông báo chung
     *
     * @param string $subject Tiêu đề email
     * @param string $content Nội dung email
     * @param array $data Dữ liệu bổ sung
     * @param string $to Địa chỉ email nhận
     * @return void
     */
    public static function sendNotification($subject, $content, $data = [], $to)
    {
        $mailable = new NotificationMail($subject, $content, $data);

        SendEmailJob::dispatch($to, $mailable);
    }

    /**
     * Gửi email thông báo gỡ bỏ quản lý
     *
     * @param object $manager Thông tin người quản lý
     * @param object $branch Thông tin chi nhánh
     * @return void
     */
    public static function sendManagerRemoved($manager, $branch)
    {
        // Đảm bảo dữ liệu phù hợp với template
        $data = [
            'manager' => [
                'full_name' => $manager->full_name,
                'email' => $manager->email,
                'phone' => $manager->phone ?? '',
                'id' => $manager->id
            ],
            'branch' => [
                'name' => $branch->name,
                'address' => $branch->address,
                'phone' => $branch->phone,
                'email' => $branch->email ?? ''
            ]
        ];

        $mailable = new NotificationMail(
            'branch_manager_removed',
            $data,
            "Thông báo gỡ bỏ quản lý chi nhánh - " . config('app.name')
        );

        // Sử dụng queue để gửi email
        SendEmailJob::dispatch($manager->email, $mailable);
    }

    /**
     * Gửi email thông báo phân công quản lý chi nhánh
     *
     * @param object $manager Thông tin người quản lý
     * @param object $branch Thông tin chi nhánh
     * @return void
     */
    public static function sendManagerAssigned($manager, $branch)
    {
        // Đảm bảo dữ liệu phù hợp với template
        $data = [
            'manager' => [
                'full_name' => $manager->full_name,
                'email' => $manager->email,
                'phone' => $manager->phone ?? '',
                'id' => $manager->id
            ],
            'branch' => [
                'name' => $branch->name,
                'address' => $branch->address,
                'phone' => $branch->phone,
                'email' => $branch->email ?? ''
            ]
        ];

        $mailable = new NotificationMail(
            'branch_manager_assigned',
            $data,
            "Phân công quản lý chi nhánh - " . config('app.name')
        );

        // Sử dụng queue để gửi email
        SendEmailJob::dispatch($manager->email, $mailable);
    }
}

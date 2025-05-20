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

} 
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

    public static function sendOrderConfirmation($order)
    {
        // Debug: Log order info
        \Log::info('EmailFactory: Order ID ' . $order->id);
        \Log::info('EmailFactory: OrderItems count before load: ' . $order->orderItems->count());
        
        // Reload order from DB to ensure latest relations
        $order->refresh();

        // Eager load all necessary relations
        $order->load([
            'orderItems' => function($q){
                $q->with(['productVariant' => function($pv) {
                    $pv->with(['product' => function($p) {
                        $p->with('images');
                    }]);
                }, 'combo']);
            },
            'payment',
            'customer'
        ]);
        
        // Debug: Log after load
        \Log::info('EmailFactory: OrderItems count after load: ' . $order->orderItems->count());
        foreach($order->orderItems as $item) {
            \Log::info('EmailFactory: Item - variant_id: ' . $item->product_variant_id . ', combo_id: ' . $item->combo_id);
        }
        
        // Determine recipient email
        $to = $order->customer->email ?? $order->guest_email;
        if (!$to) {
            return; // no email
        }

        $mailable = new NotificationMail(
            'order_confirmation',
            [
                'order' => $order
            ],
            'Thông báo đơn hàng mới'
        );

        SendEmailJob::dispatch($to, $mailable);
    }

} 
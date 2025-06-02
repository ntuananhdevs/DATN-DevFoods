<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;
    public $type;
    public $title;
    protected $templateMap = [
        'driver_rejection' => 'emails.layouts.drivers.apply-reject',
        'driver_approval' => 'emails.layouts.drivers.apply-approve',
        'order_confirmation' => 'emails.orders.confirmation',
        'password_reset' => 'emails.auth.password-reset',
        'welcome' => 'emails.auth.welcome',
        'verification' => 'emails.auth.verify',
    ];

    /**
     * Create a new message instance.
     * 
     * @param string $type Loại thông báo
     * @param array $data Dữ liệu truyền vào template
     * @param string|null $subject Tiêu đề email (nếu null, sẽ được tạo dựa trên type)
     */
    public function __construct($type, $data = [], $subject = null)
    {
        $this->type = $type;
        $this->data = $data;

        // Tự động tạo tiêu đề dựa trên loại thông báo nếu không được cung cấp
        if (is_null($subject)) {
            $this->setSubjectByType();
        } else {
            $this->title = $subject;
            $this->subject = $subject;
        }
    }

    /**
     * Tự động đặt tiêu đề email dựa trên loại thông báo
     */
    private function setSubjectByType()
    {
        switch ($this->type) {
            case 'driver_rejection':
                $this->subject = 'Thông báo kết quả ứng tuyển tài xế - ' . config('app.name');
                break;
            case 'driver_approval':
                $this->subject = 'Đơn đăng ký tài xế được chấp nhận - ' . config('app.name');
                break;
            case 'order_confirmation':
                $orderId = $this->data['order']->id ?? 'N/A';
                $this->subject = 'Xác nhận đơn hàng #' . $orderId . ' - ' . config('app.name');
                break;
            case 'password_reset':
                $this->subject = 'Đặt lại mật khẩu - ' . config('app.name');
                break;
            case 'welcome':
                $this->subject = 'Chào mừng bạn đến với ' . config('app.name');
                break;
            case 'verification':
                $this->subject = 'Xác minh tài khoản - ' . config('app.name');
                break;
            default:
                $this->subject = 'Thông báo từ ' . config('app.name');
                break;
        }

        $this->title = $this->subject;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $template = $this->templateMap[$this->type] ?? 'emails.generic';
        
        return $this->subject($this->subject)
            ->view($template)
            ->with([
                'data' => $this->data,
                'title' => $this->title,
                'content' => $this->data['content'] ?? null,
            ]);
    }
}

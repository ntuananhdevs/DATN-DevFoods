<?php

namespace App\Notifications;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class RefundRequestCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $refundRequest;
    protected $userType;

    /**
     * Create a new notification instance.
     */
    public function __construct(RefundRequest $refundRequest, string $userType = 'customer')
    {
        $this->refundRequest = $refundRequest;
        $this->userType = $userType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->getMailSubject())
            ->greeting($this->getGreeting($notifiable))
            ->line($this->getMailContent())
            ->line('**Thông tin yêu cầu hoàn tiền:**')
            ->line('- Mã yêu cầu: ' . $this->refundRequest->refund_code)
            ->line('- Mã đơn hàng: ' . $this->refundRequest->order->order_code)
            ->line('- Số tiền hoàn: ' . number_format($this->refundRequest->refund_amount, 0, ',', '.') . ' VNĐ')
            ->line('- Lý do: ' . $this->refundRequest->reason)
            ->line('- Thời gian tạo: ' . $this->refundRequest->created_at->format('d/m/Y H:i:s'));

        if ($this->userType === 'customer') {
            $mailMessage->line('Chúng tôi sẽ xem xét và phản hồi trong thời gian sớm nhất.')
                        ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
        } else {
            $mailMessage->action('Xem chi tiết', $this->getActionUrl());
        }

        return $mailMessage;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'refund_request_created',
            'title' => $this->getDatabaseTitle(),
            'message' => $this->getDatabaseMessage(),
            'refund_request_id' => $this->refundRequest->id,
            'refund_code' => $this->refundRequest->refund_code,
            'order_code' => $this->refundRequest->order->order_code,
            'customer_name' => $this->refundRequest->customer->name,
            'refund_amount' => $this->refundRequest->refund_amount,
            'status' => $this->refundRequest->status,
            'created_at' => $this->refundRequest->created_at->toISOString(),
            'action_url' => $this->getActionUrl(),
            'user_type' => $this->userType
        ];
    }

    /**
     * Get mail subject based on user type
     */
    private function getMailSubject(): string
    {
        switch ($this->userType) {
            case 'admin':
                return '[DevFoods Admin] Yêu cầu hoàn tiền mới cần xử lý';
            case 'branch':
                return '[DevFoods Branch] Yêu cầu hoàn tiền mới từ khách hàng';
            case 'customer':
            default:
                return '[DevFoods] Yêu cầu hoàn tiền đã được tạo thành công';
        }
    }

    /**
     * Get greeting based on user type
     */
    private function getGreeting(object $notifiable): string
    {
        switch ($this->userType) {
            case 'admin':
            case 'branch':
                return 'Xin chào ' . $notifiable->name . ',';
            case 'customer':
            default:
                return 'Xin chào ' . $this->refundRequest->customer->name . ',';
        }
    }

    /**
     * Get mail content based on user type
     */
    private function getMailContent(): string
    {
        switch ($this->userType) {
            case 'admin':
                return 'Có một yêu cầu hoàn tiền mới từ khách hàng ' . $this->refundRequest->customer->name . ' cần được xem xét và xử lý.';
            case 'branch':
                return 'Chi nhánh của bạn có một yêu cầu hoàn tiền mới từ khách hàng ' . $this->refundRequest->customer->name . '.';
            case 'customer':
            default:
                return 'Yêu cầu hoàn tiền của bạn đã được tạo thành công và đang được xem xét.';
        }
    }

    /**
     * Get database title based on user type
     */
    private function getDatabaseTitle(): string
    {
        switch ($this->userType) {
            case 'admin':
                return 'Yêu cầu hoàn tiền mới';
            case 'branch':
                return 'Yêu cầu hoàn tiền mới';
            case 'customer':
            default:
                return 'Yêu cầu hoàn tiền đã tạo';
        }
    }

    /**
     * Get database message based on user type
     */
    private function getDatabaseMessage(): string
    {
        switch ($this->userType) {
            case 'admin':
                return 'Khách hàng ' . $this->refundRequest->customer->name . ' đã tạo yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' cho đơn hàng ' . $this->refundRequest->order->order_code;
            case 'branch':
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' từ khách hàng ' . $this->refundRequest->customer->name;
            case 'customer':
            default:
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' đã được tạo thành công và đang chờ xử lý';
        }
    }

    /**
     * Get action URL based on user type
     */
    private function getActionUrl(): string
    {
        switch ($this->userType) {
            case 'admin':
                return url('/admin/refunds/' . $this->refundRequest->id);
            case 'branch':
                return url('/branch/refunds/' . $this->refundRequest->id);
            case 'customer':
            default:
                return url('/customer/refunds/' . $this->refundRequest->id);
        }
    }
}
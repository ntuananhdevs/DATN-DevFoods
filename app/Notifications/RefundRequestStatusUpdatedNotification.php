<?php

namespace App\Notifications;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRequestStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $refundRequest;
    protected $oldStatus;
    protected $newStatus;
    protected $userType;

    /**
     * Create a new notification instance.
     */
    public function __construct(RefundRequest $refundRequest, string $oldStatus, string $newStatus, string $userType = 'customer')
    {
        $this->refundRequest = $refundRequest;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
            ->line('- Trạng thái cũ: ' . $this->getStatusLabel($this->oldStatus))
            ->line('- Trạng thái mới: ' . $this->getStatusLabel($this->newStatus))
            ->line('- Thời gian cập nhật: ' . $this->refundRequest->updated_at->format('d/m/Y H:i:s'));

        // Thêm ghi chú admin nếu có
        if ($this->refundRequest->admin_note) {
            $mailMessage->line('- Ghi chú: ' . $this->refundRequest->admin_note);
        }

        // Thêm action button
        $mailMessage->action('Xem chi tiết', $this->getActionUrl());

        // Thêm thông tin bổ sung dựa trên status mới
        $mailMessage->line($this->getAdditionalInfo());

        return $mailMessage;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'refund_request_status_updated',
            'title' => $this->getDatabaseTitle(),
            'message' => $this->getDatabaseMessage(),
            'refund_request_id' => $this->refundRequest->id,
            'refund_code' => $this->refundRequest->refund_code,
            'order_code' => $this->refundRequest->order->order_code,
            'customer_name' => $this->refundRequest->customer->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'old_status_label' => $this->getStatusLabel($this->oldStatus),
            'new_status_label' => $this->getStatusLabel($this->newStatus),
            'updated_at' => $this->refundRequest->updated_at->toISOString(),
            'action_url' => $this->getActionUrl(),
            'user_type' => $this->userType
        ];
    }

    /**
     * Get status label in Vietnamese
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'under_review' => 'Đang xem xét',
            'approved' => 'Đã duyệt',
            'rejected' => 'Đã từ chối',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get mail subject based on user type and status
     */
    private function getMailSubject(): string
    {
        $statusLabel = $this->getStatusLabel($this->newStatus);
        
        switch ($this->userType) {
            case 'admin':
                return '[DevFoods Admin] Yêu cầu hoàn tiền đã được cập nhật: ' . $statusLabel;
            case 'branch':
                return '[DevFoods Branch] Cập nhật yêu cầu hoàn tiền: ' . $statusLabel;
            case 'customer':
            default:
                return '[DevFoods] Yêu cầu hoàn tiền: ' . $statusLabel;
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
     * Get mail content based on user type and status
     */
    private function getMailContent(): string
    {
        $statusLabel = $this->getStatusLabel($this->newStatus);
        
        switch ($this->userType) {
            case 'admin':
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' từ khách hàng ' . $this->refundRequest->customer->name . ' đã được cập nhật trạng thái thành "' . $statusLabel . '".';
            case 'branch':
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' tại chi nhánh của bạn đã được cập nhật trạng thái thành "' . $statusLabel . '".';
            case 'customer':
            default:
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' của bạn đã được cập nhật trạng thái thành "' . $statusLabel . '".';
        }
    }

    /**
     * Get database title based on user type
     */
    private function getDatabaseTitle(): string
    {
        return 'Cập nhật yêu cầu hoàn tiền';
    }

    /**
     * Get database message based on user type
     */
    private function getDatabaseMessage(): string
    {
        $statusLabel = $this->getStatusLabel($this->newStatus);
        
        switch ($this->userType) {
            case 'admin':
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' đã được cập nhật thành "' . $statusLabel . '"';
            case 'branch':
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' đã được cập nhật thành "' . $statusLabel . '"';
            case 'customer':
            default:
                return 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' của bạn đã được cập nhật thành "' . $statusLabel . '"';
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

    /**
     * Get additional information based on new status
     */
    private function getAdditionalInfo(): string
    {
        switch ($this->newStatus) {
            case 'approved':
                return 'Yêu cầu hoàn tiền đã được duyệt và sẽ được xử lý trong thời gian sớm nhất.';
            case 'rejected':
                return 'Yêu cầu hoàn tiền đã bị từ chối. Vui lòng xem ghi chú để biết thêm chi tiết.';
            case 'processing':
                return 'Yêu cầu hoàn tiền đang được xử lý. Số tiền sẽ được hoàn vào tài khoản của bạn sớm.';
            case 'completed':
                return 'Yêu cầu hoàn tiền đã hoàn thành. Số tiền đã được hoàn vào số dư tài khoản của bạn.';
            case 'cancelled':
                return 'Yêu cầu hoàn tiền đã bị hủy.';
            default:
                return 'Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!';
        }
    }
}
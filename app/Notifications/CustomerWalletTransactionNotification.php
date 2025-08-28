<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\WalletTransaction;

class CustomerWalletTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(WalletTransaction $transaction, string $oldStatus = null)
    {
        $this->transaction = $transaction;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'transaction_code' => $this->transaction->transaction_code,
            'type' => $this->transaction->type,
            'amount' => $this->transaction->amount,
            'formatted_amount' => number_format($this->transaction->amount, 0, ',', '.') . ' VND',
            'status' => $this->transaction->status,
            'old_status' => $this->oldStatus,
            'payment_method' => $this->transaction->payment_method,
            'description' => $this->transaction->description,
            'created_at' => $this->transaction->created_at->toISOString(),
            'processed_at' => $this->transaction->processed_at ? $this->transaction->processed_at->toISOString() : null,
            'title' => $this->getNotificationTitle(),
            'message' => $this->getNotificationMessage(),
            'icon' => $this->getNotificationIcon(),
            'color' => $this->getNotificationColor(),
            'url' => route('customer.wallet.transactions'),
            'metadata' => $this->transaction->metadata,
        ];
    }

    /**
     * Get notification title
     */
    private function getNotificationTitle(): string
    {
        switch ($this->transaction->status) {
            case 'completed':
                return $this->transaction->type === 'deposit' ? 'Nạp tiền thành công' : 'Rút tiền thành công';
            case 'failed':
                return $this->transaction->type === 'deposit' ? 'Nạp tiền thất bại' : 'Yêu cầu rút tiền bị từ chối';
            case 'cancelled':
                return 'Giao dịch đã hủy';
            case 'expired':
                return 'Giao dịch hết hạn';
            default:
                return 'Cập nhật giao dịch ví';
        }
    }

    /**
     * Get notification message
     */
    private function getNotificationMessage(): string
    {
        $amount = number_format($this->transaction->amount, 0, ',', '.');
        
        switch ($this->transaction->status) {
            case 'completed':
                if ($this->transaction->type === 'deposit') {
                    return "Bạn đã nạp thành công {$amount} VND vào ví";
                } else {
                    return "Yêu cầu rút {$amount} VND của bạn đã được duyệt";
                }
            case 'failed':
                if ($this->transaction->type === 'deposit') {
                    return "Giao dịch nạp {$amount} VND thất bại";
                } else {
                    $metadata = is_string($this->transaction->metadata) 
                        ? json_decode($this->transaction->metadata, true) 
                        : $this->transaction->metadata;
                    
                    $reason = $metadata['reject_reason'] ?? 'Không đáp ứng yêu cầu';
                    return "Yêu cầu rút {$amount} VND bị từ chối. Lý do: {$reason}";
                }
            case 'cancelled':
                return "Giao dịch {$amount} VND đã được hủy";
            case 'expired':
                return "Giao dịch {$amount} VND đã hết hạn";
            default:
                return "Trạng thái giao dịch {$amount} VND đã được cập nhật";
        }
    }

    /**
     * Get notification icon
     */
    private function getNotificationIcon(): string
    {
        switch ($this->transaction->status) {
            case 'completed':
                return 'fas fa-check-circle';
            case 'failed':
                return 'fas fa-times-circle';
            case 'cancelled':
                return 'fas fa-ban';
            case 'expired':
                return 'fas fa-clock';
            default:
                return 'fas fa-wallet';
        }
    }

    /**
     * Get notification color
     */
    private function getNotificationColor(): string
    {
        switch ($this->transaction->status) {
            case 'completed':
                return 'success';
            case 'failed':
                return 'danger';
            case 'cancelled':
                return 'secondary';
            case 'expired':
                return 'warning';
            default:
                return 'info';
        }
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\WalletTransaction;

class AdminWalletTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;
    protected $actionType; // 'new', 'status_updated'

    /**
     * Create a new notification instance.
     */
    public function __construct(WalletTransaction $transaction, string $actionType = 'new')
    {
        $this->transaction = $transaction;
        $this->actionType = $actionType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->getEmailSubject();
        $greeting = $this->getEmailGreeting();
        $content = $this->getEmailContent();
        
        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($content);

        // Add action button based on transaction type and action
        if ($this->transaction->type === 'withdraw' && $this->transaction->status === 'pending') {
            $mailMessage->action('Xem chi tiết yêu cầu rút tiền', route('admin.wallet.withdrawals.pending'));
        } elseif ($this->transaction->type === 'deposit') {
            $mailMessage->action('Xem lịch sử nạp tiền', route('admin.wallet.deposits.history'));
        } else {
            $mailMessage->action('Xem dashboard ví', route('admin.wallet.index'));
        }

        return $mailMessage->line('Cảm ơn bạn đã theo dõi hệ thống!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'transaction_code' => $this->transaction->transaction_code,
            'user_id' => $this->transaction->user_id,
            'user_name' => $this->transaction->user->name,
            'user_email' => $this->transaction->user->email,
            'type' => $this->transaction->type,
            'amount' => $this->transaction->amount,
            'formatted_amount' => number_format($this->transaction->amount, 0, ',', '.') . ' VND',
            'status' => $this->transaction->status,
            'payment_method' => $this->transaction->payment_method,
            'description' => $this->transaction->description,
            'created_at' => $this->transaction->created_at->toISOString(),
            'action_type' => $this->actionType,
            'title' => $this->getNotificationTitle(),
            'message' => $this->getNotificationMessage(),
            'icon' => $this->getNotificationIcon(),
            'color' => $this->getNotificationColor(),
            'url' => $this->getNotificationUrl(),
            'metadata' => $this->transaction->metadata,
        ];
    }

    /**
     * Get notification title
     */
    private function getNotificationTitle(): string
    {
        switch ($this->actionType) {
            case 'new':
                return $this->transaction->type === 'deposit' ? 'Yêu cầu nạp tiền mới' : 'Yêu cầu rút tiền mới';
            case 'status_updated':
                return 'Cập nhật trạng thái giao dịch';
            default:
                return 'Thông báo giao dịch ví';
        }
    }

    /**
     * Get notification message
     */
    private function getNotificationMessage(): string
    {
        $userName = $this->transaction->user->name;
        $amount = number_format($this->transaction->amount, 0, ',', '.');
        
        switch ($this->actionType) {
            case 'new':
                if ($this->transaction->type === 'deposit') {
                    return "{$userName} vừa nạp {$amount} VND vào ví";
                } else {
                    return "{$userName} vừa yêu cầu rút {$amount} VND từ ví";
                }
            case 'status_updated':
                switch ($this->transaction->status) {
                    case 'completed':
                        return $this->transaction->type === 'deposit' 
                            ? "{$userName} đã nạp thành công {$amount} VND"
                            : "Yêu cầu rút {$amount} VND của {$userName} đã được duyệt";
                    case 'failed':
                        return $this->transaction->type === 'deposit'
                            ? "Nạp tiền {$amount} VND của {$userName} thất bại"
                            : "Yêu cầu rút {$amount} VND của {$userName} đã bị từ chối";
                    case 'cancelled':
                        return "{$userName} đã hủy giao dịch {$amount} VND";
                    default:
                        return "Trạng thái giao dịch {$amount} VND của {$userName} đã thay đổi";
                }
            default:
                return "Có thông báo mới về giao dịch ví";
        }
    }

    /**
     * Get notification icon
     */
    private function getNotificationIcon(): string
    {
        if ($this->transaction->type === 'deposit') {
            return 'fas fa-plus-circle';
        } elseif ($this->transaction->type === 'withdraw') {
            return 'fas fa-minus-circle';
        } else {
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
            case 'pending':
                return 'warning';
            case 'cancelled':
                return 'secondary';
            default:
                return 'info';
        }
    }

    /**
     * Get notification URL
     */
    private function getNotificationUrl(): string
    {
        if ($this->transaction->type === 'withdraw') {
            if ($this->transaction->status === 'pending') {
                return route('admin.wallet.withdrawals.pending');
            } else {
                return route('admin.wallet.withdrawals.history');
            }
        } elseif ($this->transaction->type === 'deposit') {
            return route('admin.wallet.deposits.history');
        } else {
            return route('admin.wallet.index');
        }
    }

    /**
     * Get email subject
     */
    private function getEmailSubject(): string
    {
        $amount = number_format($this->transaction->amount, 0, ',', '.');
        
        if ($this->actionType === 'new') {
            return $this->transaction->type === 'deposit' 
                ? "Nạp tiền mới: {$amount} VND"
                : "Yêu cầu rút tiền mới: {$amount} VND";
        } else {
            return "Cập nhật giao dịch ví: {$amount} VND";
        }
    }

    /**
     * Get email greeting
     */
    private function getEmailGreeting(): string
    {
        return 'Xin chào Admin!';
    }

    /**
     * Get email content
     */
    private function getEmailContent(): string
    {
        $userName = $this->transaction->user->name;
        $userEmail = $this->transaction->user->email;
        $amount = number_format($this->transaction->amount, 0, ',', '.');
        $transactionCode = $this->transaction->transaction_code;
        
        $content = "Thông tin chi tiết:\n";
        $content .= "- Khách hàng: {$userName} ({$userEmail})\n";
        $content .= "- Mã giao dịch: {$transactionCode}\n";
        $content .= "- Loại giao dịch: " . ($this->transaction->type === 'deposit' ? 'Nạp tiền' : 'Rút tiền') . "\n";
        $content .= "- Số tiền: {$amount} VND\n";
        $content .= "- Trạng thái: " . $this->getStatusText($this->transaction->status) . "\n";
        
        if ($this->transaction->type === 'withdraw' && $this->transaction->metadata) {
            $bankInfo = is_string($this->transaction->metadata) 
                ? json_decode($this->transaction->metadata, true) 
                : $this->transaction->metadata;
            
            if ($bankInfo && isset($bankInfo['bank_name'])) {
                $content .= "- Ngân hàng: {$bankInfo['bank_name']}\n";
                $content .= "- Số tài khoản: {$bankInfo['bank_account']}\n";
                $content .= "- Chủ tài khoản: {$bankInfo['account_holder']}\n";
            }
        }
        
        return $content;
    }

    /**
     * Get status text in Vietnamese
     */
    private function getStatusText(string $status): string
    {
        $statusTexts = [
            'pending' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            'cancelled' => 'Đã hủy',
            'expired' => 'Hết hạn'
        ];

        return $statusTexts[$status] ?? 'Không xác định';
    }
}

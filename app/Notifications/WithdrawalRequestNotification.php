<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\WalletTransaction;

class WithdrawalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(WalletTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $bankInfo = $this->transaction->bank_info;
        
        return (new MailMessage)
            ->subject('Yêu cầu rút tiền cần xử lý - ' . $this->transaction->formatted_amount)
            ->greeting('Xin chào!')
            ->line('Có một yêu cầu rút tiền mới cần được xử lý.')
            ->line('**Thông tin giao dịch:**')
            ->line('- Mã giao dịch: ' . $this->transaction->transaction_code)
            ->line('- Số tiền: ' . $this->transaction->formatted_amount)
            ->line('- Phí xử lý: ' . number_format($this->transaction->processing_fee) . ' VND')
            ->line('- Số tiền thực nhận: ' . number_format($this->transaction->net_amount) . ' VND')
            ->line('- Khách hàng: ' . $this->transaction->user->full_name)
            ->line('- Email: ' . $this->transaction->user->email)
            ->line('- Điện thoại: ' . $this->transaction->user->phone)
            ->line('')
            ->line('**Thông tin ngân hàng:**')
            ->line('- Ngân hàng: ' . $bankInfo['bank_name'])
            ->line('- Số tài khoản: ' . $bankInfo['bank_account'])
            ->line('- Chủ tài khoản: ' . $bankInfo['account_holder'])
            ->line('')
            ->line('- Thời gian yêu cầu: ' . $this->transaction->created_at->format('d/m/Y H:i:s'))
            ->action('Xem chi tiết', url('/admin/wallet/withdrawals/' . $this->transaction->id))
            ->line('Vui lòng xử lý yêu cầu này trong thời gian sớm nhất.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'withdrawal_request',
            'transaction_id' => $this->transaction->id,
            'user_id' => $this->transaction->user_id,
            'amount' => $this->transaction->amount,
            'formatted_amount' => $this->transaction->formatted_amount,
            'transaction_code' => $this->transaction->transaction_code,
            'user_name' => $this->transaction->user->full_name,
            'user_email' => $this->transaction->user->email,
            'bank_info' => $this->transaction->bank_info,
            'created_at' => $this->transaction->created_at->toISOString(),
        ];
    }

    /**
     * Get the notification's database type.
     *
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'withdrawal_request';
    }
}

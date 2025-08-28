<?php

namespace App\Events\Wallet;

use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Notifications\AdminWalletTransactionNotification;
use App\Notifications\CustomerWalletTransactionNotification;

class WalletTransactionStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $oldStatus;
    public $newStatus;
    public $transactionData;

    /**
     * Create a new event instance.
     */
    public function __construct(WalletTransaction $transaction, string $oldStatus = null)
    {
        $this->transaction = $transaction;
        $this->oldStatus = $oldStatus ?? 'unknown';
        $this->newStatus = $transaction->status;
        
        // Prepare transaction data for broadcasting
        $this->transactionData = [
            'id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'formatted_amount' => number_format($transaction->amount, 0, ',', '.') . ' VND',
            'status' => $transaction->status,
            'old_status' => $this->oldStatus,
            'description' => $transaction->description,
            'transaction_code' => $transaction->transaction_code,
            'payment_method' => $transaction->payment_method,
            'processed_at' => $transaction->processed_at ? $transaction->processed_at->format('d/m/Y H:i:s') : null,
            'created_at' => $transaction->created_at->format('d/m/Y H:i:s'),
            'created_at_human' => $transaction->created_at->diffForHumans(),
            'user' => [
                'id' => $transaction->user->id,
                'name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
            'metadata' => $transaction->metadata,
        ];

        // Gửi notification cho admin nếu là withdrawal status update
        if ($transaction->type === 'withdraw' && in_array($this->newStatus, ['completed', 'failed', 'cancelled'])) {
            $admins = User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new AdminWalletTransactionNotification($transaction, 'status_updated'));
            }
        }

        // Gửi notification cho customer về status update
        if ($transaction->user) {
            $transaction->user->notify(new CustomerWalletTransactionNotification($transaction, $this->oldStatus));
        }
        
        Log::info('WalletTransactionStatusUpdated event constructed', [
            'transaction_id' => $transaction->id,
            'transaction_code' => $transaction->transaction_code,
            'type' => $transaction->type,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'user_id' => $transaction->user_id
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channels = [
            new PrivateChannel('admin.wallet'),
            new Channel('admin-wallet-channel'),
            new PrivateChannel('customer.' . $this->transaction->user_id),
            new PrivateChannel('wallet-transaction.' . $this->transaction->id),
        ];
        
        Log::info('WalletTransactionStatusUpdated broadcasting on channels', [
            'channels' => array_map(function($channel) {
                return $channel->name;
            }, $channels),
            'transaction_id' => $this->transaction->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus
        ]);
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'wallet-transaction-status-updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'transaction' => $this->transactionData,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->getNotificationMessage(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Get notification message based on status change
     */
    private function getNotificationMessage(): string
    {
        $userName = $this->transaction->user->name;
        $amount = number_format($this->transaction->amount, 0, ',', '.');
        
        switch ($this->newStatus) {
            case 'completed':
                if ($this->transaction->type === 'deposit') {
                    return "{$userName} đã nạp thành công {$amount} VND";
                } else {
                    return "Đã duyệt yêu cầu rút {$amount} VND của {$userName}";
                }
            case 'failed':
                if ($this->transaction->type === 'deposit') {
                    return "Nạp tiền {$amount} VND của {$userName} thất bại";
                } else {
                    return "Đã từ chối yêu cầu rút {$amount} VND của {$userName}";
                }
            case 'cancelled':
                return "{$userName} đã hủy giao dịch {$amount} VND";
            case 'expired':
                return "Giao dịch {$amount} VND của {$userName} đã hết hạn";
            default:
                return "Trạng thái giao dịch {$amount} VND của {$userName} đã thay đổi";
        }
    }
}

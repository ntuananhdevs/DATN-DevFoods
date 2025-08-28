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

class NewWalletTransactionReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $transactionData;

    /**
     * Create a new event instance.
     */
    public function __construct(WalletTransaction $transaction)
    {
        $this->transaction = $transaction;
        
        // Prepare transaction data for broadcasting
        $this->transactionData = [
            'id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'formatted_amount' => number_format($transaction->amount, 0, ',', '.') . ' VND',
            'status' => $transaction->status,
            'description' => $transaction->description,
            'transaction_code' => $transaction->transaction_code,
            'payment_method' => $transaction->payment_method,
            'created_at' => $transaction->created_at->format('d/m/Y H:i:s'),
            'created_at_human' => $transaction->created_at->diffForHumans(),
            'user' => [
                'id' => $transaction->user->id,
                'name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
            'metadata' => $transaction->metadata,
        ];

        // Gửi notification cho tất cả admin
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new AdminWalletTransactionNotification($transaction, 'new'));
        }
        
        Log::info('NewWalletTransactionReceived event constructed', [
            'transaction_id' => $transaction->id,
            'transaction_code' => $transaction->transaction_code,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'status' => $transaction->status,
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
        ];
        
        Log::info('NewWalletTransactionReceived broadcasting on channels', [
            'channels' => array_map(function($channel) {
                return $channel->name;
            }, $channels),
            'transaction_id' => $this->transaction->id,
            'type' => $this->transaction->type
        ]);
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-wallet-transaction';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'transaction' => $this->transactionData,
            'message' => $this->getNotificationMessage(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Get notification message based on transaction type
     */
    private function getNotificationMessage(): string
    {
        $userName = $this->transaction->user->name;
        $amount = number_format($this->transaction->amount, 0, ',', '.');
        
        switch ($this->transaction->type) {
            case 'deposit':
                return "{$userName} vừa nạp {$amount} VND vào ví";
            case 'withdraw':
                return "{$userName} vừa yêu cầu rút {$amount} VND từ ví";
            default:
                return "{$userName} vừa thực hiện giao dịch ví {$amount} VND";
        }
    }
}

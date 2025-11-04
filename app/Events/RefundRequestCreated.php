<?php

namespace App\Events;

use App\Models\RefundRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $refundRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(RefundRequest $refundRequest)
    {
        $this->refundRequest = $refundRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-notifications'),
            new PrivateChannel('branch.' . $this->refundRequest->branch_id),
            new PrivateChannel('user.' . $this->refundRequest->customer_id)
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'refund.request.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->refundRequest->id,
            'refund_code' => $this->refundRequest->refund_code,
            'order_code' => $this->refundRequest->order->order_code,
            'customer_name' => $this->refundRequest->customer->name,
            'refund_amount' => $this->refundRequest->refund_amount,
            'status' => $this->refundRequest->status,
            'created_at' => $this->refundRequest->created_at->toISOString(),
            'message' => 'Có yêu cầu hoàn tiền mới từ khách hàng ' . $this->refundRequest->customer->name
        ];
    }
}
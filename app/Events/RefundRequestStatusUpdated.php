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

class RefundRequestStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $refundRequest;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(RefundRequest $refundRequest, string $oldStatus, string $newStatus)
    {
        $this->refundRequest = $refundRequest;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        return 'refund.request.status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'under_review' => 'Đang xem xét',
            'approved' => 'Đã duyệt',
            'rejected' => 'Đã từ chối',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        return [
            'id' => $this->refundRequest->id,
            'refund_code' => $this->refundRequest->refund_code,
            'order_code' => $this->refundRequest->order->order_code,
            'customer_name' => $this->refundRequest->customer->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'old_status_label' => $statusLabels[$this->oldStatus] ?? $this->oldStatus,
            'new_status_label' => $statusLabels[$this->newStatus] ?? $this->newStatus,
            'updated_at' => $this->refundRequest->updated_at->toISOString(),
            'message' => 'Yêu cầu hoàn tiền ' . $this->refundRequest->refund_code . ' đã được cập nhật trạng thái thành "' . ($statusLabels[$this->newStatus] ?? $this->newStatus) . '"'
        ];
    }
}
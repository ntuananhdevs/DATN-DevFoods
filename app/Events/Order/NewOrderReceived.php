<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewOrderNotification;
use App\Notifications\AdminNewOrderNotification;
use App\Notifications\CustomerOrderSuccessNotification;
use App\Models\Branch;
use App\Models\User;

class NewOrderReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $branchId;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->branchId = $order->branch_id;
        
        // Chỉ gửi notification cho branch và admin khi đơn hàng không ở trạng thái pending_payment
        if ($order->status !== 'pending_payment') {
            // Gửi notification cho branch
            $branch = Branch::find($this->branchId);
            if ($branch) {
                $branch->notify(new NewOrderNotification($order));
            }
            
            // Gửi notification cho tất cả admin
            $admins = User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new AdminNewOrderNotification($order));
            }
        }
        
        // Gửi notification cho khách hàng
        if ($order->customer) {
            $order->customer->notify(new CustomerOrderSuccessNotification($order));
        }
        
        Log::info('NewOrderReceived event constructed', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'branch_id' => $this->branchId,
            'status' => $order->status
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channels = [
            new PrivateChannel('branch.' . $this->branchId),
            new Channel('branch-orders-channel'),
        ];
        
        Log::info('NewOrderReceived broadcasting on channels', [
            'channels' => array_map(function($channel) {
                return $channel->name;
            }, $channels),
            'order_id' => $this->order->id,
            'branch_id' => $this->branchId
        ]);
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-order-received';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        // Load relationships
        $this->order->load(['payment', 'orderItems', 'address', 'branch']);
        
        // Calculate total items count
        $itemsCount = $this->order->orderItems->sum('quantity');
        
        // Calculate distance if address exists
        $distanceKm = null;
        if ($this->order->address && $this->order->branch) {
            $orderLat = $this->order->address->latitude ?? null;
            $orderLng = $this->order->address->longitude ?? null;
            $branchLat = $this->order->branch->latitude ?? null;
            $branchLng = $this->order->branch->longitude ?? null;
            
            if ($orderLat && $orderLng && $branchLat && $branchLng) {
                $distanceKm = $this->calculateDistance($branchLat, $branchLng, $orderLat, $orderLng);
            }
        }
        
        $data = [
            'order' => [
                'id' => $this->order->id,
                'code' => $this->order->order_code,
                'order_code' => $this->order->order_code,
                'status' => $this->order->status,
                'status_text' => $this->order->statusText,
                'status_color' => $this->order->statusColor,
                'customer_name' => $this->order->customerName,
                'customer_phone' => $this->order->customerPhone,
                'total_amount' => $this->order->total_amount,
                'order_date' => $this->order->order_date,
                'estimated_delivery_time' => $this->order->estimated_delivery_time,
                'points_earned' => $this->order->points_earned,
                'notes' => $this->order->notes,
                'items_count' => $itemsCount,
                'distance_km' => $distanceKm,
                'customer' => $this->order->customer ? [
                    'id' => $this->order->customer->id,
                    'name' => $this->order->customer->name,
                    'phone' => $this->order->customer->phone,
                    'orders_count' => $this->order->customer->orders()->count(),
                    'last_order_date' => $this->order->customer->orders()->latest()->first()?->order_date?->format('Y-m-d')
                ] : null,
                'payment' => $this->order->payment ? [
                    'id' => $this->order->payment->id,
                    'method' => $this->order->payment->payment_method,
                    'payment_method' => $this->order->payment->payment_method,
                    'payment_status' => $this->order->payment->payment_status,
                    'payment_amount' => $this->order->payment->payment_amount,
                    'payment_date' => $this->order->payment->payment_date,
                ] : null,
                'branch' => $this->order->branch ? [
                    'id' => $this->order->branch->id,
                    'name' => $this->order->branch->name,
                    'address' => $this->order->branch->address,
                    'phone' => $this->order->branch->phone,
                ] : null
            ],
            'branch_id' => $this->branchId,
            'timestamp' => now()->toISOString()
        ];
        
        Log::info('NewOrderReceived broadcasting data', [
            'order_id' => $this->order->id,
            'data' => $data
        ]);
        
        return $data;
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
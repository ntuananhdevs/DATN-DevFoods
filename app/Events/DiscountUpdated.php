<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\DiscountCode;

class DiscountUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $discount;
    public $action; // create/update/delete
    public $discountData;

    public function __construct($discount, $action)
    {
        $this->discount = $discount;
        $this->action = $action;
        
        // Prepare discount data for broadcasting
        $this->discountData = [
            'id' => $discount->id,
            'code' => $discount->code,
            'name' => $discount->name,
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
            'min_requirement_type' => $discount->min_requirement_type,
            'min_requirement_value' => $discount->min_requirement_value,
            'applicable_scope' => $discount->applicable_scope,
            'applicable_items' => $discount->applicable_items,
            'usage_type' => $discount->usage_type,
            'is_active' => $discount->is_active,
            'is_featured' => $discount->is_featured,
            'start_date' => $discount->start_date,
            'end_date' => $discount->end_date,
            'valid_days_of_week' => $discount->valid_days_of_week,
            'valid_from_time' => $discount->valid_from_time,
            'valid_to_time' => $discount->valid_to_time,
            'max_total_usage' => $discount->max_total_usage,
            'max_usage_per_user' => $discount->max_usage_per_user,
            'timestamp' => now()->toISOString()
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel('discounts'); // public channel
    }

    public function broadcastAs()
    {
        return 'discount-updated';
    }
}


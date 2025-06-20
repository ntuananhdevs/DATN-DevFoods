<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VariantPriceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $variantValueId;
    public $attributeId;
    public $attributeName;
    public $variantValue;
    public $oldPriceAdjustment;
    public $newPriceAdjustment;
    public $branchId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($productId, $variantValueId, $attributeId, $attributeName, $variantValue, $oldPriceAdjustment, $newPriceAdjustment, $branchId)
    {
        $this->productId = $productId;
        $this->variantValueId = $variantValueId;
        $this->attributeId = $attributeId;
        $this->attributeName = $attributeName;
        $this->variantValue = $variantValue;
        $this->oldPriceAdjustment = $oldPriceAdjustment;
        $this->newPriceAdjustment = $newPriceAdjustment;
        $this->branchId = $branchId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::debug('Broadcasting variant price update event on channel: branch-stock-channel', [
            'productId' => $this->productId,
            'variantValueId' => $this->variantValueId,
            'attributeId' => $this->attributeId,
            'attributeName' => $this->attributeName,
            'variantValue' => $this->variantValue,
            'oldPriceAdjustment' => $this->oldPriceAdjustment,
            'newPriceAdjustment' => $this->newPriceAdjustment,
            'branchId' => $this->branchId,
            'event_name' => $this->broadcastAs(),
            'broadcast_data' => $this->broadcastWith()
        ]);

        return new Channel('branch-stock-channel');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'variant-price-updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'productId' => $this->productId,
            'variantValueId' => $this->variantValueId,
            'attributeId' => $this->attributeId,
            'attributeName' => $this->attributeName,
            'variantValue' => $this->variantValue,
            'oldPriceAdjustment' => $this->oldPriceAdjustment,
            'newPriceAdjustment' => $this->newPriceAdjustment,
            'branchId' => $this->branchId
        ];
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemTopping extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'topping_id',
        'quantity',
        'price',
        // Snapshot fields
        'topping_name_snapshot',
        'topping_unit_price_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the order item that owns the topping.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the topping associated with the order item.
     */
    public function topping()
    {
        return $this->belongsTo(Topping::class);
    }

    /**
     * Calculate total price for this topping
     */
    public function getTotalPriceAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get topping name (snapshot first, then from relation)
     */
    public function getDisplayToppingNameAttribute()
    {
        return $this->topping_name_snapshot ?? $this->topping?->name ?? 'Không xác định';
    }

    /**
     * Get topping image (from relation only since we don't snapshot images)
     * Note: We don't have topping_image_snapshot field in database
     */
    public function getDisplayToppingImageAttribute()
    {
        return $this->topping?->image ?? null;
    }

    /**
     * Check if this topping has snapshot data
     */
    public function hasSnapshotData()
    {
        return !empty($this->topping_name_snapshot);
    }
}
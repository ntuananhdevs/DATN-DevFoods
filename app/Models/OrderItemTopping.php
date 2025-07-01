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
        'price'
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
} 
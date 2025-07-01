<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItemTopping extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_item_id',
        'topping_id',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the cart item that owns the topping.
     */
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    /**
     * Get the topping associated with the cart item.
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
        return $this->topping->price * $this->quantity;
    }
} 
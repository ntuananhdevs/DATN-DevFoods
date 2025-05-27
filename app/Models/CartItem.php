<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_variant_id', 'combo_id', 'quantity', 'notes'];

    /**
     * Get the cart that owns the item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product variant for this cart item.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Alias for productVariant - to match the naming in controllers and views
     */
    public function variant()
    {
        return $this->productVariant();
    }

    /**
     * Get the combo for this cart item.
     */
    public function combo()
    {
        return $this->belongsTo(Combo::class, 'combo_id');
    }

    /**
     * Get the toppings for this cart item.
     */
    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'cart_item_toppings', 'cart_item_id', 'topping_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}

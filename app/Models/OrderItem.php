<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'combo_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product variant associated with the item.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the combo associated with the item.
     */
    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    /**
     * Get the toppings for this order item.
     */
    public function toppings()
    {
        return $this->hasMany(OrderItemTopping::class);
    }

    /**
     * Get the product through product variant.
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductVariant::class, 'id', 'id', 'product_variant_id', 'product_id');
    }
}
